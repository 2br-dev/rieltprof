<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Controller\Front;

use RS\Controller\AuthorizedFront;
use RS\Helper\Paginator;
use RS\Helper\Tools as RSTools;
use Shop\Model\ArchiveOrderApi;
use Shop\Model\OrderApi;
use Shop\Model\UserStatusApi;

/**
 * Контроллер мои заказы
 */
class MyOrders extends AuthorizedFront
{
    /** @var OrderApi */
    public $api;
    /** @var ArchiveOrderApi */
    public $archive_order_api;
    public $page;
    public $date_start;
    public $date_end;
    public $status;


    function init()
    {
        $this->api = new OrderApi();
        $this->archive_order_api = new ArchiveOrderApi();
        $this->page = $this->url->request('p', TYPE_INTEGER, 1);
        $this->date_start = $this->url->request('date_start', TYPE_STRING, null);
        $this->date_end = $this->url->request('date_end', TYPE_STRING, null);
        $this->status = $this->url->request('status', TYPE_INTEGER, null); //Статус

        if ($this->status) {
            $this->status = $this->url->convert($this->status, UserStatusApi::getStatusesIds());
        }
        //Проверим даты, если нужно
        if (!empty($this->date_start)) {
            $this->date_start = (RSTools::validateDate($this->date_start, 'd.m.Y')) ? $this->date_start : null;
        }
        if (!empty($this->date_end)) {
            $this->date_end = (RSTools::validateDate($this->date_end, 'd.m.Y')) ? $this->date_end : null;
        }
    }

    function actionIndex()
    {
        $this->app->title->addSection(t('Мои заказы'));
        $this->app->breadcrumbs->addBreadCrumb(t('Мои заказы'));

        $config = $this->getModuleConfig();
        $this->api->setFilter('user_id', $this->user['id']);
        $this->archive_order_api->setFilter('user_id', $this->user['id']);
        if ($this->status) { //Тип статуса заказа
            $this->api->setFilter('status', $this->status);
            $this->archive_order_api->setFilter('status', $this->status);
        }
        if (!empty($this->date_start)) { //Дата с
            $date_from = date("Y-m-d", strtotime($this->date_start));
            $this->api->setFilter('dateof', $date_from . " 00:00:00", '>=');
            $this->archive_order_api->setFilter('dateof', $date_from . " 00:00:00", '>=');
        }
        if (!empty($this->date_end)) { //Дата по
            $date_to = date("Y-m-d", strtotime($this->date_end));
            $this->api->setFilter('dateof', $date_to . " 23:59:59", '<=');
            $this->archive_order_api->setFilter('dateof', $date_to . " 23:59:59", '<=');
        }

        $original_page = $this->page;
        $list_count = $this->api->getListCount();
        $paginator = new Paginator($this->page, $list_count, $config['user_orders_page_size']);
        $order_list = $this->api->getList($this->page, $config['user_orders_page_size']);

        $paginator_with_archive = new Paginator($original_page, $list_count + $this->archive_order_api->getListCount(), $config['user_orders_page_size']);
        $order_list_with_archive = $this->archive_order_api->getListWithArchive($original_page, $config['user_orders_page_size']);

        $this->view->assign([
            'order_list' => $order_list,
            'order_list_with_archive' => $order_list_with_archive,
            'paginator' => $paginator,
            'paginator_with_archive' => $paginator_with_archive,
        ]);

        return $this->result->setTemplate('myorders.tpl');
    }
}
