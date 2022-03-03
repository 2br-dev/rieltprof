<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Users\Model\Notice;

/**
* Уведомление - регистрация пользователя
*/
class UserRegisterAdmin extends \Alerts\Model\Types\AbstractNotice
    implements \Alerts\Model\Types\InterfaceEmail, \Alerts\Model\Types\InterfaceSms 
{
    public
        $user,
        $password;

    public function getDescription()
    {
        return t('Регистрация пользователя (администратору)');
    } 

    
    function init(\Users\Model\Orm\User $user, $password)
    {
        $this->user = $user;
        $this->password = $password;
    }
    
    function getNoticeDataEmail()
    {
        $config = \RS\Config\Loader::getSiteConfig();
        
        $notice_data = new \Alerts\Model\Types\NoticeDataEmail();
        $notice_data->email      = $config['admin_email'];
        $notice_data->subject    = t('Регистрация на сайте ').\RS\Http\Request::commonInstance()->getDomainStr();         
        $notice_data->vars       = $this;
        
        return $notice_data;
    }
    
    function getTemplateEmail()
    {
        return '%users%/notice/toadmin_register.tpl';
    }
    
    function getNoticeDataSms()
    {
        $config = \RS\Config\Loader::getSiteConfig();
        
        if(!$config['admin_phone']) return;
        
        $notice_data = new \Alerts\Model\Types\NoticeDataSms();
        $notice_data->phone      = $config['admin_phone'];
        $notice_data->vars       = $this;
        
        return $notice_data;
    }
    
    function getTemplateSms()
    {
        return '%users%/notice/toadmin_register_sms.tpl';
    }
}

