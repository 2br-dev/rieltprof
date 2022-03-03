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
use \Catalog\Model\Orm\Inventory\Inventorization;
use Catalog\Model\Orm\Offer;
use Catalog\Model\Orm\Product;
use Catalog\Model\WareHouseApi;
use RS\Module\AbstractModel\EntityList;

/**
 * Класс содержит API функции для работы с документом инвентаризации
 */
class InventorizationApi extends EntityList
{
    private
        $counter,
        $items,
        $inventarization;

    protected $items_cache = [];

    function __construct()
    {
        parent::__construct(new \Catalog\Model\Orm\Inventory\Inventorization());
    }

    /**
     *  Получить форму документа для печати
     *
     * @param $document_id
     * @return string
     */
    function getDocumentPrintForm($document_id, $document_type)
    {
        $document = new Inventorization($document_id);
        $document['items'] = $this->getProductsByDocumentId($document_id);
        $warehouse = new \Catalog\Model\Orm\WareHouse($document['warehouse']);
        $title = t("Инвентаризация");
        $params = [
            'title' => $title,
            'document' => $document,
            'warehouse' => $warehouse,
            'is_inventorization' => true,
        ];
        $pdf_gen = new \RS\Helper\Pdf\PDFGenerator();
        $pdf_gen->set_option('enable_html5_parser', TRUE);
        $form = $pdf_gen->renderTemplate('%catalog%/inventory/document_print_form.tpl', $params);
        return $form;
    }

    /**
     *  Получить одинаковые товары в документе, если есть
     *
     * @param $products
     * @return array|bool
     */
    function getProductDoubles($products)
    {
        $in_array = [];
        $doubles = [];
        foreach ($products as $uniq => $product){
            $exist = false;
            if(isset($in_array[$product['product_id']])){
                if(isset($in_array[$product['product_id']][$product['offer_id']])){
                    $exist = true;
                    $doubles[$product['offer_id']] = $product;
                }
            }
            if(!$exist){
                $in_array[$product['product_id']][$product['offer_id']] = $product;
            }
        }
        if (!$doubles){
            return false;
        } else {
            return $doubles;
        }
    }

    /**
     *  Подготоваить товары, пришедшие из POST, для записи
     *
     * @param $items
     * @param bool $warehouse_id
     * @param bool $recalculate
     * @return array
     */
    function prepareProductsArray($items, $warehouse_id = false, $recalculate = false)
    {
        $array = [];

        foreach ($items as $uniq => $item){
            $product = new \Catalog\Model\Orm\Product($item['product_id']);
            $arr = [
                'title' => $product->title,
                'amount' => $item['amount'],
                'uniq' => $uniq,
                'product_id' => $item['product_id'],
            ];
            if(isset($item['offer_id'])){
                $arr['offer_id'] = $item['offer_id'];
            }else if ($warehouse_id){
                $offer = \Catalog\Model\Orm\Offer::loadByWhere([
                    'product_id' => $item['product_id'],
                    'sortn' => 0,
                ]);
                $arr['offer_id'] = $offer['id'];
            }
            if($warehouse_id) {
                if (!isset($item['item_id']) || $recalculate) {
                    $x_stock = \RS\Orm\Request::make()
                        ->select()
                        ->from(new \Catalog\Model\Orm\Xstock())
                        ->where([
                            'product_id' => $arr['product_id'],
                            'offer_id' => $arr['offer_id'],
                            'warehouse_id' => $warehouse_id,
                        ])
                        ->object();
                    $arr['calc_amount'] = $x_stock['stock'] ?? 0;
                    $arr['fact_amount'] = $item['fact_amount'];
                }else{
                    $arr['calc_amount'] = $item['calc_amount'];
                    $arr['fact_amount'] = $item['fact_amount'];
                }
            }
            $array[] = $arr;
        }

        return $array;
    }

    /**
     *  Сохранить товары
     *
     * @param $products
     * @param $document_id
     * @param $document
     * @return bool
     */
    function saveProducts($products, $document_id, $document = null)
    {
        foreach ($products as $uniq => $product){
            $orm = new \Catalog\Model\Orm\inventory\InventorizationProducts();
            $orm['document_id'] = $document_id;
            if(is_object($product)){
                $product = $product->getValues();
                $product['amount'] = $product['dif_amount'];
            }
            $orm->getFromArray($product);
            $orm['dif_amount'] = $product['amount'];
            $product = new \Catalog\Model\Orm\Product($product['product_id']);
            $orm['title'] = $product->title;
            $orm['uniq'] = $uniq;
            $orm->insert();
        }
        return true;
    }

    /**
     * Обновляет связанный докумсент
     *
     * @param $document
     * @param $items
     * @param $links
     */
    function updateLinkedDocuments($document, $items, $links)
    {
        $write_off_items = [];
        $arrival_items = [];
        foreach ($items as $uniq => $item){
            if($item['amount'] < 0){
                $write_off_items[$uniq] = $item;
                $write_off_items[$uniq]['amount'] = -$item['amount'];
            }else{
                $arrival_items[$uniq] = $item;
            }
        }
        $api = new DocumentApi();
        $link_arrival = null;
        $link_write_off = null;
        foreach ($links as $link){
            if($link['document_type'] == Document::DOCUMENT_TYPE_WRITE_OFF){
                $link_write_off = $link;
            }elseif($link['document_type'] == Document::DOCUMENT_TYPE_ARRIVAL){
                $link_arrival = $link;
            }
        }
        if($arrival_items) {
            $doc['type'] = Document::DOCUMENT_TYPE_ARRIVAL;
            if ($link_arrival) {
                $document_arrival = $api->updateDocument($arrival_items, $document['warehouse'], $document['date'], $link_arrival['document_id'], $document['applied'], Document::DOCUMENT_TYPE_ARRIVAL);
            } else {
                $document_arrival = $api->createDocument($arrival_items, Document::DOCUMENT_TYPE_ARRIVAL, $document['warehouse'], $document['date'], $document['applied']);
                $api->createDocumentLinks($document['id'], \Catalog\Model\Orm\Inventory\Inventorization::DOCUMENT_TYPE_INVENTORY, $document_arrival['id'], Document::DOCUMENT_TYPE_ARRIVAL);
            }
        }
        if($write_off_items) {
            $doc['type'] = Document::DOCUMENT_TYPE_WRITE_OFF;
            if ($link_write_off) {
                $document_writeoff = $api->updateDocument($write_off_items, $document['warehouse'], $document['date'],  $link_write_off['document_id'], $document['applied'], Document::DOCUMENT_TYPE_WRITE_OFF);
            } else {
                $document_writeoff = $api->createDocument($write_off_items, Document::DOCUMENT_TYPE_WRITE_OFF, $document['warehouse'], $document['date'], $document['applied']);
                $api->createDocumentLinks($document['id'], \Catalog\Model\Orm\Inventory\Inventorization::DOCUMENT_TYPE_INVENTORY, $document_writeoff['id'], Document::DOCUMENT_TYPE_WRITE_OFF);
            }
        }
        if($link_arrival && !$arrival_items){
            $api->deleteDocument($link_arrival['document_id']);
        }
        if($link_write_off && !$write_off_items){
            $api->deleteDocument($link_write_off['document_id']);
        }
    }

    /**
     *  Получить товары нужного документа
     *
     * @param $document_id
     * @return array
     */
    function getProductsByDocumentId($document_id)
    {
        if(!isset($this->items_cache[$document_id])) {
            $this->items_cache = [ //Кэшируем только один документ
                $document_id => \RS\Orm\Request::make()
                                    ->select()
                                    ->from(new \Catalog\Model\Orm\inventory\InventorizationProducts())
                                    ->where(['document_id' => $document_id])
                                    ->objects(null, 'uniq')
            ];
        }
        return $this->items_cache[$document_id];
    }

    /**
     *  Получить html с таблицей товаров для вставки в форму документа
     *
     * @param $document_id
     * @param bool $is_inventory
     * @return string
     */
    function getProductsTable ($document_id, $is_inventory = false, $items = null, $archived = false, $disable_edit = false)
    {
        if($items){
            foreach ($items as $uniq => $item){
                $items[$uniq]['fact_amount'] = $item['amount'];
            }
            $products = $items;
        }else{
            $products = $this->getProductsByDocumentId($document_id);
        }
        $smarty = new \RS\View\Engine();

        $smarty->assign([
            'products' => $products,
            'api' => $this,
            'is_inventory' => $is_inventory,
            'disable_edit' => $disable_edit,
        ]);
        return $smarty->fetch("%catalog%/form/inventory/products_in_table.tpl");
    }

    /**
     *  Получить html с элементами input type="hidden" для вставки в форму документа
     *
     * @param $document_id
     * @param bool $is_inventory
     * @return string
     */
    function getAddedItems ($document_id, $is_inventory = false, $items = false)
    {
        if($items){
            $products = $items;
        }else{
            $products = $this->getProductsByDocumentId($document_id);
        }
        $products = array_reverse($products);
        $smarty = new \RS\View\Engine();
        $smarty->assign([
            'items' => $products,
            'is_inventory' => $is_inventory,
        ]);
        return $smarty->fetch("%catalog%/form/inventory/hidden_inputs.tpl");
    }

    /**
     *  Получить объект товара
     *
     * @param $product_id
     * @return Product
     */
    function getProduct($product_id)
    {
        return new Product($product_id);
    }

    /**
     *  Получить комплектации товара
     *
     * @param $product_id
     * @return array
     */
    function getProductOffers($product_id)
    {
        $product = new Product($product_id);
        return $product->fillOffers();
    }

    /**
     *  Проводит инвентаризацию при включении складского учета
     *
     * @param integer $current_warehouse - id склада, по которому идет инвентаризация
     * @param integer $offset - стартовая позиция для выборки товаров
     * @param integer $inventory_id - id текущей инвентаризации
     * @return bool|array
     */
    function makeTotalInventorization($current_warehouse, $offset, $inventory_id)
    {
        $warehouses = WareHouseApi::staticSelectList();
        $manager = new InventoryManager();
        $limit = 200;
        $second = false;
        foreach ($warehouses as $warehouse_id => $title){
            //передать id следующего склада
            if($second){
                return [
                    'success' => false,
                    'queryParams' => [
                        'url' => \RS\Router\Manager::obj()->getAdminUrl('EnableControlBySteps'),
                        'data'=> [
                            'ajax' => 1,
                            'offset' =>  0,
                            'warehouse' => $warehouse_id,
                        ]
                    ]
                ];
            }
            if($warehouse_id != $current_warehouse && $current_warehouse != 0){
                continue;
            }
            $this->inventarization = $this->getNewInventorization($warehouse_id);
            $this->items = [];

            if($inventory_id){
                $this->inventarization = new Inventorization($inventory_id);
                $api = new \Catalog\Model\Inventory\InventorizationApi();
                $this->items = $api->getProductsByDocumentId($inventory_id);
            }

            $this->counter = 0;
            $q = \RS\Orm\Request::make()
                ->from(new \Catalog\Model\Orm\Product())
                ->limit($limit);
            while ($products = $q->offset($offset)->objects()){
                foreach ($products as $product){
                    $product->fillOffers();
                    $product->fillOffersStock();
                    $offers = $product['offers']['items'];
                    if(!$offers){
                        //Создаем нулевую комплектацию, если ее не существовало ранее
                        $offer = new Offer();
                        $offer['product_id'] = $product['id'];
                        $offer->insert();
                        $offers = [$offer];
                    }
                    foreach ($offers as $offer){
                        $amounts = $manager->getAmountByDocuments($offer['product_id'], $offer['id'], $warehouse_id);
                        $doc_num = $manager->getNum($amounts);
                        $offer_num = isset($offer['stock_num'][$warehouse_id]) ? $offer['stock_num'][$warehouse_id] : 0;
                        $amount = $offer_num - $doc_num;
                        if($amount == 0){
                            continue;
                        }

                        $uniq = md5(uniqid(rand(), true));
                        $this->items[$uniq] = [
                            'title' => $product['title'],
                            'uniq' => $uniq,
                            'amount' => $amount,
                            'calc_amount' => $doc_num,
                            'fact_amount' => $offer_num,
                            'product_id' => $product['id'],
                            'offer_id' => $offer['id'],
                        ];
                        if(count($this->items) == $limit){
                            //сохранить документ и передать offset на следующий шаг
                            $this->inventarization['items'] = $this->items;
                            if($this->inventarization['id']){
                                $this->inventarization->update();
                            }else{
                                $this->inventarization->insert();
                            }
                            return [
                                'success' => true,
                                'queryParams' => [
                                    'url' => \RS\Router\Manager::obj()->getAdminUrl('EnableControlBySteps'),
                                    'data'=> [
                                        'ajax' => 1,
                                        'offset' => $offset + $this->counter,
                                        'warehouse' => $warehouse_id,
                                    ]
                                ]
                            ];
                        }
                    }
                    $this->counter += 1;
                }
                if(!empty($this->items)){
                    // сохранить документ и если количество товаров не = limit дополнить его в следующем шаге
                    $this->inventarization['items'] = $this->items;

                    if($this->inventarization['id']){
                        $this->inventarization->update();
                    }else{
                        $this->inventarization->insert();
                    }
                    return [
                        'success' => true,
                        'queryParams' => [
                            'url' => \RS\Router\Manager::obj()->getAdminUrl('EnableControlBySteps'),
                            'data'=> [
                                'ajax' => 1,
                                'offset' => $offset + $limit,
                                'warehouse' => $warehouse_id,
                                'inventory' => count($this->items) == $limit ? 0 : $this->inventarization->id,
                            ]
                        ]
                    ];
                }
                $offset += $limit;
            }
            $second = true;
        }
        return true;
    }

    /**
     *  Получить новый объект инвентаризации
     *
     * @param integer $warehouse_id - id склада
     * @return Inventorization
     */
    function getNewInventorization($warehouse_id)
    {
        $inventarization = new Inventorization();
        $inventarization['date'] = date("Y.m.d H:i:s");
        $inventarization['applied'] = 1;
        $inventarization['type'] = Inventorization::DOCUMENT_TYPE_INVENTORY;
        $inventarization['warehouse'] = $warehouse_id;
        return $inventarization;
    }
}
