<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/ declare(strict_types=1);

namespace Shop\Model\Orm;

use Catalog\Model\Orm\Product;
use RS\Orm\AbstractObject;
use RS\Orm\Type;
use Shop\Model\Discounts\CartItemDiscount;

/**
 * Позиция в корзине
 */
abstract class AbstractCartItem extends AbstractObject
{
    const TYPE_PRODUCT = 'product';
    const TYPE_SERVICE = 'service';
    const TYPE_COUPON = 'coupon';
    const EXTRA_KEY_DISCOUNTS = 'discounts'; // данные по применённым скидкам
    const EXTRA_FLAG_FORBID_CHANGE_AMOUNT = 'forbid_change_amount';
    const EXTRA_FLAG_FORBID_REMOVE = 'forbid_remove';
    const EXTRA_FLAG_FORBID_DISCOUNTS = 'forbid_discounts';

    protected static $table = 'cart';

    /** @var CartItemDiscount[] */
    protected $discounts = null;
    /** @var Product */
    protected $product;
    protected $entity;

    function _init()
    {
        $this->getPropertyIterator()->append([
            'uniq' => new Type\Varchar([
                'maxLength' => 10,
                'description' => t('ID в рамках одной корзины'),
                'allowEmpty' => false,
            ]),
            'type' => new Type\Enum([self::TYPE_PRODUCT, self::TYPE_SERVICE, self::TYPE_COUPON], [
                'description' => t('Тип записи товар, услуга, скидочный купон')
            ]),
            'entity_id' => new Type\Varchar([
                'description' => t('ID объекта type'),
                'maxLength' => 50
            ]),
            'offer' => new Type\Integer([
                'description' => t('Комплектация')
            ]),
            'multioffers' => new Type\Text([
                'description' => t('Многомерные комплектации')
            ]),
            'amount' => new Type\Decimal([
                'description' => t('Количество'),
                'maxLength' => 11,
                'decimal' => 3,
                'default' => 1
            ]),
            'title' => new Type\Varchar([
                'description' => t('Название')
            ]),
            'extra' => new Type\Text([
                'description' => t('Дополнительные сведения (сериализованные)')
            ]),
            'extra_arr' => new Type\ArrayList([
                'description' => t('Дополнительные сведения')
            ]),
        ]);
    }

    /**
     * Вызывается после загрузки объекта
     * @return void
     */
    public function afterObjectLoad()
    {
        // Приведение типов
        $this['amount'] = (float)$this['amount'];
        $this['extra_arr'] = (isset($this['extra'])) ? @unserialize($this['extra']) : [];
    }

    /**
     * Вызывается перед сохранением объекта в storage
     * Если возвращено false, то сохранение не произойдет
     *
     * @param string $save_flag - insert|update|replace
     * @return void
     */
    public function beforeWrite($save_flag)
    {
        $this->saveDiscounts();
        if ($this->isModified('extra_arr')) {
            $this['extra'] = serialize($this['extra_arr']);
        }
    }

    /**
     * Добавляет скидку к товарной позиции
     *
     * @param CartItemDiscount $discount - скидка
     * @return void
     */
    public function addDiscount(CartItemDiscount $discount): void
    {
        if (!$this->getForbidDiscounts()) {
            $this->initDiscounts();
            $discount->setOwner($this);
            $this->discounts[] = $discount;
        }
    }

    /**
     * Удаляет у товарной позиции все скидки
     *
     * @return void
     */
    public function removeAllDiscounts(): void
    {
        $this->discounts = [];
    }

    /**
     * Удаляет у товарной позиции все скидки от указанного источника
     *
     * @param string $source - идентификатор источника
     * @return void
     */
    public function removeDiscountsBySource(string $source): void
    {
        $this->initDiscounts();
        foreach ($this->discounts as $key => $discount) {
            if ($discount->getSource() == $source) {
                unset($this->discounts[$key]);
            }
        }
    }

    /**
     * Возвращает скидки, применённые к товарной позиции
     *
     * @return CartItemDiscount[]
     */
    public function getDiscounts(): array
    {
        $this->initDiscounts();
        return $this->discounts;
    }

    /**
     * Инициализирует применённые скидки из дополнительного параметра
     *
     * @return void
     */
    protected function initDiscounts(): void
    {
        if ($this->discounts === null) {
            $this->discounts = [];
            $discounts_data = $this->getExtraParam(self::EXTRA_KEY_DISCOUNTS, []);
            foreach ($discounts_data as $item) {
                $discount = CartItemDiscount::loadFromArray($item);
                $discount->setOwner($this);
                $this->discounts[] = $discount;
            }
        }
    }

    /**
     * Сохраняет применённые скидки в дополнительном параметре
     *
     * @return void
     */
    protected function saveDiscounts(): void
    {
        if ($this->discounts !== null) {
            $discounts_data = [];
            foreach ($this->discounts as $discount) {
                $discounts_data[] = $discount->saveInArray();
            }
            $this->setExtraParam(self::EXTRA_KEY_DISCOUNTS, $discounts_data);
        }
    }

    /**
     * Возвращает шаг изменения количества товара. Если это не товар, то возвращает false
     *
     * @return float|false
     * @deprecated (20.08) - устарел
     */
    public function getProductAmountStep()
    {
        if ($product = $this->getProduct()) {
            return $product->getAmountStep();
        }
        return false;
    }

    /**
     * Возвращает значение флага, запрещающего вручную изменять количество товаров в корзине
     *
     * @return bool
     */
    public function getForbidChangeAmount()
    {
        return $this->getExtraParam(self::EXTRA_FLAG_FORBID_CHANGE_AMOUNT, false);
    }

    /**
     * Устанавливает значение флага, запрещающего вручную изменять количество товаров в корзине
     *
     * @param bool $value - значение
     * @return self
     */
    public function setForbidChangeAmount($value = true)
    {
        $this->setExtraParam(self::EXTRA_FLAG_FORBID_CHANGE_AMOUNT, $value);
        return $this;
    }

    /**
     * Возвращает значение флага, запрещающего вручную удалять товар из корзины
     *
     * @return bool
     */
    public function getForbidRemove()
    {
        return $this->getExtraParam(self::EXTRA_FLAG_FORBID_REMOVE, false);
    }

    /**
     * Устанавливает значение флага, запрещающего вручную удалять товар из корзины
     *
     * @param bool $value - значение
     * @return self
     */
    public function setForbidRemove($value = true)
    {
        $this->setExtraParam(self::EXTRA_FLAG_FORBID_REMOVE, $value);
        return $this;
    }

    /**
     * Возвращает значение флага, запрещающего применять скидки к товару
     *
     * @return bool
     */
    public function getForbidDiscounts()
    {
        return $this->getExtraParam(self::EXTRA_FLAG_FORBID_DISCOUNTS, false);
    }

    /**
     * Устанавливает значение флага, запрещающего применять скидки к товару
     *
     * @param bool $value - значение
     * @return self
     */
    public function setForbidDiscounts($value = true)
    {
        $this->setExtraParam(self::EXTRA_FLAG_FORBID_DISCOUNTS, $value);
        return $this;
    }

    /**
     * Возвращает данные из дополнительного параметра
     *
     * @param string $key - ключ в массиве, если не передан то вернётся весь массив
     * @param mixed $default - значение по умолчанию
     * @return mixed
     */
    public function getExtraParam($key = null, $default = null)
    {
        if ($key === null) {
            return $this['extra_arr'];
        }
        return isset($this['extra_arr'][$key]) ? $this['extra_arr'][$key] : $default;
    }

    /**
     * Устанавливает данные в дополнительный параметр
     *
     * @param string $key - ключ в массиве
     * @param mixed $value - значение
     * @return void
     */
    public function setExtraParam($key, $value)
    {
        $extra_arr = $this['extra_arr'];
        $extra_arr[$key] = $value;
        $this['extra_arr'] = $extra_arr;
    }

    /**
     * Удаляет данные из дополнительного параметра
     *
     * @param string $key - ключ в массиве
     * @return void
     */
    public function unsetExtraParam($key)
    {
        $extra = $this['extra_arr'];
        if (isset($extra[$key])) {
            unset($extra[$key]);
        }
        $this['extra_arr'] = $extra;
    }

    /**
     * Возвращает массив с названиями и выбранными значениями многомерных комплектаций
     *
     * @return array
     */
    public function getMultiOfferTitles()
    {
        $multioffers = @unserialize((string)$this['multioffers']);
        return $multioffers ?: [];
    }

    /**
     * Устанавливает объект связанного товара
     *
     * @param Product $product
     * @return void
     * @deprecated (20.08) - вмето данного метода следует использовать setEntity
     */
    public function setProduct(Product $product): void
    {
        if ($this['type'] == self::TYPE_PRODUCT) {
            $this->product = $product;
        }
    }

    /**
     * Возвращает связанный объект
     *
     * @return object|null
     */
    public function getEntity()
    {
        if ($this->entity === null) {
            switch ($this['type']) {
                case self::TYPE_PRODUCT:
                    $this->entity = new Product($this['entity_id']);
                    break;
                case self::TYPE_COUPON:
                    $this->entity = new Discount($this['entity_id']);
                    break;
            }
        }
        return $this->entity;
    }

    /**
     * Устанавливает связанный объект
     *
     * @param object $entity
     * @param void
     */
    public function setEntity($entity): void
    {
        switch ($this['type']) {
            case self::TYPE_PRODUCT:
                if ($entity instanceof Product) {
                    $this->entity = $entity;
                }
                break;
            case self::TYPE_COUPON:
                if ($entity instanceof Discount) {
                    $this->entity = $entity;
                }
                break;
            default:
                $this->entity = $entity;
        }
    }

    /**
     * Возвращает объект связанного товара
     * если позиция не является товаром - возвращает false
     *
     * @return Product|null
     * @deprecated (20.08) - вмето данного метода следует использовать getEntity
     */
    public function getProduct(): ?Product
    {
        if ($this['type'] == self::TYPE_PRODUCT) {
            if ($this->product === null) {
                $this->product = new Product($this['entity_id']);
            }
            return $this->product;
        }
        return null;
    }
}
