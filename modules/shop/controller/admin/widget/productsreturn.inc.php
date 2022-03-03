<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Controller\Admin\Widget;
use \Shop\Model;

class ProductsReturn extends \RS\Controller\Admin\Widget
{
    protected
        $info_title = 'Возвраты товаров',
        $info_description = 'Отображает список возвратов товаров';
    
    function actionIndex()
    {
        $cookie_filter_var = 'productsreturn_filter_'.\RS\Site\Manager::getSiteId();
        $default_filter = $this->url->cookie($cookie_filter_var, TYPE_STRING);
        $filter = $this->url->convert( $this->myRequest('filter', TYPE_STRING, $default_filter), [Model\Orm\ProductsReturn::STATUS_NEW, 'all']);
        
        $cookie_expire = time()+60*60*24*730;
        $cookie_path = $this->router->getUrl('main.admin');
        $this->app->headers->addCookie($cookie_filter_var, $filter, $cookie_expire, $cookie_path);
        
        $page = $this->myRequest('p', TYPE_INTEGER, 1);
        $pageSize = 10;

        $return_api = new \Shop\Model\ProductsReturnApi();
        $return_api->setOrder('dateof DESC');
        if ($filter != 'all') {
            $return_api->setFilter('status',
               [Model\Orm\ProductsReturn::STATUS_COMPLETE, Model\Orm\ProductsReturn::STATUS_REFUSE],
                'notin');
        }
        
        $paginator = new \RS\Helper\Paginator($page, $return_api->getListCount(), $pageSize, 'main.admin', ['mod_controller' => $this->getUrlName()]);
        
        $this->view->assign([
            'list' => $return_api->getList($page, $pageSize),
            'paginator' => $paginator,
            'filter' => $filter
        ]);
        return $this->result->setTemplate('widget/productsreturn.tpl');
    }
}