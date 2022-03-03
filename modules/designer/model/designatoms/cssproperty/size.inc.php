<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Model\DesignAtoms\CSSProperty;

/**
 * Class Size - класс для свойства CSS означающего размер, дополнительно устанавливает свойство units - px, rem, em, vh, vw, %
 */
class Size extends AbstractCssProperty {


    protected $units = [ //Единицы измерения
        'px', 'rem', 'em', 'vh', 'vw', '%'
    ];
    protected $sizes = [ //Размеры разрешений
        'xl', 'lg', 'md', 'sm', 'xs'
    ];

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
        //Установим предварительные данные
        $this->setAdditionDataByKey('units', $this->units);
        $this->setAdditionDataByKey('sizes', $this->sizes);
        parent::__construct($property, $title, $value, $data);
    }
}