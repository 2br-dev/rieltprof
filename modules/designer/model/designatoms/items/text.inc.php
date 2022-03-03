<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Model\DesignAtoms\Items;

use \Designer\Model\DesignAtoms;
use \Designer\Model\DesignAtoms\CSSPresets;

/**
 * Class Text - класс обычного текстового элемента
 */
class Text extends DesignAtoms\AbstractAtom {
    protected $title = "Текст"; //Название компонента
    protected $tag   = "div"; //Тег с помощью которого будет формироваться содержимое
    protected $tags  = ['div', 'p']; //Тег с помощью которого будет формироваться содержимое
    protected $image = "text.svg"; //Картинка компонента
    protected $html  = "<p>Щелкните два раза мышкой для редактирования</p>"; //Html компонента
    protected $html_type = "inline"; //Картинка компонента


    /**
     * Конструктор класса
     */
    function __construct()
    {
        parent::__construct();

        $background = new CSSPresets\Background();
        $this->addCSSPreset([
            $background,
            new DesignAtoms\CSSPresets\Border()
        ]);

        $this->addMaxWidthAndAlignSelfCSS();
        $this->addCSS([
            new DesignAtoms\CSSProperty\Size('line-height', t('Высота строки'), '')
        ]);
    }

}