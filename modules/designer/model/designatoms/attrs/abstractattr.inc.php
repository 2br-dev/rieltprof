<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Model\DesignAtoms\Attrs;


use Designer\Model\DesignAtoms\CSSProperty\AbstractCssProperty;

/**
 * Class AbstractCssPresets - абстракный класс для группы свойств CSS
 */
abstract class AbstractAttr{

    protected $name    = ""; //Название аттрибута
    protected $title   = ""; //Имя аттрибута
    protected $hint    = ""; //Подсказка
    protected $value   = ""; //Значение по умолчанию
    protected $visible = true;
    protected $data    = [];
    protected $options = []; //Дополнительные параметры
    protected $debug_event = null;


    /**
     * AbstractCssProperty constructor.
     *
     * @param string $attribute - название аттрибута
     * @param string $title - имя аттрибута
     * @param mixed $value - значение аттрибута
     */
    function __construct($attribute, $title, $value = "")
    {
        $this->setName($attribute);
        $this->setTitle($title);
        $this->setValue($value);
    }

    /**
     * Возращает имя аттрибута
     *
     * @return string
     */
    function getTitle()
    {
        return $this->title;
    }

    /**
     * Установка имя аттрибута
     *
     * @param $title - имя аттрибута
     * @return $this
     */
    function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Возращает название атррибута
     *
     * @return string
     */
    function getName()
    {
        return $this->name;
    }

    /**
     * Установка названия
     *
     * @param $name - название аттрибута
     * @return $this
     */
    function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Возращает значение аттрибута
     *
     * @return mixed
     */
    function getValue()
    {
        return $this->value;
    }

    /**
     * Установка значения видимости
     *
     * @param $value - название аттрибута
     * @return $this
     */
    function setValue($value)
    {
        $this->value = $value;
        return $this;
    }


    /**
     * Возращает значение видимости аттрибута
     *
     * @return bool
     */
    function getVisible()
    {
        return $this->visible;
    }

    /**
     * Установка значения подсказки
     *
     * @param $value - название подсказки
     * @return $this
     */
    function setHint($value)
    {
        $this->hint = $value;
        return $this;
    }


    /**
     * Возращает значение подсказки
     *
     * @return string
     */
    function getHint()
    {
        return $this->hint;
    }

    /**
     * Установка видимости
     *
     * @param boolean $is_visible - название аттрибута
     * @return $this
     */
    function setVisible($is_visible)
    {
        $this->visible = $is_visible;
        return $this;
    }

    /**
     * Возращает значение типа аттрибута
     *
     * @return string
     */
    function getType()
    {
        $class_path = explode("\\", get_class($this));
        return mb_strtolower(array_pop($class_path));
    }

    /**
     * Устанавливает дополнительные значения данных по ключу
     *
     * @param string $key - ключ
     * @param mixed $value - значение
     * @return $this
     */
    function setAdditionalDataByKey($key, $value)
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * Получает дополнительные значения по ключу. Если ключ не указывать, возващет все доп свойства. Если свойства нет, возвращает false
     *
     * @param string $key - ключ свойства
     * @return mixed
     */
    function getAdditionalDataByKey($key = null)
    {
        if (!$key){
            return $this->data;
        }
        if (isset($this->data[$key])){
            return $this->data[$key];
        }
        return false;
    }

    /**
     * Устанавливает дополнительные значения данных опций
     *
     * @param mixed $values - значения опций
     *
     * @return $this
     */
    function setOptions($values = [])
    {
        $this->data['options'] = $values;
        return $this;
    }

    /**
     * Получает дополнительные значения по ключу. Если ключ не указывать, возващет все доп свойства. Если свойства нет, возвращает false
     *
     * @return array
     */
    function getOptions()
    {
        return isset($this->data['options']) ? $this->data['options'] : [];
    }

    /**
     * Вызывает событие в публичной части в режиме правки при изменении свойства
     *
     * @param string $eventName - идентификатор события
     */
    function initDebugEventOnChange($eventName)
    {
        $this->debug_event = $eventName;
    }

    /**
     * Возвращает данные для хранлища для публичной части
     *
     * @return array
     */
    function getInfo()
    {
        $additional_data = $this->getAdditionalDataByKey();
        $data = [
            'title'   => $this->getTitle(),
            'hint'    => $this->getHint(),
            'name'    => $this->getName(),
            'type'    => $this->getType(),
            'value'   => $this->getValue(),
            'visible' => $this->getVisible()
        ];
        if (!empty($additional_data)){
            $data['data'] = $additional_data;
        }
        if ($this->debug_event){
            $data['debug_event'] = $this->debug_event;
        }
        return $data;
    }
}