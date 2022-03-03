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
 * Class Text - класс обычного вставки картинки
 */
class Image extends DesignAtoms\AbstractAtom {
    protected $title = "Картинка"; //Название компонента
    protected $tag   = "IMG"; //Тег с помощью которого будет формироваться содержимое
    protected $image = "image.svg"; //Картинка компонента

    public static $css_for_wrapper = [
        "height"
    ];

    function __construct()
    {
        parent::__construct();

        $attr_js = new DesignAtoms\Attrs\TextArea('onclick', t('Javascript Код<br/>event - переменная с событием'));
        $attr_js->setHint(t('Будет выполнено при нажатии картинки'));

        $src = new DesignAtoms\Attrs\Text('src', t('Адрес картинки'));
        $src->setVisible(false);

        $original = new DesignAtoms\Attrs\Text('original', t('Оригинал картинки'));
        $original->setVisible(false);

        $this->setAttr([
            $src,
            $original,
            new DesignAtoms\Attrs\Link('href', t('Ссылка')),
            $attr_js
        ]);

        $width  = new DesignAtoms\CSSProperty\Size('width', t('Ширина'));
        $width->setVisible(false);

        $height = new DesignAtoms\CSSProperty\Size('height', t('Высота'));
        $height->setVisible(false);
        $this->addCSS([
            new DesignAtoms\CSSProperty\FlexAlignItems('align-self', t('Позиция'), 'center'),
            $width,
            $height
        ]);
    }

    /**
     * Возвращает массив стилей для обертки атома
     *
     * @param array $data - массив данных атома
     * @return array
     */
    public static function getAtomWrapperStyles($data)
    {
        $wrap_styles = parent::getAtomWrapperStyles($data);

        if (isset($wrap_styles['height']) && !empty($wrap_styles['height'])){ //Применим нужную высоты
            $wrap_styles['max-height'] = $wrap_styles['height'];
            unset($wrap_styles['height']);
        }
        return $wrap_styles;
    }

    /**
     * Действия перед удалением атома
     *
     * @param array $data - массив данных атома в виде массива
     */
    function beforeDelete($data)
    {
        //Удалим ненужные ресурсы
        if (isset($data['attrs']['src']['value'])){
            $file = \Setup::$ROOT.$data['attrs']['src']['value'];
            if (file_exists($file)){
                @unlink($file);
            }
        }
        if (isset($data['attrs']['original']['value'])){
            $file = \Setup::$ROOT.$data['attrs']['original']['value'];
            if (file_exists($file)){
                @unlink($file);
            }
        }
    }

    /**
     * Добавляет дополнительные аттрибуты элементу
     *
     * @param array $data - массив данных элемента
     * @param array $attrs - аттрибуты для изменения
     *
     * @return array
     */
    public static function addAdditionalAttributesToAtom(&$data, $attrs)
    {
        $attrs = parent::addAdditionalAttributesToAtom($data, $attrs);
        $attrs['src'] = $attrs['data-src'];
        return $attrs;
    }
}