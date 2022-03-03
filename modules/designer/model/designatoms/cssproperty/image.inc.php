<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Model\DesignAtoms\CSSProperty;

/**
 * Class Size - класс для свойства CSS задающего картинку
 */
class Image extends AbstractCssProperty {

    /**
     * Свойства CSS задающего цвет для отображения
     *
     * @param string $property - название элемента CSS свойства. Например: margin
     * @param string $title - название для отображения CSS свойства. Наприимер: Задний фон. Если пусто будет взято из имени элемента
     * @param string $value - значение CSS свойства
     * @param array $data - дополниельные параметры для передачи в вместе со свойством 'ключ' => 'значение'
     */
    function __construct($property = "background-image", $title = "Картинка заднего фона", $value = null, $data = [])
    {
        parent::__construct($property, $title, $value, $data);
    }
}