<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Orm\Type;

use RS\Orm\Type\VariableList\AbstractVariableListField;

/**
 * Тип - список с произвольным набором полей.
 */
class VariableList extends ArrayList
{
    protected $form_template = '%system%/coreobject/type/form/variable_list.tpl';
    protected $table_fields = [];

    /**
     * Устанавливает список полей таблицы
     *
     * @param AbstractVariableListField[] $fields
     * @return static
     */
    public function setTableFields($fields)
    {
        $this->table_fields = $fields;
        return $this;
    }

    /**
     * Возвращает список полей таблицы
     *
     * @return AbstractVariableListField[]
     */
    public function getTableFields()
    {
        return $this->table_fields;
    }
}
