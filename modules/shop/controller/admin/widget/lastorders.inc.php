<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Controller\Admin\Widget;
use \Shop\Model;

class LastOrders extends \RS\Controller\Admin\Widget
{
    protected
        $info_title = 'Недавние заказы',
        $info_description = 'Отображает список оформленных заказов';
    
    function actionIndex()
    {
        $cookie_filter_var = 'lastorder_filter_'.\RS\Site\Manager::getSiteId();
        $default_filter = $this->url->cookie($cookie_filter_var, TYPE_STRING);
        $filter = $this->url->convert( $this->myRequest('filter', TYPE_STRING, $default_filter), ['active', 'all']);
        
        $cookie_expire = time()+60*60*24*730;
        $cookie_path = $this->router->getUrl('main.admin');
        $this->app->headers->addCookie($cookie_filter_var, $filter, $cookie_expire, $cookie_path);
        
        $page = $this->myRequest('p', TYPE_INTEGER, 1);
        $pageSize = 10;

        $order_api = new \Shop\Model\OrderApi();
        $order_api->setOrder('dateof DESC');
        if ($filter != 'all') {
            $order_api->setFilter('status', 
                array_merge(Model\UserStatusApi::getStatusesIdByType(Model\Orm\UserStatus::STATUS_SUCCESS),
                Model\UserStatusApi::getStatusesIdByType(Model\Orm\UserStatus::STATUS_CANCELLED)), 'notin');
        }
        
        $paginator = new \RS\Helper\Paginator($page, $order_api->getListCount(), $pageSize, 'main.admin', ['mod_controller' => $this->getUrlName()]);
        
        $this->view->assign([
            'orders' => $order_api->getList($page, $pageSize),
            'paginator' => $paginator,
            'filter' => $filter
        ]);
        return $this->result->setTemplate('widget/lastorder.tpl');
    }
}
