<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace EmailSubscribe\Model\Notice;
/**
* Уведомление - подписка на рассылку
*/
class UserSubscribe extends \Alerts\Model\Types\AbstractNotice
    implements \Alerts\Model\Types\InterfaceEmail
{
    public
        $subscribe;

    public function getDescription()
    {
        return t('Подписка на рассылку (пользователю)');
    } 
    
    /**
    * Инициализация уведомления
    *         
    * @param array $subscribe - массив с параметрами для передачи 
    * @return void
    */
    function init($subscribe)
    {
        $this->subscribe = $subscribe;
    }

    function getNoticeDataEmail()
    {
        $site_config = \RS\Config\Loader::getSiteConfig();
        
        $notice_data = new \Alerts\Model\Types\NoticeDataEmail();
        
        $notice_data->site_config = $site_config;
        $notice_data->email       = $this->subscribe['email'];
        $notice_data->subject     = t('Подтверждение подписки на новости на сайте %0', [\RS\Http\Request::commonInstance()->getDomainStr()]);
        $notice_data->vars      = $this;
        
        return $notice_data;
    }
    
    function getTemplateEmail()
    {
        return '%emailsubscribe%/notice/touser_subscribe.tpl';
    }
}

