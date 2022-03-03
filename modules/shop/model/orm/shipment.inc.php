<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model\Orm;

use Catalog\Model\CurrencyApi;
use Catalog\Model\Orm\Currency;
use Catalog\Model\Orm\Product;
use RS\Orm\OrmObject;
use RS\Orm\Request as OrmRequest;
use RS\Orm\Type;

/**
 * Отгрузки
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $temp_id Временный id
 * @property integer $order_id id заказа
 * @property string $date Дата
 * @property string $info_order_num Номер заказа
 * @property float $info_total_sum Сумма отгрузки
 * --\--
 */
class Shipment extends OrmObject
{
    protected static $table = 'order_shipment';

    /** @var ShipmentItem[] */
    protected $shipment_items;

    function _init()
    {
        parent::_init()->append([
            'temp_id' => (new Type\Integer())
                ->setDescription(t('Временный id'))
                ->setRuntime(true)
                ->setVisible(false),
            'order_id' => (new Type\Integer())
                ->setDescription(t('id заказа')),
            'date' => (new Type\Datetime())
                ->setDescription(t('Дата')),
            'info_order_num' => (new Type\Varchar())
                ->setDescription(t('Номер заказа')),
            'info_total_sum' => (new Type\Decimal())
                ->setDescription(t('Сумма отгрузки'))
                ->setDecimal(2),
            '_items' => (new Type\UserTemplate('%shop%/form/shipment/shipment_items.tpl')),
        ]);
    }

    /**
     * Удаляет объект из хранилища
     * @return boolean - true, в случае успеха
     */
    public function delete()
    {
        $uit_ids = (new OrmRequest())
            ->select('uit_id')
            ->from(ShipmentItem::_getTable())
            ->where(['shipment_id' => $this['id']])
            ->where('uit_id > ""')
            ->exec()->fetchSelected(null, 'uit_id');

        if ($uit_ids) {
            (new OrmRequest())
                ->delete()
                ->from(OrderItemUIT::_getTable())
                ->whereIn('id', $uit_ids)
                ->exec();
        }

        (new OrmRequest())
            ->delete()
            ->from(ShipmentItem::_getTable())
            ->where(['shipment_id' => $this['id']])
            ->exec();

        return parent::delete();
    }

    /**
     * Вызывается перед сохранением объекта в storage
     * Если возвращено false, то сохранение не произойдет
     *
     * @param string $save_flag - тип операции (insert|update|replace)
     * @return null|false
     */
    public function beforeWrite($save_flag)
    {
        if ($save_flag == $this::INSERT_FLAG) {
            $this['date'] = date('Y-m-d H:i:s');
        }

        return null;
    }

    /**
     * Вызывается после сохранения объекта в storage
     *
     * @param string $save_flag - тип операции (insert|update|replace)
     * @return void
     */
    public function afterWrite($save_flag)
    {
        if ($save_flag == $this::INSERT_FLAG) {
            (new OrmRequest())
                ->update(ShipmentItem::_getTable())
                ->set(['shipment_id' => $this['id']])
                ->where(['shipment_id' => $this['temp_id']])
                ->exec();
        }
    }

    /**
     * Возвращает связанный заказ
     *
     * @return Order
     */
    public function getOrder()
    {
        return new Order($this['order_id']);
    }

    /**
     * Массовая загрузка OrderItem в объекты ShipmentItem
     *
     * @return void
     */
    public function fillShipmentItems()
    {
        if ($this->getShipmentItems()) {
            $order_item_uniqs = [];
            //Загружаем только те orderItems, которые есть в отгрузке

            foreach ($this->getShipmentItems() as $shipment_item) {
                $order_item_uniqs[] = $shipment_item['order_item_uniq'];
            }

            /** @var OrderItem[] $order_items */
            $order_items = (new OrmRequest())
                ->from(new OrderItem())
                ->where([
                    'order_id' => $this['order_id'],
                ])
                ->whereIn('uniq', $order_item_uniqs)
                ->objects(null, 'uniq');

            $product_ids = [];
            foreach ($order_items as $order_item) {
                if ($order_item['type'] == OrderItem::TYPE_PRODUCT) {
                    $product_ids[] = $order_item['entity_id'];
                }
            }

            if ($product_ids) {
                /** @var Product[] $products */
                $products = (new OrmRequest())
                    ->from(new Product())
                    ->whereIn('id', $product_ids)
                    ->objects(null, 'id');
            } else {
                $products = [];
            }

            foreach ($this->getShipmentItems() as $shipment_item) {
                if (isset($order_items[$shipment_item['order_item_uniq']])) {
                    $order_item = $order_items[$shipment_item['order_item_uniq']];
                } else {
                    $order_item = new OrderItem();
                    $order_item['title'] = t('Товар удален из заказа');
                }
                if ($order_item['type'] == OrderItem::TYPE_PRODUCT) {
                    $order_item->setEntity($products[$order_item['entity_id']]);
                }
                $shipment_item->setOrderItem($order_item);
            }
        }

    }

    /**
     * Возвращает список отгруженных товаров
     *
     * @return ShipmentItem[]
     */
    public function getShipmentItems()
    {
        if ($this->shipment_items === null) {
            $this->shipment_items = (new OrmRequest())
                ->from(new ShipmentItem())
                ->where([
                    'shipment_id' => $this['id'],
                ])
                ->objects();
        }
        return $this->shipment_items;
    }

    /**
     * Устанавливает временный id
     */
    public function setTempId()
    {
        $this['temp_id'] = -rand();
    }

    /**
     * Возвращает базовую валюту, используется в шаблоне
     *
     * @return Currency
     */
    public function getBaseCurrency()
    {
        return CurrencyApi::getBaseCurrency();
    }
}
