<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/ declare (strict_types=1);

namespace Shop\Model\Discounts;

use Catalog\Model\CostApi;
use RS\Exception as RSException;
use Shop\Model\Orm\AbstractCartItem;

class CartItemDiscount extends AbstractDiscount
{
    const UNIT_SINGLE_BASE_COST = 'single_base_cost'; // фиксированная скидка на единицу товара

    /** @var AbstractCartItem */
    protected $owner;

    /**
     * Возвращает размер скидки в базовой валюте
     *
     * @return float
     * @throws RSException
     */
    public function getAmountOfDiscount(): float
    {
        $discount = 0;
        switch ($this->getUnit()) {
            case self::UNIT_PERCENT:
                $base_cost = DiscountManager::instance()->getCartItemBaseCost($this->getOwner());
                $discount = $base_cost / 100 * $this->getDiscount();
                $discount = CostApi::roundCost($discount, CostApi::FLOOR);
                break;
            case self::UNIT_BASE_COST:
                $discount = $this->getDiscount();
                break;
            case self::UNIT_SINGLE_BASE_COST:
                $cart_item = $this->getOwner();
                $discount = $this->getDiscount() * $cart_item['amount'];
                break;
        }

        return $discount;
    }

    /**
     * Возвращает владельца скидки
     *
     * @return AbstractCartItem
     */
    public function getOwner(): AbstractCartItem
    {
        return $this->owner;
    }

    /**
     * Устанавливает владельца скидки
     *
     * @param AbstractCartItem $cart_item
     * @return void
     */
    public function setOwner(AbstractCartItem $cart_item): void
    {
        $this->owner = $cart_item;
    }
}
