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
 * Class Padding - Пресет внутренних отступов
 */
class Padding extends AbstractCssPreset {

    /**
     * Пресет заднего фона
     *
     * @param string $title - название пресета
     */
    function __construct($title = 'Отступы')
    {
        $this->addCSS([
            new CSSProperty\Size('padding-top', t('Внутренный отступ сверху'), '20px'),
            new CSSProperty\Size('padding-left', t('Внутренный отступ слева'), '20px'),
            new CSSProperty\Size('padding-bottom', t('Внутренный отступ снизу'), '20px'),
            new CSSProperty\Size('padding-right', t('Внутренный отступ справа'), '20px')
        ]);
        parent::__construct($title);
    }
}