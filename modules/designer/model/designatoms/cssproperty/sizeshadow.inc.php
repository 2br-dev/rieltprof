<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Model\DesignAtoms\CSSProperty;

/**
 * Class SizeShadow - класс для свойства CSS означающего указание тени для текста или блока
 */
class SizeShadow extends Size {

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
        if (!$value){
            $value = [
                'top' => '',
                'left' => '',
                'radius'  => '',
                'color' => '#000000FF',
            ];
        }
        parent::__construct($property, $title, $value, $data);
    }
}