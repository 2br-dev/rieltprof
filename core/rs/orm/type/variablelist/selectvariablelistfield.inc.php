<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Orm\Type\VariableList;

class SelectVariableListField extends AbstractVariableListField
{
    protected $first_list;
    protected $list;
    protected $values = null;

    /**
     * SelectVariableListField constructor.
     *
     * @param string $name - атрибут name поля
     * @param string $column_title - название колонки
     * @param string[] $first_list - изначальный список значений
     * @param callable $list - функция для полученя список значений
     */
    public function __construct($name, $column_title, $first_list, $list = null)
    {
        parent::__construct($name, $column_title);
        $this->first_list = $first_list;
        $this->list = $list;
    }

    /**
     * Возвращает список возможных значений
     *
     * @return string[]
     */
    protected function getValues()
    {
        if ($this->values === null) {
            $this->values = $this->first_list;
            if ($this->list) {
                $list = call_user_func($this->list);
                foreach ($list as $key=>$value) {
                    $this->values[$key] = $value;
                }
            }
        }
        return $this->values;
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
        $result = "<select name='{$field_name}[$row_index][{$this->getName()}]'>";
        foreach ($this->getValues() as $key=>$item) {
            $selected = ($value == $key) ? 'selected' : '';
            $result .= "<option value='$key' $selected>$item</option>";
        }
        $result .= '</select>';

        return $result;
    }
}
