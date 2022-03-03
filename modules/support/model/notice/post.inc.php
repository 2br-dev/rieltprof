<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Support\Model\Notice;

/**
* Уведомление - обращение в поддержку
*/
class Post extends \Alerts\Model\Types\AbstractNotice
           implements \Alerts\Model\Types\InterfaceEmail, 
                      \Alerts\Model\Types\InterfaceSms,
                      \Alerts\Model\Types\InterfaceDesktopApp
{
    public
        $support;
        

    public function getDescription()
    {
        return t('Сообщение в службу поддержки (администратору)');
    } 

    function init(\Support\Model\Orm\Support $support)
    {
        $this->support = $support;
    }
    
    function getNoticeDataEmail()
    {
        $site_config = \RS\Config\Loader::getSiteConfig();
        
        $notice_data = new \Alerts\Model\Types\NoticeDataEmail();
        
        $notice_data->email     = $site_config['admin_email'];
        $notice_data->subject   = t('Сообщение в поддержку ').\RS\Http\Request::commonInstance()->getDomainStr();
        $notice_data->vars      = $this;
        
        return $notice_data;
    }
    
    function getTemplateEmail()
    {
        return '%support%/notice/toadmin_support.tpl';
    }
    
    
    function getNoticeDataSms()
    {
        $site_config = \RS\Config\Loader::getSiteConfig();
        
        $notice_data = new \Alerts\Model\Types\NoticeDataSms();
        
        if(!$site_config['admin_phone']) return;
        
        $notice_data->phone     = $site_config['admin_phone'];
        $notice_data->vars      = $this;
        
        return $notice_data;
    }
    
    function getTemplateSms()
    {
        return '%support%/notice/toadmin_support_sms.tpl';
    }
    
    /**
    * Возвращает путь к шаблону уведомления для Desktop приложения
    * 
    * @return string
    */
    public function getTemplateDesktopApp() 
    {
        return '%support%/notice/desktop_support.tpl';
    }
    
    /**
    * Возвращает данные, которые необходимо передать при инициализации уведомления
    * 
    * @return NoticeDataDesktopApp
    */
    public function getNoticeDataDesktopApp() 
    {
        $notice_data = new \Alerts\Model\Types\NoticeDataDesktopApp();
        $notice_data->title = t('Обращение в поддержку на сайте');
        
        $notice_data->short_message = t('%user %nl%message', [
            'nl' => "\n",
            'user' => $this->support->getUser()->getFio(),
            'message' => \RS\Helper\Tools::teaser($this->support->message, 100, true)
        ]);
        
        $notice_data->link = \RS\Router\Manager::obj()->getAdminUrl(false, ['id' => $this->support->topic_id], 'support-supportctrl', true);
        $notice_data->link_title = t('Перейти к переписке');
        $notice_data->vars = $this;
        
        return $notice_data;                
    }
}
