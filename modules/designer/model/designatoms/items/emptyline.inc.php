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
 * Class EmptyLine - класс пустого пространства
 */
class EmptyLine extends DesignAtoms\AbstractAtom {
    protected $title = "Пустота"; //Название компонента
    protected $tag   = "div";//Тег с помощью которого будет формироваться содержимое
    protected $image = "emptyline.svg"; //Картинка компонента

    /**
     * Конструктор класса
     */
    function __construct()
    {
        parent::__construct();

        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $this->addMaxWidthAndAlignSelfCSSToPreset($preset);

        $this->addCSSPreset([
            self::getBackgroundParamsSettings(),
            new DesignAtoms\CSSPresets\Border(),
            $preset
        ]);

        $height = new DesignAtoms\CSSProperty\Size('height', t('Высота'), '50px');
        $height->setVisible(false);
        $this->addCSS([
            $height,
        ]);
    }
}