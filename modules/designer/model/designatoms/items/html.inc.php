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
 * Class Html - класс атома произвольный HTML
 */
class Html extends DesignAtoms\AbstractAtom {
    protected $title        = "HTML"; //Название компонента
    protected $tag          = "div";//Тег с помощью которого будет формироваться содержимое
    protected $image        = "html.svg"; //HTML

    protected $html         = '
    <br/> 
    <br/> 
    <p style="text-align: center; font-size: 14px">Наведитесь на блок и<br/> 
    нажмите на область редактирования,<br/> 
    чтобы изменить содержимое</p>
    <br/> 
    <br/> '; //Картинка компонента
    protected $html_visible = false; //Видимость содержимого

    /**
     * Конструктор класса
     */
    function __construct()
    {
        parent::__construct();

        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $this->addMaxWidthAndAlignSelfCSSToPreset($preset);
        $preset->addCSS([
            new DesignAtoms\CSSProperty\SizeFourDigits('padding', t('Внутренний отступ')),
        ]);
        $this->addCSSPreset([
            new DesignAtoms\CSSPresets\Background(),
            new DesignAtoms\CSSPresets\Border(),
            $preset
        ]);
    }

}