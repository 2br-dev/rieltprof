<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/ declare (strict_types=1);

namespace Shop\Model\Discounts;

use Catalog\Model\CostApi;
use Catalog\Model\Orm\Product;
use RS\Config\Loader as ConfigLoader;
use Shop\Model\Orm\AbstractCartItem;
use Shop\Model\Orm\CartItem;
use Shop\Model\Orm\Discount;

// TODO зарефакторить после рефакторинга комплектаций (пора)
class DiscountManager
{
    const DISCOUNT_COMBINATION_MAX = 'max';
    const DISCOUNT_COMBINATION_MIN = 'min';
    const DISCOUNT_COMBINATION_SUM = 'sum';

    protected $discount_combination_rule;

    /**
     * DiscountManager constructor.
     */
    protected function __construct()
    {
        $shop_config = ConfigLoader::byModule(__CLASS__);
        /** @var string $discount_combination_rule */
        $discount_combination_rule = $shop_config['discount_combination'];
        $this->setDiscountCombinationRule($discount_combination_rule);
    }

    /**
     * Возвращает экземпляр класса
     *
     * @return static
     */
    public static function instance(): self
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new static();
        }
        return $instance;
    }

    /**
     * Возвращает иотговую скидку на товарную позицию
     *
     * @param AbstractCartItem $cart_item
     * @return float
     */
    public function getCartItemFinalDiscount($cart_item): float
    {
        $base_cost = $this->getCartItemBaseCost($cart_item);
        return round($this->calculateDiscountSum($cart_item->getDiscounts(), $base_cost), 2);
    }

    /**
     * Возвращает базовую цену на товарную позицию
     *
     * @param AbstractCartItem $cart_item - товарная позиция
     * @return float
     */
    public function getCartItemBaseCost(AbstractCartItem $cart_item): float
    {
        if ($cart_item instanceof CartItem) {
            $single_cost = $cart_item->getExtraParam(CartItem::EXTRA_KEY_PRICE, null);
            if ($single_cost === null && $cart_item['type'] == AbstractCartItem::TYPE_PRODUCT) {
                $product = $cart_item->getEntity();
                $offer = (int)$cart_item['offer'];
                $single_cost = $this->getProductBaseCost($product, $offer);
            }
        } else {
            $single_cost = $cart_item['single_cost'];
        }

        return $single_cost * $cart_item['amount'];
    }

    /**
     * Возвращает базовую цену товара
     *
     * @param Product $product - товар
     * @param int $offer_id - id комплектации
     * @return float
     */
    public function getProductBaseCost(Product $product, int $offer_id = null): float
    {
        $shop_config = ConfigLoader::byModule($this);
        $base_cost = (float)$product->getCost(CostApi::getUserCost(), $offer_id, false, true);

        $old_cost_id = CostApi::getOldCostId();
        if ($shop_config['old_cost_delta_as_discount'] && $old_cost_id) {
            $old_cost = (float)$product->getCost($old_cost_id, $offer_id, false, true);
            if ($old_cost > $base_cost) {
                $base_cost = $old_cost;
            }
        }

        return $base_cost;
    }

    /**
     * Расчитывает суммарный размер скидки на основе переданного массиве скидок
     *
     * @param AbstractDiscount[] $discounts - массив скидок
     * @param float $base_cost - базовая цена, от которой расчитывается скидка
     * @return float
     */
    protected function calculateDiscountSum(array $discounts, $base_cost): float
    {
        $shop_config = ConfigLoader::byModule($this);
        $result = 0;
        $always_add_sum = 0;

        foreach ($discounts as $discount) {
            $discount_amount = $discount->getAmountOfDiscount();

            if ($discount->isFlagAlwaysAddDiscount()) {
                $always_add_sum += $discount_amount;
            } else {
                switch ($this->getDiscountCombinationRule()) {
                    case self::DISCOUNT_COMBINATION_MAX:
                        if ($discount_amount > $result) {
                            $result = $discount_amount;
                        }
                        break;
                    case self::DISCOUNT_COMBINATION_MIN:
                        if ($discount_amount > 0 && ($result == 0 || $discount_amount < $result)) {
                            $result = $discount_amount;
                        }
                        break;
                    case self::DISCOUNT_COMBINATION_SUM:
                        $result += $discount_amount;
                        break;
                }
            }
        }
        $result += $always_add_sum;

        $max_discount = $base_cost * ($shop_config['cart_item_max_discount'] / 100);
        if ($result > $max_discount) {
            $result = $max_discount;
        }

        return $result;
    }

    /**
     * Справочник правил сочетания скидок
     *
     * @return string[]
     */
    public static function handbookDiscountCombination(): array
    {
        return [
            self::DISCOUNT_COMBINATION_MAX => t('Применяется максимальная скидка'),
            self::DISCOUNT_COMBINATION_MIN => t('Применяется минимальная скидка'),
            self::DISCOUNT_COMBINATION_SUM => t('Скидки суммируются'),
        ];
    }

    /**
     * Возвращает правило совмещения скидок
     *
     * @return string
     */
    protected function getDiscountCombinationRule(): string
    {
        return $this->discount_combination_rule;
    }

    /**
     * Устанавливает правило совмещения скидок
     *
     * @param string $discount_combination_rule
     * @return void
     */
    protected function setDiscountCombinationRule(string $discount_combination_rule): void
    {
        $this->discount_combination_rule = $discount_combination_rule;
    }
}
