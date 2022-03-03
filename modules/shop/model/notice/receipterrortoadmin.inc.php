<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\Notice;

/**
* Уведомление - ошибка в чека кассы
*/
class ReceiptErrorToAdmin extends \Alerts\Model\Types\AbstractNotice 
                    implements \Alerts\Model\Types\InterfaceEmail, 
                               \Alerts\Model\Types\InterfaceSms,
                               \Alerts\Model\Types\InterfaceDesktopApp
{
    public
        $receipt,
        $transaction;
        
    public function getDescription()
    {
        return t('Ошибка пробития чека кассы (администратору)');
    }
    
    function init(\Shop\Model\Orm\Receipt $receipt)
    {
        $this->receipt     = $receipt;
        $this->transaction = $this->receipt->getTransaction();
        
    }
    
    function getNoticeDataEmail()
    {
        $system_config = \RS\Config\Loader::getSystemConfig();
        $config        = \RS\Config\Loader::getSiteConfig();
        
        $email_to_admin             = new \Alerts\Model\Types\NoticeDataEmail();
        $email_to_admin->email      = $config['admin_email'];
        $email_to_admin->subject    = t('Ошибка в чеке кассы по транзакции N%0 на сайте %1', [$this->transaction['id'], \RS\Http\Request::commonInstance()->getDomainStr()]);
        $email_to_admin->vars       = $this;
        
        return $email_to_admin;
    }

    public function getTemplateEmail()
    {
        return '%shop%/notice/toadmin_receipterror.tpl';
    }
    
    function getNoticeDataSms()
    {
        $system_config = \RS\Config\Loader::getSystemConfig();
        $config        = \RS\Config\Loader::getSiteConfig();
        
        if(!$config['admin_phone']) return;
        
        $sms_to_admin        = new \Alerts\Model\Types\NoticeDataSms();
        $sms_to_admin->phone = $config['admin_phone'];
        $sms_to_admin->vars  = $this;
        
        return $sms_to_admin;
    }

    public function getTemplateSms()
    {
        return '%shop%/notice/toadmin_receipterror_sms.tpl';
    }
    
    /**
    * Возвращает путь к шаблону уведомления для Desktop приложения
    * 
    * @return string
    */
    public function getTemplateDesktopApp()
    {
        return '%shop%/notice/desktop_receipterror.tpl';
    }
    
    /**
    * Возвращает данные, которые необходимо передать при инициализации уведомления
    * 
    * @return NotideDataDesktopApp
    */
    public function getNoticeDataDesktopApp()
    {
        $desktop_data = new \Alerts\Model\Types\NoticeDataDesktopApp();
        
        $desktop_data->title = t('Ошибка в чеке кассы по транзакции N%0', [$this->transaction['id']]);
        $desktop_data->short_message = t("Ошибка в чеке кассы по транзакции N%0", [
            $this->transaction['id']
        ]);
        
        $desktop_data->link = \RS\Router\Manager::obj()->getAdminUrl(false, ['transaction_id' => $this->transaction['id']], 'shop-receiptsctrl', true);
        $desktop_data->link_title = t('Перейти в админ панель');
        
        $desktop_data->vars = $this;
        
        return $desktop_data;
    }    
}
