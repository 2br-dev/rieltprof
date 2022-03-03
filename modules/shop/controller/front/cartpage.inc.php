<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Controller\Front;

use Main\Model\StatisticEvents;
use RS\Application\Application;
use RS\Config\Loader as ConfigLoader;
use RS\Controller\Front;
use RS\Controller\Result\Standard;
use RS\Event\Manager as EventManager;
use RS\Exception as RSException;
use RS\Router\Manager as RouterManager;
use Shop\Config\File as ShopConfig;
use Shop\Model\Cart;

/**
 * Просмотр корзины
 */
class CartPage extends Front
{
    const CART_SOURCE_CART_PAGE = 'cart_page'; // источник "товар добавлен вручную"

    /** @var Cart $cart */
    public $cart;

    function init()
    {
        $this->cart = Cart::currentCart();
    }

    /**
     * Корзина
     */
    function actionIndex()
    {
        /** @var ShopConfig $config */
        $config = ConfigLoader::byModule($this);
        $float_cart = $this->url->request('floatCart', TYPE_BOOLEAN, false);
        $id = $this->url->request('add', TYPE_INTEGER);       //id товара
        $amount = $this->url->request('amount', TYPE_FLOAT);    //Количество
        $offer = $this->url->request('offer', TYPE_STRING);      //Комплектация
        $multioffers = $this->url->request('multioffers', TYPE_ARRAY); //Многомерные комплектации
        $concomitants = $this->url->request('concomitant', TYPE_ARRAY); //Сопутствующие товары
        $concomitants_amount = $this->url->request('concomitant_amount', TYPE_ARRAY); //Количество сопутствующих твоаров
        $additional_uniq = $this->url->request('uniq', TYPE_STRING); // Дополнительный унификатор товара
        $checkout = $this->url->request('checkout', TYPE_BOOLEAN);

        $this->app->breadcrumbs->addBreadCrumb(t('Корзина'));
        $this->app->title->addSection(t('Корзина'));

        if (!empty($id)) {

            $this->cart->addProduct($id, $amount, $offer, $multioffers, $concomitants, $concomitants_amount, $additional_uniq, self::CART_SOURCE_CART_PAGE, true);

            if (!$this->url->isAjax()) {
                $this->app->redirect($this->router->getUrl('shop-front-cartpage'));
            }
        }

        if ($config->getCheckoutType() == ShopConfig::CHECKOUT_TYPE_CART_CHECKOUT && !$this->url->isPost() && !$float_cart) {
            Application::getInstance()->redirect(RouterManager::obj()->getUrl('shop-front-checkout'));
        }

        $cart_data = $this->cart->getCartData();

        $this->view->assign([
            'cart' => $this->cart,
            'cart_data' => $cart_data,
        ]);

        if ($checkout && !$cart_data['has_error']) {
            // Фиксация события "Начало оформления заказа" для статистики
            EventManager::fire('statistic', ['type' => StatisticEvents::TYPE_SALES_CART_SUBMIT]);

            if ($config->getCheckoutType() == ShopConfig::CHECKOUT_TYPE_ONE_PAGE) {
                $this->result->setRedirect($this->router->getUrl('shop-front-checkout', ['Act' => 'freezeCart']));
            } else {
                $this->result->setRedirect($this->router->getUrl('shop-front-checkout'));
            }
        }

        $this->result->addSection('cart', [
            'can_checkout' => !$cart_data['has_error'],
            'total_unformated' => $cart_data['total_unformatted'],
            'total_price' => $cart_data['total'],
            'items_count' => $cart_data['items_count'],
            'session_cart_products' => $_SESSION[Cart::SESSION_CART_PRODUCTS],
        ]);

        return $this->result->setTemplate('cartpage.tpl');
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
     */
    function actionCleanCart()
    {
        $this->cart->clean();
        return $this->actionIndex();
    }

    /**
     * Повторяет предыдущий заказ
     *
     * @return void
     * @throws RSException
     */
    function actionRepeatOrder()
    {
        $order_num = $this->url->request('order_num', TYPE_STRING, false); //Номер заказа

        if ($order_num) { //Если заказ найден, повторим его и переключимся в корзину
            $this->getCart()->repeatCartFromOrder($order_num);
        }
        Application::getInstance()->redirect($this->router->getUrl('shop-front-cartpage'));
    }

    /**
     * Возвращает корзину
     *
     * @return Cart
     */
    function getCart()
    {
        return $this->cart;
    }
}
