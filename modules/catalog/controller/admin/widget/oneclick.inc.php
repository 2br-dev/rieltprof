<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Catalog\Controller\Admin\Widget;

class OneClick extends \RS\Controller\Admin\Widget
{
    protected $info_title = 'Покупки в 1 клик';
    protected $info_description = 'Отображает список заявок на товар';
    
    function actionIndex()
    {
        $cookie_filter_var = 'oneclick_filter_'.\RS\Site\Manager::getSiteId();
        $default_filter = $this->url->cookie($cookie_filter_var, TYPE_STRING, 'new');
        
        $filter = $this->url->convert( $this->myRequest('filter', TYPE_STRING, $default_filter), ['new', 'viewed']);

        $cookie_expire = time()+60*60*24*730;
        $cookie_path = $this->router->getUrl('main.admin');
        $this->app->headers->addCookie($cookie_filter_var, $filter, $cookie_expire, $cookie_path);
        
        $page = $this->myRequest('p', TYPE_INTEGER, 1);
        $pageSize = 10;

        $oneclick_api = new \Catalog\Model\OneClickItemApi();
        $oneclick_api->setOrder('dateof DESC');
        if ($filter != 'viewed') {
            $oneclick_api->setFilter('status', 'new');
        }else{
            $oneclick_api->setFilter('status', 'viewed');
        }
        
        $paginator = new \RS\Helper\Paginator($page, $oneclick_api->getListCount(), $pageSize, 'main.admin', ['mod_controller' => $this->getUrlName()]);
        
        $this->view->assign([
            'list' => $oneclick_api->getList($page, $pageSize),
            'paginator' => $paginator,
            'filter' => $filter
        ]);
        return $this->result->setTemplate('widget/oneclick.tpl');
    }
}

