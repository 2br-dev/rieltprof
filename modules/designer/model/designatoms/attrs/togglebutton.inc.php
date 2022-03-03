<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Model\DesignAtoms\Attrs;

/**
 * Class ToggleButton - аттрибут типа переключения в виде кнопки
 */
class ToggleButton extends AbstractAttr {
    /**
     * AbstractCssProperty constructor.
     *
     * @param string $attribute - название аттрибута
     * @param string $title - имя аттрибута
     * @param mixed $value - значение аттрибута
     */
    function __construct($attribute, $title, $value = 0)
    {
        parent::__construct($attribute, $title, $value);
        $this->setOptions([
            t('Да'),
            t('Нет')
        ]);
    }
}