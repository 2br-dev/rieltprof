<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace ExternalApi\Model\App;

use \ExternalApi\Model\Orm\AuthorizationToken;

/**
 * Класс описывает Приложение (Тип приложения), а также требуемые права для приложения.
 *
 * Идентификатор приложения нужно будет передавать для получения авторизационного токена вместе с логином и паролем.
 * Для успешного получения авторизационного токена, пользователь должен иметь права к данному приложению.
 *
 * Токен всегда будет привязан к Приложению, что позволит по токену всегда понять какие права он имеет
 */
abstract class AbstractAppType extends \RS\RemoteApp\AbstractAppType implements InterfaceHasApi
{
    const FULL_RIGHTS = 'all';

    /** @var AuthorizationToken */
    private $token;

    /**
     * Устанавливает/сбрасывает token, который может влиять на результат метода getAppRights
     *
     * @param AuthorizationToken $token
     */
    public function setToken(AuthorizationToken $token = null)
    {
        $this->token = $token;
    }

    /**
     * Возвращает token, установленный с помощью setToken
     *
     * @return AuthorizationToken
     */
    public function getToken()
    {
        return $this->token;
    }
}
