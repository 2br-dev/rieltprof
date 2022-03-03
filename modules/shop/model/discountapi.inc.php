<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/ declare (strict_types=1);

namespace Shop\Model;

use Catalog\Model\DirApi;
use Catalog\Model\Orm\Product;
use RS\Config\Loader as ConfigLoader;
use RS\Event\Exception as EventException;
use RS\Exception as RSException;
use RS\Module\AbstractModel\EntityList;
use Shop\Model\Discounts\CartItemDiscount;
use Shop\Model\Orm\AbstractCartItem;
use Shop\Model\Orm\Discount;
use RS\Db\Exception as DbException;

class DiscountApi extends EntityList
{
    const DISCOUNT_SOURCE_PERCENTAGE_COUPON = 'percentage_coupon';
    const DISCOUNT_SOURCE_FIXED_COUPON = 'fixed_coupon';

    public function __construct()
    {
        parent::__construct(new Discount(), [
            'multisite' => true,
        ]);
    }

    /**
     * Применяет скидки от процентных купонов к корзине
     *
     * @param Cart $cart - объект корзины
     * @throws RSException
     */
    public static function applyCouponPercentDiscountsToCart(Cart $cart)
    {
        $coupon_items = $cart->getCouponItems();
        $in_basket = $cart->getProductItemsWithConcomitants();
        $cart_data = $cart->getCartData(false, false);
        $cart_total = $cart_data['total_base'] + $cart_data['total_discount'];

        foreach ($in_basket as $basket_item) {
            /** @var AbstractCartItem $cart_item */
            $cart_item = $basket_item['cartitem'];
            $cart_item->removeDiscountsBySource(self::DISCOUNT_SOURCE_PERCENTAGE_COUPON);
        }
        foreach ($coupon_items as $key => $coupon_item) {
            /** @var Discount $coupon */
            $coupon = $coupon_item['coupon'];

            if ($coupon['discount_type'] == Discount::DISCOUNT_TYPE_PERCENT) {
                //Проверим купон на минимальную сумму заказа
                $min_order_price = $coupon->getMinOrderPrice();
                $date_expire = ($coupon['period'] == 'timelimit') && (date('Y-m-d H:i:s') > $coupon['endtime']);
                if (($min_order_price > $cart_total || $date_expire || !$coupon['active']) && $cart->getMode() == CART::MODE_SESSION) {
                    $cart->removeItem($key);
                } else {
                    $coupon_applied = false;

                    $linked_products = $coupon['products']['product'] ?? [];
                    $linked_groups = $coupon['products']['group'] ?? [];
                    $linked_all = in_array(0, $linked_groups) || (empty($linked_groups) && empty($linked_products));
                    if (!$linked_all) {
                        $linked_groups = DirApi::getChildsId($linked_groups);
                    }

                    foreach ($in_basket as $basket_item) {
                        /** @var AbstractCartItem $cart_item */
                        $cart_item = $basket_item['cartitem'];
                        /** @var Product $product */
                        $product = $basket_item['product'];

                        if ($linked_all || array_intersect($product['xdir'], $linked_groups) || in_array($product['id'], $linked_products)) {
                            $discount = new CartItemDiscount((float)$coupon['discount'], CartItemDiscount::UNIT_PERCENT, DiscountApi::DISCOUNT_SOURCE_PERCENTAGE_COUPON);
                            $cart_item->addDiscount($discount);
                            $coupon_applied = true;
                        }
                    }

                    if (!$coupon_applied) {
                        $cart->removeItem($key);
                    }
                }
            }
        }
    }

    /**
     * Применяет скидки от фиксированых купонов к корзине
     *
     * @param Cart $cart - объект корзины
     * @throws RSException
     */
    public static function applyCouponFixedDiscountsToCart(Cart $cart)
    {
        $shop_config = ConfigLoader::byModule(__CLASS__);
        $coupon_items = $cart->getCouponItems();
        $in_basket = $cart->getProductItemsWithConcomitants();
        $cart_data = $cart->getCartData(false, false);
        $cart_total = $cart_data['total_base'] + $cart_data['total_discount'];

        foreach ($in_basket as $basket_item) {
            /** @var AbstractCartItem $cart_item */
            $cart_item = $basket_item['cartitem'];
            $cart_item->removeDiscountsBySource(self::DISCOUNT_SOURCE_FIXED_COUPON);
        }
        foreach ($coupon_items as $key => $coupon_item) {
            /** @var Discount $coupon */
            $coupon = $coupon_item['coupon'];

            if ($coupon['discount_type'] == Discount::DISCOUNT_TYPE_BASE_CURRENCY) {
                //Проверим купон на минимальную сумму заказа
                $min_order_price = $coupon->getMinOrderPrice();
                $date_expire = ($coupon['period'] == 'timelimit') && (date('Y-m-d H:i:s') > $coupon['endtime']);
                if (($min_order_price > $cart_total || $date_expire || !$coupon['active']) && $cart->getMode() == CART::MODE_SESSION) {
                    $cart->removeItem($key);
                } else {
                    $linked_products = $coupon['products']['product'] ?? [];
                    $linked_groups = $coupon['products']['group'] ?? [];
                    $linked_all = in_array(0, $linked_groups) || (empty($linked_groups) && empty($linked_products));
                    if (!$linked_all) {
                        $linked_groups = DirApi::getChildsId($linked_groups);
                    }

                    $remaining_discount_limit = $cart->getItemsRemainingDiscountLimit();
                    $linked_remaining_discount_limit = [];
                    foreach ($in_basket as $uniq => $basket_item) {
                        /** @var AbstractCartItem $cart_item */
                        $cart_item = $basket_item['cartitem'];
                        /** @var Product $product */
                        $product = $basket_item['product'];

                        if (!$cart_item->getForbidDiscounts() && ($linked_all || array_intersect($product['xdir'], $linked_groups) || in_array($product['id'], $linked_products))) {
                            $linked_remaining_discount_limit[$uniq] = $remaining_discount_limit[$uniq];
                        }
                    }

                    if (empty($linked_remaining_discount_limit)) {
                        $cart->removeItem($key);
                    } else {
                        /** @var float $coupon_discount_amount */
                        $coupon_discount_amount = (float)$coupon['discount'];
                        $total_without_discount = $cart_data['total_base'] + $cart_data['total_discount'];
                        if ($coupon_discount_amount > ($total_without_discount * ($shop_config['fixed_discount_max_order_percent'] / 100))) {
                            $coupon_discount_amount = $total_without_discount * ($shop_config['fixed_discount_max_order_percent'] / 100);
                        }

                        $coupon_discounts = Cart::evenlyAllocateTheSum($coupon_discount_amount, $linked_remaining_discount_limit);
                        foreach ($coupon_discounts as $uniq => $discount_amount) {
                            /** @var AbstractCartItem $cart_item */
                            $cart_item = $in_basket[$uniq]['cartitem'];
                            $discount = new CartItemDiscount($coupon_discounts[$uniq], CartItemDiscount::UNIT_BASE_COST, self::DISCOUNT_SOURCE_FIXED_COUPON);
                            $discount->setFlagAlwaysAddDiscount();
                            $cart_item->addDiscount($discount);
                        }
                    }
                }
            }
        }
    }

    /**
     * Приводит код купона к виду, в которм он хранится в базе
     *
     * @param string $code
     * @return string
     */
    public static function normalizeCode(string $code): string
    {
        return str_replace('-', '', strtolower($code));
    }

    /**
     * Сохраняет купон в базе данных
     *
     * @param integer|null $id - id купона. если задан будет update иначе insert
     * @param array $user_post - добавить к посту
     * @return bool
     * @throws EventException
     * @throws RSException
     */
    public function save($id = null, array $user_post = []): bool
    {
        $result = parent::save($id, $user_post);

        /** @var Discount $element */
        $element = $this->getElement();
        if ($id === null && $result && $element['makecount'] > 1) { //Если нужно создать несколько купонов
            $yet = $element['makecount'] - 1;
            for ($i = 0; $i < $yet; $i++) {
                $element['id'] = null;
                $element['code'] = $element->generateCode();
                $result = $result && $element->insert();
            }
        }
        return $result;
    }

    /**
     * Сохраняет купон при мультиредактировании
     *
     * @param array $data - ассоциативный массив с изменяемыми сведеними
     * @param array $ids
     * @return int
     * @throws DbException
     */
    public function multiUpdate(array $data, $ids = [])
    {
        if (isset($data['products'])) {
            $data['sproducts'] = serialize($data['products']);
            unset($data['products']);
        }
        return parent::multiUpdate($data, $ids);
    }
}
