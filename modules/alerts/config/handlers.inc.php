<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Alerts\Config;

use Alerts\Model\Log\LogAlerts;
use Alerts\Model\SMS\SMSUslugi\Sender as SMSUslugiSender;
use Alerts\Model\Orm\NoticeLock;
use RS\Event\HandlerAbstract;
use RS\Log\AbstractLog;
use RS\Orm\Type\ArrayList;
use RS\Orm\Type;

class Handlers extends HandlerAbstract
{
    function init()
    {

        $this->bind('getlogs');
        $this->bind('getmenus');
        $this->bind('getroute');
        $this->bind('alerts.getsmssenders');
        $this->bind('orm.init.users-user');
        $this->bind('orm.init.site-config');
        $this->bind('orm.afterwrite.users-user');
        $this->bind('getapps');
    }

    public static function alertsGetSmsSenders($list)
    {
        $list[] = new SMSUslugiSender();
        return $list;
    }

    public static function getRoute($routes)
    {
        return $routes;
    }

    /**
     * Возвращает пункты меню этого модуля в виде массива
     *
     */
    public static function getMenus($items)
    {
        $items[] = [
            'title' => t('Уведомления'),
            'alias' => 'alerts',
            'link' => '%ADMINPATH%/alerts-ctrl/',
            'typelink' => 'link',
            'parent' => 'website',
            'sortn' => 90
        ];
        return $items;
    }

    /**
     * Возвращает классы логирования этого модуля
     *
     * @param AbstractLog[] $list - список классов логирования
     * @return AbstractLog[]
     */
    public static function getLogs($list)
    {
        $list[] = LogAlerts::getInstance();
        return $list;
    }

    /**
     * Привносим Desktop приложение Уведомления ReadyScript в список приложений для API
     *
     * @param \Alerts\Model\AppTypes\Notifier[] $app_types
     * @return \Alerts\Model\AppTypes\Notifier[]
     */
    public static function getApps($app_types)
    {
        $app_types[] = new \Alerts\Model\AppTypes\Notifier();
        return $app_types;
    }

    /**
     * Расширяем объект пользователя, добавляем поля, связанные с выбором
     * доступных типов уведомлений для Desktop приложения
     *
     * @param \Users\Model\Orm\User $user
     */
    public static function ormInitUsersUser($user)
    {
        $user->getPropertyIterator()->append([
            t('Desktop-уведомления'),
            'desktop_notice_locks' => new ArrayList([
                'description' => t('Запретить Desktop уведомления'),
                'hint' => t('Отметьте уведомления, которые не будут доступны в Desktop приложении для данного пользователя'),
                'template' => '%alerts%/form/user/user_desktop_notices.tpl',
                'alerts_api' => new \Alerts\Model\Api(),
                'sites' => \RS\Site\Manager::getSiteList(),
                'meVisible' => false,
            ])
        ]);
    }

    /**
     * Расширяем настройки сайта
     */
    public static function ormInitSiteConfig($site_config)
    {
        $site_config->getPropertyIterator()->append([
            t('Организация'),
            'firm_name_for_notice' => new Type\Varchar([
                'description' => t('Наименование организации в письмах'),
                'hint' => t('Если не указано - в письмах будет использован основной домен сайта')
            ]),
        ]);
    }

    /**
     * Сохраняем сведения о запретах на Desktop уведомления
     *
     * @param $param
     */
    public static function ormAfterwriteUsersUser($param)
    {
        $user = $param['orm'];

        if ($user->isModified('desktop_notice_locks')) {
            $site_id = \RS\Site\Manager::getSiteId();

            \RS\Orm\Request::make()
                ->delete()
                ->from(new NoticeLock())
                ->where([
                    'user_id' => $user['id']
                ])->exec();

            foreach ($user['desktop_notice_locks'] as $site_id => $data) {
                foreach ($data as $type) {
                    $lock = new NoticeLock();
                    $lock['site_id'] = $site_id;
                    $lock['user_id'] = $user['id'];
                    $lock['notice_type'] = $type;
                    $lock->insert();
                }
            }
        }
    }
}
