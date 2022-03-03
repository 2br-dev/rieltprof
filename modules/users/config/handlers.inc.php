<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Users\Config;

use RS\Application\Auth as AppAuth;
use RS\Db\Exception as DbException;
use RS\Event\Exception as EventException;
use RS\Event\HandlerAbstract;
use RS\Exception as RSException;
use RS\Orm\Exception as OrmException;
use RS\Router;
use Users\Model\GroupApi;
use Users\Model\Verification\Provider\Sms;

class Handlers extends HandlerAbstract
{
    function init()
    {
        $this
            ->bind('start')
            ->bind('getmenus')
            ->bind('getroute')
            ->bind('verification.getproviders')
            ->bind('orm.afterwrite.site-site');
    }

    public static function getRoute(array $routes)
    {
        $routes[] = new Router\Route('users-front-auth', ['/auth/{Act}/', '/auth/'], null, t('Авторизация'));
        $routes[] = new Router\Route('users-front-register', '/register/', null, t('Регистрация пользователя'));
        $routes[] = new Router\Route('users-front-profile', '/my/', null, t('Профиль пользователя'), false, '^{pattern}$');

        return $routes;
    }

    /**
     * Добавляет в систему провайдеры верификации кода
     */
    public static function verificationGetProviders($list)
    {
        $list[] = new Sms();
        return $list;
    }

    /**
     * Возвращает пункты меню этого модуля в виде массива
     *
     * @param array $items
     * @return array
     */
    public static function getMenus($items)
    {
        $items[] = [
            'title' => t('Учетные записи'),
            'alias' => 'users',
            'link' => '%ADMINPATH%/users-ctrl/',
            'typelink' => 'link',
            'parent' => 'userscontrol',
            'sortn' => 10
        ];
        $items[] = [
            'title' => t('Группы'),
            'alias' => 'groups',
            'link' => '%ADMINPATH%/users-ctrlgroup/',
            'typelink' => 'link',
            'parent' => 'userscontrol',
            'sortn' => 20
        ];
        return $items;
    }

    /**
     * Очищает логи по вероятности
     */
    public static function start()
    {
        if (\Setup::$INSTALLED) {
            //Сохраняем дату последнего посещения
            if (!Router\Manager::obj()->isAdminZone()) {
                AppAuth::getCurrentUser()->saveVisitDate();
            }
        }
    }

    /**
     * Обработка события создания cайта, копирование прав доступа групп пользователей
     *
     * @param $params
     * @throws DbException
     * @throws EventException
     * @throws RSException
     * @throws OrmException
     */
    public static function ormAfterwriteSiteSite($params)
    {
        if (\Setup::$INSTALLED) {
            $new_site_id = $params['orm']['id'];
            GroupApi::cloneRightFromDefaultSite($new_site_id);
        }
    }
}
