<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace MobileManagerApp\Model\Push;

/**
* Push уведомление курьеру о новом заказе
*/
class NewOrderToAdmin extends AbstractPushToAdmin
{
    public
        $order;
    
    public function init(\Shop\Model\Orm\Order $order)
    {
        $this->order = $order;
    }
    
    /*
    * Возвращает описание уведомления для внутренних нужд системы и 
    * отображения в списках админ. панели
    * 
    * @return string
    */
    public function getTitle()
    {
        return t('Новый заказ(администратору)');
    }
    
    /**
    * Возвращает Заголовок для Push уведомления
    * 
    * @return string
    */
    public function getPushTitle()
    {
        return t('Новый заказ N%num на сумму %total', [
            'num' => $this->order['order_num'],
            'total' => \RS\Helper\CustomView::cost($this->order['totalcost'], $this->order['currency_stitle'])
        ]);
    }
    
    /**
    * Возвращает текст Push уведомления
    * 
    * @return string
    */
    public function getPushBody()
    {
        return t('Покупатель: %fio, Сайт: %site', [
            'fio' => $this->order->getUser()->getFio(),
            'site' => \Setup::$DOMAIN
        ]);
    }
    
    /**
    * Возвращает произвольные данные ключ => значение, которые должны быть переданы с уведомлением
    * 
    * @return array
    */
    public function getPushData()
    {
        $site = new \Site\Model\Orm\Site($this->order['__site_id']->get());
            
        return [
            'order_id' => $this->order['id'],
            'site_uid' => $site->getSiteHash()
        ];
    }
    
    /**
    * Возвращает click_action для данного уведомления
    * 
    * @return string
    */
    public function getPushClickAction()
    {
        return 'com.readyscript.dk.storemanagement.Order.Detail_TARGET_NOTIFICATION';
    }
}