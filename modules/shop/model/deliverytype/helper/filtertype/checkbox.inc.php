<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\DeliveryType\Helper\FilterType;

/**
 * Класс отвечает за работу с фильтрами типа галочки
 */
class Checkbox extends AbstractFilter
{
    public $tpl = '%shop%/delivery/helper/filterstype/checkbox.tpl'; //Шаблон фильтра
    protected $value; //Значение истина

    /**
     * Конструктор выпадающего списка
     *
     * @param string $key - наименование ключа по которому будет фильтроваться
     * @param string $value - значение ключа, которое будет устанавливать в истину галочку
     * @param string $title - наименование фильтра
     * @param array $options - массив параметров
     */
    function __construct($key, $value, $title, $options = [])
    {
        $this->value = $value;
        parent::__construct($key, $title, $options);
    }

    /**
     * Возвращает значние являющиееся истиной для галочки
     */
    function getValue()
    {
        return $this->value;
    }
}