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
use Catalog\Model\Orm\Inventory\Movement;
use Catalog\Model\Orm\Product;

/**
 * Класс содержит API функции для работы с документом перемещения
 */
class MovementApi extends \RS\Module\AbstractModel\EntityList
{
    protected $items_cache = [];

    function __construct()
    {
        parent::__construct(new \Catalog\Model\Orm\Inventory\Movement());
    }

    /**
     *  Получить форму документа для печати
     *
     * @param $document_id
     * @return string
     */
    function getDocumentPrintForm($document_id, $document_type)
    {
        $document = new Movement($document_id);
        $document['items'] = $this->getProductsByDocumentId($document_id);
        $warehouse_from = new \Catalog\Model\Orm\WareHouse($document['warehouse_from']);
        $warehouse_to = new \Catalog\Model\Orm\WareHouse($document['warehouse_to']);
        $title = t("Перемещение");
        $params = [
            'title' => $title,
            'document' => $document,
            'warehouse_from' => $warehouse_from['title'],
            'warehouse_to' => $warehouse_to['title'],
            'is_movement' => true,
        ];
        $pdf_gen = new \RS\Helper\Pdf\PDFGenerator();
        $pdf_gen->set_option('enable_html5_parser', TRUE);
        $form = $pdf_gen->renderTemplate('%catalog%/inventory/document_print_form.tpl', $params);
        return $form;
    }

    /**
     *  Подготоваить данные товаров из POST для записи
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
                    $arr['calc_amount'] = $x_stock['stock'];
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
            $orm = new \Catalog\Model\Orm\inventory\MovementProducts();
            $orm['document_id'] = $document_id;
            $orm->getFromArray($product);
            $product = new \Catalog\Model\Orm\Product($product['product_id']);
            $orm['title'] = $product->title;
            $orm['uniq'] = $uniq;
            $orm->insert();
        }
        return true;
    }

    /**
     *  Проверить ошибки перед сохранением документа
     *
     * @param $document
     * @param $products
     * @return array|bool
     */
    function checkSaveErrors($document, $products)
    {
        if($document['warehouse_from'] == $document['warehouse_to']){
            return ['field' => 'warehouse_to', 'text' => t('Начальный и конечный склады не могут совпадать')];
        }
        if(!count($products)){
            return ['field' => 'products', 'text' => t('Не добавлено ни одного товара')];
        }
        $manager = new InventoryManager();
        foreach ($products as $uniq => $product){
            $amounts = $manager->getAmountByDocuments($product['product_id'], $product['offer_id'], $document['warehouse_from']);
            $num = $manager->getNum($amounts);
            if($num - $product['amount'] < 0){
                $product_orm = new \Catalog\Model\Orm\Product($product['product_id']);
                return ['field' => 'products', 'text' => t('Количество товара "%0" после перемещения станет отрицательным', [$product_orm['title']])];
            }
            if($product['offer_id'] == -1){
                return ['field' => 'products', 'text' => t('Не у всех товаров выбрана комплектация')];
            }
        }
        return true;
    }

    /**
     *  Cохранить связанный документ
     *
     * @param $document
     * @param $items
     * @param $flag
     * @return void
     */
    function saveLinkedDocuments($document, $items, $flag)
    {
        $write_off_id = null;
        $waiting_id = null;
        $api = new \Catalog\Model\Inventory\DocumentApi();
        if($flag == \RS\Orm\AbstractObject::UPDATE_FLAG){
            $manager = new \Catalog\Model\DocumentLinkManager();
            $linked_documents = $manager->getLinks($document);
            foreach ($linked_documents as $linked_document){
                if($linked_document['document_type'] == Document::DOCUMENT_TYPE_WRITE_OFF){
                    $write_off_id = $linked_document['document_id'];
                }elseif($linked_document['document_type'] == Document::DOCUMENT_TYPE_WAITING){
                    $waiting_id = $linked_document['document_id'];
                }
            }
        }
        $write_off = new \Catalog\Model\Orm\Inventory\Document($write_off_id);
        $write_off['type'] = Document::DOCUMENT_TYPE_WRITE_OFF;
        $write_off['date'] = $document['date'];
        $write_off['warehouse'] = $document['warehouse_from'];
        $write_off['applied'] = $document['applied'];
        $write_off['items'] = $items;
        $write_off->replace();
        $waiting = new \Catalog\Model\Orm\Inventory\Document($waiting_id);
        $waiting['date'] = $document['date'];
        $waiting['applied'] = $document['applied'];
        $waiting['warehouse'] = $document['warehouse_to'];
        $waiting['items'] = $items;
        $waiting['type'] = Document::DOCUMENT_TYPE_WAITING;
        $waiting->replace();
        if(!$write_off_id){
            $api->createDocumentLinks($document['id'], \Catalog\Model\Orm\Inventory\Movement::DOCUMENT_TYPE_MOVEMENT, $write_off['id'], Document::DOCUMENT_TYPE_WRITE_OFF);
        }
        if(!$waiting_id){
            $api->createDocumentLinks($document['id'], \Catalog\Model\Orm\Inventory\Movement::DOCUMENT_TYPE_MOVEMENT, $waiting['id'], Document::DOCUMENT_TYPE_WAITING);
        }
    }

    /**
     *  Получить товары документа
     *
     * @param $document_id
     * @return array
     */
    function getProductsByDocumentId($document_id)
    {
        if(!$this->items_cache || $this->items_cache['document_id'] != $document_id){
            $this->items_cache['document_id'] = $document_id;
            $this->items_cache = \RS\Orm\Request::make()
                ->select()
                ->from(new \Catalog\Model\Orm\inventory\MovementProducts())
                ->where(['document_id' => $document_id])
                ->objects();
        }
        return $this->items_cache;
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
}
