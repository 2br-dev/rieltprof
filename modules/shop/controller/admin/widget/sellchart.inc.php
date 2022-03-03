<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Controller\Admin\Widget;

class SellChart extends \RS\Controller\Admin\Widget
{
    protected
        $info_title = 'Динамика продаж',
        $info_description = 'График заказов по месяцам с возможностью сравнивать продажи в разных годах';
    
    public
        $api;
    
    function init()
    {
        $this->api = new \Shop\Model\OrderApi();
    }
    
    function actionIndex()
    {
        $site_id = \RS\Site\Manager::getSiteId();
        $default_range = $this->url->cookie('sellchart_range'.$site_id, TYPE_STRING, 'year');
        $default_orders = $this->url->cookie('sellchart_orders'.$site_id, TYPE_STRING, 'all');
        $default_show_type = $this->url->cookie('sellchart_show_type'.$site_id, TYPE_STRING, 'num');
        
        $range = $this->myRequest('sellchart_range', TYPE_STRING, $default_range);
        $orders = $this->myRequest('sellchart_orders', TYPE_STRING, $default_orders);
        $show_type = $this->myRequest('sellchart_show_type', TYPE_STRING, $default_show_type);
        
        $cookie_expire = time()+60*60*24*730;
        $cookie_path = $this->router->getUrl('main.admin');
        $this->app->headers
            ->addCookie('sellchart_range'.$site_id, $range, $cookie_expire, $cookie_path)
            ->addCookie('sellchart_orders'.$site_id, $orders, $cookie_expire, $cookie_path)
            ->addCookie('sellchart_show_type'.$site_id, $show_type, $cookie_expire, $cookie_path);
        
        if ($range == 'year') {
            $order_dynamics_arr = $this->api->ordersByYears($orders, $show_type);
        } else {
            $order_dynamics_arr = $this->api->ordersByMonth($orders, $show_type);
        }
        
        $this->view->assign([
            'range' => $range,
            'orders' => $orders,
            'show_type' => $show_type,
            'dynamics_arr' => $order_dynamics_arr,
            'years' => array_keys($order_dynamics_arr),
            'chart_data' => json_encode([
                'points' => $order_dynamics_arr,
                'currency' => \Catalog\Model\CurrencyApi::getBaseCurrency()->stitle,
                'range' => $range
            ])
        ]);
        return $this->result->setTemplate('widget/sellchart.tpl');
    }
}