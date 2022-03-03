<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Model\DesignAtoms\CSSProperty;

/**
 * Class Size - класс для свойства CSS означающего размер в четырех размерах верх, низ, лево, право, дополнительно устанавливает свойство units - px, rem, em, vh, vw
 */
class SizeFourDigits extends Size {

    /**
     * Свойства CSS означающее размер в четырех размерах верх, низ, лево, право, дополнительно устанавливает свойство units - px, rem, em, vh, vw
     *
     * @param string $property - название элемента CSS свойства. Например: margin
     * @param string $title - название для отображения CSS свойства. Наприимер: Задний фон. Если пусто будет взято из имени элемента
     * @param string $value - значение CSS свойства в формате array('top' => '0px', 'left' => '0px',' bottom' => '0px', 'right' => '0px')
     * @param array $data - дополниельные параметры для передачи в вместе со свойством 'ключ' => 'значение'
     */
    function __construct($property, $title = "", $value = null, $data = [])
    {
        if ($value === null || (is_array($value) && empty($value))){
            $value = [
                'top'    => '0px',
                'right'   => '0px',
                'bottom' => '0px',
                'left'  => '0px'
            ];
        }
        parent::__construct($property, $title, $value, $data);
    }
}