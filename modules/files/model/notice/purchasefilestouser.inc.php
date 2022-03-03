<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Files\Model\Notice;

/**
* Уведомление, срабатывающее во время оплаты заказа, к которому прикреплены платные файлы
* Отправляет ссылки на скачивание платных файлов
*/
class PurchaseFilesToUser extends \Alerts\Model\Types\AbstractNotice
    implements \Alerts\Model\Types\InterfaceEmail
{
    public
        $order,
        $files = [];
        
    function getDescription()
    {
        return t('Отправка ссылок на оплаченные файлы (Покупателю)');
    }
    
    /**
    * Инициализация уведомления
    *         
    * @param \Shop\Model\Orm\Order $order - объект заказа
    * @return void
    */
    function init($order)
    {
        $this->order = $order;
        $this->user  = $order->getUser();
    }
    
    /**
    * Возвращает объект NoticeData
    * @return \Alerts\Model\Types\NoticeDataEmail
    */    
    function getNoticeDataEmail()
    {
        if ($this->files = $this->order->getFiles('afterpay')) {
            $site_config = \RS\Config\Loader::getSiteConfig();
            
            $notice_data = new \Alerts\Model\Types\NoticeDataEmail();
            $notice_data->email     = $this->user['e_mail'];
            $notice_data->subject   = t('Ссылки на оплаченные файлы по заказу N%0', [$this->order['order_num']]);
            $notice_data->vars      = $this;
            return $notice_data;
        }
    }

    /**
    * Возвращает путь к шаблону письма
    * 
    * @return string
    */    
    function getTemplateEmail()
    {
        return '%files%/notice/purchasefilestouser.tpl';
    }
    
    
}
