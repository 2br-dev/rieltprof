<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\Notice;

/**
* Уведомление - ссылка на электронный чек пользователю
*/
class ReceiptToUser extends \Alerts\Model\Types\AbstractNotice 
                    implements \Alerts\Model\Types\InterfaceEmail, 
                               \Alerts\Model\Types\InterfaceSms
{
    public $receipt;
    public $user; 
    public $provider; 
    public $info; 
    public $receipt_url; //Адрес на проверку ссылки на сам выбитый чек
    public $transaction;
        
    public function getDescription()
    {
        return t('Электронный чек (пользователю)');
    }
    
    /**
    * Первичная инициалиция уведомления
    * 
    * @param \Shop\Model\Orm\Receipt $receipt - объект чека
    */
    function init(\Shop\Model\Orm\Receipt $receipt)
    {
        $this->receipt     = $receipt;
        $this->transaction = $this->receipt->getTransaction();
        $this->user        = $this->transaction->getUser();
        $this->order       = $this->transaction->getOrder();
    }
    
    function getNoticeDataEmail()
    {
        if (!$this->user['e_mail']) return;
        
        $email_to_user             = new \Alerts\Model\Types\NoticeDataEmail();
        $email_to_user->email      = $this->user['e_mail'];
        $email_to_user->subject    = t('Электронный чек на сайте %0', [\RS\Http\Request::commonInstance()->getDomainStr()]);
        $email_to_user->vars       = $this;
        
        return $email_to_user;
    }

    public function getTemplateEmail()
    {
        return '%shop%/notice/touser_receipt.tpl';
    }
    
    /**
    * Возвращает сведения для отправки SMS уведомления 
    * 
    */
    function getNoticeDataSms()
    {   
        if(!$this->user['phone']) return;
        
        $sms_to_user        = new \Alerts\Model\Types\NoticeDataSms();
        $sms_to_user->phone = $this->user['phone'];
        $sms_to_user->vars  = $this;
        
        return $sms_to_user;
    }

    public function getTemplateSms()
    {
        return '%shop%/notice/touser_receipt_sms.tpl';
    }    
}
