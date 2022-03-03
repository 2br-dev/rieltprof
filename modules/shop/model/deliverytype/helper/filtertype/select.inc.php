<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\DeliveryType\Helper\FilterType;

/**
 * Класс отвечает за работу с фильтрами в виде выпадающего списка
 */
class Select extends AbstractFilter
{
    public $tpl = '%shop%/delivery/helper/filterstype/select.tpl'; //Шаблон фильтра

    protected
        $list;

    /**
     * Конструктор выпадающего списка
     *
     * @param string $key - наименование ключа по которому будет фильтроваться
     * @param string $title - наименование фильтра
     * @param array $list - массив значений списка
     * @param array $options - массив параметров
     */
    function __construct($key, $title, $list, $options = [])
    {
        $this->list = $list;
        parent::__construct($key, $title, $options);
    }

    /**
     * Возвращает список значений
     *
     * @return mixed
     */
    function getList()
    {
        return $this->list;
    }
}