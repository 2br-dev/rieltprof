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
class Background extends AbstractCssPreset {

    /**
     * Пресет заднего фона
     *
     * @param string $title - название пресета
     */
    function __construct($title = 'Задний фон')
    {
        $background_position = new CSSProperty\Select('background-position', t('Позиция'), "top left");
        $background_position->setOptions([
            'top left' => t('Лево верх'),
            'bottom left' => t('Лево низ'),
            'top center' => t('Центр верх'),
            'center center' => t('Центр'),
            'bottom center' => t('Центр низ'),
            'left center' => t('Центр слева'),
            'right center' => t('Центр справа'),
            'top right' => t('Право верх'),
            'bottom right' => t('Право низ')
        ]);
        $background_repeat = new CSSProperty\Select('background-repeat', t('Повтор заднего фона'), 'inherit');
        $background_repeat->setOptions([
            'inherit' => t('По умолчанию'),
            'repeat' => t('Повторять везде'),
            'no-repeat' => t('Не повторять'),
            'repeat-x' => t('Повторять по горизонтали'),
            'repeat-y' => t('Повторять по вертикали')
        ]);
        $background_size = new CSSProperty\Select('background-size', t('Размер заднего фона'), 'auto');
        $background_size->setOptions([
            'auto' => t('По умолчанию'),
            'cover' => t('Обложка'),
            'contain' => t('Всегда внутри'),
        ]);
        $background_size->setSelectorType(CSSProperty\Select::SELECT_TYPE_SELECT_WITH_MY);
        $box_shadow = new CSSProperty\SizeShadow('box-shadow', t('Тень для блока'));
        $this->addCSS([
            new CSSProperty\Color('background-color', t('Цвет заднего фона'), ''),
            new CSSProperty\Image('background-image', t('Картинка заднего фона'),  ''),
            $background_position,
            $background_repeat,
            $background_size,
            $box_shadow
        ]);
        parent::__construct($title);
    }
}