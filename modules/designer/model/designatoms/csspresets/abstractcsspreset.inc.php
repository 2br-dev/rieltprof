<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Model\DesignAtoms\CSSPresets;

use Designer\Model\DesignAtoms\AbstractAtom;
use \Designer\Model\DesignAtoms\CSSProperty;

/**
 * Class AbstractCssPresets - абстракный класс для группы свойств CSS
 */
class AbstractCssPreset{

    private $title = ""; //Название пресета

    /**
     * @var CSSProperty\AbstractCssProperty[] $css
     */
    private $css = []; //CSS cвойства компонента для редактирования

    /**
     * AbstractCssProperty constructor.
     *
     * @param string $title - название пресета
     */
    function __construct($title = "Элемент")
    {
        $this->setTitle($title);
    }

    /**
     * Возращает заголовок пресета
     *
     * @return string
     */
    function getTitle()
    {
        return $this->title;
    }

    /**
     * Установка заголовка пресета
     *
     * @param $title
     */
    function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Добавляет CSS свойство атома
     *
     * @param CSSProperty\AbstractCssProperty|CSSProperty\AbstractCssProperty[] $css_property - объект CSS характеристики или массив характеристик для установки
     *
     * @return $this
     */
    function addCSS($css_property)
    {
        if (is_array($css_property)){
            foreach ($css_property as $css){
                $this->css[] = $css;
            }
        }else{
            $this->css[] = $css_property;
        }
        return $this;
    }

    /**
     * Устанавливает для группы CSS свойств, событие, которое нужно вызвать
     *
     * @param array $css_keys - массив ключей CSS свойств
     * @param string $event - событие которое будет назначаться
     * @return $this
     */
    function setGroupDebugEvent($css_keys, $event)
    {
        if (!empty($this->css)){
            foreach ($this->css as $css){
                /**
                 * @var CSSProperty\AbstractCssProperty $css
                 */
                $name = $css->getPropertyName();
                if (in_array($name, $css_keys)){
                    $css->initDebugEventOnChange($event);
                }
            }
        }
        return $this;
    }

    /**
     * Добавляет внешний и внутренний отступ к настройкам атома
     *
     * @param array $padding - массив внутренних отступов
     * @param array $margin - массив внешних отступов
     *
     * @return $this
     */
    function addMarginAndPaddingCSS($padding = [], $margin = [])
    {
        return $this->addCSS([
            new CSSProperty\SizeFourDigits('padding', t('Внутренний отступ'), $padding),
            new CSSProperty\SizeFourDigits('margin', t('Внешний отступ'), $margin)
        ]);
    }

    /**
     * Устанавливает значения свойств входящих в пресет в виде массив "ключ"=>"значение" .
     *
     * @param array $defaults - массив ключ=>значение, который перегружает значения у свойств в пресете
     */
    function setDefaults($defaults = [])
    {
        $css = $this->getCSS();
        foreach ($defaults as $css_key=>$css_value){
            foreach ($css as $item_css){
                $prop_name = $item_css->getPropertyName();

                if ($css_key == $prop_name){
                    $item_css->setValue($css_value);
                }
            }
        }
    }

    /**
     * Возвращает CSS свойство по ключу. Если ключ не указан, то возвращает все css свойства
     *
     * @param string $title - название CSS свойства
     *
     * @return CSSProperty\AbstractCssProperty|CSSProperty\AbstractCssProperty[]|null
     */
    function getCSS($title = null)
    {
        if (!$title){
            return $this->css;
        }

        foreach ($this->css as $css){
            /**
             * @var CSSProperty\AbstractCssProperty $css
             */
            if ($title == $css->getPropertyName()){
                return $css;
            }
        }
        return null;
    }

    /**
     * Возвращает данные CSS для импорта
     *
     * @return array
     */
    function getCSSData()
    {
        $arr = [];
        if (!empty($this->css)){
            foreach ($this->css as $css){
                $property_info = $css->getPropertyInfo();
                $arr[$property_info['name']] = $property_info;//Добавляем свойство
            }
        }
        return $arr;
    }

    /**
     * Возвращает данные для хранлища публичной части
     *
     * @return array
     */
    function getData()
    {
        $explodeCSSPreset = explode("\\", get_class($this));
        $data = [
            'title' => $this->getTitle(),
            'type' => mb_strtolower(array_pop($explodeCSSPreset)),
            'css' => [],
        ];
        if (!empty($this->css)){
            foreach ($this->css as $css){
                $property_info = $css->getPropertyInfo();
                $data['css'][] = $property_info['name'];//Добавляем свойство
            }
        }
        return $data;
    }
}