<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\Notice;

/**
* Уведомление об автоматической смене статуса заказа
*/
class AutoChangeStatus extends \Alerts\Model\Types\AbstractNotice
    implements \Alerts\Model\Types\InterfaceEmail, \Alerts\Model\Types\InterfaceSms
{
    public
        $order;
    
    /**
    * Возвращает заголовок сообщения   
    */
    public function getDescription()
    {
        return t('Автосмена статуса заказа (Пользователю)');
    } 

    /**
    * Инициализация класса
    * 
    * @param \Shop\Model\Orm\Order $order - объект заказа
    */
    function init(\Shop\Model\Orm\Order $order)
    {
        $this->order = $order;
    }
    
    function getNoticeDataEmail()
    {
        $notice_data = new \Alerts\Model\Types\NoticeDataEmail();
        
        $notice_data->email     = $this->order->getUser()->e_mail;
        $notice_data->subject   = t('Статус заказа N%0 изменен на сайте %1', [$this->order['order_num'], \RS\Http\Request::commonInstance()->getDomainStr()]);
        $notice_data->vars      = $this;
        
        return $notice_data;
    }
    
    function getTemplateEmail()
    {
        return '%shop%/notice/touser_autochange_status.tpl';
    }
    
    function getNoticeDataSms()
    {        
        $notice_data = new \Alerts\Model\Types\NoticeDataSms();
        
        $notice_data->phone     = $this->order->getUser()->phone;
        $notice_data->vars      = $this;
        
        return $notice_data;
    }
    
    function getTemplateSms()
    {
        return '%shop%/notice/touser_autochange_status_sms.tpl';
    }
}
