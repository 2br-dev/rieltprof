<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\Notice;
/**
* Уведомление - купить в один клик
*/
class OneClickAdmin extends \Alerts\Model\Types\AbstractNotice
                    implements \Alerts\Model\Types\InterfaceEmail, 
                               \Alerts\Model\Types\InterfaceSms,
                               \Alerts\Model\Types\InterfaceDesktopApp
{
    public
        $oneclick,
        $products_data,
        $ext_fields;

    public function getDescription()
    {
        return t('Купить в один клик (администратору)');
    } 
    
    /**
    * Инициализация уведомления
    *         
    * @param \Catalog\Model\Orm\OneClickItem $oneclick  - массив с параметрами для передачи 
    * @return void
    */
    function init(\Catalog\Model\Orm\OneClickItem $oneclick)
    {
        $this->oneclick = $oneclick;

        $stext = [];
        $this->products_data = unserialize($this->oneclick['stext']);  
        $this->ext_fields = unserialize($this->oneclick['sext_fields']);
    }
    
    function getNoticeDataEmail()
    {
        $site_config = \RS\Config\Loader::getSiteConfig();
        
        $notice_data = new \Alerts\Model\Types\NoticeDataEmail();
        
        $notice_data->email     = $site_config['admin_email'];
        $notice_data->subject   = t('Купить в один клик на сайте %0 №-%1', [\RS\Http\Request::commonInstance()->getDomainStr(), $this->oneclick['id']]);
        $notice_data->vars      = $this;
        
        return $notice_data;
    }
    
    function getTemplateEmail()
    {
        return '%catalog%/notice/toadmin_oneclick.tpl';
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
        return '%catalog%/notice/toadmin_oneclick_sms.tpl';
    }
    
    function getTemplateDesktopApp()
    {
        return '%catalog%/notice/desktop_oneclick.tpl';
    }
    
    function getNoticeDataDesktopApp()
    {
        $notice_data = new \Alerts\Model\Types\NoticeDataDesktopApp();
        $notice_data->title = t('Покупка в один клик №%0', [$this->oneclick->id]);
        
        $products = $this->oneclick->tableDataUnserialized();
        
        if (count($products) == 1) {
            $product_title = t('Товар: %product(%barcode)', [
                'product' => \RS\Helper\Tools::teaser($products[0]['title'], 120),
                'barcode' => $products[0]['barcode']
            ]);
        } else {
            $product_title = t('Заказано: %count [plural:%count:товар|товара|товаров]', ['count' => count($products)]);
        }
        
        $notice_data->short_message = $this->oneclick->user_fio
                                      .($this->oneclick->user_phone ? " ({$this->oneclick->user_phone})" : '')
                                      ."\n".$product_title;
        
        $notice_data->link = \RS\Router\Manager::obj()->getAdminUrl('edit', ['id' => $this->oneclick->id], 'catalog-oneclickctrl', true);
        $notice_data->link_title = t('Перейти к покупке');
        
        $notice_data->vars = $this;        
        
        return $notice_data;
    }
}

