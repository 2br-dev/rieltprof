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
 * Class Background - Пресет заднего фона
 */
class TextEdit extends AbstractCssPreset {

    /**
     * Пресет заднего фона
     *
     * @param string $title - название пресета
     */
    function __construct($title = 'Текст')
    {
        //Перечислим нужные свойства
        $font_family = new CSSProperty\Select('font-family', t('Шрифт'), "");
        $font_family->setOptions([
            '' => t('По умолчанию'),
            'Arial' => 'Arial',
            'Times New Roman' => 'Times New Roman',
            'sans-serif' => 'sans-serif',
            'Helvetica' => 'Helvetica',
        ]);
        $font_family->setSelectorType($font_family::SELECT_TYPE_SELECT_WITH_MY);
        $font_weight = new CSSProperty\Select('font-weight', t('Жирность'), "inherit");
        $font_weight->setOptions([
            'inherit' => t('По умолчанию'),
            'lighter' => t('Тонкий'),
            'normal'  => t('Обычный'),
            'bold'    => t('Жирный'),
            'bolder'  => t('Жирнее')
        ]);
        $text_shadow     = new CSSProperty\SizeShadow('text-shadow', t('Тень для текста'));
        $text_decoration = new CSSProperty\Select('text-decoration', t('Декорирование'));
        $text_decoration->setOptions([
            'inherit' => t('По умолчанию'),
            'none' => t('Нет'),
            'overline' => t('Линия сверху'),
            'underline' => t('Линия снизу'),
            'line-through' => t('Зачеркнутый текст')
        ]);



        $this->addCSS([
            $font_family,
            new CSSProperty\Size('font-size', t('Размер шрифта'), "12px"),
            $font_weight,
            new CSSProperty\Color('color', t('Цвет текста')),
            $text_shadow,
            new CSSProperty\Size('text-indent', t('Отступ слева'), "0px"),
            $text_decoration,
            new CSSProperty\Size('letter-spacing', t('Межбуквенный интервал'), ""),
            new CSSProperty\Size('line-height', t('Высота строки'), "")
        ]);
        parent::__construct($title);
    }
}