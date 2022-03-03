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
* Интерфейс 
*/
interface InterfaceHasApi
{
    /**
    * Возвращает true, если client_secret корректный
    * 
    * @return string
    */
    public function checkSecret($client_secret);
    
    /**
    * Метод возвращает массив, содержащий требуемые права доступа к json api для приложения
    * 
    * @return [
    *   'oauth/authorize' => [RIGHT_CODE_1, RIGHT_CODE_2,...]  //Точно перечисленные права
    *   'oauth/token' => FULL_RIGHTS //Полные права
    * ]
    */
    public function getAppRights();
    
    /**
    * Устанавливает/сбрасывает token, который может влиять на результат метода getAppRights
    * 
    * @param AuthorizationToken $token
    */
    public function setToken(AuthorizationToken $token = null);
    
    /**
    * Возвращает авторизационный token
    * 
    * @return AuthorizationToken
    */
    public function getToken();
    
    /**
    * Возвращает группы пользователей, которым доступно данное приложение
    * 
    * @return ["group_id_1", "group_id_2", ...]
    */
    public function getAllowUserGroup();    
}
