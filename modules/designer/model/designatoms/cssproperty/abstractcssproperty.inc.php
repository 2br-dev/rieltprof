<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Model\DesignAtoms\CSSProperty;

/**
 * Class AbstractCssProperty - абстракный класс для свойства CSS
 */
abstract class AbstractCssProperty{

    private $property; //Само CSS свойство
    protected $visible = true; //Свойство видимое
    private $data      = []; //Допольнительные свойства CSS характеристики
    protected $debug_event = null;

    /**
     * AbstractCssProperty constructor.
     * @param mixed $property - название элемента CSS свойства. Например: margin
     * @param string $title - название для отображения CSS свойства. Наприимер: Задний фон. Если пусто будет взято из имени элемента
     * @param mixed $value - значение CSS свойства
     * @param array $data - дополниельные параметры для передачи в вместе со свойством 'ключ' => 'значение'
     */
    function __construct($property, $title, $value = null, $data = [])
    {
        $this->setPropertyName($property);
        $this->setData($data);
        $this->setTitle(!empty($title) ? $title : $property);
        $this->setValue($value !== null ? $value : "");
    }

    /**
     * Возвращает название свойства CSS
     *
     * @return string
     */
    function getPropertyName()
    {
        return $this->property;
    }

    /**
     * Устанавливает название CSS свойства
     *
     * @param string $title - название свойства
     * @return $this
     */
    function setPropertyName($title)
    {
        $this->property = $title;
        return $this;
    }

    /**
     * Возвращает название CSS свойства для отображения
     *
     * @return string
     */
    function getTitle()
    {
        return $this->getData('title');
    }

    /**
     * Устанавливает название CSS свойства для отображения
     *
     * @param string $title - название свойства
     * @return $this
     */
    function setTitle($title)
    {
        $this->setDataByKey('title', $title);
        return $this;
    }

    /**
     * Возвращает значение CSS свойства
     *
     * @return string
     */
    function getValue()
    {
        return $this->getData('value');
    }

    /**
     * Устанавливает видимость свойства
     *
     * @param boolean $flag - название свойства
     * @return $this
     */
    function setVisible(bool $flag)
    {
        $this->visible = $flag;
        return $this;
    }

    /**
     * Возвращает значение CSS свойства
     *
     * @return boolean
     */
    function getVisible()
    {
        return $this->visible;
    }

    /**
     * Устанавливает значение CSS свойства
     *
     * @param mixed $value - значение свойства
     * @return $this
     */
    function setValue($value)
    {
        $this->setDataByKey('value', $value);
        return $this;
    }

    /**
     * Устанавливает данные этого свойства через массив 'ключ' => 'значение'
     *
     * @param array $data - массив 'ключ' => 'значение'
     * @return array|string|null
     */
    function setData($data = [])
    {
        $this->data = $data + $this->data;
    }

    /**
     * Устанавливает значения данных по ключу
     *
     * @param string $key - ключ
     * @param mixed $value - значение
     * @return $this
     */
    function setDataByKey($key, $value)
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * Установка значения подсказки
     *
     * @param $value - название подсказки
     * @return $this
     */
    function setHint($value)
    {
        $this->setDataByKey('hint', $value);
        return $this;
    }


    /**
     * Возращает значение подсказки
     *
     * @return string
     */
    function getHint()
    {
        return $this->getData('hint');
    }

    /**
     * Устанавливает дополнительные значения данных по ключу
     *
     * @param string $key - ключ
     * @param mixed $value - значение
     * @return $this
     */
    function setAdditionDataByKey($key, $value)
    {
        $this->data['data'][$key] = $value;
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
            return isset($this->data['data']) ? $this->data['data'] : [];
        }
        if (isset($this->data['data'][$key])){
            return $this->data['data'][$key];
        }
        return false;
    }

    /**
     * Возвращает дополниельные данные свойства, если передан ключ, то значение нужного свойства, если свойство не найдено, то возвращает null
     *
     * @param string $key - ключ свойства
     * @return array|string|null
     */
    function getData($key = null)
    {
        if ($key){
            return isset($this->data[$key]) ? $this->data[$key] : null;
        }
        return $this->data;
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
     * Вызывает событие в публичной части в режиме правки при изменении свойства
     *
     * @param string $eventName - идентификатор события
     */
    function initDebugEventOnChange($eventName)
    {
        $this->debug_event = $eventName;
    }

    /**
     * Возвращает информацию по CSS свойства для хранилища в публичной части
     *
     * @return array
     */
    function getPropertyInfo()
    {
        $value = $this->getValue();
        $data = [
            'title'   => $this->getTitle(),
            'hint'    => $this->getHint(),
            'name'    => $this->getPropertyName(),
            'type'    => $this->getType(),
            'visible' => $this->getVisible(),
            'value'   => $value,
            'hover'   => is_array($value) ? [] : "",
        ];


        if ($this->debug_event){
            $data['debug_event'] = $this->debug_event;
        }

        return $this->getData() + $data;
    }

    /**
     * Удаляет данные по ключу
     *
     * @param string $key - ключ данных
     * @return $this
     */
    function deleteDataByKey($key)
    {
        unset($this->data[$key]);
        return $this;
    }

    /**
     * Очищает полностью свойство со всеми данными
     */
    function clearData()
    {
        $this->data = [];
        return $this;
    }
}