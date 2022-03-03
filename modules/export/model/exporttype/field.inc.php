<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Export\Model\ExportType;

use \RS\Orm\Type;
use \Export\Model\Orm\ExportProfile;
use \Catalog\Model\Orm\Product;

/**
* Структура данных, описывающая поле в экспортируемом XML документе
*/
class Field
{
    public $name;
    public $hint;
    public $title;
    public $type = TYPE_STRING;
    public $required = false;
    public $maxlen = 0;
    public $hidden = false;
    public $boolAsInt = false;
    
    /**
    * Возвращает значение свойства товара
    * 
    * @param string $name - имя поля в fieldmap
    * @param \Export\Model\Orm\ExportProfile $profile - объект профиля экспорта
    * @param \Catalog\Model\Orm\Product $product - объект товара
    * @return mixed
    */
    public function getValue($name, ExportProfile $profile, Product $product) {
        $export_type_object = $profile->getTypeObject();
        if (!empty($export_type_object['fieldmap'][$name]['prop_id'])) {
            $property_id = (int) $export_type_object['fieldmap'][$name]['prop_id']; // Идентификатор свойстава товара
            $default_value = $export_type_object['fieldmap'][$name]['value']; // Значение по умолчанию
            $value = $product->getPropertyValueById($property_id); // Получаем значение свойства товара
            // Выводим значение свойства, либо значение по умолчанию
            return $value === null ? $default_value : $value;
        } else {
            return null;
        }
    }
}