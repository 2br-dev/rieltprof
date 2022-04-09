<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\Behavior;

use Catalog\Model\Orm\Product;
use RS\Behavior\BehaviorAbstract;
use RS\Config\Loader;
use Shop\Model\Cart;

/**
 * Класс, расширяет методы товара
 */
class CatalogProduct extends BehaviorAbstract
{
    /**
     * @var $owner Product
     */
    protected $owner;

    /**
     * Возвращает true, если товар присутствует в корзине
     *
     * @param null $offer_id ID комплектации
     * @return bool
     */
    public function inCart($offer_id = null)
    {
        if ($offer_id) {
            return isset($_SESSION[Cart::SESSION_CART_PRODUCTS][$this->owner['id']][$offer_id]);
        } else {
            return isset($_SESSION[Cart::SESSION_CART_PRODUCTS][$this->owner['id']]);
        }
    }

    /**
     * Возвращает параметры товара для кнопки "В Корзину" в формате JSON,
     * только для основной комплектации
     *
     * @return string
     */
    public function getAmountParamsJson()
    {
        $shop_config = Loader::byModule($this);

        $amount_step = $this->owner->getAmountStep();
        $product_stock = $this->owner->getNum();
        $amount_add_to_cart = max($this->owner->getMinOrderQuantity(), $amount_step);
        $cart_amount_options = [
            'productId' => $this->owner['id'],
            'amountStep' => $amount_step,
            'minAmount' => $this->owner->getMinOrderQuantity()
        ];
        if ($shop_config['allow_buy_num_less_min_order'] && $product_stock < $this->owner->getMinOrderQuantity()) {
            $break_point = ($shop_config['allow_buy_all_stock_ignoring_amount_step']) ? $product_stock : floor($product_stock / $amount_step) * $amount_step;
            $cart_amount_options['amountBreakPoint'] = $break_point;
            $amount_add_to_cart = $break_point;
        }
        elseif ($shop_config['allow_buy_all_stock_ignoring_amount_step'] && $product_stock > $this->owner->getMinOrderQuantity()) {
            $cart_amount_options['amountBreakPoint'] = $product_stock;
            if ($product_stock < $amount_step) {
                $amount_add_to_cart = $product_stock;
            }
        }
        $cart_amount_options['amountAddToCart'] = $amount_add_to_cart;

        if ($shop_config['check_quantity']) {
            $cart_amount_options['maxAmount'] = $this->owner->getNum();
        }

        return json_encode($cart_amount_options);
    }

    /**
     * Возвращает текущее количество товара во всех комплектациях в корзине
     *
     * @return float
     */
    public function getAmountInCart()
    {
        $amount = 0;
        if ($this->inCart()) {
            foreach ($_SESSION[Cart::SESSION_CART_PRODUCTS][$this->owner['id']] as $offer_amount) {
                $amount += $offer_amount;
            }
        }

        return $amount;
    }

    /**
     * Возвращает массив кодов ТН ВЭД
     *
     * @return array
     */
    public function getTnVedCodes()
    {
        return explode(',', $this->owner['tn_ved_codes']);
    }

    /**
     * Возвращает Номер Грузовой Таможенной Декларации
     *
     * @return string
     */
    public function getGtd()
    {
        return $this->owner['gtd'];
    }
}