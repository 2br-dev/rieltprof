<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Controller\Block;
use \RS\Orm\Type;

/**
* Блок-контроллер Список сопутствующих товаров
*/
class Concomitant extends \RS\Controller\StandartBlock
{
    protected static
        $controller_title       = 'Сопутствующие товары',
        $controller_description = 'Отображает товары, отмеченные как сопутствующие';

    protected
        $default_params = [
            'indexTemplate' => 'blocks/concomitant/concomitant.tpl',
    ];
        

    function actionIndex()
    {
        $shop_config = \RS\Config\Loader::byModule('shop');
        if (!$shop_config){ //Если модуля магазин нет, то и нечего покупать
            return false;
        }
        $route = \RS\Router\Manager::obj()->getCurrentRoute();
        if ($route->getId() == 'catalog-front-product' || $route->getId() == 'shop-front-multioffers') {
            if (isset($route->product)) {
                if ($shop_config['check_quantity'] && ($route->product['num']<=0)){
                    return false;
                }
                $this->view->assign([
                    'shop_config' => $shop_config,
                    'current_product' => $route->product,
                    'list' => $route->product->getConcomitant(),
                ]);
            }
        }
        
        return $this->result->setTemplate( $this->getParam('indexTemplate') );
    }

}