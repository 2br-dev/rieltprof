<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Controller\Front;

use Catalog\Model\CurrencyApi;
use RS\Application\Application;
use RS\Controller\Front;
use Shop\Model\Cart;
use Shop\Model\Orm\Order;

/**
 * Корзина, объединенная с оформлением заказа.
 *
 * Используется, если в административной панели опция "Тип оформления заказа" установлена в
 * значение "Оформление на одной странице", "Оформление в корзине"
 */
class CartCheckout extends Front
{
    public $order;

    public function init()
    {
        $this->app->title->addSection(t('Оформление заказа'));
    }

    /**
     * Главная страница оформления заказа
     *
     * @return \RS\Controller\Result\Standard
     */
    public function actionIndex()
    {
        $this->app->breadcrumbs->addBreadCrumb(t('Оформление заказа'));

        $this->view->assign([
            'cart' => Cart::currentCart(),
        ]);

        return $this->result->setTemplate('cart_checkout.tpl');
    }

    /**
     * Страница с информацией об оформленном заказе
     *
     * @return \RS\Controller\Result\Standard
     */
    public function actionFinish()
    {
        $this->order = Order::currentOrder();
        $this->app->title->addSection(t('Заказ №%0 успешно оформлен', [$this->order['order_num']]));
        $this->app->breadcrumbs->addBreadCrumb(t('Завершение заказа'));

        $this->view->assign([
            'order' => $this->order,
            'cart' => $this->order->getCart(),
        ]);

        return $this->result->setTemplate('checkout/finish.tpl');
    }

    /**
     * "Замораживает" корзину, т.е. отвязывает ее от текущей корзины
     * на сайте и переносит в заказ.
     *
     * @throws \RS\Db\Exception
     * @throws \RS\Exception
     */
    public function actionFreezeCart()
    {
        $frozen_cart = Cart::preOrderCart();
        $frozen_cart->splitSubProducts();
        $frozen_cart->mergeEqual();

        $order = Order::currentOrder();
        $order->linkSessionCart($frozen_cart);
        $order->setCurrency(CurrencyApi::getCurrentCurrency());

        $order->resetOrderForCheckout();

        Application::getInstance()->redirect($this->router->getUrl('shop-front-checkout'));
    }
}
