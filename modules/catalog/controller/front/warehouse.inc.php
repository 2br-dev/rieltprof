<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Controller\Front;
use RS\Application\Application;

/**
* Контроллер отвечает за просмотр сведений о складе
*/
class Warehouse extends \RS\Controller\Front
{            
    public
        $api;
        
    function init()
    {
        $this->api = new \Catalog\Model\WareHouseApi(); 
    }
    
    function actionIndex()
    {
        $id = $this->url->get('id', TYPE_STRING,false);
        $warehouse = $this->api->getById($id);
        
        if (!$warehouse) $this->e404(t('Склада с таким именем не существует'));
        //Если есть alias и открыта страница с id вместо alias, то редирект
        $this->checkRedirectToAliasUrl($id, $warehouse, $warehouse->getUrl());

        //Если есть alias и открыта страница с id вместо alias, то редирект
        if ($id == $warehouse['id'] && !empty($warehouse['alias'])){
            Application::getInstance()->redirect($warehouse->getUrl());
        }
              
        //Хлебные крошки
        $this->app->breadcrumbs
            ->addBreadCrumb($warehouse['title']);
            
        //Установим мета данные в тегах
        $this->app->title
            ->addSection($warehouse['meta_title'] ?: $warehouse['title']);
                
        $this->app->meta
            ->addKeywords($warehouse['meta_keywords'])
            ->addDescriptions($warehouse['meta_description']);
        
        $this->view->assign([
            'warehouse' => $warehouse    //Склад
        ]);
        
        return $this->result->setTemplate('warehouse.tpl');
    }
}
