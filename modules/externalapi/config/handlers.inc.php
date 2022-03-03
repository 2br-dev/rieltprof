<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace ExternalApi\Config;

use ExternalApi\Model\Behavior\UsersUser;
use ExternalApi\Model\Orm\UserApiMethodAccess;
use RS\Orm\Request;
use Users\Model\Orm\User;
use RS\Orm\Type;

class Handlers extends \RS\Event\HandlerAbstract
{
    function init()
    {
        $this
            ->bind('initialize')
            ->bind('getroute')
            ->bind('orm.init.users-user')
            ->bind('orm.afterwrite.users-user')
            ->bind('externalapi.getexceptions');
    }

    /**
     * Расширяет поведение объекта Пользователь
     */
    public static function initialize()
    {
        \Users\Model\Orm\User::attachClassBehavior(new UsersUser());
    }

    /**
     * Регистрирует маршруты модуля в системе
     *
     * @param $routes
     * @return array
     */
    public static function getRoute($routes)
    {
        $config = \RS\Config\Loader::byModule(__CLASS__);
        $api_key = $config->api_key ? '-'.$config->api_key : '';
        
        $routes[] = new \RS\Router\Route('externalapi-front-apigate', [
            "/api{$api_key}/methods/{method}",
        ], null, t('Шлюз обмена данными по API'), true);

        $routes[] = new \RS\Router\Route('externalapi-front-apigate-help', [
            "/api{$api_key}/help/{method}",
            "/api{$api_key}/help"
        ], [
            'controller' => 'externalapi-front-apigate',
            'Act' => 'help'
        ], t('Описание методов API'), true);
        
        return $routes;
    }


    /**
     * Добавляет к пользователю возможность настроить разрешенные методы API, требующие авторизации
     */
    public static function ormInitUsersUser(User $user)
    {
        $user->getPropertyIterator()->append([
            t('Внешнее API'),
            'allow_api_methods' => new Type\ArrayList([
                'description' => t('Разрешенные авторизованные методы API'),
                'hint' => t('Методы API, поддерживающие передачу авторизационного токена могут быть включены для выборочных пользователей. Данные настройки будут перекрывать настройки модуля Внешнее API'),
                'list' => [['ExternalApi\Model\ApiRouter', 'getAuthorizedApiMethodsSelectList'], ['all' => 'Все']],
                'checkboxListView' => true,
                'sites' => \RS\Site\Manager::getSiteList(),
                'template' => '%externalapi%/form/user/allow_api_methods.tpl'
            ])
        ]);
    }
    
    /**
    * Удаляет все token'ы, выданные пользователю, если тот сменил пароль
    * 
    * @param array $param
    */
    public static function ormAfterwriteUsersUser($param)
    {
        $user = $param['orm'];
        if ($user->isModified('pass')) {
            
            \RS\Orm\Request::make()
                ->delete()
                ->from(new \ExternalApi\Model\Orm\AuthorizationToken())
                ->where([
                    'user_id' => $user['id']
                ])
                ->exec();
        }

        //Сохраняем доступные методы API для пользователя
        if ($user->isModified('allow_api_methods')) {
            Request::make()
                ->delete()
                ->from(new UserApiMethodAccess())
                ->where([
                    'user_id' => $user['id']
                ])->exec();

            foreach($user['allow_api_methods'] as $site_id => $methods) {
                foreach($methods as $method) {
                    $user_access = new UserApiMethodAccess();
                    $user_access['site_id'] = $site_id;
                    $user_access['user_id'] = $user['id'];
                    $user_access['api_method'] = $method;
                    $user_access->insert();
                }
            }
        }
    }
    
    /**
    * Возвращаем классы исключений, которые используются в методах API
    * 
    * @param \ExternalApi\Model\AbstractException[] $list
    * @return \ExternalApi\Model\AbstractException[]
    */
    public static function externalApiGetExceptions($list)
    {
        $list[] = new \ExternalApi\Model\Exception();
        return $list;
    }
}
