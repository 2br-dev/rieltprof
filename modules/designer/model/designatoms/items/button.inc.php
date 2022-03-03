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
 * Class Button - класс кнопки
 */
class Button extends DesignAtoms\AbstractAtom {
    protected $title = "Кнопка"; //Название компонента
    protected $tag = "button";//Тег с помощью которого будет формироваться содержимое
    protected $tags   = [ //Допустиые теги
        "button" => 'Кнопка'
    ];
    protected $image        = "button.svg"; //Картинка компонента
    protected $html         = "Кнопка"; //HTML
    protected $html_type    = "inline"; //Тип компонента
    protected $html_visible = true; //Видимость содержимого

    public static $css_for_wrapper = [
        "justify-content",
    ];

    /**
     * Конструктор класса
     */
    function __construct()
    {
        parent::__construct();

        $this->addCSSPreset([
            self::getTextEditParamsSettings('14px', '#FFFFFFFF'),
            self::getBackgroundParamsSettings('#000000FF'),
            new DesignAtoms\CSSPresets\Border()
        ]);

        $this->addCSS([
            new DesignAtoms\CSSProperty\FlexAlignItems('align-self', t('Позиция'), 'center'),
            new DesignAtoms\CSSProperty\Size('width', t('Ширина'), '200px'),
            new DesignAtoms\CSSProperty\Size('line-height', t('Высота'), '40px'),
            new DesignAtoms\CSSProperty\Size('padding-left', t('Отступ слева'), '20px'),
            new DesignAtoms\CSSProperty\Size('padding-right', t('Отступ справа'), '20px'),
        ]);

        $attr_js = new \Designer\Model\DesignAtoms\Attrs\TextArea('onclick', t('Javascript Код:<br/><b>event</b> - переменная с событием'));
        $attr_js->setHint(t('Будет выполнено при нажатии кнопки'));

        $this->setAttr([
            new \Designer\Model\DesignAtoms\Attrs\Link('href', t('Ссылка')),
            $attr_js
        ]);
    }

}