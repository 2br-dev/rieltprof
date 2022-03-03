<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Export\Model\ExportType\Yandex\OfferType;
use \Export\Model\ExportType\Field;
use \Export\Model\Orm\ExportProfile as ExportProfile;
use \Catalog\Model\Orm\Product as Product;

class VendorModel extends CommonOfferType
{
    /**
    * Возвращает название типа описания
    * 
    * @return string
    */
    function getTitle()
    {
        return t('Произвольный товар (vendor.model)');
    }
    
    /**
    * Возвращает идентификатор данного типа описания. (только англ. буквы)
    * 
    * @return string
    */
    public function getShortName()
    {
        return 'vendor.model';
    }
    
    static private $boolTags = [
        'manufacturer_warranty'
    ];
    
    /**
    * Дополняет список "особенных" полей, персональными для данного типа описания
    * 
    * @param $fields - массив "особенных" полей
    * @return Filed[]
    */
    protected function addSelfEspecialTags($fields)
    {
        $ret = [];

        $field = new Field();
        $field->name        = 'vendor';
        $field->title       = t('Производитель (если не укакзан,<br>используется бренд товара)');
        $field->required    = true;
        $ret[$field->name]  = $field;
        
        $field = new Field();
        $field->name        = 'vendorCode';
        $field->title       = t('Код производителя');
        $ret[$field->name]  = $field;

        $field = new Field();
        $field->name        = 'model';
        $field->title       = t('Модель');
        $field->required    = true;
        $ret[$field->name]  = $field;
        
        $field = new Field();
        $field->name        = 'typePrefix';
        $field->title       = t('Тип/категория товара (typePrefix)');
        $ret[$field->name]  = $field;
        
        $ret = array_merge($ret, $fields);
        
        return $ret;
    }



    public function writeEspecialOfferTags(ExportProfile $profile, \XMLWriter $writer, Product $product, $offer_index)
    {
        //(typePrefix?, vendor, vendorCode?, model, (provider, tarifplan?)?)
        $fields = $this->getEspecialTags();
        
        // Выводим специальные теги в правильном порядке
        $this->writeElementFromFieldmap($fields['typePrefix'], $profile, $writer, $product);
        // Для поля VENDOR особенное поведение. Если не удалось получить значение из настроек fieldmap, то заполняем его наименованием продукта
        $vendor = $this->getElementFromFieldmap($fields['vendor'], $profile, $writer, $product);
        if(!$vendor) {
            $brand = $product->getBrand();
            $vendor = $brand['title'];
        }
        $writer->writeElement('vendor', $vendor);
        $this->writeElementFromFieldmap($fields['vendorCode'], $profile, $writer, $product);
        // Для поля MODEL особенное поведение. Если не удалось получить значение из настроек fieldmap, то заполняем его наименованием продукта
        $model = $this->getElementFromFieldmap($fields['model'], $profile, $writer, $product);
        if(!$model){
            $model = $offer_index === false ? $product->title : $product->getOfferTitle($offer_index);
        }
        $writer->writeElement('model', $model);
        
        $this->writeElementFromFieldmap($fields['delivery'], $profile, $writer, $product);   
        $this->writeElementFromFieldmap($fields['pickup'], $profile, $writer, $product);   
        $this->writeElementFromFieldmap($fields['store'], $profile, $writer, $product);
        $this->writeElementFromFieldmap($fields['offer_delivery_cost'], $profile, $writer, $product);
        $this->writeElementFromFieldmap($fields['sales_notes'], $profile, $writer, $product);
        $this->writeElementFromFieldmap($fields['country_of_origin'], $profile, $writer, $product);
        $this->writeElementFromFieldmap($fields['manufacturer_warranty'], $profile, $writer, $product);
        $this->writeElementFromFieldmap($fields['dimensions_l'], $profile, $writer, $product);
        $this->writeElementFromFieldmap($fields['age'], $profile, $writer, $product);
        $this->writeElementFromFieldmap($fields['weight'], $profile, $writer, $product);
        $this->writeElementFromFieldmap($fields['adult'], $profile, $writer, $product);

        // Добавим дополнительные поля через хук и запишем их
        $customfields = [];
        $customfields = self::addCustomEspecialTags($customfields);
        foreach ($customfields as $field) {
            $this->writeElementFromFieldmap($field, $profile, $writer, $product, $offer_index);
        }
    }
}
