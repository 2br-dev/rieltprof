<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Model\DesignAtoms\Attrs;

/**
 * Class SelectFieldValueAsTree - аттрибут для выбора значения поля, атома используя дерево данных
 */
class SelectFieldValueAsTree extends AbstractAttr {

    /**
     * Конструктор класса
     *
     * @param string $atom_field - название поля атома, в которое запишется значение
     * @param string $title - имя поля атома
     * @param string $url_to_data - url с данными для получения дерева
     */
    function __construct($atom_field, $title, $url_to_data)
    {
        $this->setAdditionalDataByKey('param_name', 'root');
        parent::__construct($atom_field, $title, $url_to_data);
    }

    /**
     * Устанавливает имя параметра, в котором будет передаваться идентификатор корневого элемета
     *
     * @param string $param_name - название параметра, которое будет использоваться для запроса для запроса
     *
     * @return $this
     */
    function setParamNameForRoot($param_name)
    {
        $this->setAdditionalDataByKey('param_name', $param_name);
        return $this;
    }
}