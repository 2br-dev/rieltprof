<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/ declare (strict_types=1);

namespace Shop\Model\Discounts;

abstract class AbstractDiscount
{
    const UNIT_PERCENT = 'percent'; // скидка в процентах
    const UNIT_BASE_COST = 'base_cost'; // фиксированная скидка
    const SAVE_KEY_DISCOUNT = 'discount';
    const SAVE_KEY_UNIT = 'unit';
    const SAVE_KEY_SOURCE = 'source';
    const SAVE_KEY_ALWAYS_ADD = 'always_add';

    protected $discount;
    protected $unit;
    protected $source;
    protected $flag_always_add_discount = false;

    /**
     * AbstractDiscount constructor.
     *
     * @param float $discount - размер скидки
     * @param string $unit - единица измерения скидки
     * @param string $source - источник применения скидки
     * @param bool $always_add - всегда добавлять скидку
     */
    public function __construct(float $discount, string $unit, string $source, bool $always_add = false)
    {
        $this->setDiscount($discount);
        $this->setUnit($unit);
        $this->setSource($source);
        $this->setFlagAlwaysAddDiscount($always_add);
    }

    /**
     * Возвращает размер скидки в базовой валюте
     *
     * @return float
     */
    abstract public function getAmountOfDiscount(): float;

    /**
     * Загружает себя из массива
     *
     * @param array $array
     * @return static
     */
    public static function loadFromArray(array $array): self
    {
        $object = new static($array[self::SAVE_KEY_DISCOUNT], $array[self::SAVE_KEY_UNIT], $array[self::SAVE_KEY_SOURCE], $array[self::SAVE_KEY_ALWAYS_ADD]??false);
        return $object;
    }

    /**
     * Сохраняет себя в виде массива
     *
     * @return array
     */
    public function saveInArray(): array
    {
        return [
            self::SAVE_KEY_DISCOUNT => $this->getDiscount(),
            self::SAVE_KEY_UNIT => $this->getUnit(),
            self::SAVE_KEY_SOURCE => $this->getSource(),
            self::SAVE_KEY_ALWAYS_ADD => $this->isFlagAlwaysAddDiscount(),
        ];
    }

    /**
     * Возвращает размер скидки
     *
     * @return float
     */
    protected function getDiscount()
    {
        return $this->discount;
    }

    /**
     * Устанавливает размер скидки
     *
     * @param float $discount
     * @return void
     */
    protected function setDiscount($discount)
    {
        $this->discount = $discount;
    }

    /**
     * Возвращает единицу измерения скидки
     *
     * @return string
     */
    protected function getUnit()
    {
        return $this->unit;
    }

    /**
     * Устанавливает единицу измерения скидки
     *
     * @param string $unit
     * @return void
     */
    protected function setUnit($unit)
    {
        $this->unit = $unit;
    }

    /**
     * Возвращает источник применения скидки
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Устанавливает источник применения скидки
     *
     * @param string $source
     * @return void
     */
    protected function setSource($source)
    {
        $this->source = $source;
    }

    /**
     * Возвращает флаг "всегда прибавлять размер скидки"
     *
     * @return bool
     */
    public function isFlagAlwaysAddDiscount(): bool
    {
        return $this->flag_always_add_discount;
    }

    /**
     * Устанавливает флаг "всегда прибавлять размер скидки"
     *
     * @param bool $value - значение
     */
    public function setFlagAlwaysAddDiscount(bool $value = true): void
    {
        $this->flag_always_add_discount = $value;
    }
}
