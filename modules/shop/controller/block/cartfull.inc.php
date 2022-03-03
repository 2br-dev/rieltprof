<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Controller\Block;

use Catalog\Model\CurrencyApi;
use RS\Controller\Result\Standard;
use RS\Controller\StandartBlock;
use RS\Exception as RSException;
use Shop\Model\Cart;

/**
 * Блок-контроллер Корзина
 */
class CartFull extends StandartBlock
{
    protected static $controller_title = 'Корзина';
    protected static $controller_description = 'Отображает корзину';

    protected $action_var = 'action';
    protected $default_params = [
        'indexTemplate' => 'blocks/cart/cart_full.tpl',
    ];

    /** @var \Shop\Model\Cart $cart */
    public $cart;

    function init()
    {
        $this->cart = \Shop\Model\Cart::currentCart();
    }

    /**
     * @return Standard
     * @throws RSException
     */
    function actionIndex()
    {
        $cart = \Shop\Model\Cart::currentCart();
        $cart_data = $cart->getCartData();
        $this->view->assign([
            'cart' => $cart,
            'cart_data' => $cart_data,
            'cart_info' => $cart->getCurrentInfo(),
            'currency' => CurrencyApi::getCurrentCurrency(),
        ]);
        $section_cart = [
            'can_checkout' => !$cart_data['has_error'],
            'total_unformated' => $cart_data['total_unformatted'],
            'total_price' => $cart_data['total'],
            'items_count' => $cart_data['items_count'],
            'session_cart_products' => $_SESSION[Cart::SESSION_CART_PRODUCTS],
        ];
        return $this->result->setTemplate($this->getParam('indexTemplate'))->addSection('cart', $section_cart);
    }

    /**
     * Обновляет информацию о товарах, их количестве в корзине. Добавляет купон на скидку, если он задан
     */
    function actionUpdate()
    {
        if ($this->url->isPost()) {
            $products = $this->url->request('products', TYPE_ARRAY);
            $coupon = trim($this->url->request('coupon', TYPE_STRING));
            $apply_coupon = $this->cart->update($products, $coupon, true, true);

            if ($apply_coupon !== true) {
                $this->cart->addUserError($apply_coupon, false, 'coupon');

                $this->view->assign([
                    'coupon_code' => $coupon,
                ]);
            }
        }

        return $this->actionIndex();
    }

    /**
     * Удаляет товар из корзины
     *
     * @return Standard
     * @throws RSException
     */
    function actionRemoveItem()
    {
        $uniq = $this->url->request('id', TYPE_STRING);
        $this->cart->removeItem($uniq, true);
        return $this->actionIndex();
    }

    /**
     * Очищает корзину
     *
     * @return Standard
     * @throws RSException
     */
    function actionCleanCart()
    {
        $this->cart->clean();
        return $this->actionIndex();
    }
}
