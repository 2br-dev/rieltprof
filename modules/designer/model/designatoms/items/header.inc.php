<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Model\DesignAtoms\Items;

use \Designer\Model\DesignAtoms;
use \Designer\Model\DesignAtoms\CSSProperty;


/**
 * Class Text - класс обычного текстового элемента
 */
class Header extends DesignAtoms\AbstractAtom {
    protected $title = "Заголовок"; //Название компонента
    protected $tag   = "h1";//Тег с помощью которого будет формироваться содержимое
    protected $tags  = [ //Допустиые теги
        "h1",
        "h2",
        "h3",
        "h4",
        "h5",
        "h6",
        "p",
        "div",
        "span",
    ];
    protected $image     = "header.svg"; //Картинка компонента
    protected $html      = "Ваш заголовок"; //Картинка компонента
    protected $html_type = "inline"; //Тип компонента

    /**
     * Конструктор класса
     */
    function __construct()
    {
        parent::__construct();

        $this->addCSSPreset([
            new DesignAtoms\CSSPresets\Background(),
            new DesignAtoms\CSSPresets\Border()
        ]);
        $text_align = new CSSProperty\Select('text-align', t('Позиционирование'), 'left', [
            'left' => t('Слева'),
            'center' => t('Центр'),
            'right' => t('Справа'),
            'justify' => t('На всю ширину')
        ]);
        $text_align->setVisible(false);

        $this->addMaxWidthAndAlignSelfCSS();
        $this->addCSS([
            $text_align,
            new DesignAtoms\CSSProperty\Size('line-height', t('Высота строки'), '')
        ]);
    }

}