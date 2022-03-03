<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Model\DesignAtoms\CSSProperty;

/**
 * Class SelectColumnClass - класс для указания класса, с выбором в виде выпадающего списка
 */
class SelectColumnClass extends Select {
    /**
     * Обычное свойство CSS, но c выбором из выпадающего списка
     *
     * @param string $property - название элемента CSS свойства. Например: margin
     * @param string $title - название для отображения CSS свойства. Наприимер: Задний фон. Если пусто будет взято из имени элемента
     * @param string $value - значение CSS свойства
     * @param array $data - дополниельные параметры для передачи в вместе со свойством 'ключ' => 'значение'
     */
    function __construct($property, $title, $value = null, $data = [])
    {
        parent::__construct($property, $title, $value, $data);
        $this->setOptions([
            'd-col-md-12' => '12',
            'd-col-md-11' => '11',
            'd-col-md-10' => '10',
            'd-col-md-9' => '9',
            'd-col-md-8' => '8',
            'd-col-md-7' => '7',
            'd-col-md-6' => '6',
            'd-col-md-5' => '5',
            'd-col-md-4' => '4',
            'd-col-md-3' => '3',
            'd-col-md-2' => '2',
            'd-col-md-1' => '1'
        ]);
    }
}