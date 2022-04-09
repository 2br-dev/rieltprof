<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace ExternalApi\Model\ExternalApi\Verification;

use ExternalApi\Model\AbstractMethods\AbstractMethod;
use ExternalApi\Model\Exception as ApiException;
use ExternalApi\Model\ExternalApi\Oauth\Login;
use ExternalApi\Model\TokenApi;
use RS\Application\Auth;
use RS\Site\Manager as SiteManager;
use Users\Model\Verification\VerificationEngine;

/**
 * Отправляет код для второго фактора верификации
 */
class CheckCode extends AbstractMethod
{

    /**
     * Проверяет верификационный код, завершает начатое действие для которого нужен код.
     * (Если это авторизация, то авторизовывает пользователя. Если это проверка номера телефона, то подтверждает номер телефона)
     *
     * @param string $session_token Токен сессии верификации
     * @param string $code ерификационный код
     * @example Успешный ответ для корректного верификационного кода
     *
     * POST /api/methods/verification.checkCode
     * session_token=c6e71f40ab23da3b518925691630abd8b6de84c4&code=9085
     * <pre>
     * {
     *      'response': {
     *            'success': true,
     *            'auth': {
     *                'token' => '38b83885448a8ad9e2fb4f789ec6b0b690d50041',
     *                'expire' => '1504785044',
     *            },
     *            'user': {
     *                "id": "1",
     *                "name": "Супервизор тест тест",
     *                "surname": " Моя фамилия",
     *                "midname": " ",
     *                "e_mail": "admin3@admin.ru",
     *                "login": "admin3@admin.ru",
     *                "phone": "+7(xxx)xxx-xx-xx",
     *                "sex": "",
     *                "subscribe_on": "0",
     *                "dateofreg": "2016-03-14 19:58:58",
     *                "ban_expire": null,
     *                "last_visit": "2016-11-09 15:29:14",
     *                "is_company": "0",
     *                "company": "",
     *                "company_inn": "",
     *                "data": [],
     *                "push_lock": null,
     *                "user_cost": null,
     *                "birthday": null,
     *                "fio": "Моя фамилия Супервизор тест тест",
     *                "groups": [
     *                    "guest",
     *                    "clients",
     *                    "supervisor"
     *                ],
     *                "is_courier": false
     *            },
     *            'site_uid' : "38b83885448a8ad9e2fb4f789ec6b0b690d50041"
     *        }
     * }
     * </pre>
     * В случае ошибочного верификационного кода, возвращаются сведения о верификационной сессии
     *
     * <pre>
     * {
     *       "response": {
     *           "success": false,
     *           "verification": {
     *               "session": {
     *                   "token": "c6e71f40ab23da3b518925691630abd8b6de84c4",
     *                   "error": "Код просрочен, получите новый код",
     *                   "code_send_flag": true,
     *                   "code_refresh_delay": 60,
     *                   "code_send_phone_mask": "+70000****01",
     *                   "code_debug": "8042"
     *                }
     *            }
     *       }
     * }
     * </pre>
     *
     * @return array
     * @throws ApiException
     */
    function process($session_token, $code)
    {
        $verification_engine = new VerificationEngine();

        if (!$verification_engine->initializeByToken($session_token)) {
            throw new ApiException(t('Верификационная сессия с таким токеном не найдена'), ApiException::ERROR_OBJECT_NOT_FOUND);
        }

        $ok = $verification_engine->checkCode($code);
        $action = $verification_engine->getSession()->getAction();

        $response = [
            'response' => [
                'success' => $ok !== false,
            ]
        ];

        if ($ok !== false && in_array($action->getId(),
                ['users-twostepauthorize', 'users-twostepregisterbyphone'])) {
            //В случае успеха создаем авторизационный токен и возвращаем сведения о пользователе

            $user = Auth::getCurrentUser();
            $token = TokenApi::createToken($user['id'], $action->getAppClientId());

            $response['response'] += [
                'auth' => Login::makeResponseAuthTokenData($token),
                'user' => Login::makeResponseUserData($user),
                'site_uid' => SiteManager::getSite()->getSiteHash()
            ];
        } else {
            $response['response'] += [
                'verification' => [
                    'session' => Login::makeResponseVerificationSessionData($verification_engine->getSession())
                ]
            ];
        }

        return $response;
    }
}