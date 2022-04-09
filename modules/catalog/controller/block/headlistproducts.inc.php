<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Controller\Block;

use Catalog\Controller\Front\ListProducts;
use RS\Controller\StandartBlock;

class HeadListProducts extends StandartBlock
{
    protected static $controller_title = 'Заголовок категории, список подкатегорий';       //Краткое название контроллера
    protected static $controller_description = 'Отображает заголовок просматриваемой категории, список подкатегорий'; //Описание контроллера

    protected $default_params = [
        'indexTemplate' => 'blocks/headlistproducts/head_list_products.tpl', //Должен быть задан у наследника
    ];

    function actionIndex()
    {
        $current_route = $this->router->getCurrentRoute();
        $this->view->assign([
            'category' => $current_route->getExtra(ListProducts::ROUTE_EXTRA_CATEGORY),
            'query' => $current_route->getExtra(ListProducts::ROUTE_EXTRA_QUERY),
            'sub_dirs' => $current_route->getExtra(ListProducts::ROUTE_EXTRA_SUBCATEGORIES),
        ]);

        return $this->result->setTemplate($this->getParam('indexTemplate'));
    }
}