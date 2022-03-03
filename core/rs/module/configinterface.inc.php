<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Module;

/**
* Интерфейс описывает класс конфигурации модуля
*/
interface ConfigInterface {
    
    /**
    * Должен возвращать массив со значениями свойств по-умолчанию. 
    * Метод может зависеть только от функций ядра, т.к. вызов данного метода 
    * происходит в том числе и перед установкой модуля, т.е. классов модуля на момент вызова не существует.
    * 
    * @return array
    */
    public static function getDefaultValues();
    
}
