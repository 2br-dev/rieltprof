<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model;

use Shop\Model\Orm\Order;

interface StockInterface
{
    /**
     *  Обновляет остатки товаров из заказа
     *
     * @param \Shop\Model\Orm\Order $order - объект заказа
     * @param string $flag - флаг сохранения (update или insert)
     * @param int|null $old_warehouse - id предыдущий склад заказа
     * @return void
     */
    function updateRemainsFromOrder(Order $order, $flag, $old_warehouse = null);
}
