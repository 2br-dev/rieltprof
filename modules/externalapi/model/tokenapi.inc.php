<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace ExternalApi\Model;

/**
* Работа с авторизационными токенами
*/
class TokenApi extends \RS\Module\AbstractModel\EntityList
{
    function __construct()
    {
        parent::__construct(new Orm\AuthorizationToken(), [
            'idField' => 'token'
        ]);
    }

    /**
     * Создаёт новый токен для пользователя по его id и id клиентского модуля
     *
     * @param integer $user_id - id пользователя
     * @param integer $client_id - id клиентского приложения для которго создан токен
     * @return Orm\AuthorizationToken
     * @throws \RS\Exception
     */
    public static function createToken($user_id, $client_id)
    {
         $config = \RS\Config\Loader::byModule('externalapi'); 
         $token = new \ExternalApi\Model\Orm\AuthorizationToken();
            
         //Удаляем протухшие token'ы
         \RS\Orm\Request::make()
            ->delete()
            ->from($token)
            ->where("expire < '#time'", [
                'time' => time(),
            ])->exec();
        
         //Регистрируем новый token
         $token['user_id'] = $user_id;
         $token['app_type'] = $client_id;
         $token['ip'] = $_SERVER['REMOTE_ADDR'];
         $token['dateofcreate'] = date('c');
         $token['expire'] = time() + $config->token_lifetime;
         $token->insert();
         return $token;
    }
}
