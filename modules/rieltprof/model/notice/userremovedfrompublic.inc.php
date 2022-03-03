<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Rieltprof\Model\Notice;

/**
* Уведомление - регистрация пользователя
*/
class UserRemovedFromPublic extends \Alerts\Model\Types\AbstractNotice
    implements \Alerts\Model\Types\InterfaceEmail, \Alerts\Model\Types\InterfaceSms
{
    public
        $user,
        $ad;

    public function getDescription()
    {
        return t('Объявление снято с публикации (пользователю)');
    } 

    
    function init(\Users\Model\Orm\User $user, $object)
    {
        $this->user = $user;
        $this->ad = $object;
    }
    
    function getNoticeDataEmail()
    {
        $config = \RS\Config\Loader::getSiteConfig();
        
        $notice_data = new \Alerts\Model\Types\NoticeDataEmail();
        $notice_data->email       = $this->user['e_mail'];
        $notice_data->subject     = t('Ваше объявление ').$this->ad['title'].t(' снято с публикации');
        $notice_data->vars        = $this;
        
        return $notice_data;
    }
    
    function getTemplateEmail()
    {
        return '%rieltprof%/notice/touser_removedfrompublic.tpl';
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
        return '%users%/notice/touser_register_sms.tpl';
    }
}

