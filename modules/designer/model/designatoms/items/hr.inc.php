<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Model\DesignAtoms\Items;

use \Designer\Model\DesignAtoms;

/**
 * Class Hr - класс горизонтальной линии
 */
class Hr extends DesignAtoms\AbstractAtom {
    protected $title = "Линия"; //Название компонента
    protected $tag   = "hr";//Тег с помощью которого будет формироваться содержимое
    protected $image = "hr.svg"; //Картинка компонента

    public static $css_for_wrapper = [
        "margin-top",
        "margin-bottom",
    ];

    /**
     * Конструктор класса
     */
    function __construct()
    {
        parent::__construct();

        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $this->addMaxWidthAndAlignSelfCSSToPreset($preset);
        $preset->addCSS([
            new DesignAtoms\CSSProperty\Size('height', t('Высота'), '1px'),
            new DesignAtoms\CSSProperty\SizeFourDigits('padding', t('Внутренний отступ')),
            new DesignAtoms\CSSProperty\Size('margin-left', t('Внешний отступ слева')),
            new DesignAtoms\CSSProperty\Size('margin-right', t('Внешний отступ справа')),
            new DesignAtoms\CSSProperty\Size('margin-bottom', t('Внешний отступ снизу'), '20px'),
        ]);

        $this->addCSSPreset([
            self::getBackgroundParamsSettings('#FF0000FF'),
            new DesignAtoms\CSSPresets\Border(),
            $preset
        ]);
        $margin_top = $this->getCSS('margin-top');
        $margin_top->setValue('20px');
    }

}