<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace MobileSiteApp\Model\Push;

/**
* Push уведомление пользователю, о том, что заказ изменился.
*/
class OrderChangeToUser extends \PushSender\Model\Firebase\Push\GoogleFCMPushNotice
{
    public
        $order;
    
    /**
    * Инициализация PUSH уведомления
    * 
    * @param \Shop\Model\Orm\Order $order - объект заказа
    */
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
        return t('Изменения в заказе(пользователю)');
    }
    
    /**
    * Возвращает для какого приложения (идентификатора приложения в ReadyScript) предназначается push
    * 
    * @return string
    */
    public function getAppId()
    {
        return 'mobilesiteapp';
    }
        
    /**
    * Возвращает одного или нескольких получателей по пользователям
    * 
    * @return array
    */
    public function getRecipientUserIds()
    {
        if ($this->order['user_id']){
            return (array)$this->order['user_id'];
        }
        return [];
    }
    
    /**
    * Возвращает Заголовок для Push уведомления
    * 
    * @return string
    */
    public function getPushTitle()
    {
        return t('Заказ N%num', [
            'num' => $this->order['order_num']
        ]);
    }
    
    /**
    * Возвращает текст Push уведомления
    * 
    * @return string
    */
    public function getPushBody()
    {
        return t('В заказе произошли изменения');
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
            'site_uid' => $site->getSiteHash(),   
            'soundname' => "default",    
  
            'content-available' => "1", //Включает отработку события когда приложение в спящем режиме
            'action' => "OrderPage", //Класс страницы в мобильном приложении, какую нужно открыть в внутри приложения    
            'params' => json_encode([   //Дополнительные параметры отправляемые открываемой странице
                'order' => [
                    'id' => $this->order['id'],
                    'order_num' => $this->order['order_num'],
                    'status' => $this->order['status'],
                    'use_addr' => $this->order['use_addr'],
                    'payment' => $this->order['payment'],
                    'delivery' => $this->order['delivery'],
                    'warehouse' => $this->order['warehouse'],
                    'dateof_date' => date("d.m.Y", strtotime($this->order['dateof']))
                ]
            ]),
        ];
    }
    
    /**
    * Возвращает click_action для данного уведомления
    * 
    * @return string
    */
    public function getPushClickAction()
    {
        return false;
    }
}