<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Orm;

/**
* Класс со вспомогательными функциями по работе с core объектами
*/
class Tools
{
    /**
    * Возвращает имя таблицы у Core объекта
    * 
    * @param \RS\Orm\AbstractObject | string  $object_class
    */
    public static function getTable($object_class)
    {
        if (!is_object($object_class)) {
            $object_class = new $object_class();
        }
        return $object_class->_getTable();
    }
    
}

