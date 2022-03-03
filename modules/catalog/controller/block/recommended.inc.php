<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Controller\Block;

use Catalog\Model\Api as ProductApi;
use Catalog\Model\Dirapi;
use RS\Controller\StandartBlock;
use RS\Orm\ControllerParamObject;
use RS\Orm\Type;
use RS\Router\Manager as RouterManager;

/**
* Блок-контроллер Список категорий
*/
class Recommended extends StandartBlock
{
    protected static $controller_title = 'Рекомендуемые товары';
    protected static $controller_description = 'Отображает товары, отмеченные как рекомендуемые';

    protected $default_params = [
        'indexTemplate' => 'blocks/recommended/recommended.tpl',
    ];

    /**
     * Возвращает параметры блока
     *
     * @return ControllerParamObject
     */
    function getParamObject()
    {
        return parent::getParamObject()->appendProperty([
            'random' => new Type\Integer([
                'description' => t('Показывать в случайном порядке'),
                'default' => 0,
                'checkboxView' => [1, 0]
            ]),
            'in_stock' => new Type\Integer([
                'description' => t('Показывать только те что в наличии'),
                'default' => 0,
                'checkboxView' => [1, 0]
            ]),
        ]);
    }

    /** @var \Catalog\Model\Dirapi $dirapi */
    public $dirapi;
    /** @var \Catalog\Model\Api $api */
    public $api;
        
    function init()
    {
        $this->api = new ProductApi();
        $this->dirapi = Dirapi::getInstance();
    }                    
    
    function actionIndex()
    {
        $route = RouterManager::obj()->getCurrentRoute();
        if ($route->getId() == 'catalog-front-product') {
            if (isset($route->product)) {
                $products = $route->product->getRecommended();
                if ($this->getParam('random')){
                    shuffle($products);
                }
                
                if ($this->getParam('in_stock')){
                    $arr = [];
                    foreach ($products as $product){
                        if ($product['num']>0){
                            $arr[] = $product;    
                        }
                    }
                    $products = $arr;
                }
                
                $this->view->assign([
                    'current_product' => $route->product,
                    'recommended' => $products,
                    'recommended_title' => 'Рекомендуемые товары'
                ]);
            }
        }
        
        return $this->result->setTemplate( $this->getParam('indexTemplate') );
    }
}
