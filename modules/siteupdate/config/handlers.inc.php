<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace SiteUpdate\Config;
use Main\Model\NoticeSystem\InternalAlerts;
use RS\Helper\Tools;
use RS\Router\Manager as RouterManager;

/**
* Класс предназначен для объявления событий, которые будет прослушивать данный модуль и обработчиков этих событий.
*/
class Handlers extends \RS\Event\HandlerAbstract
{
    function init()
    {
        $this
            ->bind('getmenus')
            ->bind('internalalerts.get')
            ->bind('visiblealerts.get')
            ->bind('meter.recalculate');
    }
    
    /**
    * Возвращает пункты меню этого модуля в виде массива
    * 
    */
    public static function getMenus($items)
    {
       $items[] = [
                'title' => t('Центр обновления'),
                'alias' => 'siteupdate',
                'link' => '%ADMINPATH%/siteupdate-wizard/',
                'parent' => 'control',
                'sortn' => 2,
                'typelink' => 'link',
       ];
        return $items;
    }

    /**
     * Выводим сообщение о том, что подписка на обновления истекла
     */
    public static function visibleAlertsGet($params)
    {
        $visible_alerts = $params['visible_alerts'];
        $update_api = new \SiteUpdate\Model\Api();
        if ($update_api->canCheckUpdate() === true
            && $update_api->getUpdateExpireDays() <= 0) {

            $visible_alerts->addMessage(t('Подписка на обновление системы истекла, безопасность интернет-магазина может быть под угрозой.'),
                $update_api->getSaleUpdateUrl(), '_blank', t('Продлить подписку на обновления.'));
        }
    }

    /**
     * Уведомим пользователя, когда будут в наличии обновления
     */
    public static function internalAlertsGet($params)
    {
        $internal_alerts = $params['internal_alerts'];
        $router = RouterManager::obj();
        $update_api = new \SiteUpdate\Model\Api();

        if (version_compare(phpversion(),'7.1.0', '<')) {
            $internal_alerts->addMessage(t('<b>Внимание!</b> С 14 мая 2019 года, версия PHP на вашем сервере для обновления системы должна
                             быть не ниже PHP 7.1 (У вас %php_ver).', ['php_ver' => phpversion()]), null, null, InternalAlerts::STATUS_WARNING);
        }

        if ($update_api->canCheckUpdate() === true) { //Есть лицензия на продукт

            if (($expire_days = $update_api->getUpdateExpireDays())>0) {
                //Подписка на обновления активна
                $update_data = $update_api->getCachedUpdateData();

                if ($update_data !== false) {
                    $href = $router->getAdminUrl(false, [], 'siteupdate-wizard').'#start';
                    if ($update_data['error']) {

                        $internal_alerts->addMessage($update_data['error'], $href, null, InternalAlerts::STATUS_CRITICAL);

                    } elseif (!empty($update_data['has_updates'])) {

                        $internal_alerts->addMessage(t('Доступны новые обновления. <u>Нажмите, чтобы перейти к просмотру списка доступных обновлений.</u>'), $href, null, InternalAlerts::STATUS_CRITICAL);

                    }
                }
            } else {
                $description = t('Обновления могут содержать улучшение безопасности, скорости работы платформы, исправления ошибок, новые возможности. Рекомендуем постоянно обновлять вашу платформу.');
                //Подписка на обновления истекла
                if ($expire_sale = $update_api->getSaleUpdateExpire()) {
                    //Доступен льготный период для обновлений
                    $internal_alerts->addMessage(t('<u>Продлите подписку</u> на обновления со скидкой 30% до %date (осталось %expire_days дней)', [
                        'date' => date('d.m.Y H:i', $expire_sale),
                        'expire_days' =>  $update_api->getSaleUpdateExpireDays()
                    ]), $update_api->getSaleUpdateUrl(), '_blank', InternalAlerts::STATUS_CRITICAL, $description);
                } else {
                    $internal_alerts->addMessage(t('Срок доступных обновлений истек. <u>Продлите подписку на обновление продукта</u>.'),
                                                $update_api->getSaleUpdateUrl(), '_blank', InternalAlerts::STATUS_CRITICAL, $description);
                }
            }
        }
    }

    /**
     * Периодически будем проверять наличие обновлений на сервере
     */
    public static function meterRecalculate()
    {
        $update_api = new \SiteUpdate\Model\Api();
        if ($update_api->canCheckUpdate() === true && $update_api->getCachedUpdateData() === false ) {
            $update_api->checkUpdates();
        }
    }
}