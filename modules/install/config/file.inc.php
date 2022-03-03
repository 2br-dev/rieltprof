<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Install\Config;

class File extends \RS\Orm\ConfigObject
{   
    /**
    * Объявляет стандартные поля у объектов
    * @return \RS\Orm\PropertyIterator
    */
    function _init()
    {
        parent::_init()->offsetGet('enabled')->setReadOnly(true);
    }    
    
    /**
    * Возвращает значения свойств по-умолчанию
    * 
    * @return array
    */
    public static function getDefaultValues()
    {
        return parent::getDefaultValues() + [
            'installed' => true,
            'enabled' => !\Setup::$INSTALLED,
            ];
    }        

    function load($primaryKeyValue = null)
    {
        return true;
    }    
    
}
