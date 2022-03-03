<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Model\DesignAtoms\CSSProperty;

/**
 * Class SizeForOutline - класс для свойства CSS внешней границы outline
 */
class SizeForOutline extends SizeFourDigitsForBorder {


    /**
     * Свойства CSS означающее размер, дополнительно устанавливает свойство units - px, rem, em, vh, vw
     *
     * @param string $property - название элемента CSS свойства. Например: margin
     * @param string $title - название для отображения CSS свойства. Наприимер: Задний фон. Если пусто будет взято из имени элемента
     * @param mixed $value - значение CSS свойства
     * @param array $data - дополниельные параметры для передачи в вместе со свойством 'ключ' => 'значение'
     */
    function __construct($property, $title, $value = null, $data = [])
    {
        if ($value === null){
            $value = [
                'width' => '0px',
                'type' => 'solid',
                'color' => '#000000FF',
            ];
        }
        parent::__construct($property, $title, $value, $data);
    }
}