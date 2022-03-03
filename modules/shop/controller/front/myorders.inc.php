<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Controller\Front;

use RS\Controller\AuthorizedFront;
use Shop\Model\OrderApi;
use Shop\Model\UserStatusApi;

/**
 * Контроллер мои заказы
 */
class MyOrders extends AuthorizedFront
{
    /** @var OrderApi */
    public $api;
    public $page;
    public $date_start;
    public $date_end;
    public $status;


    function init()
    {
        $this->api         = new \Shop\Model\OrderApi();
        $this->page        = $this->url->request('p', TYPE_INTEGER, 1);
        $this->date_start  = $this->url->request('date_start', TYPE_STRING, null);
        $this->date_end    = $this->url->request('date_end', TYPE_STRING, null);
        $this->status      = $this->url->request('status', TYPE_INTEGER, null); //Статус
        if ($this->status){
            $this->status = $this->url->convert($this->status, UserStatusApi::getStatusesIds());
        }
        //Проверим даты, если нужно
        if (!empty($this->date_start)){
            $this->date_start = (\RS\Helper\Tools::validateDate($this->date_start, 'd.m.Y')) ? $this->date_start : null;
        }
        if (!empty($this->date_end)){
            $this->date_end = (\RS\Helper\Tools::validateDate($this->date_end, 'd.m.Y')) ? $this->date_end : null;
        }
    }

    function actionIndex()
    {
        $this->app->title->addSection(t('Мои заказы'));
        $this->app->breadcrumbs->addBreadCrumb(t('Мои заказы'));

        $config = $this->getModuleConfig();
        $this->api->setFilter('user_id', $this->user['id']);
        if ($this->status){ //Тип статуса заказа
            $this->api->setFilter('status', $this->status);
        }
        if (!empty($this->date_start)){ //Дата с
            $date_from = date("Y-m-d", strtotime($this->date_start));
            $this->api->setFilter('dateof', $date_from." 00:00:00", '>=');
        }
        if (!empty($this->date_end)){ //Дата по
            $date_to = date("Y-m-d", strtotime($this->date_end));
            $this->api->setFilter('dateof', $date_to." 23:59:59", '<=');
        }

        $paginator = new \RS\Helper\Paginator($this->page, $this->api->getListCount(), $config['user_orders_page_size']);
        $order_list = $this->api->getList($this->page, $config['user_orders_page_size']);

        $this->view->assign([
            'order_list' => $order_list,
            'paginator' => $paginator,
        ]);

        return $this->result->setTemplate('myorders.tpl');
    }
}
