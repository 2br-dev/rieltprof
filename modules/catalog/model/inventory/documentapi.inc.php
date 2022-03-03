<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Catalog\Model\Inventory;

use Catalog\Model\DocumentLinkManager;
use Catalog\Model\Orm\Inventory\Movement;
use Catalog\Model\Orm\Inventory\LinkedDocument;
use Catalog\Model\Orm\Inventory\Inventorization;
use Catalog\Model\Orm\Inventory\DocumentProducts;
use Catalog\Model\Orm\Inventory\DocumentProductsArchive;
use Catalog\Model\Orm\Inventory\Document;
use Catalog\Model\Orm\Offer;
use Catalog\Model\Orm\Product;
use Catalog\Model\Orm\WareHouse;
use Catalog\Model\Orm\Xstock;
use RS\Helper\Pdf\PDFGenerator;
use RS\Module\AbstractModel\EntityList;
use RS\Orm\Request as OrmRequest;
use RS\Site\Manager as SiteManager;
use Shop\Model\Orm\Order;
use Users\Model\Orm\User;

/**
 *  Api с функциями складского учета
 *
 * Class Api
 * @package Catalog\Model\Inventory
 */
class DocumentApi extends EntityList
{
    protected
        $items_cache = [];

    function __construct()
    {
        parent::__construct(new Document());
    }

    /**
     *  Получить названия комплектаций
     *
     * @param $items
     * @return array|bool
     */
    function getOfferTitles($items)
    {
        $ids = [];
        foreach ($items as $uniq => $item) {
            $ids[] = $item['offer_id'];
        }
        if ($ids) {
            $offers = OrmRequest::make()
                ->select('id', 'title', 'sortn')
                ->from(new Offer())
                ->whereIn('id', $ids)
                ->exec()
                ->fetchSelected('id');
            foreach ($offers as $id => $offer) {
                $offers[$id]['title'] = ($offer['sortn'] == 0 && !$offer['title']) ? t('Основная') : $offer['title'];
            }
            return $offers;
        } else {
            return false;
        }
    }

    /**
     *  Получить форму документа для печати
     *
     * @param int $document_id
     * @param string $document_type
     * @return string
     */
    function getDocumentPrintForm($document_id, $document_type)
    {
        $document = new Document($document_id);
        $document['items'] = $this->getProductsByDocumentId($document_id);
        $warehouse = new WareHouse($document['warehouse']);
        $titles = $this->getDocumentTitles();
        $title = $titles[$document_type];
        $provider = $document['provider'] ? new User($document['provider']) : false;
        if ($provider) {
            if ($provider['company']) {
                $company_line = $provider['company'];
                if ($provider['company_inn']) {
                    $company_line .= t(' (ИНН: ') . $provider['company_inn'] . (')');
                }
                $provider = $company_line;
            } else {
                $provider = $provider->getFio();
            }
        }
        $params = [
            'title' => $title,
            'document' => $document,
            'warehouse' => $warehouse,
            'provider' => $provider,
            'is_document' => true,
        ];
        $pdf_gen = new PDFGenerator();
        $pdf_gen->set_option('enable_html5_parser', TRUE);
        $form = $pdf_gen->renderTemplate('%catalog%/inventory/document_print_form.tpl', $params);
        return $form;
    }

    /**
     *  Не позволяем удалить документы, у которых есть связи
     *
     * @param array $ids - массив со списком id объектов
     * @return bool - Возвращает true, если удаление всех элементов прошло успешно, иначе false
     */
    function multiDelete($ids)
    {
        $document = new Document($ids[0]);
        $type = $document['type'];
        $this->load_on_delete = true;
        if ($type == Document::DOCUMENT_TYPE_RESERVE || $type == Document::DOCUMENT_TYPE_ARRIVAL ||
            $type == Document::DOCUMENT_TYPE_WAITING || $type == Document::DOCUMENT_TYPE_WRITE_OFF){
                $link_manager = new DocumentLinkManager();
                $can_delete_ids = [];
                foreach ($ids as $id){
                    $links = $link_manager->getLinks(['id' => $id, 'type' => $type]);
                    if(!$links){
                        $can_delete_ids[] = $id;
                    }
                }
                return $this->del($can_delete_ids);
        }
        return $this->del($ids); //Вызываем стандартный механизм по умолчанию
    }

    /**
     *  Подготавливает массив товаров, приходящий из POST
     *
     * @param array $items - массив items, приходящий из POST запросы формы документа
     * @return array
     */
    function prepareProductsArray($items)
    {
        $array = [];
        foreach ($items as $uniq => $item){
            $product = new Product($item['product_id']);
            $arr = [
                'title'     => $product['title'],
                'amount'    => $item['amount'],
                'uniq'      => $uniq,
                'product_id' => $item['product_id'],
            ];
            if(isset($item['offer_id'])){
                $arr['offer_id'] = $item['offer_id'];
            }
            $array[] = $arr;
        }
        return $array;
    }

    /**
     *  Получить уникальный id для товара
     *
     * @return string
     */
    static function getUniq()
    {
        return md5(uniqid(rand(), true));
    }

    /**
     *  Сохраняет товары документа
     *
     * @param array $products - Массив с товарами
     * @param \Catalog\Model\Orm\Inventory\Document $document - объект документа, которому принадлежат товары
     * @param integer $document_type - тип документа
     * @return boolean
     */
    function saveProducts($products, $document, $document_type)
    {
        foreach ($products as $uniq => $product){
            $orm = new DocumentProducts();
            $orm['document_id'] = $document['id'];
            $orm['title'] = $product['title'];
            $orm['product_id'] = $product['product_id'];
            if($product['offer_id'] == 0){
                $offer = new Offer();
                $offer['site_id'] = \RS\Site\Manager::getSiteId();
                $offer['product_id'] = $product['product_id'];
                $offer['sortn'] = 0;
                $offer->insert();
                $product['offer_id'] = $offer['id'];
            }
            $orm['warehouse'] = $document['warehouse'];
            $orm['offer_id'] = $product['offer_id'];
            $orm['document_id'] = $document['id'];
            $orm['amount'] = $document_type == Document::DOCUMENT_TYPE_WRITE_OFF ? -abs($product['amount']) : abs($product['amount']);
            $orm['uniq'] = $product['uniq'];
            $product = new Product($product['product_id']);
            $orm['title'] = $product['title'];
            $orm->insert();
        }
        return true;
    }

    /**
     *  Создает документ
     *
     * @param array $items - Массив с товарами
     * @param string $document_type - тип документа
     * @param integer $warehouse - Id склада
     * @param string $date - Дата
     * @param integer $applied - проведен
     * @return Document
     */
    function createDocument($items, $document_type, $warehouse, $date, $applied = 0)
    {
        $document = new Document();
        $document['warehouse'] = $warehouse;
        $document['type'] = $document_type;
        $document['date'] = $date;
        $document['items'] = $this->prepareProductsArray($items);;
        $document['applied'] = $applied;
        $document->insert();
        return $document;
    }

    /**
     *  Обновляет существующий документ
     *
     * @param array $items - Массив с товарами
     * @param integer $warehouse - Id склада
     * @param integer $id - Id
     * @param integer $applied - проведен
     * @param string $date - Дата
     * @param string $document_type - тип документа
     * @return Document
     */
    function updateDocument($items , $warehouse, $date, $id, $applied = 0, $document_type = null)
    {
        $document = new Document($id);
        if($document['id']){
            $document['items'] = $items;
            $document['applied'] = $applied;
            $document['warehouse'] = $warehouse;
            $document['date'] = $date;
            if($document_type){
                $document['type'] = $document_type;
            }
            $document->update();
        }
        else{
            return $this->createDocument($items, $document_type, $warehouse, $date, $applied);
        }
        return $document;
    }

    /**
     *  Сохраняет связанный документ заказа
     *
     * @param \Shop\Model\Orm\Order $order - объект заказа
     * @param array $items - подготовленный массив товаров для сохранения
     * @param integer $applied - документ проведен?
     * @param string $document_type - тип документа
     * @return Document
     */
    function saveDocumentFromOrder(Order $order, $items, $applied, $document_type)
    {
        $order_warehouse = $order->getStockWarehouse();
        $link_manager = new DocumentLinkManager($order['id'], $order::DOCUMENT_TYPE_ORDER);
        $links = $link_manager->getLinks();
        if ($links) {
            $document = $this->updateDocument($items, $order_warehouse['id'], $order['dateof'], $links[0]['document_id'], $applied, $document_type);
        } else {
            $document = $this->createDocument($items, $document_type, $order_warehouse['id'], $order['dateof'], $applied);
        }
        return $document;
    }

    /**
     *  Подготавливает массив товаров для сохранения в документе
     *
     * @param \Shop\Model\Orm\Order $order - объект заказа
     * @return array
     */
    function prepareItemsFromOrder(Order $order)
    {
        $new_items = [];
        $cart = $order->getCart();
        $items = $cart->getProductItems();
        $order_warehouse = $order->getStockWarehouse();
        foreach ($items as $uniq => $item) {
            $offers = $item['product']->fillOffers();
            $new_item = [
                'amount' => $item['cartitem']['amount'],
                'product_id' => $item['cartitem']['entity_id'],
            ];
            $offer_id = $item['cartitem']['offer'] !== null ? $offers['items'][$item['cartitem']['offer']]['id'] : OrmRequest::make()
                ->select()
                ->from(new Xstock())
                ->where([
                    'product_id' => $item['cartitem']['entity_id'],
                    'warehouse_id' => $order_warehouse['id'],
                ])
                ->exec()
                ->getOneField('offer_id');

            if ($offer_id == null) {
                $offer = new Offer();
                $offer['site_id'] = SiteManager::getSiteId();
                $offer['product_id'] = $item['cartitem']['entity_id'];
                $offer['sortn'] = 0;
                $offer->insert();
                $offer_id = $offer['id'];
            }
            $new_item['offer_id'] = $offer_id;
            $new_items[$uniq] = $new_item;
        }
        return $new_items;
    }

    /**
     *  Возвращает массив товаров, принадлежащих определенному документу
     *
     * @param integer $document_id - Id документа
     * @param $orm DocumentProducts|DocumentProductsArchive - orm архива или товаров
     * @param $archived bool - заархивирован?
     * @return array
     */
    function getProductsByDocumentId($document_id, $orm = null, $archived = false)
    {
        if(!$orm){
            $orm = new DocumentProducts();
        }
        if($archived){
            $orm = new DocumentProductsArchive();
        }
        if(!$this->items_cache || $this->items_cache['document_id'] != $document_id){
            $this->items_cache['document_id'] = $document_id;
            $this->items_cache = \RS\Orm\Request::make()
                ->select()
                ->from($orm)
                ->where(['document_id' => $document_id])
                ->objects(null, 'uniq');
        }
        return $this->items_cache;
    }

    /**
     *  Удаляет товары определенного документа
     *
     * @param integer $document_id - id документа
     */
    function deleteProductsByDocument($document_id)
    {
        \RS\Orm\Request::make()
            ->delete()
            ->from(new DocumentProducts())
            ->where(['document_id' => $document_id])
            ->exec();
        \RS\Orm\Request::make()
            ->delete()
            ->from(new DocumentProductsArchive())
            ->where(['document_id' => $document_id])
            ->exec();
    }

    /**
     *  Формирует шаблон с таблицей товаров документа дял поля "products"
     *
     * @param integer $document_id - Id документа
     * @param $type string - тип документа
     * @param $items array - массив с товарами документа
     * @param $archived bool - документ заархивирован?
     * @return string
     */
    function getProductsTable ($document_id = null, $type, $items = null, $archived = false, $disable_edit = false)
    {
        if($type == Inventorization::DOCUMENT_TYPE_INVENTORY){
            $inventorization_api = new InventorizationApi();
            return $inventorization_api->getProductsTable($document_id, true, $items);
        }
        if($type == Movement::DOCUMENT_TYPE_MOVEMENT){
            $movement_api = new MovementApi();
            return $movement_api->getProductsTable($document_id, false, $items);
        }
        if($items){
            $products = $items;
        }else{
            $products = $this->getProductsByDocumentId($document_id, null, $archived);
        }
        $smarty = new \RS\View\Engine();
        $smarty->assign([
            'products'     => $products,
            'api'          => $this,
            'disable_edit' => $disable_edit,
        ]);
        return $smarty->fetch("%catalog%/form/inventory/products_in_table.tpl");
    }

    /**
     *  Формирует скрытые элементы input для формы редактирования документа. По этим элементам формируется массив товаров для обработки
     *
     * @param integer $document_id - Id документа
     * @param $type string - тип документа
     * @param $items array - массив с товарами документа
     * @param $archived bool - документ заархивирован?
     * @return string
     */
    function getAddedItems ($document_id = null, $type, $items = null, $archived = false)
    {
        if($type == Inventorization::DOCUMENT_TYPE_INVENTORY){
            $inventorization_api = new InventorizationApi();
            return $inventorization_api->getAddedItems($document_id, true, $items);
        }
        if($type == Movement::DOCUMENT_TYPE_MOVEMENT){
            $movement_api = new MovementApi();
            return $movement_api->getAddedItems($document_id, false, $items);
        }
        if($items){
            $products = $items;
        }else{
            $products = $this->getProductsByDocumentId($document_id, null, $archived);
        }
        $products = array_reverse($products);
        $smarty = new \RS\View\Engine();
        $smarty->assign([
            'items'        => $products,
        ]);
        return $smarty->fetch("%catalog%/form/inventory/hidden_inputs.tpl");
    }

    /**
     *  Возвращает обхект товара.
     *
     * @param integer $product_id - Id товара
     * @return Product
     */
    function getProduct($product_id)
    {
        return new Product($product_id);
    }

    /**
     *  Возвращает массив комплектаций определенного товара
     *
     * @param integer $product_id - Id товара
     * @return array
     */
    function getProductOffers($product_id)
    {
        $product = new Product($product_id);
        return $product->fillOffers();
    }

    /**
     *  Получить api перемещения
     *
     * @return MovementApi
     */
    function getMovementApi()
    {
        return new MovementApi();
    }

    /**
     *  Создать связь документов
     *
     * @param integer $source_id - id документа источника
     * @param string $source_type - id типа документа
     * @param integer $document_id - id связанного документа
     * @param string $document_type - тип связанного документа
     * @return void
     */
    function createDocumentLinks($source_id, $source_type, $document_id, $document_type)
    {
        $link = new LinkedDocument();
        $link['source_id'] = $source_id;
        $link['source_type'] = $source_type;
        $link['document_id'] = $document_id;
        $link['document_type'] = $document_type;
        $link->insert();
    }

    /**
     *  Удалить документ
     *
     * @param integer $doc_id - id документа
     * @return void
     */
    function deleteDocument($doc_id)
    {
        $doc = new Document($doc_id);
        $doc->delete();
    }

    /**
     *  Получить api по типу документа
     *
     * @param string $document_type - тип документа
     * @return DocumentApi|bool|InventorizationApi|MovementApi
     */
    function getApiForDocumentType($document_type)
    {
        if($document_type == Document::DOCUMENT_TYPE_ARRIVAL
            || $document_type == Document::DOCUMENT_TYPE_WRITE_OFF
            || $document_type == Document::DOCUMENT_TYPE_RESERVE
            || $document_type == Document::DOCUMENT_TYPE_WAITING){
            return $this;
        }elseif($document_type == Movement::DOCUMENT_TYPE_MOVEMENT){
            return new MovementApi();
        }elseif($document_type == Inventorization::DOCUMENT_TYPE_INVENTORY){
            return new InventorizationApi();
        }
        return false;
    }

    /**
     *  Получить контроллер по типу документа
     *
     * @param string $document_type - тип документа
     * @param string $action - action контроллера
     * @param array $params - параметры
     * @return bool|string
     */
    function getControllerUrlByDocumentType($document_type, $action, $params)
    {
        $router = \RS\Router\Manager::obj();
        if($document_type == Document::DOCUMENT_TYPE_ARRIVAL){
            return $router->getAdminUrl($action, $params, 'catalog-inventoryarrivalctrl');
        }elseif($document_type == Document::DOCUMENT_TYPE_WRITE_OFF){
            return $router->getAdminUrl($action, $params, 'catalog-inventorywriteoffctrl');
        }elseif($document_type == Document::DOCUMENT_TYPE_WAITING){
            return $router->getAdminUrl($action, $params, 'catalog-inventorywaitingsctrl');
        }elseif($document_type == Document::DOCUMENT_TYPE_RESERVE){
            return $router->getAdminUrl($action, $params, 'catalog-inventoryreservationctrl');
        }elseif($document_type == Movement::DOCUMENT_TYPE_MOVEMENT){
            return $router->getAdminUrl($action, $params, 'catalog-inventorymovementctrl');
        }elseif($document_type == Inventorization::DOCUMENT_TYPE_INVENTORY){
            return $router->getAdminUrl($action, $params, 'catalog-inventorizationctrl');
        }
        return false;
    }

    /**
     *  Получить типы документов
     *
     * @return array
     */
    function getDocumentTypes()
    {
        return [
            'reserve'  => Document::DOCUMENT_TYPE_RESERVE,
            'waiting'  => Document::DOCUMENT_TYPE_WAITING,
            'writeoff' => Document::DOCUMENT_TYPE_WRITE_OFF,
            'arrival'  => Document::DOCUMENT_TYPE_ARRIVAL,
        ];
    }

    /**
     *  Получает товары для добавления в документ
     *
     * @return mixed
     */
    function getProductsToAdd()
    {
        $ids = $_SESSION[InventoryTools::$session_product_ids];
        $_SESSION[InventoryTools::$session_product_ids] = null;
        return $ids;
    }

    /**
     *  Получить массив с названиями типов документов
     *
     * @return array
     */
    function getDocumentTitles()
    {
        return [
            Document::DOCUMENT_TYPE_ARRIVAL => t('Оприходование'),
            Document::DOCUMENT_TYPE_WRITE_OFF => t('Списание'),
            Document::DOCUMENT_TYPE_RESERVE => t('Резервирование'),
            Document::DOCUMENT_TYPE_WAITING => t('Ожидание'),
        ];
    }

    /**
     *  Получить документы, в которых состоит товар
     *
     * @param integer $product_id - id товара
     * @param integer $offer_id - id комплектации
     * @param integer $warehouse_id - id склада
     * @param products_orm - из какой таблицы брать товары
     * @return array
     */
    function getDocumentsByProduct($product_id, $offer_id, $warehouse_id, $products_orm = null)
    {
        if(!$products_orm){
            $products_orm = new \Catalog\Model\Orm\Inventory\DocumentProducts();
        }
        $result = \RS\Orm\Request::make()
            ->select('doc.date', 'item.product_id', 'item.offer_id', 'item.warehouse', 'doc.type', 'item.amount', 'doc.id', 'doc.archived')
            ->from($products_orm, 'item')
            ->join(new \Catalog\Model\Orm\Inventory\Document(), 'item.document_id = doc.id', 'doc')
            ->where([
                'doc.site_id' => \RS\Site\Manager::getSiteId(),
                'item.product_id' => $product_id,
                'item.offer_id' => $offer_id,
                'item.warehouse' => $warehouse_id,
                'doc.applied' => 1,
            ])
            ->exec()
            ->fetchAll();
        return $result;
    }
}
