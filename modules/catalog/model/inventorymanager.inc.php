<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Catalog\Model;
use \Catalog\Model\Inventory\DocumentApi;
use Catalog\Model\Orm\Inventory\Document;
use \Catalog\Model\Orm\Inventory\StartNum;
use \Catalog\Model\Orm\Inventory\DocumentProductsArchive;

/**
 *  Класс с функциями складского учета
 *
 * Class InventoryManager
 * @package Catalog\Model
 */
class InventoryManager implements \Catalog\Model\StockInterface
{
    /**
     *  Обновить остатки товаров
     *
     * @param array $items - товары документа
     * @param integer $warehouse_id - id склада
     * @param null|array $old_items - старые товары
     * @param null|integer $old_warehouse  - старый склад
     * @return void
     */
    function updateStocks($items, $warehouse_id, $old_items = null, $old_warehouse = null)
    {
        // если сменился склад, обновить остатки на старом складе
        if ($old_warehouse && $old_warehouse != $warehouse_id){
            $this->revertItems($old_items, $old_warehouse);
        }
        // обновить остатки товаров, входящих в документ
        foreach ($items as $uniq => $item){
            if($old_items && isset($old_items[$uniq])){
                unset($old_items[$uniq]);
            }
            $this->recalculateStocks($item, $warehouse_id);
        }

        // обновить остатки у товаров, удаленных из документа
        if (isset($old_items) && count($old_items)){
            $this->revertItems($old_items, $warehouse_id);
        }
    }

    /**
     *  Пересчитать показатели количества товаров
     *
     * @param array $items - массив с объектами товаров документа
     * $items = [
     *     'uniq' => (\Catalog\Model\Orm\DocumentProduct) - объект товара документа
     * ]
     * @param $warehouse_id - id склада
     */
    function revertItems($items, $warehouse_id)
    {
        foreach ($items as $uniq => $item){
            $this->recalculateStocks($item, $warehouse_id);
        }
    }

    /**
     *  Перезаписывает остатки
     *
     * @param array $item - массив с информацией об объекте товара в документе
     *    $item = [
     *      'product_id' => (integer) id товара.
     *      'offer_id'   => (integer) id комплектации.
     *      'amount'     => (integer) количество.
     *    ]
     * @param integer $warehouse_id - id склада
     * @return void
     */
    function recalculateStocks($item, $warehouse_id)
    {
        if($item['offer_id'] == 0){
            $offer = \Catalog\Model\Orm\Offer::loadByWhere([
                'product_id' => $item['product_id'],
                'site_id' => \RS\Site\Manager::getSiteId(),
                'sortn' => 0]);
            $item['offer_id'] = $offer['id'];
        }

        $this->recalculateStartNum($item, $warehouse_id);
        $amounts = $this->getAmountByDocuments($item['product_id'], $item['offer_id'], $warehouse_id);

        $num = $this->getNum($amounts);
        $x_stock = \Catalog\Model\Orm\Xstock::loadByWhere([
            'warehouse_id' => $warehouse_id,
            'product_id' => $item['product_id'],
            'offer_id' => $item['offer_id'],
        ]);
        $x_stock['stock']        = $num;
        $x_stock['reserve']      = $amounts['reserve_sum'];
        $x_stock['waiting']      = $amounts['waiting_sum'];
        $x_stock['remains']      = $amounts['remains_sum'];
        $x_stock['warehouse_id'] = $warehouse_id;
        $x_stock['product_id']   = $item['product_id'];
        $x_stock['offer_id']     = $item['offer_id'];

        $x_stock->replace();
        $offer_num = $this->getStockSum($item['product_id'], $item['offer_id']);

        \Rs\Orm\Request::make()
            ->update(new \Catalog\Model\Orm\Offer())
            ->set([
                'num'     => $offer_num[0]['num'],
                'reserve' => $amounts['reserve_sum'],
                'waiting' => $amounts['waiting_sum'],
                'remains' => $amounts['remains_sum'],
            ])
            ->where([
                'product_id' => $item['product_id'],
                'id'         => $item['offer_id'],
            ])
            ->exec();

        $product_stock = $this->getStockSum($item['product_id']);

        \Rs\Orm\Request::make()
            ->update(new \Catalog\Model\Orm\Product())
            ->set([
                'num'     => $product_stock[0]['num'],
                'reserve' => $product_stock[0]['reserve_sum'],
                'waiting' => $product_stock[0]['waiting_sum'],
                'remains' => $product_stock[0]['remains_sum'],
            ])
            ->where([
                'id' => $item['product_id'],
            ])
            ->exec();
    }

    /**
     *  Пересчитать количество товара в архиве
     *
     * @param $item
     * @param $warehouse_id
     */
    function recalculateStartNum($item, $warehouse_id)
    {
        $amounts_arr = $this->getAmountByDocuments($item['product_id'], $item['offer_id'], $warehouse_id, new DocumentProductsArchive(), false);
        $num = $this->getNum($amounts_arr);
        if($num == 0){
            \RS\Orm\Request::make()
                ->delete()
                ->from(new StartNum())
                ->where([
                    'product_id' => $item['product_id'],
                    'offer_id' => $item['offer_id'],
                    'warehouse_id' => $warehouse_id,
                ])
                ->exec();
        }else{
            $start_num = new StartNum();
            $start_num['product_id'] = $item['product_id'];
            $start_num['offer_id'] = $item['offer_id'];
            $start_num['warehouse_id'] = $warehouse_id;
            $start_num['stock'] = $num;
            $start_num['remains'] = $amounts_arr['remains_sum'];
            $start_num['reserve'] = $amounts_arr['reserve_sum'];
            $start_num['waiting'] = $amounts_arr['waiting_sum'];
            $start_num->replace();
        }
    }

    /**
     *  Создает документы после записи заказа
     *
     * @param \Shop\Model\Orm\Order $order - объект заказа
     * @param string $flag - флаг сохранения (update или insert)
     * @param null|integer $old_warehouse - предыдущий склад заказа
     * @return bool
     */
    function updateRemainsFromOrder(\Shop\Model\Orm\Order $order, $flag, $old_warehouse = null)
    {
        $cancelled_status = \Shop\Model\UserStatusApi::getStatusesIdByType( \Shop\Model\Orm\UserStatus::STATUS_CANCELLED );
        $success_status   = \Shop\Model\UserStatusApi::getStatusesIdByType( \Shop\Model\Orm\UserStatus::STATUS_SUCCESS );

        $cancelled        = in_array($order['status'], $cancelled_status);
        $applied = $cancelled ? 0 : 1;

        $finished_order = in_array($order['status'], $success_status);

        $manager = new \Catalog\Model\DocumentLinkManager();

        $document_api = new DocumentApi();
        $items = $document_api->prepareItemsFromOrder($order);

        if(!$items){
            return false;
        }
        $document_type = $finished_order ? Document::DOCUMENT_TYPE_WRITE_OFF : Document::DOCUMENT_TYPE_RESERVE;
        $document = $document_api->saveDocumentFromOrder($order, $items, $applied, $document_type);

        $order['type'] = \Shop\Model\Orm\Order::DOCUMENT_TYPE_ORDER;
        $manager->deleteLinksByDocument($order);
        $manager->createLinkOrder($document, $order);

        /*
        if($finished_order && $links){
            $tools = new \Catalog\Model\Inventory\InventoryTools();
            $tools->changeDocumentType($links[0]['document_id'], Document::DOCUMENT_TYPE_RESERVE, Document::DOCUMENT_TYPE_WRITE_OFF);
        }*/
        return true;
    }

    /**
     *  Расчитывает доступное количество товара из заданной формулы
     *
     * @param array $amounts - массив со значениями: резервирования, ожидания, остатки.
     * @return integer
     */
    function getNum($amounts)
    {
        return $amounts['remains_sum'] + $amounts['waiting_sum'] - $amounts['reserve_sum'];
    }


    /**
     *  Получить количетво комплектации товара исходя из документов
     *
     * @param integer $product_id - id товара
     * @param integer $offer_id - id комплектации
     * @param integer $warehouse_id - id склада
     * @return array
     */
    function getAmountByDocuments($product_id, $offer_id, $warehouse_id, $products_orm = null, $add_start_num = true)
    {
        if(!$products_orm){
            $products_orm = new \Catalog\Model\Orm\Inventory\DocumentProducts();
        }
        \RS\Helper\Debug\Time::setTimePoint();
        $result = \RS\Orm\Request::make()
            ->select('item.amount, doc.type')
            ->from(new \Catalog\Model\Orm\Inventory\Document(), 'doc')
            ->leftjoin($products_orm, 'doc.id = item.document_id', 'item')
            ->where([
                'item.product_id' => $product_id,
                'item.offer_id'  => $offer_id,
                'doc.site_id'    => \RS\Site\Manager::getSiteId(),
                'doc.warehouse'  => $warehouse_id,
                'doc.applied'    => 1,
            ])
            ->whereIn('doc.type', [
                Document::DOCUMENT_TYPE_WAITING,
                Document::DOCUMENT_TYPE_RESERVE,
                Document::DOCUMENT_TYPE_ARRIVAL,
                Document::DOCUMENT_TYPE_WRITE_OFF,
            ])
            ->exec()
            ->fetchSelected(null);
        $remains_sum = 0;
        $waiting_sum = 0;
        $reserve_sum = 0;

        foreach ($result as $row){
            if($row['type'] == Document::DOCUMENT_TYPE_ARRIVAL || $row['type'] == Document::DOCUMENT_TYPE_WRITE_OFF){
                $remains_sum += $row['amount'];
            }elseif ($row['type'] == Document::DOCUMENT_TYPE_WAITING){
                $waiting_sum += $row['amount'];
            }elseif ($row['type'] == Document::DOCUMENT_TYPE_RESERVE){
                $reserve_sum += $row['amount'];
            }
        }

        if($add_start_num){
            $start_num = $this->getStartNums($product_id, $offer_id, $warehouse_id);
            if($start_num){
                $reserve_sum += $start_num['reserve'];
                $waiting_sum += $start_num['waiting'];
                $remains_sum += $start_num['remains'];
            }
        }

        return [
            'remains_sum' => $remains_sum,
            'waiting_sum' => $waiting_sum,
            'reserve_sum' => $reserve_sum,
        ];
    }

    /**
     *  Получить остатки товара в архиве
     *
     * @param integer $product_id - id товара
     * @param integer $offer_id - id комплектации
     * @param integer $warehouse_id - id склада
     * @return bool|array
     */
    function getStartNums($product_id, $offer_id = null, $warehouse_id = null)
    {
        $q = \RS\Orm\Request::make()
            ->from(new \Catalog\Model\Orm\Inventory\StartNum())
            ->where([
                'product_id' => $product_id,
            ]);
        if($offer_id){
            $q->where(['offer_id' => $offer_id,]);
        }
        if($warehouse_id){
            $q->where(['warehouse_id' => $warehouse_id,]);
        }
        if($offer_id && $warehouse_id){
            return $q->object();
        }else{
            return $q->objects();
        }
    }

    /**
     *  Получить сумму остатков товара или комплектации из таблицы x_stock
     *
     * @param integer $product_id - id товара
     * @param null|integer $offer_id - id комплектации, если не указано, будут учтены все комплектации
     * @return array
     */
    function getStockSum($product_id, $offer_id = null)
    {
        $product_stock = \RS\Orm\Request::make()
            ->select('sum(stock) as num, sum(waiting) as waiting_sum, sum(reserve) as reserve_sum, sum(remains) as remains_sum')
            ->from(new \Catalog\Model\Orm\Xstock())
            ->where([
                'product_id' => $product_id,
            ]);
        if($offer_id){
            $product_stock ->where([
                'offer_id'   => $offer_id,
            ]);
        }
        return $product_stock
            ->exec()
            ->fetchAll();
    }
}