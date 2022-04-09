<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model;

use RS\Db\Adapter as DbAdapter;
use RS\Module\AbstractModel\EntityList;
use RS\Orm\Request as OrmRequest;
use Shop\Model\Marking\MarkingException;
use Shop\Model\Orm\OrderItem;
use Shop\Model\Orm\OrderItemUIT;
use Shop\Model\Orm\Shipment;
use Shop\Model\Orm\ShipmentItem;

/**
 * API функции для работы с отгрузками
 */
class ShipmentApi extends EntityList
{
    function __construct()
    {
        parent::__construct(new Shipment());
    }

    /**
     * Возвращает уже имеющиеся uit'ы в базе
     *
     * @param array $uits_post_data Данные от формы Отгрузки в администратиной панели
     * @return array
     */
    public function getExistsUits($uits_post_data)
    {

        $uit_ids = [];
        foreach ($uits_post_data as $item_uit_list) {
            foreach ($item_uit_list as $uit_key => $uit_item) {
                $uit_ids[] = DbAdapter::escape($uit_key);
            }
        }

        $exist_uits = (new OrmRequest())
            ->select('concat(U.gtin, U.serial) uit_id')
            ->from(new OrderItemUIT(), 'U')
            ->join(ShipmentItem::_getTable(), 'U.id = S.uit_id', 'S')
            ->where('concat(U.gtin, U.serial) in ("#0")', [implode('","', $uit_ids)])
            ->exec()->fetchSelected(null, 'uit_id');

        return $exist_uits;
    }

    /**
     * Обновляет Uits в базе данных
     *
     * @param array $product_items результат $order->getCart()->getProductItems()
     * @param array $uits_post_data Данные от формы Отгрузки в администратиной панели
     * @throws MarkingException
     */
    public function saveUits($product_items, $uits_post_data)
    {
        foreach ($product_items as $uniq => $item) {
            /** @var OrderItem $order_item */
            $order_item = $item[Cart::CART_ITEM_KEY];
            try {
                $order_item->rewriteUITs($uits_post_data[$uniq] ?? []);
            } catch (MarkingException $e) {
                throw $e;
            }
        }
    }
}
