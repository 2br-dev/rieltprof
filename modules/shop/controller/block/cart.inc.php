<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Controller\Block;

use Catalog\Model\CurrencyApi;
use RS\Application\Application;
use RS\Controller\StandartBlock;

/**
 * Блок-контроллер Корзина
 */
class Cart extends StandartBlock
{
    protected static $controller_title = 'Корзина (минималистичная)';
    protected static $controller_description = 'Отображает количество товаров и общую стоимость в корзине';

    protected $default_params = [
        'indexTemplate' => 'blocks/cart/cart.tpl'
    ];

    function actionIndex()
    {
        Application::getInstance()->addJsVar('cartProducts', $_SESSION[\Shop\Model\Cart::SESSION_CART_PRODUCTS]);

        $this->view->assign([
            'cart_info' => \Shop\Model\Cart::getCurrentInfo(),
            'currency' => CurrencyApi::getCurrentCurrency()
        ]);
        return $this->result->setTemplate($this->getParam('indexTemplate'));
    }
}
