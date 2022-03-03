<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\Orm\Inventory;
use \RS\Orm\Type;

/**
 *  Таблица с документами: списание, оприходование, ожидание, резервирование
 *
 * Class InventoryDoc
 * @package Catalog\Model\Orm\Inventory
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property integer $applied Проведен
 * @property string $comment Комментарий
 * @property integer $archived Заархивирован?
 * @property integer $warehouse Склад
 * @property string $date Дата
 * @property integer $provider Поставщик
 * @property string $linked_documents Связанный документ
 * @property string $products Товары
 * @property string $type Тип документа
 * @property array $items Товары
 * @property integer $items_count Количество товаров
 * --\--
 */
class Document extends \RS\Orm\OrmObject
{
    const
        DOCUMENT_TYPE_ARRIVAL         = 'arrival',
        DOCUMENT_TYPE_WRITE_OFF       = 'write_off',
        DOCUMENT_TYPE_RESERVE         = 'reserve',
        DOCUMENT_TYPE_WAITING         = 'waiting';

    protected static
        $table = 'document_inventory'; //Имя таблицы в БД

    /**
     * Инициализирует свойства ORM объекта
     *
     * @return void
     */
    function _init()
    {
        parent::_init()->append([
            t('Основные'),
            'site_id' => new Type\CurrentSite(),
            'applied' => new Type\Integer([
                'checkboxview' => [1, 0],
                'description' => t('Проведен'),
                'default' => 1,
            ]),
            'comment' => new Type\Text([
                'description' => t('Комментарий'),
            ]),
            'archived' => new Type\Integer([
                'description' => t('Заархивирован?'),
                'visible' => false,
                'meVisible' => false,
                'default' => 0,
            ]),
            'warehouse' => new Type\Integer([
                'list' => [['Catalog\Model\Warehouseapi', 'staticSelectList']],
                'maxLength' => '250',
                'description' => t('Склад'),
                'checker' => ['chkEmpty', t('Укажите склад')],
            ]),
            'date' => new Type\Datetime([
                'maxLength' => '250',
                'description' => t('Дата'),
                'checker' => ['chkEmpty', t('Укажите дату')],
            ]),
            'provider' => new Type\Integer([
                'list' => [['Catalog\Model\Inventory\InventoryTools', 'staticSelectProviders']],
                'writeoffVisible' => false,
                'description' => t('Поставщик'),
            ]),
            'linked_documents' => new Type\Varchar([
                'runtime' => true,
                'description' => t('Связанный документ'),
                'template' => '%catalog%/form/inventory/field_linked_document.tpl',
            ]),
            'products' =>  new Type\Varchar([
                'runtime' => true,
                'description' => t('Товары'),
                'template' => '%catalog%/form/inventory/products.tpl',
            ]),
            'type' => new Type\Enum(
                [
                    self::DOCUMENT_TYPE_ARRIVAL,
                    self::DOCUMENT_TYPE_WAITING,
                    self::DOCUMENT_TYPE_RESERVE,
                    self::DOCUMENT_TYPE_WRITE_OFF,
                ],
                [
                    'visible' => false,
                    'description' => t('Тип документа'),
                ]),
            'items' => new Type\ArrayList([
                'runtime' => true,
                'description' => t('Товары'),
                'visible' => false,
                'checker' => [[__CLASS__, 'checkProductsErrors']],
            ]),
            'items_count' => new Type\Integer([
                'description' => t('Количество товаров'),
                'visible' => false,
            ]),
        ]);
    }

    /**
     * Вызывается перед сохранением объекта в storage
     * @param string $save_flag insert|update|replace
     *
     * @return null | false Если возвращено false, то сохранение не произойдет
     */
    function beforeWrite($save_flag)
    {
        if($this['id']){
            $old_doc = new \Catalog\Model\Orm\Inventory\Document($this['id']);
            $this['old_warehouse'] = (int)$old_doc['warehouse'];
        }
        $this['items_count'] = count($this['items']);
    }

    /**
     * Проверить ошибки в товарах документа
     *
     * @param $coreobj
     * @param $products
     * @return bool|string
     */
    public static function checkProductsErrors($coreobj, $products)
    {
        if(!count($products)){
            return t('Не добавлено ни одного товара');
        }
        foreach ($products as $uniq => $product){
            if($product['offer_id'] == -1){
                return t('Не у всех товаров выбрана комплектация');
            }
        }
        return true;
    }

    /**
     * Вызывается после сохранения объекта в storage
     * @param string $save_flag insert|update|replace
     *
     * @return void
     */
    function afterWrite ($save_flag)
    {
        $api = new \Catalog\Model\Inventory\DocumentApi();
        $old_items = $api->getProductsByDocumentId($this['id']);
        $api->deleteProductsByDocument($this['id']);
        $api->saveProducts($api->prepareProductsArray($this['items']), $this, $this['type']);
        $stock_manager = new \Catalog\Model\InventoryManager();
        $stock_manager->updateStocks($this['items'], $this['warehouse'], $old_items, $this['old_warehouse']);
    }

    /**
     * Удаляет объект из хранилища
     * @return boolean - true, в случае успеха
     */
    function delete()
    {
        $api = new \Catalog\Model\Inventory\DocumentApi();
        $doc = new \Catalog\Model\Orm\Inventory\Document($this['id']);
        $items = $api->getProductsByDocumentId($this['id'], null, $this['archived']);
        \RS\Orm\Request::make()
            ->delete()
            ->from(new \Catalog\Model\Orm\Inventory\DocumentProductsArchive())
            ->where(['document_id' => $this->id])
            ->exec();
        $stock_manager = new \Catalog\Model\InventoryManager();
        $api->deleteProductsByDocument($this['id']);
        $stock_manager->updateStocks($items, $doc['warehouse']);
        $manager = new \Catalog\Model\DocumentLinkManager($this['id']);
        $manager->deleteLinksByDocument($doc);
        return parent::delete();
    }

    /**
     *  Удаляет товары документа
     */
    function deleteProducts()
    {
        $api = new \Catalog\Model\Inventory\DocumentApi();
        $api->deleteProductsByDocument($this['id']);
    }

    /**
     *  Получить связанные документы
     *
     * @return array|bool
     */
    function getLinkedDocuments()
    {
        $manager = new \Catalog\Model\DocumentLinkManager($this['id'], $this['type']);
        return $manager->getLinks();
    }

    /**
     *  Возвращает объект api
     *
     * @return \Catalog\Model\inventory\DocumentApi
     */
    function getApi()
    {
        return new \Catalog\Model\inventory\DocumentApi();
    }

    /**
     *  Получить количество товаров в документе
     *
     * @return integer
     */
    function getProductsAmount()
    {
        $result = \RS\Orm\Request::make()
            ->select('item.amount')
            ->from(new \Catalog\Model\Orm\Inventory\Document(), 'doc')
            ->leftjoin(new \Catalog\Model\Orm\Inventory\DocumentProducts(), 'doc.id = item.document_id', 'item')
            ->where(['doc.id' => $this->id])
            ->exec()
            ->fetchSelected(null, 'amount');
        $amount = $result ? count($result) : 0;
        return $amount;
    }

}