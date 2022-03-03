<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Model\Notice;

/**
* Уведомление - генерация нового пароля
*/
class UserGeneratePassword extends \Alerts\Model\Types\AbstractNotice
    implements \Alerts\Model\Types\InterfaceEmail
{
    public
        $user,
        $password;

    public function getDescription()
    {
        return t('Генерация нового пароля (пользователю)');
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
        $notice_data->email       = $this->user['e_mail'];
        $notice_data->subject     = t('Смена пароля на сайте ').\RS\Http\Request::commonInstance()->getDomainStr();
        $notice_data->vars        = $this;
        
        return $notice_data;
    }
    
    function getTemplateEmail()
    {
        return '%users%/notice/touser_generate_pass.tpl';
    }
}