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


class Book extends CommonOfferType
{
    /**
    * Возвращает название типа описания
    * 
    * @return string
    */
    function getTitle()
    {
        return t('Книги (book)');
    }
    
    /**
    * Возвращает идентификатор данного типа описания. (только англ. буквы)
    * 
    * @return string
    */
    public function getShortName()
    {
        return 'book';
    }
    
    /**
    * Дополняет список "особенных" полей, персональными для данного типа описания
    * 
    * @param $fields - массив "особенных" полей
    * @return Filed[]
    */
    protected function addSelfEspecialTags($fields)
    {
        $field = new Field();
        $field->name        = 'author';
        $field->title       = t('Автор');
        $fields[$field->name]  = $field;

        $field = new Field();
        $field->name        = 'publisher';
        $field->title       = t('Издательство');
        $fields[$field->name]  = $field;

        $field = new Field();
        $field->name        = 'series';
        $field->title       = t('Серия');
        $fields[$field->name]  = $field;

        $field = new Field();
        $field->name        = 'year';
        $field->title       = t('Год издания');
        $fields[$field->name]  = $field;

        $field = new Field();
        $field->name        = 'ISBN';
        $field->title       = t('Код книги (ISBN)');
        $fields[$field->name]  = $field;

        $field = new Field();
        $field->name        = 'volume';
        $field->title       = t('Кол-во томов');
        $fields[$field->name]  = $field;

        $field = new Field();
        $field->name        = 'part';
        $field->title       = t('Номер тома');
        $fields[$field->name]  = $field;

        $field = new Field();
        $field->name        = 'language';
        $field->title       = t('Язык');
        $fields[$field->name]  = $field;

        $field = new Field();
        $field->name        = 'binding';
        $field->title       = t('Переплет');
        $fields[$field->name]  = $field;

        $field = new Field();
        $field->name        = 'page_extent';
        $field->title       = t('Кол-во страниц');
        $fields[$field->name]  = $field;

        $field = new Field();
        $field->name        = 'table_of_contents';
        $field->title       = t('Оглавление');
        $fields[$field->name]  = $field;
        
        $field = new Field();
        $field->name        = 'delivery';
        $field->title       = t('Возможность доставки (delivery)<br/>Характеристика Да/Нет');
        $field->type        = TYPE_BOOLEAN;
        $field->required    = false;
        $fields[$field->name]  = $field;
        
        $field = new Field();
        $field->name        = 'local_delivery_cost';
        $field->title       = t('Стоимость доставки в Вашем регионе (local_delivery_cost)<br/>Числовая характеристика');
        $field->required    = false;
        $fields[$field->name]  = $field;
        
        return $fields;
    }
    
    public function writeEspecialOfferTags(ExportProfile $profile, \XMLWriter $writer, Product $product, $offer_index)
    {
        //(author?, name, publisher?, series?, year?, ISBN?, volume?, part?, language?, binding?, page_extent?, table_of_contents?)
        $fields = $this->getEspecialTags();

        // Выводим специальные теги в правильном порядке
        $this->writeElementFromFieldmap($fields['author'], $profile, $writer, $product);
        $writer->writeElement('name', $product->title.' '.(($offer_index !== false && !$profile->no_export_offers && !$profile->no_export_offers_title) ? $product->getOfferTitle($offer_index) : '') );
        $this->writeElementFromFieldmap($fields['publisher'], $profile, $writer, $product);
        $this->writeElementFromFieldmap($fields['series'], $profile, $writer, $product);
        $this->writeElementFromFieldmap($fields['year'], $profile, $writer, $product);
        $this->writeElementFromFieldmap($fields['ISBN'], $profile, $writer, $product);
        $this->writeElementFromFieldmap($fields['volume'], $profile, $writer, $product);
        $this->writeElementFromFieldmap($fields['part'], $profile, $writer, $product);
        $this->writeElementFromFieldmap($fields['language'], $profile, $writer, $product);
        $this->writeElementFromFieldmap($fields['binding'], $profile, $writer, $product);
        $this->writeElementFromFieldmap($fields['page_extent'], $profile, $writer, $product);
        $this->writeElementFromFieldmap($fields['table_of_contents'], $profile, $writer, $product);
        $this->writeElementFromFieldmap($fields['delivery'], $profile, $writer, $product);
        $this->writeElementFromFieldmap($fields['local_delivery_cost'], $profile, $writer, $product);
        
    }
}