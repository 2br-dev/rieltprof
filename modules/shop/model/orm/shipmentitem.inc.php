<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model\Orm;

use RS\Orm\AbstractObject;
use RS\Orm\Type;

/**
 * Отгрузки
 * --/--
 * @property integer $order_id id заказа
 * @property integer $shipment_id id отгрузки
 * @property string $order_item_uniq Идентификатор товарной позиции
 * @property float $amount Количество
 * @property integer $uit_id УИТ
 * @property float $cost Сумма
 * --\--
 */
class ShipmentItem extends AbstractObject
{
    protected static $table = 'order_shipment_item';

    protected $order_item;
    protected $uit;

    function _init()
    {
        $this->getPropertyIterator()->append([
            'order_id' => (new Type\Integer())
                ->setDescription(t('id заказа')),
            'shipment_id' => (new Type\Integer())
                ->setDescription(t('id отгрузки')),
            'order_item_uniq' => (new Type\Varchar())
                ->setDescription(t('Идентификатор товарной позиции')),
            'amount' => (new Type\Decimal())
                ->setDescription(t('Количество'))
                ->setDecimal(3),
            'uit_id' => (new Type\Integer())
                ->setDescription(t('УИТ')),
            'cost' => (new Type\Decimal())
                ->setDescription(t('Сумма'))
                ->setMaxLength(15)
                ->setDecimal(2),
        ]);

        $this->addIndex(['order_id', 'shipment_id', 'order_item_uniq', 'uit_id'], self::INDEX_UNIQUE);
        $this->addIndex(['shipment_id'], self::INDEX_KEY);
    }

    /**
     * Возвращает товарную позицию
     *
     * @return mixed
     */
    public function getOrderItem(): ?OrderItem
    {
        if ($this->order_item === null) {
            $this->order_item = OrderItem::loadByWhere([
                'order_id' => $this['order_id'],
                'uniq' => $this['order_item_uniq'],
            ]);
        }
        return $this->order_item;
    }

    /**
     * Устанавливает товарную позицию
     *
     * @param mixed $order_item
     */
    public function setOrderItem(OrderItem $order_item): void
    {
        $this->order_item = $order_item;
    }

    /**
     * Возвращает УИТ
     *
     * @return OrderItemUIT|false
     */
    public function getUit(): ?OrderItemUIT
    {
        if ($this->uit === null) {
            $uit = $this->uit = OrderItemUIT::loadByWhere([
                'id' => $this['uit_id'],
            ]);
            $this->uit = ($uit['id']) ? $uit : false;
        }
        return $this->uit;
    }

    /**
     * Устанавливает УИТ
     *
     * @param mixed $uit
     */
    public function setUit(OrderItemUIT $uit): void
    {
        $this->uit = $uit;
    }
}
