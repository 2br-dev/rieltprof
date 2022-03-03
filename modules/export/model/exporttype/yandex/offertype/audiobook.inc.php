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


class AudioBook extends CommonOfferType
{
    /**
    * Возвращает название типа описания
    * 
    * @return string
    */
    function getTitle()
    {
        return t('Аудиокниги (audiobook)');
    }
    
    /**
    * Возвращает идентификатор данного типа описания. (только англ. буквы)
    * 
    * @return string
    */
    public function getShortName()
    {
        return 'audiobook';
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
        $field->name        = 'performed_by';
        $field->title       = t('Исполнитель');
        $fields[$field->name]  = $field;

        $field = new Field();
        $field->name        = 'performance_type';
        $field->title       = t('Тип');
        $fields[$field->name]  = $field;

        $field = new Field();
        $field->name        = 'language';
        $field->title       = t('Язык');
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
        $field->name        = 'format';
        $field->title       = t('Формат аудиокниги');
        $fields[$field->name]  = $field;

        $field = new Field();
        $field->name        = 'storage';
        $field->title       = t('Носитель');
        $fields[$field->name]  = $field;

        $field = new Field();
        $field->name        = 'recording_length';
        $field->title       = t('Время звучания (mm.ss)');
        $fields[$field->name]  = $field;

        $field = new Field();
        $field->name        = 'table_of_contents';
        $field->title       = t('Оглавление');
        $fields[$field->name]  = $field;
        
        return $fields;
    }
    

    public function writeEspecialOfferTags(ExportProfile $profile, \XMLWriter $writer, Product $product, $offer_index)
    {
        // (author?, name, publisher?, series?, year?, ISBN?, volume?, part?, language?, table_of_contents?, performed_by?, performance_type?, storage?, format?, recording_length?)
        
        $fields = $this->getEspecialTags();

        // Выводим специальные теги в правильном порядке
        $this->writeElementFromFieldmap($fields['author'], $profile, $writer, $product);
        $writer->writeElement('name', $offer_index === false ? $product->title : $product->getOfferTitle($offer_index));
        $this->writeElementFromFieldmap($fields['publisher'], $profile, $writer, $product);
        $this->writeElementFromFieldmap($fields['series'], $profile, $writer, $product);
        $this->writeElementFromFieldmap($fields['year'], $profile, $writer, $product);
        $this->writeElementFromFieldmap($fields['ISBN'], $profile, $writer, $product);
        $this->writeElementFromFieldmap($fields['volume'], $profile, $writer, $product);
        $this->writeElementFromFieldmap($fields['part'], $profile, $writer, $product);
        $this->writeElementFromFieldmap($fields['language'], $profile, $writer, $product);
        $this->writeElementFromFieldmap($fields['table_of_contents'], $profile, $writer, $product);
        $this->writeElementFromFieldmap($fields['performed_by'], $profile, $writer, $product);
        $this->writeElementFromFieldmap($fields['performance_type'], $profile, $writer, $product);
        $this->writeElementFromFieldmap($fields['storage'], $profile, $writer, $product);
        $this->writeElementFromFieldmap($fields['format'], $profile, $writer, $product);
        $this->writeElementFromFieldmap($fields['recording_length'], $profile, $writer, $product);
    }
}