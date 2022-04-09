<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace ExternalApi\Model\ExternalApi\Verification;

use ExternalApi\Model\AbstractMethods\AbstractMethod;
use ExternalApi\Model\ExternalApi\Oauth\Login;
use Users\Model\Verification\Action\AbstractVerifyAction;
use Users\Model\Verification\VerificationEngine;
use ExternalApi\Model\Exception as ApiException;

/**
 * Отправляет код для второго фактора верификации
 */
class SendCode extends AbstractMethod
{
    /**
     * Повторно отправляет проверочный код сессии верификации.
     * Сессия верификации может быть использована для второго фактора авторизации или для подтверждения номера телефона в формах.
     *
     * @param string $session_token Токен сессии верификации
     * @param string $phone Номер телефона обязателен, если это inline форма подтверждения номера телефона
     *
     * @example Пример неудачного запроса на повторную отправку кода при авторизации. Ошибка записана в поле error
     * GET /api/methods/verification.sendCode?session_token=c6e71f40ab23da3b518925691630abd8b6de84c4
     * <pre>
     * {
     *       "response": {
     *           "verification": {
     *               "session": {
     *                   "token": "c6e71f40ab23da3b518925691630abd8b6de84c4",
     *                   "error": "Отправить код можно будет через 00:47 секунд",
     *                   "code_send_flag": true,
     *                   "code_refresh_delay": 47,
     *                   "code_send_phone_mask": "+70000****01",
     *                   "code_debug": "8042"
     *                }
     *            }
     *       }
     * }
     * </pre>
     * Пример удачного запроса на отправку кода верификации
     *
     * <pre>
     * {
     *       "response": {
     *           "verification": {
     *               "session": {
     *                   "token": "c6e71f40ab23da3b518925691630abd8b6de84c4",
     *                   "error": "",
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
     * @throws ApiException
     * @throws \Users\Model\Verification\VerificationException
     *
     * @return array Возвращает полные сведения о верификационной сессии, включая ошибку, если она есть.
     *
     * <b>response.verification.session.token</b> - token верификационной сессии, будет необходим при отправке кода второго фактора
     * <b>response.verification.session.error</b> - ошибка, которую нужно отобразить возле формы ввода второго фактора
     * <b>response.verification.session.code_send_flag</b> - флаг, говорящий о том, что код был отправлен
     * <b>response.verification.session.code_refresh_delay</b> - количесто секунд, через которое можно повторить отправку кода
     * <b>response.verification.session.code_send_phone_mask</b> - маска нмера телефона, на который был отправлен код
     * <b>response.verification.session.code_debug</b> - отладочный код второго фактора. (будет присутствовать, если включен "демо-режим" в настройках модуля Пользователи и группы)
     */
    function process($session_token, $phone = null)
    {
        $verification_engine = new VerificationEngine();

        if (!$verification_engine->initializeByToken($session_token)) {
            throw new ApiException(t('Верификационная сессия с таким токеном не найдена'), ApiException::ERROR_OBJECT_NOT_FOUND);
        }

        if ($verification_engine->getAction()->getTypeVerification() == AbstractVerifyAction::TYPE_VERIFICATION_PHONE_INLINE) {
            $verification_engine->setPhone($phone); //установим телефон, на который необходимо отправить код
        }

        $verification_engine->sendCode();

        return [
            'response' => [
                'verification' => [
                    'session' => Login::makeResponseVerificationSessionData($verification_engine->getSession())
                ]
            ]
        ];
    }
}