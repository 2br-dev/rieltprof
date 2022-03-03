<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\Notice;

/**
* Уведомление - оплата заказа
*/
class OrderPayed extends \Alerts\Model\Types\AbstractNotice
                 implements \Alerts\Model\Types\InterfaceEmail, 
                            \Alerts\Model\Types\InterfaceSms,
                            \Alerts\Model\Types\InterfaceDesktopApp
{
    public
        $order,
        $user;
        
    public function getDescription()
    {
        return t('Заказ оплачен');
    } 

    function init(\Shop\Model\Orm\Order $order)
    {
        $this->order = $order;
    }
    
    function getNoticeDataEmail()
    {
        $system_config = \RS\Config\Loader::getSystemConfig();
        $config = \RS\Config\Loader::getSiteConfig();
        
        $notice_data = new \Alerts\Model\Types\NoticeDataEmail();
        $manager =new \Users\Model\Orm\User($this->order->manager_user_id);
        $notice_data->email     = $config['admin_email'];
        $manager =new \Users\Model\Orm\User($this->order->manager_user_id);
        if (isset ($manager)){
            $notice_data->email = trim(($notice_data->email .=','.$manager['e_mail']),',') ;
        }
        $notice_data->subject   = t('Оплачен заказ N%0 на сайте %1', [$this->order['order_num'], \RS\Http\Request::commonInstance()->getDomainStr()]);
        $notice_data->vars      = $this;
        
        return $notice_data;
    }
    
    function getTemplateEmail()
    {
        return '%shop%/notice/toadmin_orderpayed.tpl';
    }
    
    function getNoticeDataSms()
    {
        $system_config = \RS\Config\Loader::getSystemConfig();
        $config = \RS\Config\Loader::getSiteConfig();
        $manager =new \Users\Model\Orm\User($this->order->manager_user_id);
        if(!$config['admin_phone'] && !$manager['phone']) return;
        $notice_data =  new \Alerts\Model\Types\NoticeDataSms();
        $notice_data->phone     = $config['admin_phone'];
        if (isset ($manager)){
            $notice_data->phone = trim(($notice_data->phone .=','.$manager['phone']),',') ;
        }
        $notice_data->vars      = $this;
        
        return $notice_data;
    }
    
    function getTemplateSms()
    {
        return '%shop%/notice/toadmin_orderpayed_sms.tpl';
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
        $notice_data->title = t('Оплачен заказ №%num от %date', [
            'num' => $this->order->order_num,
            'date' => date('d.m.Y', strtotime($this->order->dateof))
        ]);
        $notice_data->short_message = t('%user %nlСумма заказа: %cost %currency', [
            'nl' => "\n",
            'user' => $this->order->getUser()->getFio(),
            'cost' => $this->order->totalcost,
            'currency' => $this->order->currency_stitle
        ]);
        $notice_data->link = \RS\Router\Manager::obj()->getAdminUrl('edit', ['id' => $this->order->id], 'shop-orderctrl', true);
        $notice_data->link_title = t('Перейти к заказу');
        
        return $notice_data;
    }
}