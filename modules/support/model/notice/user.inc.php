<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Support\Model\Notice;

/**
* Уведомление - обращение от службы поддержки
*/
class User extends \Alerts\Model\Types\AbstractNotice
    implements \Alerts\Model\Types\InterfaceEmail, \Alerts\Model\Types\InterfaceSms
{
    public
        $user,  // объект пользователя, которому будет отправлено сообщение
        $support;
        

    public function getDescription()
    {
        return t('Сообщение от службы поддержки (Пользователю)');
    } 

    function init(\Support\Model\Orm\Support $support)
    {
        $this->support = $support;
    }
    
    /**
    * Установка пользователя, которому будет отправлено сообщение
    * 
    * @param \Users\Model\Orm\User $user - объект пользователя
    */
    function setUser(\Users\Model\Orm\User $user){
       $this->user = $user; 
    }
    
    function getNoticeDataEmail()
    {
        $site_config = \RS\Config\Loader::getSiteConfig();
        
        $notice_data = new \Alerts\Model\Types\NoticeDataEmail();
        
        $notice_data->email     = $this->user['e_mail'];
        $notice_data->subject   = t('Ответ службы поддержки ').\RS\Http\Request::commonInstance()->getDomainStr();
        $notice_data->vars      = $this;
        
        return $notice_data;
    }
    
    function getTemplateEmail()
    {
        return '%support%/notice/fromadmin_support.tpl';
    }
    
    
    function getNoticeDataSms()
    {
        $site_config = \RS\Config\Loader::getSiteConfig();
        
        $notice_data = new \Alerts\Model\Types\NoticeDataSms();
        
        if(!$this->user['phone']) return;
        
        $notice_data->phone     = $this->user['phone'];
        $notice_data->vars      = $this;
        
        return $notice_data;
    }
    
    function getTemplateSms()
    {
        return '%support%/notice/fromadmin_support_sms.tpl';
    }
    
}
