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
class UserRecoverPassAdmin extends \Alerts\Model\Types\AbstractNotice
    implements \Alerts\Model\Types\InterfaceEmail, \Alerts\Model\Types\InterfaceSms
{
    public
        $user,
        $password;

    public function getDescription()
    {
        return t('Восстановление пароля (администратору)');
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
        $notice_data->subject    = t('Восстановление пароля на сайте ').\RS\Http\Request::commonInstance()->getDomainStr();         
        $notice_data->vars       = $this;
        
        return $notice_data;
    }
    
    function getTemplateEmail()
    {
        return '%users%/notice/toadmin_passrecover.tpl';
    }

    function getNoticeDataSms()
    {
        $config = \RS\Config\Loader::getSiteConfig();
        
        $notice_data = new \Alerts\Model\Types\NoticeDataSms();
        
        if(!$config['admin_phone']) return;

        $notice_data->phone      = $config['admin_phone'];
        $notice_data->vars       = $this;
        
        return $notice_data;
    }
    
    function getTemplateSms()
    {
        return '%users%/notice/toadmin_passrecover_sms.tpl';
    }
    
}
