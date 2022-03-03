<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Controller\Admin\Block;

use Crm\Model\DealApi;
use RS\Controller\Admin\Block;
use RS\Html\Paginator\Element as PaginatorElement;

/**
 * Блок управления сделками
 */
class DealBlock extends Block
{
    const
        PAGE_SIZE = 20;

    protected $action_var = 'dealdo';

    protected $default_params = [
        'link_id' => '',
        'link_type' => ''
    ];

    protected $default_n_sort = [ //Направление сортировки по умолчанию
        'deal_num' => 'asc',
        'title' => 'asc',
        'date_of_create' => 'desc',
        'creator_user_id' => 'asc'
    ];

    public
        $api,
        $link_id,
        $link_type,
        $state_params = [],
        $page_params = [];

    function init()
    {
        $this->api = new DealApi();
        $this->api->initRightsFilters();

        $this->link_id = $this->url->request('link_id', TYPE_INTEGER, $this->getParam('link_id'));
        $this->link_type = $this->url->request('link_type', TYPE_STRING, $this->getParam('link_type'));
    }

    /**
     * Отображает список взаимодействий
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionIndex()
    {
        $page_size_default = $this->url->cookie('dealblock_page_size', TYPE_INTEGER, self::PAGE_SIZE);
        $cur_sort_default = $this->url->cookie('dealblock_sort', TYPE_STRING, 'date_of_create');
        $cur_sort_n_default = $this->url->cookie('dealblock_nsort', TYPE_STRING, 'desc');

        $page = $this->request('deal_page', TYPE_INTEGER, 1);
        $page_size = $this->request('deal_page_size', TYPE_INTEGER, $page_size_default);

        $this->link_id = $this->url->request('link_id', TYPE_INTEGER, $this->getParam('link_id'));
        $this->link_type = $this->url->request('link_type', TYPE_STRING, $this->getParam('link_type'));

        $cur_sort = $this->url->convert($this->url->request('sort', TYPE_STRING, $cur_sort_default), array_keys($this->default_n_sort));
        $cur_n_sort = $this->url->convert($this->url->request('nsort', TYPE_STRING, $cur_sort_n_default), ['desc','asc']);

        $expire = time()+3600*700;
        $this->app->headers->addCookie('dealblock_page_size', $page_size, $expire, '/');
        $this->app->headers->addCookie('dealblock_sort', $cur_sort, $expire, '/');
        $this->app->headers->addCookie('dealblock_nsort', $cur_n_sort, $expire, '/');

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
            'deal_page' => $page,
            'deal_page_size' => $page_size
        ];

        //Маска урлов для пагинации
        $url_pattern = $this->router->getAdminPattern(false, [
                ':deal_page' => '%PAGE%',
                'deal_page_size' => $page_size
            ] + $this->state_params, 'crm-block-dealblock');

        $paginator = new PaginatorElement($total, $url_pattern, [
            'pageKey' => 'deal_page',
            'pageSizeKey' => 'deal_page_size',
            'total' => $total,
            'pageSize' => $page_size,
            'page' => $page,
        ]);

        $this->view->assign([
            'deals' => $this->api->getList($page, $page_size),
            'paginator' => $paginator,
            'link_type' => $this->link_type,
            'link_id' => $this->link_id,
            'default_n_sort' => $this->default_n_sort,
            'cur_sort' => $cur_sort,
            'cur_n_sort' => $cur_n_sort
        ]);

        return $this->result->setTemplate('admin/blocks/deal/deal_list.tpl');
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
        return $this->router->getAdminUrl(false, $params, 'crm-block-dealblock');
    }

    /**
     * Удаляет взаимодействие
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionRemove()
    {
        $select_all = $this->url->request('selectAll', TYPE_STRING);
        $ids = $this->url->request('deal', TYPE_ARRAY, []);

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