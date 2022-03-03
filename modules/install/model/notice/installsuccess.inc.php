<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Install\Model\Notice;

/**
* Уведомление об успешном завершении установки
*/
class InstallSuccess extends \Alerts\Model\Types\AbstractNotice
    implements \Alerts\Model\Types\InterfaceEmail
{                                                      
    public
        $data;        

    public function getDescription()
    {
        return t('Установка успешно завершена');
    } 
            
    function init($supervisor_email, $supervisor_password, $admin_section, $domain)
    {
        $this->data = [
            'supervisor_email' => $supervisor_email,
            'supervisor_password' => $supervisor_password,
            'admin_section' => $admin_section,
            'domain' => $domain
        ];
    }
    
    function getNoticeDataEmail()
    {
        $system_config = \RS\Config\Loader::getSystemConfig();
        
        $notice_data = new \Alerts\Model\Types\NoticeDataEmail();
        
        $notice_data->email     = $this->data['supervisor_email'];
        $notice_data->subject   = t('Установка ReadyScript успешно завершена');
        $notice_data->vars      = $this;
        
        return $notice_data;
    }
    
    function getTemplateEmail()
    {
        return '%install%/notice/install_success.tpl';
    }
    
}