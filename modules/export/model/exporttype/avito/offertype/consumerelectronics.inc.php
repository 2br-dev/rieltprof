<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Export\Model\ExportType\Avito\OfferType;
use \Export\Model\Orm\ExportProfile;
use \Export\Model\ExportType\Field;
use \Catalog\Model\Orm\Product as Product;


class ConsumerElectronics extends CommonAvitoOfferType
{
    /**
    * Возвращает название типа описания
    * 
    * @return string
    */
    function getTitle()
    {
        return t('Бытовая электроника');
    }
    
    /**
    * Возвращает идентификатор данного типа описания. (только англ. буквы)
    * 
    * @return string
    */
    function getShortName()
    {
        return 'consumer_electronics';
    }
    
    /**
    * Получить список "особенных" полей для данного типа описания
    * Возвращает массив объектов класса Field.
    * 
    * @param string $exporttype_name - короткое название типа экспорта
    * @return Filed[]
    */
    protected function addSelfEspecialTags($fields)
    {
        $field = new Field();
        $field->name        = 'Category';
        $field->title       = t('Категория товара');
        $field->hint        = t('Значение из списка, см. документацию Avito');
        $field->required    = true;
        $fields[$field->name]  = $field;
        
        $field = new Field();
        $field->name        = 'GoodsType';
        $field->title       = t('Вид товара');
        $field->hint        = t('Значение из списка, см. документацию Avito');
        $field->required    = true;
        $fields[$field->name]  = $field;
        
        $field = new Field();
        $field->name        = 'Barcode';
        $field->title       = t('Штрихкод');
        $field->hint        = t('Только для некоторых категорий, см. документацию Avito');
        $fields[$field->name]  = $field;

        $field = new Field();
        $field->name        = 'Condition';
        $field->title       = t('Состояние вещи');
        $field->hint        = t('Поле "Condition" <br>(Для категорий "Детская одежда и обувь", "Товары для детей и игрушки")<br><br>
                                 Одно из значений списка:<br>
                                 - Новый<br>
                                 - Б/у');
        $fields[$field->name]  = $field;
        
        return $fields;
    }
    
    /**
    * Запись товарного предложения
    * 
    * @param \Export\Model\Orm\ExportProfile $profile - объект профиля экспорта
    * @param \XMLWriter $writer - объект библиотеки для записи XML
    * @param \Catalog\Model\Orm\Product $product - объект товара
    * @param integer $offer_index - индекс комплектации для отображения
    */
    public function writeEspecialOfferTags(ExportProfile $profile, \XMLWriter $writer, Product $product, $offer_index)
    {
        $title = mb_substr($product['title'], 0, 50);
        $product_description = html_entity_decode(strip_tags($product['short_description'] ?: $product['description']));
        $description = "{$product['title']}.\n {$product_description}";
        
        parent::writeEspecialOfferTags($profile, $writer, $product, $offer_index);
        $writer->writeElement('Title', $title);
        $writer->writeElement('Description', $description);
    }
}
