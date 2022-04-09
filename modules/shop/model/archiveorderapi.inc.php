<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model;

use RS\Db\Adapter as DbAdapter;
use RS\Db\Exception as DbException;
use RS\Exception as RSException;
use RS\Module\AbstractModel\EntityList;
use RS\Orm\Request as OrmRequest;
use RS\Orm\Type;
use Shop\Model\Orm\ArchiveOrder;
use Shop\Model\Orm\ArchiveOrderItem;
use Shop\Model\Orm\Order;
use Shop\Model\Orm\OrderItem;

class ArchiveOrderApi extends EntityList
{
    const MOVE_STEP_COUNT = 100;

    public function __construct()
    {
        parent::__construct(new ArchiveOrder(), [
            'multisite' => true,
            'aliasField' => 'order_num',
            'defaultOrder' => 'id DESC',
        ]);
    }

    /**
     * Перемещает заказы в архив
     *
     * @param int[] $order_ids - идентификаторы заказов
     * @throws DbException
     * @throws RSException
     */
    public function moveToArchive($order_ids)
    {
        $fields = [];
        foreach ((new ArchiveOrder())->getProperties() as $property) {
            /** @var Type\AbstractType $property */
            if (!$property->isRuntime()) {
                $fields[] = $property->getName();
            }
        }

        $sql = 'INSERT INTO ' . ArchiveOrder::_getTable() . ' (' . implode(',', $fields) . ')';
        $sql .= ' SELECT ' . implode(',', $fields);
        $sql .= ' FROM ' . Order::_getTable(false);
        $sql .= ' WHERE id IN (' . implode(',', $order_ids) . ')';
        DbAdapter::sqlExec($sql);


        $fields = [];
        foreach ((new ArchiveOrderItem())->getProperties() as $property) {
            /** @var Type\AbstractType $property */
            if (!$property->isRuntime()) {
                $fields[] = $property->getName();
            }
        }

        $sql = 'INSERT INTO ' . ArchiveOrderItem::_getTable(false) . ' (' . implode(',', $fields) . ')';
        $sql .= ' SELECT ' . implode(',', $fields);
        $sql .= ' FROM ' . OrderItem::_getTable(false);
        $sql .= ' WHERE order_id IN (' . implode(',', $order_ids) . ')';
        DbAdapter::sqlExec($sql);


        (new OrmRequest())
            ->delete()
            ->from(Order::_getTable())
            ->whereIn('id', $order_ids)
            ->exec();

        (new OrmRequest())
            ->delete()
            ->from(OrderItem::_getTable())
            ->whereIn('order_id', $order_ids)
            ->exec();
    }

    /**
     * Перемещает заказы из архива
     *
     * @param int[] $order_ids - идентификаторы заказов
     * @throws DbException
     * @throws RSException
     */
    public function moveFromArchive($order_ids)
    {
        $fields = [];
        foreach ((new ArchiveOrder())->getProperties() as $property) {
            /** @var Type\AbstractType $property */
            if (!$property->isRuntime()) {
                $fields[] = $property->getName();
            }
        }

        $sql = 'INSERT INTO ' . Order::_getTable(false) . ' (' . implode(',', $fields) . ')';
        $sql .= ' SELECT ' . implode(',', $fields);
        $sql .= ' FROM ' . ArchiveOrder::_getTable(false);
        $sql .= ' WHERE id IN (' . implode(',', $order_ids) . ')';
        DbAdapter::sqlExec($sql);


        $fields = [];
        foreach ((new ArchiveOrderItem())->getProperties() as $property) {
            /** @var Type\AbstractType $property */
            if (!$property->isRuntime()) {
                $fields[] = $property->getName();
            }
        }

        $sql = 'INSERT INTO ' . OrderItem::_getTable(false) . ' (' . implode(',', $fields) . ')';
        $sql .= ' SELECT ' . implode(',', $fields);
        $sql .= ' FROM ' . ArchiveOrderItem::_getTable(false);
        $sql .= ' WHERE order_id IN (' . implode(',', $order_ids) . ')';
        DbAdapter::sqlExec($sql);


        (new OrmRequest())
            ->delete()
            ->from(ArchiveOrder::_getTable())
            ->whereIn('id', $order_ids)
            ->exec();

        (new OrmRequest())
            ->delete()
            ->from(ArchiveOrderItem::_getTable())
            ->whereIn('order_id', $order_ids)
            ->exec();
    }

    /**
     * Возвращает заказы вместе с заказами из архива
     *
     * @param integer $page Номер страницы, начиная с 1
     * @param integer $page_size, количесто элментов на страницу
     * @return array
     * @throws DbException
     */
    public function getListWithArchive($page = null, $page_size = null)
    {
        $query_object = $this->queryObj();

        $fields = [];
        foreach ((new ArchiveOrder())->getProperties() as $property) {
            /** @var Type\AbstractType $property */
            if (!$property->isRuntime()) {
                $fields[] = $property->getName();
            }
        }

        $query_normal = 'SELECT ' . implode(',', $fields) . ',"0" as is_archive FROM ' . Order::_getTable();
        $query_archive = 'SELECT ' . implode(',', $fields) . ',"1" as is_archive FROM ' . ArchiveOrder::_getTable();

        if ($query_object->where) {
            $where = str_replace('`A`.', '', $query_object->where);
            $query_normal .= " WHERE $where";
            $query_archive .= " WHERE $where";
        }

        $query = "$query_normal UNION $query_archive ORDER BY ";
        $query .= ($query_object->orderby) ? $query_object->orderby : 'dateof desc';

        if ($page_size) {
            $offset = ($page - 1) * $page_size;
            $query .= " LIMIT $offset, $page_size";
        }

        $db_result = DbAdapter::sqlExec($query);

        $result = [];
        while ($row = $db_result->fetchRow()) {
            if ($row['is_archive']) {
                $object = new ArchiveOrder();
            } else {
                $object = new Order();
            }
            $object->getFromArray($row, null, false, true);
            $result[] = $object;
        }

        return $result;
    }
}
