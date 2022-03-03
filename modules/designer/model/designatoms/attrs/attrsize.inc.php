<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Model\DesignAtoms\Attrs;

/**
 * Class Size - аттрибут типа размер
 */
class AttrSize extends AbstractAttr {
    protected $units = [ //Единицы измерения
        'px', 'rem', 'em', 'vh', 'vw'
    ];
    protected $sizes = [ //Размеры разрешений
        'xl', 'lg', 'md', 'sm', 'xs'
    ];

    /**
     * AbstractCssProperty constructor.
     *
     * @param string $attribute - название аттрибута
     * @param string $title - имя аттрибута
     * @param mixed $value - значение аттрибута
     */
    function __construct($attribute, $title, $value = "")
    {
        //Установим предварительные данные
        $this->setAdditionalDataByKey('units', $this->units);
        $this->setAdditionalDataByKey('sizes', $this->sizes);
        parent::__construct($attribute, $title, $value);
    }
}