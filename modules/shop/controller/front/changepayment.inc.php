<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Controller\Front;

/**
* Контроллер мои заказы
*/
class ChangePayment extends \RS\Controller\AuthorizedFront
{
    public 
        $api;
    
    function init()
    {
        $this->api = new \Shop\Model\OrderApi();
    }
    
    function actionIndex()
    {
        $order_id = urldecode($this->url->get('order_id', TYPE_STRING));
        $config   = \RS\Config\Loader::byModule($this);
        
        $order = $this->api
            ->setFilter([
                'order_num' => $order_id,
                'user_id' => $this->user['id']
            ])->getFirst();
        
        if (!$order){
            $this->e404(t('Заказ не найден'));
        }
            
        $this->router->getCurrentRoute()->order_id = $order['id'];
        
        $this->view->assign([
            'order' => $order
        ]);
        
        $this->app->breadcrumbs
            ->addBreadCrumb(t('Мои заказы'), $this->router->getUrl('shop-front-myorders'))
            ->addBreadCrumb(t('Заказ №%0', [$order['order_num']]));
            
        return $this->result->setTemplate('myorder_view.tpl');
    }


    /**
     * Метод для смены оплаты
     */
    function actionChangePayment()
    {
        return $this->result->setTemplate('myorder_change_payment.tpl');
    }
}
