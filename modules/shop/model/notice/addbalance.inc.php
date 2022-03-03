<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\Notice;

/**
* Уведомление - пополнение счёта
*/
class AddBalance extends \Alerts\Model\Types\AbstractNotice
                 implements \Alerts\Model\Types\InterfaceEmail, 
                            \Alerts\Model\Types\InterfaceSms,
                            \Alerts\Model\Types\InterfaceDesktopApp
{
    public
        $transaction, //Объект транзакции
        $user;
    
    /**
    * Возвращает заголовок сообщения   
    */
    public function getDescription()
    {
        return t('Баланс лиц. счета пополнен (администратору)');
    } 

    /**
    * Инициализация класса
    * 
    * @param \Shop\Model\Orm\Transaction $transaction - объект транзакции со всеми полями
    * @param \Users\Model\Orm\User $user              - объект пользователя полонившего балан
    */
    function init(\Shop\Model\Orm\Transaction $transaction,\Users\Model\Orm\User $user)
    {
        $this->transaction = $transaction;
        $this->user        = $user;
    }
    
    function getNoticeDataEmail()
    {
        $system_config = \RS\Config\Loader::getSystemConfig();
        $config        = \RS\Config\Loader::getSiteConfig();
        
        $notice_data = new \Alerts\Model\Types\NoticeDataEmail();
        
        $notice_data->email     = $config['admin_email'];
        $notice_data->subject   = t('Баланс пользователя пополнен на сайте %0', [\RS\Http\Request::commonInstance()->getDomainStr()]);
        $notice_data->vars      = $this;
        
        return $notice_data;
    }
    
    function getTemplateEmail()
    {
        return '%shop%/notice/toadmin_addbalance.tpl';
    }
    
    function getNoticeDataSms()
    {
        $system_config = \RS\Config\Loader::getSystemConfig();
        $config        = \RS\Config\Loader::getSiteConfig();
        
        $notice_data = new \Alerts\Model\Types\NoticeDataSms();
        
        $notice_data->phone     = $config['admin_phone'];
        $notice_data->vars      = $this;
        
        return $notice_data;
    }
    
    function getTemplateSms()
    {
        return '%shop%/notice/toadmin_addbalance_sms.tpl';
    }
    
    /**
    * Возвращает путь к шаблону уведомления для Desktop приложения.
    * Уведомление не имеет детального просмотра. Не будет сохранено в истории Desktop приложения.
    * 
    * @return string
    */
    public function getTemplateDesktopApp()
    {}
    
    /**
    * Возвращает данные, которые необходимо передать при инициализации уведомления
    * 
    * @return NoticeDataDesktopApp
    */
    public function getNoticeDataDesktopApp()
    {
        $notice_data = new \Alerts\Model\Types\NoticeDataDesktopApp();
        $notice_data->title = t('Пополнен баланс лицевого счета');
        
        $notice_data->short_message = t('%user %nlСумма: %cost %currency', [
            'nl' => "\n",
            'user' => $this->user->getFio(),
            'cost' => $this->transaction->cost,
            'currency' => \Catalog\Model\CurrencyApi::getBaseCurrency()->stitle
        ]);
        $notice_data->link = \RS\Router\Manager::obj()->getAdminUrl(false, null, 'shop-transactionctrl', true);
        $notice_data->link_title = t('Перейти к заказу');
        
        return $notice_data;        
    }
}
