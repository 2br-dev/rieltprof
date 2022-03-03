<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Catalog\Model\Inventory;
use Catalog\Model\InventoryManager;
use Catalog\Model\Orm\Inventory\Document;

/**
 * Класс содержит API функции для работы со статистикой складского учета
 */
class StatisticsApi extends \RS\Module\AbstractModel\BaseModel
{
    /**
     *  Получить статистику движения комплектации по складам
     *
     * @param $product_id
     * @param $offer_id
     * @param $warehouse_id
     * @return bool|array
     */
    function getOfferStatistics($product_id, $offer_id, $warehouse_id)
    {
        $api = new \Catalog\Model\Inventory\DocumentApi();
        $inventory_manager = new \Catalog\Model\InventoryManager();

        $documents = $api->getDocumentsByProduct($product_id, $offer_id, $warehouse_id);
        $archived_documents = $api->getDocumentsByProduct($product_id, $offer_id, $warehouse_id, new \Catalog\Model\Orm\Inventory\DocumentProductsArchive());

        if($documents || $archived_documents){
            $archived_num = 0;

            if($archived_documents){
                $start_nums = $inventory_manager->getStartNums($product_id, $offer_id, $warehouse_id);
                $archived_num = $start_nums['stock'];
            }

            $offer_amounts = $inventory_manager->getAmountByDocuments($product_id, $offer_id, $warehouse_id);
            $num = $inventory_manager->getNum($offer_amounts);
            $offer = new \Catalog\Model\Orm\Offer($offer_id);

            return [
                'offer' => $offer,
                'docs' => array_reverse($documents),
                'archived_docs' => $archived_documents,
                'num' => $num,
                'archived_num' => $archived_num,
            ];
        }
        return false;
    }

    function getPaginator($total, $doc_amount, $product_info, $page)
    {
        return new \RS\Helper\Paginator($page, $total, $doc_amount, 'main.admin',
            [
                'mod_controller' => 'catalog-inventorystatisticsctrl',
                'do' => 'GetStatisticTable',
                'product_id' => $product_info['product_id'],
                'offer_id' => $product_info['offer_id'],
                'warehouse_id' => $product_info['warehouse_id'],
            ]
        );
    }
}