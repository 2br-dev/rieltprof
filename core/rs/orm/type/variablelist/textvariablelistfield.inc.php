<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Orm\Type\VariableList;

class TextVariableListField extends AbstractVariableListField
{
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
        return "<input type='text' name='{$field_name}[$row_index][{$this->getName()}]' value='$value'>";
    }
}
