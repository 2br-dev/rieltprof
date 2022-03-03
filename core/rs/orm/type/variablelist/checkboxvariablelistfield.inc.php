<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Orm\Type\VariableList;

class CheckboxVariableListField extends AbstractVariableListField
{
    protected $on_value;
    /**
     * SelectVariableListField constructor.
     *
     * @param string $name - атрибут name поля
     * @param string $column_title - название колонки
     * @param string[] $first_list - изначальный список значений
     * @param callable $list - функция для полученя список значений
     */
    public function __construct($name, $column_title, $on_value = 1)
    {
        parent::__construct($name, $column_title);
        $this->on_value = $on_value;
    }

    /**
     * Возвращает html элемента
     *
     * @param string $field_name - имя ORM поля
     * @param string $row_index - индекс строки в таблице
     * @param mixed $value - значение
     * @return string
     */
    public function getElementHtml($field_name, $row_index = '%index%', $value = null)
    {
        return "<input type='checkbox' name='{$field_name}[$row_index][{$this->getName()}]' value='{$this->on_value}'".($value == $this->on_value ? 'checked' : '').">";
    }
}