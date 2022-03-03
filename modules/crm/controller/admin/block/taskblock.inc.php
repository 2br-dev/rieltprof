<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Controller\Admin\Block;

use Crm\Model\TaskApi;
use RS\Controller\Admin\Block;
use RS\Html\Paginator\Element as PaginatorElement;

/**
 * Блок управления задачками
 */
class TaskBlock extends Block
{
    const
        PAGE_SIZE = 20;

    protected $action_var = 'taskdo';

    protected $default_params = [
        'link_id' => '',
        'link_type' => ''
    ];

    protected $default_n_sort = [ //Направление сортировки по умолчанию
        'date_of_create' => 'desc',
        'title' => 'asc',
        'creator_user_id' => 'asc',
        'implementer_user_id' => 'asc',
        'status_id' => 'asc',
        'date_of_planned_end' => 'asc',
    ];

    /**
     * @var TaskApi
     */
    public $api;
    public $link_id;
    public $link_type;
    public $state_params = [];
    public $page_params = [];

    function init()
    {
        $this->api = new TaskApi();
        $this->api->initRightsFilters();

        $this->link_id = $this->url->request('link_id', TYPE_INTEGER, $this->getParam('link_id'));
        $this->link_type = $this->url->request('link_type', TYPE_STRING, $this->getParam('link_type'));
    }

    /**
     * Удаляет взаимодействие
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionIndex()
    {
        $page_size_default = $this->url->cookie('task_page_size', TYPE_INTEGER, self::PAGE_SIZE);
        $cur_sort_default = $this->url->cookie('task_sort', TYPE_STRING, 'date_of_create');
        $cur_sort_n_default = $this->url->cookie('task_nsort', TYPE_STRING, 'desc');

        $page = $this->request('task_page', TYPE_INTEGER, 1);
        $page_size = $this->request('task_page_size', TYPE_INTEGER, $page_size_default);

        $this->link_id = $this->url->request('link_id', TYPE_INTEGER, $this->getParam('link_id'));
        $this->link_type = $this->url->request('link_type', TYPE_STRING, $this->getParam('link_type'));

        $cur_sort = $this->url->convert($this->url->request('sort', TYPE_STRING, $cur_sort_default), array_keys($this->default_n_sort));
        $cur_n_sort = $this->url->convert($this->url->request('nsort', TYPE_STRING, $cur_sort_n_default), ['desc','asc']);

        $expire = time()+3600*700;
        $this->app->headers->addCookie('task_page_size', $page_size, $expire, '/');
        $this->app->headers->addCookie('task_sort', $cur_sort, $expire, '/');
        $this->app->headers->addCookie('task_nsort', $cur_n_sort, $expire, '/');

        //Сортировка
        if ($this->default_n_sort[$cur_sort] == $cur_n_sort) {
            $this->default_n_sort[$cur_sort] = ($this->default_n_sort[$cur_sort] == 'asc') ? 'desc' : 'asc';
        }

        $this->api->addFilterByLink($this->link_type, $this->link_id);
        $this->api->setOrder($cur_sort.' '.$cur_n_sort);

        $total = $this->api->getListCount();

        //Параметры, характеризующие состояние текущего блока
        $this->state_params = [
            'link_type' => $this->link_type,
            'link_id' => $this->link_id,
            'sort' => $cur_sort,
            'nsort' => $cur_n_sort,
        ];

        //Параметры, характеризующие положение пагинатора текущего блока
        $this->page_params = [
            'task_page' => $page,
            'task_page_size' => $page_size
        ];

        //Маска урлов для пагинации
        $url_pattern = $this->router->getAdminPattern(false, [
            ':task_page' => '%PAGE%',
            'task_page_size' => $page_size
            ] + $this->state_params, 'crm-block-taskblock');

        $paginator = new PaginatorElement($total, $url_pattern, [
            'pageKey' => 'task_page',
            'pageSizeKey' => 'task_page_size',
            'total' => $total,
            'pageSize' => $page_size,
            'page' => $page,
        ]);

        $this->view->assign([
            'tasks' => $this->api->getList($page, $page_size),
            'paginator' => $paginator,
            'link_type' => $this->link_type,
            'link_id' => $this->link_id,
            'default_n_sort' => $this->default_n_sort,
            'cur_sort' => $cur_sort,
            'cur_n_sort' => $cur_n_sort,
        ]);

        return $this->result->setTemplate('admin/blocks/task/task_list.tpl');
    }

    /**
     * Формирует URL с откорректированными параметрами для метода Index
     *
     * @param array $custom_params - параметры, которые должны отличаться от текущего состояния
     * @return string
     */
    function makeUrl($custom_params = [])
    {
        $params = array_merge($this->state_params + $this->page_params, $custom_params);
        return $this->router->getAdminUrl(false, $params, 'crm-block-taskblock');
    }


    /**
     * Удаляет задачу
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionRemove()
    {
        $select_all = $this->url->request('selectAll', TYPE_STRING);
        $ids = $this->url->request('task', TYPE_ARRAY, []);

        if ($select_all == 'on') {
            $result = $this->api->removeAllByLink($this->link_type, $this->link_id);
        } else {
            $result = $this->api->removeByIds($this->link_type, $this->link_id, $ids);
        }

        $this->result->setSuccess($result);

        if (!$this->result->isSuccess()) {
            return $this->result->setErrors($this->api->getDisplayErrors());
        }

        return $this->result;
    }
}