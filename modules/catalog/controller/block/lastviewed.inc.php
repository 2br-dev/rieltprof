<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Controller\Block;

use Catalog\Model\Api as ProductApi;
use RS\Controller\StandartBlock;
use RS\Orm\Type;
use Users\Model\LogApi as UserLogApi;

/**
* Контроллер блока последних просмотренных товаров
*/
class LastViewed extends StandartBlock
{
    protected static
        $controller_title = 'Просмотренные раннее товары',
        $controller_description = 'Блок со списком просмотренных раннее товаров пользователя';
        
    protected
        $default_params = [
            'indexTemplate' => 'blocks/lastviewed/products.tpl', //Должен быть задан у наследника
            'pageSize' => 16
    ],
        $products;
        
    /** @var UserLogApi */
    public $log_api;
    /** @var ProductApi */
    public $api;
        
    function getParamObject()
    {
        return parent::getParamObject()->appendProperty([
            'pageSize' => new Type\Integer([
                'description' => t('Количество отображаемых элементов'),
            ]),
            'only_in_stock' => new Type\Integer([
                'description' => t('Показывать только товары в наличии'),
                'checkboxView' => [1,0],
                'default' => 0,
            ]),
        ]);
    }
        
    function init()
    {
        $this->log_api = new UserLogApi();
        $this->api = new ProductApi();
    }
    
    function actionIndex()
    {
        $products = $this->makeList();
        $this->view->assign([
            'products' => $products,
        ]);
        
        return $this->result->setTemplate( $this->getParam('indexTemplate') );
    }
    
    
    protected function makeList()
    {
        $list = $this->log_api->getLogItems('Catalog\Model\Logtype\ShowProduct', $this->getParam('pageSize', 16), 0, null);        
        $products = [];
        $products_id = [];
        foreach($list as $event) {
            $products_id[] = $event->getObjectId();
        }
        
        if (!empty($products_id)) {
            //Загружает сразу группу товаров, подгружает разом категории и фото ко всем товарам
            $this->api->setFilter('id', $products_id, 'in');
            $this->api->setFilter('public', 1);
            if ($this->getParam('only_in_stock')) {
                $this->api->setFilter('num', 0 , '>');
            }
            $products = $this->api->getList();
            $products = $this->api->addProductsPhotos($products);
        }
        $this->products = $products;
        return $products;
    }
}
