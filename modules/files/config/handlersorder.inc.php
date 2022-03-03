<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Files\Config;
use \RS\Orm\Type;

/**
* В классе реализованы обработчики событий для заказа
*/
class HandlersOrder extends \RS\Event\HandlerAbstract
{
    function init()
    {
        //Добавляем функциональность файлов к заказам
        $this
            ->bind('orm.init.shop-order')
            ->bind('orm.afterwrite.shop-order')
            ->bind('orm.delete.shop-order')
            ->bind('order.change');
    }
    
    /**
    * Добавляет вкладку Файлы к заказу
    */
    public static function ormInitShopOrder($order)
    {
        $order->getPropertyIterator()->append([
            t('Файлы'),
            '__files__' => new Type\UserTemplate('%files%/shop/order_files.tpl', false, [
                'footerVisible' => true
            ]),
        ]);
    }    
    
    /**
    * Обрабатывает привязку файлов при создании заказа
    */
    public static function ormAfterwriteShopOrder($params)
    {
        $order = $params['orm'];
        
        if ($order['_tmpid']<0) {
            \RS\Orm\Request::make()
                    ->update(new \Files\Model\Orm\File())
                    ->set(['link_id' => $order['id']])
                    ->where([
                        'link_type_class' => 'files-shoporder',
                        'link_id' => $order['_tmpid']
                    ])->exec();
        }
    }
    
    /**
    * Обрабатывает удаление заказа
    */
    public static function ormDeleteShopOrder($params)
    {
        $order = $params['orm'];
        
        $file_api = new \Files\Model\FileApi();
        $file_api->setFilter('link_id', $order['id']);
        $file_api->setFilter('link_type_class', 'files-shoporder');
        $files = $file_api->getList();
        foreach ($files as $file) {
            $file->delete();
        }
    }
    
    /**
    * Обработчик события оплаты заказа
    * 
    * @param mixed $params
    */
    public static function orderChange($params)
    {
        $order_before   = $params['order_before'];
        $order          = $params['order'];
        if (!$order_before->is_payed && $order->is_payed) {
            //Отправляем ссылки на платные файлы товаров на почту
            $notice = new \Files\Model\Notice\PurchaseFilesToUser();
            $notice->init($order);
            \Alerts\Model\Manager::send($notice);
        }
    }
}
