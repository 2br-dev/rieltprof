<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\Notice;

/**
* Уведомление - пополнение счёта
*/
class NewTransaction extends \Alerts\Model\Types\AbstractNotice
    implements \Alerts\Model\Types\InterfaceEmail, \Alerts\Model\Types\InterfaceSms
{
    public
        $transaction, //Объект транзакции
        $user;
    
    /**
    * Возвращает заголовок сообщения   
    */
    public function getDescription()
    {
        return t('Инициирован платеж(администратору)');
    } 

    /**
    * Инициализация класса
    * 
    * @param \Shop\Model\Orm\Transaction $transaction - объект транзакции со всеми полями
    * @param \Users\Model\Orm\User $user              - объект пользователя полонившего балан
    */
    function init(\Shop\Model\Orm\Transaction $transaction,\Users\Model\Orm\User $user)
    {
        $this->transaction = $transaction;
        $this->user        = $user;
    }
    
    function getNoticeDataEmail()
    {
        $system_config = \RS\Config\Loader::getSystemConfig();
        $config        = \RS\Config\Loader::getSiteConfig();
        
        $notice_data = new \Alerts\Model\Types\NoticeDataEmail();
        
        $notice_data->email     = $config['admin_email'];
        $notice_data->subject   = t('Инициирован платеж на сайте %0', [\RS\Http\Request::commonInstance()->getDomainStr()]);
        $notice_data->vars      = $this;
        
        return $notice_data;
    }
    
    function getTemplateEmail()
    {
        return '%shop%/notice/toadmin_newtransaction.tpl';
    }
    
    function getNoticeDataSms()
    {
        $system_config = \RS\Config\Loader::getSystemConfig();
        $config        = \RS\Config\Loader::getSiteConfig();
        
        $notice_data = new \Alerts\Model\Types\NoticeDataSms();
        
        $notice_data->phone     = $config['admin_phone'];
        $notice_data->vars      = $this;
        
        return $notice_data;
    }
    
    function getTemplateSms()
    {
        return '%shop%/notice/toadmin_newtransaction_sms.tpl';
    }
    
}