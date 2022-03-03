<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Controller\Admin\Widget;
use \Shop\Model;

class Reservation extends \RS\Controller\Admin\Widget
{
    protected
        $info_title = 'Предварительные заказы',
        $info_description = 'Отображает список предварительных заказов';
    
    function actionIndex()
    {
        $cookie_filter_var = 'reservation_filter_'.\RS\Site\Manager::getSiteId();
        $default_filter = $this->url->cookie($cookie_filter_var, TYPE_STRING, 'open');
        
        $filter = $this->url->convert( $this->myRequest('filter', TYPE_STRING, $default_filter), ['open', 'close']);

        $cookie_expire = time()+60*60*24*730;
        $cookie_path = $this->router->getUrl('main.admin');
        $this->app->headers->addCookie($cookie_filter_var, $filter, $cookie_expire, $cookie_path);
        
        $page = $this->myRequest('p', TYPE_INTEGER, 1);
        $pageSize = 5;

        $reservation_api = new \Shop\Model\ReservationApi();
        $reservation_api->setOrder('dateof DESC');
        if ($filter != 'close') {
            $reservation_api->setFilter('status', 'open');
        }else{
            $reservation_api->setFilter('status', 'close');
        }
        
        $paginator = new \RS\Helper\Paginator($page, $reservation_api->getListCount(), $pageSize, 'main.admin', ['mod_controller' => $this->getUrlName()]);
        
        $this->view->assign([
            'list' => $reservation_api->getList($page, $pageSize),
            'paginator' => $paginator,
            'filter' => $filter
        ]);
        return $this->result->setTemplate('widget/reservation.tpl');
    }
}