<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Model\DesignAtoms\CSSPresets;

use \Designer\Model\DesignAtoms\CSSProperty;

/**
 * Class Border - Пресет границ блока
 */
class Border extends AbstractCssPreset {

    /**
     * Пресет заднего фона
     *
     * @param string $title - название пресета
     */
    function __construct($title = 'Границы')
    {
        //Перечислим нужные свойства
        $border        = new CSSProperty\SizeFourDigitsForBorder('border', t('Граница вокруг'));
        $outline       = new CSSProperty\SizeForOutline('outline', t('Внешняя граница'));
        $border_radius = new CSSProperty\SizeFourDigits('border-radius', t('Радиус границы'));
        $border_radius->setAdditionDataByKey('units', [
            'px',
            '%'
        ]);

        $this->addCSS([
            $border,
            $outline,
            $border_radius
        ]);
        parent::__construct($title);
    }
}