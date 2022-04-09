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
use Users\Model\Verification\VerificationEngine;

/**
 * Отправляет код для второго фактора верификации
 */
class ResetSession extends AbstractMethod
{

    /**
     * Сбрасывает верификацию. Позволяет изменить номер телефона
     * Используется только для inline форм подтверждения номера
     *
     * @param string $session_token Токен сессии верификации
     *
     * @example Пример удачного запроса на сброс верификации
     * GET /api/methods/verification.resetSession?session_token=c6e71f40ab23da3b518925691630abd8b6de84c4
     *
     * <pre>
     * {
     *       "response": {
     *           "verification": {
     *               "session": {
     *                   "token": "c6e71f40ab23da3b518925691630abd8b6de84c4",
     *                   "error": "",
     *                   "code_send_flag": false,
     *                   "code_refresh_delay": 0
     *                }
     *            }
     *       }
     * }
     * </pre>
     *
     * @throws ApiException
     * @throws \Users\Model\Verification\VerificationException
     * @return array Возвращает полные сведения о верификационной сессии, включая ошибку, если она есть.
     *
     * <b>response.verification.session.token</b> - token верификационной сессии, будет необходим при отправке кода второго фактора
     * <b>response.verification.session.error</b> - ошибка, которую нужно отобразить возле формы ввода второго фактора
     * <b>response.verification.session.code_send_flag</b> - флаг, говорящий о том, что код был отправлен
     * <b>response.verification.session.code_refresh_delay</b> - количесто секунд, через которое можно повторить отправку кода
     * <b>response.verification.session.code_send_phone_mask</b> - маска нмера телефона, на который был отправлен код
     * <b>response.verification.session.code_debug</b> - отладочный код второго фактора. (будет присутствовать, если включен "демо-режим" в настройках модуля Пользователи и группы)
     */
    function process($session_token)
    {
        $verification_engine = new VerificationEngine();

        if (!$verification_engine->initializeByToken($session_token)) {
            throw new ApiException(t('Верификационная сессия с таким токеном не найдена'), ApiException::ERROR_OBJECT_NOT_FOUND);
        }

        $verification_engine->reset();

        return [
            'response' => [
                'verification' => [
                    'session' => Login::makeResponseVerificationSessionData($verification_engine->getSession())
                ]
            ]
        ];
    }
}