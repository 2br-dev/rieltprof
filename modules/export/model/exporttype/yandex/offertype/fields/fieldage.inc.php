<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Export\Model\ExportType\Yandex\OfferType\Fields;

use Catalog\Model\Orm\Product;
use \Export\Model\ExportType;
use Export\Model\Orm\ExportProfile;

/**
* Структура данных, описывающая поле в экспортируемом XML документе
*/
class FieldAge extends ExportType\Field implements ExportType\ComplexFieldInterface
{
    /**
    * Добавляет необходимую структуру тегов в итоговый XML
    * 
    * @param \Export\Model\Orm\ExportProfile $profile - объект профиля экспорта
    * @param \XMLWriter $writer - объект библиотеки для записи XML
    * @param \Catalog\Model\Orm\Product $product - объект товара
    * @param integer $offer_index - индекс комплектации для отображения
    */
    function writeSomeTags(\XMLWriter $writer, ExportProfile $profile, Product $product, $offer_index = null){
        $export_type_object = $profile->getTypeObject();
        if (isset($export_type_object['fieldmap'][$this->name])) {
            if (!empty($export_type_object['fieldmap'][$this->name]['prop_id'])
                && $export_type_object['fieldmap'][$this->name]['prop_id'] != -1
            ) {

                $prop_id = (int)$export_type_object['fieldmap'][$this->name]['prop_id'];
                $value = $product->getPropertyValueById($prop_id);
            } else {
                $value = $export_type_object['fieldmap'][$this->name]['value'];
            }
            if($value){
                $writer->startElement('age');
                $writer->writeAttribute('unit', "year");
                $writer->text($value);
                $writer->endElement();
            }
        }
    }
}