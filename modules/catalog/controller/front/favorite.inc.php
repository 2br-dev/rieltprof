<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Controller\Front;

use Catalog\Model\FavoriteApi;

class Favorite extends \RS\Controller\Front
{
    /**
     * @var \Catalog\Model\Api $api
     */
    public $api;
    /**
     * @var \Catalog\Model\FavoriteApi $favoriteapi
     */
    public $favoriteapi;
    public $config;
    public $page;
    public $pageSize;
        
    function init()
    {
        $this->favoriteapi = FavoriteApi::getInstance();
        $this->api = new \Catalog\Model\Api();
        $this->config = \RS\Config\Loader::byModule($this);
        
        $this->page = $this->url->get('p', TYPE_INTEGER, 1);
        $items_on_page_str = empty($this->config['items_on_page']) ? $this->config['list_page_size'] : $this->config['items_on_page'];
        $this->items_on_page = preg_split('/[ ]*,[ ]*/', trim($items_on_page_str));
        $this->pageSize = $this->url->request('pageSize', TYPE_INTEGER, $this->config['list_page_size']);
        $this->pageSize = $this->url->convert($this->pageSize, $this->items_on_page);
        $this->view_as  = $this->url->request('viewAs', TYPE_STRING, $this->config['list_default_view_as']);
        
        $cookie_expire = time()+60*60*24*730;
        $cookie_path = $this->router->getUrl('catalog-front-listproducts');
        $this->app->headers
            ->addCookie('viewAs', $this->view_as, $cookie_expire, $cookie_path)
            ->addCookie('pageSize', $this->pageSize, $cookie_expire, $cookie_path);
    }

    /**
     * Список избранного
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionIndex()
    {        
        $paginator = new \RS\Helper\Paginator($this->page, $this->favoriteapi->getFavoriteCount(), $this->pageSize);
        $list = $this->favoriteapi->getFavoriteList($this->page, $this->pageSize);
        $list = $this->api->addProductsDynamicNum($list);
        
        $this->app->title->addSection(t('Избранное'));
        $this->app->breadcrumbs->addBreadCrumb(t('Избранное'));
        $this->view->assign([
            'list' => $list,
            'paginator' => $paginator,
            'items_on_page' => $this->items_on_page,
            'page_size' => $this->pageSize,
            'view_as' => $this->view_as,
        ]);
       
        return $this->result->setTemplate('favorite.tpl');  
    }

    /**
     * Добавление товара из избранное
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionAdd(){
        $product_id = $this->url->request('product_id', TYPE_INTEGER);
        
        if($this->url->cookie('guest', TYPE_STRING)){
            $this->favoriteapi->addToFavorite($product_id);
        }
        
        $this->result->setSuccess(true)->addSection('count', $this->favoriteapi->getFavoriteCount());
        return $this->result;
    }

    /**
     * Удаление товара из избранного
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionRemove(){
        $product_id = $this->url->request('product_id', TYPE_INTEGER);
        
        $this->favoriteapi->removeFromFavorite($product_id);
        
        $this->result->setSuccess(true)->addSection('count', $this->favoriteapi->getFavoriteCount());
        return  $this->result;
    }
}
