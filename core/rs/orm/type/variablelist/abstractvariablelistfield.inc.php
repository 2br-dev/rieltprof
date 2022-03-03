<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Orm\Type\VariableList;

abstract class AbstractVariableListField
{
    protected $name;
    protected $column_title;

    /**
     * AbstractVariableListField constructor.
     *
     * @param string $name - имя элемента
     * @param string $column_title - название колонки
     */
    public function __construct($name, $column_title)
    {
        $this->name = $name;
        $this->column_title = $column_title;
    }

    /**
     * Возвращает имя поля
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Возвращает название колонки
     *
     * @return string
     */
    public function getColumnTitle()
    {
        return $this->column_title;
    }

    /**
     * Возвращает html элемента
     *
     * @param string $field_name - имя ORM поля
     * @param string $row_index - индекс строки в таблице
     * @param mixed $value - значение
     * @return string
     */
    abstract public function getElementHtml($field_name, $row_index = '%index%', $value = null);
}
