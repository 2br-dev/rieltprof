<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\Orm\Cargo;

use RS\Orm\OrmObject;
use RS\Orm\Request;
use RS\Orm\Type;
use Shop\Model\Orm\OrderItem;
use Shop\Model\Orm\OrderItemUIT;

/**
 * Товар в грузоместе
 */
class OrderCargoItem extends OrmObject
{
    protected static $table = 'order_cargo_item';

    function _init()
    {
        parent::_init()->append([
            'order_id' => (new Type\Integer)
                ->setDescription(t('ID заказа')),
            'order_cargo_id' => (new Type\Integer)
                ->setDescription(t('ID грузоместа в заказе'))
                ->setIndex(true),
            'order_item_uniq' => (new Type\Varchar)
                ->setMaxLength(50)
                ->setDescription(t('Товарная позиция')),
            'order_item_uit_id' => (new Type\Integer)
                ->setDescription(t('Идентификатор маркировки'))
                ->setAllowEmpty(false),
            'amount' => (new Type\Decimal())
                ->setDescription(t('Количество'))
                ->setMaxLength(11)
                ->setDecimal(3)
        ]);

        $this->addIndex(['order_cargo_id', 'order_item_uniq', 'order_item_uit_id'], self::INDEX_UNIQUE);
    }

    /**
     * Возвращает сязанный объект OrderItem
     * @return OrderItem | bool(false)
     */
    function getOrderItem()
    {
        return Request::make()
            ->from(new OrderItem())
            ->where([
                'uniq' => $this['order_item_uniq'],
                'order_id' => $this['order_id']
            ])->object();
    }

    /**
     * Возращает объект одной отсканированной маркировки
     *
     * @return OrderItemUIT
     */
    function getUit()
    {
        return new OrderItemUIT($this['order_item_uit_id']);
    }
}