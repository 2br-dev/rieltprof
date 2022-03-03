<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\DeliveryType\Helper\FilterType;

/**
 * Класс отвечает за работу с фильтрами
 */
abstract class AbstractFilter
{
    protected $tpl = ''; //Шаблон фильтра
    protected $id; //id фильтра
    protected $title;
    protected $attr;  //Массив аттрибутов
    protected $default;  //Значение по умолчанию
    protected $options;  //Массив параметров для установки
    protected $abstract_tpl = '%shop%/delivery/helper/filterstype/wrapper.tpl';  //Оберка фильтров

    /**
     * Конструктор абстрактного класса фильтров
     *
     * @param string $key - наименование ключа по которому будет фильтроваться
     * @param string $title - наименование фильтра
     * @param array $options - массив параметров
     */
    function __construct($key, $title, $options = [])
    {
        $this->key   = $key;
        $this->title = $title;

        $this->options = $options;
        foreach($options as $option => $value){
            $prefix = 'set';
            $method_name = $prefix.$option;
            if (method_exists($this, $method_name)) {
                $this->$method_name($value);
            }
        }
    }

    /**
     * Возвращает название фильтра
     *
     * @return string
     */
    function getTitle()
    {
        return $this->title;
    }

    /**
     * Установка названия фильтра
     *
     * @param string $title
     */
    function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Возвращает ключ фильтра пункта выдачи
     *
     * @return string
     */
    function getKey()
    {
        return $this->key;
    }

    /**
     * Установка ключа фильтра пункта выдачи
     *
     * @param string $key
     */
    function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * Устанавливает аттрибуты для dom-элмента формы.
     *
     * @param array $attr - Массив, где ключ - это аттрибут, значение - значение аттрибута
     * @return AbstractFilter
     */
    function setAttr(array $attr)
    {
        $this->attr = array_merge_recursive($this->attr, $attr);
        return $this;
    }

    /**
     * Возвращает массив аттрибутов фильтра
     *
     * @return array
     */
    function getAttr()
    {
        return $this->attr;
    }

    /**
     * Устанавливает значение по умолчанию
     *
     * @return AbstractFilter
     */
    function setDefault($default)
    {
        $this->default = $default;
    }

    /**
     * Возвращает значение по умолчанию
     *
     * @return mixed
     */
    function getDefault()
    {
        return $this->default;
    }

    /**
     * Устанавливает идентификатор фильтра доставки
     *
     * @param string $id - id доставки
     */
    function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Возвращает идентификатор фильтра доставки
     *
     * @return mixed
     */
    function getId()
    {
        return $this->id;
    }

    /**
     * Возвращает элемент формы фильтра пункта выдачи
     *
     * @param int $delivery_id - id доставки
     * @param int $i - номер фильтра
     * @return string
     * @throws \SmartyException
     */
    function getView($delivery_id = 0, $i = 0)
    {
        $this->setId('deliveryFilter-'.$delivery_id."-".$i);
        $wrap = new \RS\View\Engine();
        $wrap->assign([
            'fitem' => $this,
            'delivery_id' => $delivery_id,
            'i' => $i
        ]);
        return $wrap->fetch($this->abstract_tpl);
    }
}