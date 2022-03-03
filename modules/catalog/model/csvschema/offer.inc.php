<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\CsvSchema;
use \RS\Csv\Preset;

/**
* Схема экспорта/импорта справочника цен в CSV
*/
class Offer extends \RS\Csv\AbstractSchema
{
    protected
        $reset_base_query = true;
    
    function __construct()
    {
        $product_search_field = \RS\Config\Loader::byModule($this)->csv_offer_product_search_field;
        $offer_search_field   = \RS\Config\Loader::byModule($this)->csv_offer_search_field;
        $request              = \RS\Orm\Request::make()->from(new \Catalog\Model\Orm\Offer())->where('product_id > 0');
        $config = \RS\Config\Loader::byModule($this);
        $presets = [
            new Preset\LinkedTable([
                'ormObject' => new \Catalog\Model\Orm\Product(),
                'save' => false,
                'fields' => [$product_search_field],
                'titles' => [$product_search_field => t('Товар')],
                'idField' => 'id',
                'multisite' => true,
                'linkForeignField' => 'product_id',
                'linkPresetId' => 0,
                'linkDefaultValue' => 0
            ]),
            new \Catalog\Model\CsvPreset\OfferPrice([
                'ormObject' => new \Catalog\Model\Orm\Offer(),
                'linkPresetId' => 0,
                'linkIdField' => 'id',
                'sortnField' => 'sortn',
                'arrayField' => 'pricedata_arr', //Поле для обновления цены комплектации в товаре
            ]),
            new Preset\SerializedArray([
                'linkPresetId' => 0,
                'linkForeignField' => 'propsdata',
                'title' => t('Характеристики комплектации')
            ]),
            new \Catalog\Model\CsvPreset\OfferPhotos([
                'linkPresetId' => 0,
                'link_orm_type' => 'catalog',
                'linkForeignField' => 'photos_arr',
                'title' => t('Фотографии'),
            ])
        ];
        $inventory_control_off = [
            new \Catalog\Model\CsvPreset\OfferStock([
                'linkPresetId' => 0,
                'linkOfferIdField' => 'id',
                'linkForeignField' => 'offer_id'
            ]),
        ];
        parent::__construct(new \Catalog\Model\CsvPreset\Offer([
            'ormObject' => new \Catalog\Model\Orm\Offer(),
            'excludeFields' => [
                'product_id', 'pricedata', 'propsdata', 'site_id', 'id', 'num', 'photos', 'processed'
            ],
            'nullFields' => [
                'xml_id'
            ],
            'multisite' => true,
            'ormUnsetFields' => ['pricedata'],
            'selectOrder' => 'product_id ASC, sortn ASC',
            'selectRequest' => $request,
            'searchFields' => ['product_id', $offer_search_field],
            'beforeRowImport' => function ($_this) {
                if (empty($_this->row['product_id'])) {
                    return false;
                }
                if (isset($_this->row['propsdata'])) {
                    $_this->row['propsdata_arr'] = unserialize($_this->row['propsdata']);
                }
                return null;
            },
            'afterRowImport' => [$this, 'afterBaseRowImport']
        ]), $config['inventory_control_enable'] ? $presets : array_merge($presets, $inventory_control_off), [
            'afterImport' => [$this, 'afterImport']
        ]);
    }
    
    /**
    * Возвращает запрос для базовой выборки
    * 
    * @return \RS\Orm\Request
    */
    function getBaseQuery()
    {
        if (!$this->query) {
            $this->query = $this->base_preset->getSelectRequest();
        }
        
        //Если есть запрос с выборкой в сессии
        if ($this->reset_base_query && $savedRequest = \Catalog\Model\Api::getSavedRequest('Catalog\Controller\Admin\Ctrl_list')) {
            /** @var \RS\ORM\Request $q */
            $q = clone $savedRequest;
            $q->select  = "OFFERS.*";
            $q->limit(null)
              ->orderby('OFFERS.product_id ASC, OFFERS.sortn ASC')
              ->where('OFFERS.product_id>0')
              ->leftjoin(new \Catalog\Model\Orm\Offer(), 'OFFERS.product_id = A.id', 'OFFERS')
              ->setReturnClass(new \Catalog\Model\Orm\Offer())
              ->groupby('OFFERS.id');

            $this->query = $q;
        }
        
        return $this->query;
    }
    
    /**
    * Устанавливает флаг сброса базового запроса
    * 
    * @param bool $value
    */
    function setResetBaseQuery($value)
    {
        $this->reset_base_query = $value;
    }
    
    /**
    * Возвращает возможные колонки для идентификации продукта
    * 
    * @return array
    */
    public static function getPossibleProductFields()
    {
        $product = new \Catalog\Model\Orm\Product();
        $fields = array_flip(['title', 'barcode', 'xml_id', 'alias']);
        foreach($fields as $k => $v) {
            $fields[$k] = $product['__'.$k]->getTitle();
        }
        return $fields;
    }
    
    /**
    * Возвращает возможные колонки для идентификации комплектации
    * 
    * @return array
    */
    public static function getPossibleOffersFields()
    {
        $product = new \Catalog\Model\Orm\Offer();
        $fields = array_flip(['title', 'barcode', 'sortn', 'xml_id']);
        foreach($fields as $k => $v) {
            $fields[$k] = $product['__'.$k]->getTitle();
        }
        return $fields;
    }
    
     
    
    /**
    * Обработчик, выполняющийся после импорта набора (которые уложились по 
    * времени в 1 шаг) данных
    * 
    * @param Catalog\Model\CsvSchema\Offer 
    */
    function afterImport()
    {
        //Производим пересчет общих остатков у товаров
        $offer = new \Catalog\Model\Orm\Offer();
        \RS\Orm\Request::make()
            ->update()
            ->from(new \Catalog\Model\Orm\Product(), 'P')
            ->set("P.num = (SELECT SUM(num) FROM {$offer->_getTable()} O WHERE O.product_id = P.id)")
            ->exec();
    }
    
    /**
    * Выполняется после импорта одной строки у пресета
    * 
    * @param mixed $preset
    */
    function afterBaseRowImport($preset)
    {
        //Обновляем сведения о ценах основной комплектации у товара
        if ($preset->row['product_id'] && empty($preset->row['sortn'])) {
            if (isset($preset->row['pricedata_arr'])) {
                $pricedata = $preset->row['pricedata_arr'];
                if (isset($pricedata['price'])) {
                    $excost = [];
                    foreach($pricedata['price'] as $cost_id => $data) {
                        $excost[$cost_id] = [
                            'cost_original_val' => $data['original_value'],
                            'cost_original_currency' => $data['unit']
                        ];
                    }
                    
                    $product = new \Catalog\Model\Orm\Product($preset->row['product_id']);
                    $product['excost'] = $excost;
                    $product->update();
                }
            }
        }
    }
}
