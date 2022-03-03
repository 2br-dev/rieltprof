<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\Notice;

/**
* Уведомление - оформление заказа
*/
class CheckoutAdmin extends \Alerts\Model\Types\AbstractNotice 
                    implements \Alerts\Model\Types\InterfaceEmail, 
                               \Alerts\Model\Types\InterfaceSms,
                               \Alerts\Model\Types\InterfaceDesktopApp
{
    public
        $order,
        $user;
        
    public function getDescription()
    {
        return t('Заказ оформлен (администратору)');
    }
    
    function init(\Shop\Model\Orm\Order $order)
    {
        $this->order = $order;
        $this->user = $order->getUser();
    }
    
    function getNoticeDataEmail()
    {
        $system_config = \RS\Config\Loader::getSystemConfig();
        $config = \RS\Config\Loader::getSiteConfig();
        
        $email_to_admin = new \Alerts\Model\Types\NoticeDataEmail();
        $email_to_admin->email      = $config['admin_email'];
        $manager =new \Users\Model\Orm\User($this->order->manager_user_id);
        if (isset ($manager)){
            $email_to_admin->email = trim(($email_to_admin->email .=','.$manager['e_mail']),',') ;
        }
        $email_to_admin->subject    = t('Оформлен заказ N%0 на сайте %1', [$this->order['order_num'], \RS\Http\Request::commonInstance()->getDomainStr()]);
        $email_to_admin->vars       = $this;
        
        return $email_to_admin;
    }

    public function getTemplateEmail()
    {
        return '%shop%/notice/toadmin_checkout.tpl';
    }
    
    function getNoticeDataSms()
    {
        $system_config = \RS\Config\Loader::getSystemConfig();
        $config = \RS\Config\Loader::getSiteConfig();
        $manager =new \Users\Model\Orm\User($this->order->manager_user_id);
        if(!$config['admin_phone'] && !$manager['phone']) return;
        
        $sms_to_admin = new \Alerts\Model\Types\NoticeDataSms();
        $sms_to_admin->phone      = $config['admin_phone'];

        if (isset ($manager)){
            $sms_to_admin->phone = trim(($sms_to_admin->phone .=','.$manager['phone']),',') ;
        }
        $sms_to_admin->vars       = $this;
        
        return $sms_to_admin;
    }

    public function getTemplateSms()
    {
        return '%shop%/notice/toadmin_checkout_sms.tpl';
    }
    
    /**
    * Возвращает путь к шаблону уведомления для Desktop приложения
    * 
    * @return string
    */
    public function getTemplateDesktopApp()
    {
        return '%shop%/notice/desktop_checkout.tpl';
    }
    
    /**
    * Возвращает данные, которые необходимо передать при инициализации уведомления
    * 
    * @return NotideDataDesktopApp
    */
    public function getNoticeDataDesktopApp()
    {
        $desktop_data = new \Alerts\Model\Types\NoticeDataDesktopApp();
        
        $desktop_data->title = t('Оформлен заказ N%0', [$this->order->order_num]);
        $desktop_data->short_message = t("%user %nlСумма заказа: %total", [
            'nl' => "\n",
            'user' => $this->order->getUser()->getFio(),
            'total' => \RS\Helper\CustomView::cost($this->order->totalcost, $this->order->currency_stitle)
        ]);
        
        $desktop_data->link = \RS\Router\Manager::obj()->getAdminUrl('edit', ['id' => $this->order->id], 'shop-orderctrl', true);
        $desktop_data->link_title = t('Перейти к заказу');
        
        if ($this->order->manager_user_id) {
            //Отправляем уведомление только назначенному менеджеру, если таковой есть
            $desktop_data->destination_user_id = $this->order->manager_user_id;
        }
        
        $desktop_data->vars = $this;
        
        return $desktop_data;
    }    
}
