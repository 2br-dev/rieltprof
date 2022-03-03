<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\Notice;
/**
* Уведомление - оформлен предварительный заказ
*/
class AssignOrderToCourier extends \Alerts\Model\Types\AbstractNotice
    implements \Alerts\Model\Types\InterfaceEmail, \Alerts\Model\Types\InterfaceSms
{
    public
        $reserve;

    public function getDescription()
    {                                             
        return t('Назначение заказа пользователю-курьеру (пользователю)');
    } 
            
    function init(\Shop\Model\Orm\Order $order)
    {
        $this->order = $order;
    }
    
    function getNoticeDataEmail()
    {
        $notice_data = new \Alerts\Model\Types\NoticeDataEmail();
        
        $notice_data->email     = $this->order->getCourierUser()->e_mail;
        $notice_data->subject   = t('Вам назначен новый заказ на сайте %0', [\RS\Http\Request::commonInstance()->getDomainStr()]);
        $notice_data->vars      = $this;
        
        return $notice_data;
    }
    
    function getTemplateEmail()
    {
        return '%shop%/notice/touser_assignorder.tpl';
    }

    function getNoticeDataSms()
    {
        $notice_data = new \Alerts\Model\Types\NoticeDataSms();
        
        if(!$this->order->getCourierUser()->phone) return;
        
        $notice_data->phone     = $this->order->getCourierUser()->phone;
        $notice_data->vars      = $this;
        
        return $notice_data;
    }
    
    function getTemplateSms()
    {
        return '%shop%/notice/touser_assignorder_sms.tpl';
    }
}

