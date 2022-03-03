<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace MobileManagerApp\Config;

use MobileManagerApp\Model\Push\AddBalanceToAdmin;
use MobileManagerApp\Model\Push\OrderPayedToAdmin;
use RS\Module\Manager as ModuleManager;
use Shop\Model\Notice\AddBalance;
use Shop\Model\Notice\OrderPayed;

class Handlers extends \RS\Event\HandlerAbstract
{
    function init()
    {
        $this
            ->bind('getapps')
            ->bind('order.change')
            ->bind('orm.afterwrite.shop-order')
            ->bind('orm.afterwrite.catalog-oneclickitem')
            ->bind('orm.afterwrite.shop-reservation')
            ->bind('alerts.beforenoticesend');
    }
    
    public static function getApps($app_types)
    {
        $app_types[] = new \MobileManagerApp\Model\AppTypes\StoreManagement();
        
        return $app_types;
    }
    
    /**
    * Отправляем Push уведомление о назначении заказа курьеру
    * 
    * @param array $params
    */
    public static function orderChange($param)
    {
        if (\RS\Config\Loader::byModule(__CLASS__)->push_enable) {            
            if ($param['order']['courier_id'] && ($param['order']['courier_id'] != $param['order_before']['courier_id'])) {
                //Назначение курьера
                $push = new \MobileManagerApp\Model\Push\NewOrderToCourier();
                $push->init($param['order']);
                $push->send();
            }
        }
    }
    
    /**
    * Отправляем Push администратору при создании заказа
    * 
    * @param mixed $param
    */
    public static function ormAfterwriteShopOrder($param)
    {
        if (\RS\Config\Loader::byModule(__CLASS__)->push_enable 
            && $param['flag'] == \RS\Orm\AbstractObject::INSERT_FLAG) 
        {
            $push = new \MobileManagerApp\Model\Push\NewOrderToAdmin();
            $push->init($param['orm']);
            $push->send();            
        }
    }

    /**
     * Отправляем Push администратору при создании покупки в 1 клик
     *
     * @param mixed $param
     */
    public static function ormAfterwriteCatalogOneClickItem($param)
    {
        if (\RS\Config\Loader::byModule(__CLASS__)->push_enable
            && $param['flag'] == \RS\Orm\AbstractObject::INSERT_FLAG)
        {
            $push = new \MobileManagerApp\Model\Push\NewOneClickToAdmin();
            $push->init($param['orm']);
            $push->send();
        }
    }

    /**
     * Отправляем Push администратору при создании предзаказа
     *
     * @param $param
     */
    public static function ormAfterwriteShopReservation($param)
    {
        if (\RS\Config\Loader::byModule(__CLASS__)->push_enable
            && $param['flag'] == \RS\Orm\AbstractObject::INSERT_FLAG)
        {
            $push = new \MobileManagerApp\Model\Push\NewReservationToAdmin();
            $push->init($param['orm']);
            $push->send();
        }
    }

    /**
     * Перехватывает событие отправки уведомлений
     *
     * @param $param
     */
    public static function alertsBeforeNoticeSend($param)
    {
        $notice = $param['notice'];

        if (ModuleManager::staticModuleExists('shop')) {
            if ($notice instanceof AddBalance) {
                $push = new AddBalanceToAdmin();
                $push->init($notice->transaction, $notice->user);
                $push->send();
            }

            if ($notice instanceof OrderPayed) {
                $push = new OrderPayedToAdmin();
                $push->init($notice->order);
                $push->send();
            }
        }
    }
}
