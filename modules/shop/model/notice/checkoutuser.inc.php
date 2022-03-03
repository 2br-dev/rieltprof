<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\Notice;

/**
* Уведомление - оформление заказа
*/
class CheckoutUser extends \Alerts\Model\Types\AbstractNotice
    implements \Alerts\Model\Types\InterfaceEmail, \Alerts\Model\Types\InterfaceSms
{
    public
        /**
        * @var \Shop\Model\Orm\Order $order
        */
        $order,
        /**
        * @var \Users\Model\Orm\User $user
        */
        $user;
        
    /**
    * Возвращает название уведомления
    *     
    */
    public function getDescription()
    {
        return t('Заказ оформлен (покупателю)');
    } 

    /**
    * Инициализация уведомления
    * 
    * @param \Shop\Model\Orm\Order $order - объект заказа
    */
    function init(\Shop\Model\Orm\Order $order)
    {
        $this->order = $order;
        $this->user  = $order->getUser();
    }
    
    /**
    * Получаение информации о письме
    * 
    * @return \Alerts\Model\Types\NoticeDataEmail|false
    */
    function getNoticeDataEmail()
    {
        $email_to_user = new \Alerts\Model\Types\NoticeDataEmail();
        
        if (filter_var($this->user['e_mail'], FILTER_VALIDATE_EMAIL)){ //Если задан пользовательский E-mail
            $email_to_user->email = $this->user['e_mail']; 
        }else{ //Если пользовательского E-mail нет
            return false;
        }

        $email_to_user->subject  = t('Оформлен заказ N%0 на сайте %1', [$this->order['order_num'], \RS\Http\Request::commonInstance()->getDomainStr()]);
        $email_to_user->template = '%shop%/notice/touser_checkout.tpl';
        $email_to_user->vars     = $this;                                          
        
        return $email_to_user;
        
    }

    /**
    * Возвращает шаблон письма
    * 
    * @return string
    */
    public function getTemplateEmail()
    {
        return '%shop%/notice/touser_checkout.tpl';
    }
    
    /**
    * Возвращает сведения об уведомлении на телефон
    * 
    * @return \Alerts\Model\Types\NoticeDataSms|false
    */
    function getNoticeDataSms()
    {
        if(!$this->user['phone']) return false;
        
        $sms_to_admin        = new \Alerts\Model\Types\NoticeDataSms();
        $sms_to_admin->phone = $this->user['phone'];
        $sms_to_admin->vars  = $this;
        
        return $sms_to_admin;
    }

    /**
    * Возвращает шаблон SMS
    * 
    * @return string
    */
    public function getTemplateSms()
    {
        return '%shop%/notice/touser_checkout_sms.tpl';
    }
}
