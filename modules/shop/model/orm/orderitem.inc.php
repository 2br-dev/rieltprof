<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model\Orm;

use Catalog\Model\Orm\Product;
use RS\Config\Loader as ConfigLoader;
use RS\Exception as RSException;
use RS\Orm\AbstractObject;
use RS\Orm\Type;
use Shop\Model\Marking\MarkingApi;
use Shop\Model\Marking\MarkingException;

/**
 * Позиция в корзине
 * --/--
 * @property string $uniq ID в рамках одной корзины
 * @property string $type Тип записи товар, услуга, скидочный купон
 * @property string $entity_id ID объекта type
 * @property integer $offer Комплектация
 * @property string $multioffers Многомерные комплектации
 * @property float $amount Количество
 * @property string $title Название
 * @property string $extra Дополнительные сведения (сериализованные)
 * @property array $extra_arr Дополнительные сведения
 * @property integer $order_id ID заказа
 * @property string $barcode Артикул
 * @property string $sku Штрихкод
 * @property string $model Модель
 * @property double $single_weight Вес
 * @property float $single_cost Цена за единицу продукции
 * @property float $price Стоимость
 * @property float $profit Доход
 * @property float $discount Скидка
 * @property integer $sortn Порядок
 * --\--
 */
class OrderItem extends AbstractCartItem
{
    const TYPE_COMMISSION = 'commission';
    const TYPE_TAX = 'tax';
    const TYPE_DELIVERY = 'delivery';
    const TYPE_ORDER_DISCOUNT = 'order_discount';
    const TYPE_SUBTOTAL = 'subtotal';

    protected static $table = 'order_items';

    /** @var OrderItemUIT[] */
    protected $uit = null;

    function _init()
    {
        parent::_init();

        $this->getPropertyIterator()->append([
            'order_id' => new Type\Integer([
                'description' => t('ID заказа'),
            ]),
            'type' => new Type\Enum([
                self::TYPE_PRODUCT,
                self::TYPE_COUPON,
                self::TYPE_COMMISSION,
                self::TYPE_TAX,
                self::TYPE_DELIVERY,
                self::TYPE_ORDER_DISCOUNT,
                self::TYPE_SUBTOTAL], [
                'description' => t('Тип записи товар, услуга, скидочный купон'),
                'index' => true
            ]),
            'barcode' => new Type\Varchar([
                'description' => t('Артикул'),
                'maxLength' => 100,
            ]),
            'sku' => new Type\Varchar([
                'description' => t('Штрихкод'),
                'maxLength' => 50,
            ]),
            'model' => new Type\Varchar([
                'description' => t('Модель')
            ]),
            'single_weight' => new Type\Double([
                'description' => t('Вес')
            ]),
            'single_cost' => new Type\Decimal([
                'description' => t('Цена за единицу продукции'),
                'maxlength' => 20,
                'decimal' => 2
            ]),
            'price' => new Type\Decimal([
                'description' => t('Стоимость'),
                'maxlength' => 20,
                'decimal' => 2,
                'default' => 0
            ]),
            'profit' => new Type\Decimal([
                'description' => t('Доход'),
                'maxlength' => 20,
                'decimal' => 2,
                'default' => 0
            ]),
            'discount' => new Type\Decimal([
                'description' => t('Скидка'),
                'maxlength' => 20,
                'decimal' => 2,
                'default' => 0
            ]),
            'sortn' => new Type\Integer([
                'description' => t('Порядок')
            ]),
        ]);

        $this->addIndex(['order_id', 'uniq'], self::INDEX_PRIMARY);
        $this->addIndex(['type', 'entity_id'], self::INDEX_KEY);
    }

    /**
     * Возвращает первичный ключ.
     *
     * @return string[]
     */
    function getPrimaryKeyProperty(): array
    {
        return ['order_id', 'uniq'];
    }

    /**
     * Вызывается перед сохранением объекта в storage
     * Если возвращено false, то сохранение не произойдет
     *
     * @param string $flag - insert|update|replace
     * @return void
     * @throws RSException
     */
    function beforeWrite($flag)
    {
        if (!$this->isModified('profit')) {
            $this['profit'] = $this->getProfit();
        }
        parent::beforeWrite($flag);
    }

    /**
     * Удаляет объект из хранилища
     * @return boolean - true, в случае успеха
     */
    public function delete()
    {
        MarkingApi::instance()->deleteOrderItemUITs($this);
        return parent::delete();
    }

    /**
     * Возвращает доход, от продажи товара в базовой валюте
     * Возвращает null, в случае если невозможно рассчитать доход для записи
     *
     * @return double|null
     * @throws RSException
     */
    function getProfit()
    {
        $config = ConfigLoader::byModule($this);
        if ($this['type'] == self::TYPE_PRODUCT && $config['source_cost']) {
            $product = new Product($this['entity_id']);

            if ($product['id']) {
                $sell_price = $this['price'] - $this['discount'];
                $source_cost = $product->getCost($config['source_cost'], $this['offer'], false, true);
                return $sell_price - ($source_cost * $this['amount']);
            }
        }
        return null;
    }

    /**
     * Загружает новые УИТ, заменяя имеющиеся
     *
     * @param array $data - новые данные УИТ
     * @return void
     * @throws MarkingException
     */
    public function rewriteUITs($data): void
    {
        $this->initUITs();
        foreach ($this->getUITs() as $uit) {
            $key = $uit['gtin'] . $uit['serial'];
            if (isset($data[$key])) {
                unset($data[$key]);
            } else {
                $uit->delete();
            }
        }
        foreach ($data as $key => $item_data) {
            $errors = [];
            try {
                $new_uit = OrderItemUIT::loadFromData($item_data);
                $new_uit['order_id'] = $this['order_id'];
                $new_uit['order_item_uniq'] = $this['uniq'];
                if ($new_uit->insert()) {
                    $this->uit[] = $new_uit;
                }
            } catch (MarkingException $e) {
                $errors[] = $e->getMessage();
            }

            if ($errors) {
                $error_text = t('%0 [plural:%0:код не прошёл|кода не прошли|кодов не прошли] валидацию (%1)', [count($errors), implode(', ', $errors)]);
                throw new MarkingException($error_text, MarkingException::ERROR_ORDER_ITEM_CODES_PARSE);
            }
        }
    }

    /**
     * Очищает УИТ, сохранённые в товарной позиции
     *
     * @return void
     */
    public function clearUITs(): void
    {
        $this->uit = [];
    }

    /**
     * Возвращает УИТ, сохранённые в товарной позиции
     *
     * @return OrderItemUIT[]
     */
    public function getUITs()
    {
        $this->initUITs();
        return $this->uit;
    }

    /**
     * Инициализирует сохранённые УИТ из БД
     *
     * @return void
     */
    protected function initUITs(): void
    {
        if ($this->uit === null) {
            $this->uit = MarkingApi::instance()->loadOrderItemUITs($this);
        }
    }

    /**
     * Сохраняет УИТ в БД
     *
     * @return void
     */
    protected function saveUITs(): void
    {
        MarkingApi::instance()->saveOrderItemUITs($this);
    }

    /**
     * Возвращает связанный объект
     *
     * @return AbstractObject
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
                case self::TYPE_DELIVERY:
                    $this->entity = new Delivery($this['entity_id']);
                    break;
                case self::TYPE_TAX:
                    $this->entity = new Tax($this['entity_id']);
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
            case self::TYPE_DELIVERY:
                if ($entity instanceof Delivery) {
                    $this->entity = $entity;
                }
                break;
            case self::TYPE_TAX:
                if ($entity instanceof Tax) {
                    $this->entity = $entity;
                }
                break;
            default:
                $this->entity = $entity;
        }
    }

    /**
     * Возвращает объект заказа
     *
     * @return Order
     */
    function getOrder()
    {
        return new Order($this['order_id']);
    }
}
