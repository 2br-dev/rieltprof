<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Controller\Front;

/**
* Контроллер отвечает за отображение всех брендов в виде алфавита
*/
class AllBrands extends \RS\Controller\Front
{            
    public
        /**
        * @var \Catalog\Model\BrandApi
        */
        $api;
        
    function init()
    {
        $this->api = new \Catalog\Model\BrandApi();
    }
    
    function actionIndex()
    {
        $this->api->setFilter('public', 1);
        $this->api->setOrder("title ASC");
        $brands = $this->api->getList();
        
        //Хлебные крошки
        $this->app->breadcrumbs->addBreadCrumb(t("Бренды"), $this->router->getUrl('catalog-front-allbrands'));
        $this->app->title->addSection(t('Все бренды'));
            
        $this->view->assign([
            'brands' => $brands,         //Товары бренда в спец. категориях
        ]);
        
        return $this->result->setTemplate('allbrands.tpl');
    }
}