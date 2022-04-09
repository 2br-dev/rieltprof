<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\Orm\Cargo;

use Catalog\Model\Api as ProductApi;
use RS\Orm\OrmObject;
use RS\Orm\Request;
use RS\Orm\Type;
use Shop\Model\Cart;
use Shop\Model\Orm\Order;
use Shop\Model\Orm\OrderItem;

/**
 * ORM-объект, характеризует грузо-место в заказе
 */
class OrderCargo extends OrmObject
{
    const WEIGHT_UNIT_G = 'g';
    const WEIGHT_UNIT_KG = 'kg';
    const WEIGHT_UNIT_T = 't';
    const WEIGHT_UNIT_LB = 'lb';

    protected $weight_ratio = [
        self::WEIGHT_UNIT_G => 1,
        self::WEIGHT_UNIT_KG => 1000,
        self::WEIGHT_UNIT_T => 1000000,
        self::WEIGHT_UNIT_LB => 453.59237
    ];

    protected static $table = 'order_cargo';
    protected $products_in_cargo;
    protected $cargo_items;

    function _init()
    {
        parent::_init()->append([
            'order_id' => (new Type\Integer)
                ->setDescription('ID заказа')
                ->setIndex(true),
            'title' => (new Type\Varchar())
                ->setDescription(t('Название коробки')),
            'width' => (new Type\Integer)
                ->setDescription(t('Ширина в мм'))
                ->setChecker('ChkEmpty', t('Не указана ширина упаковки')),
            'height' => (new Type\Integer)
                ->setDescription(t('Высота в мм'))
                ->setChecker('ChkEmpty', t('Не указана длина упаковки')),
            'dept' => (new Type\Integer)
                ->setDescription(t('Глубина в мм'))
                ->setChecker('ChkEmpty', t('Не указана высота упаковки')),
            'weight' => (new Type\Integer)
                ->setDescription(t('Вес коробки'))
                ->setChecker('ChkEmpty', t('Не указан вес упаковки')),
            'extra' => (new Type\Text())
                ->setDescription(t('Произвольные данные')),
            'extra_arr' => (new Type\ArrayList())
                ->setDescription(t('Массив произвольных данных'))
                ->setHint(t('Здесь можно хранить ссылки на наклейки от служб доставок, id во внешних системах')),
            'products' => (new Type\ArrayList())
                ->setDescription(t('Товары, входящие в данное грузовое место'))
        ]);
    }

    /**
     * Возвращает читаемое название упаковки
     *
     * @return string
     */
    public function getTitle()
    {
        return $this['title'] ? $this['title'] : t('Коробка %size', [
            'size' => ($this['width']/10).'x'.($this['height']/10).'x'.($this['dept']/10)
        ]);
    }

    /**
     * Загружает элементы, добавленные в данную коробку
     *
     * @param bool $cache
     * @return OrderCargoItem[]
     */
    public function getCargoItems($cache = true)
    {
        if (!isset($this->cargo_items) || !$cache) {
            $this->cargo_items = Request::make()
                ->from(new OrderCargoItem())
                ->where([
                    'order_cargo_id' => $this['id']
                ])->objects();
        }

        return $this->cargo_items;
    }

    /**
     * Возвращает количество товара в данной коробке
     *
     * @param $order_item_uniq
     * @param $uit_id
     * @param bool $cache Если true, то будет использовано кэширование
     * @return float
     */
    public function getProductAmount($order_item_uniq, $uit_id, $cache = true)
    {
        $uit_id = $uit_id ?: 0;

        if ($this->products_in_cargo === null || !$cache) {
            $this->getCargoItems($cache);
            foreach($this->cargo_items as $item) {
                $this->products_in_cargo[$item['order_item_uniq']][$item['order_item_uit_id']] = $item['amount'];
            }
        }

        return (float)$this->products_in_cargo[$order_item_uniq][$uit_id] ?? 0;
    }

    /**
     * Обработчик, вызываемый сразу после загрузки объекта
     */
    public function afterObjectLoad()
    {
        $this['extra_arr'] = @unserialize($this['extra']) ?: [];
    }

    /**
     * Обработчик, вызыается перед сохранением объекта
     *
     * @param string $save_flag
     * @return false|void
     */
    public function beforeWrite($save_flag)
    {
        $this['extra'] = serialize($this['extra_arr']);
    }

    /**
     *  Обработчик, вызывается сразу после сохранения объекта
     *
     * @param string $save_flag
     */
    public function afterWrite($save_flag)
    {
        if ($this->isModified('products')) {
            $this->saveCargoItems($save_flag);
        }
    }

    /**
     * Сохраняет товары, находящиеся в грузоместе
     *
     * @param $save_flag
     */
    protected function saveCargoItems($save_flag)
    {
        $processed_ids = [];
        foreach($this['products'] as $order_item_uniq => $item_data) {
            foreach($item_data as $uit_id => $data) {
                if ($data['amount'] > 0) {
                    if ($save_flag == self::INSERT_FLAG) {
                        $cargo_item = new OrderCargoItem();
                    } else {
                        $cargo_item = OrderCargoItem::loadByWhere([
                            'order_cargo_id' => $this['id'],
                            'order_item_uniq' => $order_item_uniq,
                            'order_item_uit_id' => $uit_id
                        ]);
                    }

                    $cargo_item['order_id'] = $this['order_id'];
                    $cargo_item['order_cargo_id'] = $this['id'];
                    $cargo_item['order_item_uniq'] = $order_item_uniq;
                    $cargo_item['order_item_uit_id'] = $uit_id;
                    $cargo_item['amount'] = $data['amount'];
                    if ($cargo_item['id']) {
                        $cargo_item->update();
                    } else {
                        $cargo_item->insert();
                    }
                    $processed_ids[] = $cargo_item['id'];
                }
            }
        }

        if ($save_flag == self::UPDATE_FLAG) {
            $q = Request::make()
                ->delete()
                ->from(new OrderCargoItem())
                ->where([
                    'order_cargo_id' => $this['id']
                ]);
            if ($processed_ids) {
                $q->whereIn('id', $processed_ids, 'AND', true);
            }
            $q->exec();
        }
    }

    /**
     * Удаляет объект из хранилища
     *
     * @return bool
     */
    public function delete()
    {
        if ($result = parent::delete()) {
            Request::make()
                ->delete()
                ->from(new OrderCargoItem())
                ->where([
                    'order_cargo_id' => $this['id']
                ])->exec();
        }

        return $result;
    }

    /**
     * Возвращает, связанный объект заказа
     *
     * @return Order
     */
    public function getOrder()
    {
        return new Order($this['order_id']);
    }

    /*
     * Возвращает список объектов OrderItem, CargoItem, находящихся в коробке
     *
     * @return array

    public function getOrderItems()
    {
        $result = [];
        $order_items = Request::make()
            ->select('OI.*')
            ->from(new OrderItem(), 'OI')
            ->join(new OrderCargoItem(), 'OI.uniq = CI.order_item_uniq', 'CI')
            ->where([
                'CI.order_cargo_id' => $this['id']
            ])->objects(null, 'OI.uniq');

        foreach($this->getCargoItems() as $cargo_item) {
            $uniq = $cargo_item['order_item_uniq'];
            if (isset($order_items[ $uniq ])) {
                $result[$uniq] = [
                    'cargo_item' => $cargo_item,
                    'order_item' => $order_items[ $uniq ]
                ];
            }
        }

        return $result;
    }*/

    /**
     * Возращает Общий вес коробки с товарами в единицах измерения $unit
     *
     * @param string $unit Единица измерения, константа self::WEIGHT_UNIT_*
     * @return float
     */
    public function getTotalWeight($unit = self::WEIGHT_UNIT_G)
    {
        $weight_in_gramm = $this['weight'];

        $cart = $this->getOrder()->getCart();
        $product_items = $cart->getProductItems();
        foreach($this->getCargoItems() as $cargo_item) {
            $cart_item_data = $product_items[ $cargo_item['order_item_uniq'] ] ?? null;
            if ($cart_item_data) {
                $weight_in_gramm += $cart_item_data[Cart::TYPE_PRODUCT]
                    ->getWeight($cart_item_data[Cart::CART_ITEM_KEY]['offer'], ProductApi::WEIGHT_UNIT_G);
            }
        }

        return round($weight_in_gramm / $this->weight_ratio[$unit], 3);
    }
}