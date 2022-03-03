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
class UserRecoverPassUser extends \Alerts\Model\Types\AbstractNotice
    implements \Alerts\Model\Types\InterfaceEmail, \Alerts\Model\Types\InterfaceSms
{
    public
        $user,
        $password;

    public function getDescription()
    {
        return t('Восстановление пароля (пользователю)');
    } 
    
    function init(\Users\Model\Orm\User $user, $password)
    {
        $this->user = $user;
        $this->password = $password;
    }
    
    function getNoticeDataEmail()
    {
        $config = \RS\Config\Loader::getSiteConfig();
        
        $email_to_user = new \Alerts\Model\Types\NoticeDataEmail();
        $email_to_user->email       = $this->user['e_mail'];
        $email_to_user->subject     = t('Восстановление пароля на сайте ').\RS\Http\Request::commonInstance()->getDomainStr();
        $email_to_user->vars        = $this;
         
        return $email_to_user;
    }
    
    function getTemplateEmail()
    {
        return '%users%/notice/touser_passrecover.tpl';
    }

    function getNoticeDataSms()
    {
        $config = \RS\Config\Loader::getSiteConfig();
        
        if(!$this->user['phone']) return;
        
        $notice_data = new \Alerts\Model\Types\NoticeDataSms();
        $notice_data->phone       = $this->user['phone'];
        $notice_data->vars        = $this;
         
        return $notice_data;
    }
    
    function getTemplateSms()
    {
        return '%users%/notice/touser_passrecover_sms.tpl';
    }
    
}
