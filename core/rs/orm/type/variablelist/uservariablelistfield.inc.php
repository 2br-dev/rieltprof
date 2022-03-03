<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Orm\Type\VariableList;

use RS\Orm\Type;
use RS\View\Engine;

class UserVariableListField extends AbstractVariableListField
{

    /**
     * Возвращает html элемента
     *
     * @param string $field_name - имя ORM поля
     * @param string $row_index - индекс строки в таблице
     * @param mixed $value - значение
     * @return string
     * @throws \SmartyException
     */
    public function getElementHtml($field_name, $row_index = '%index%', $value = null)
    {
        $user_field = new Type\User();
        $user_field->set($value);
        $user_field->setName("{$field_name}[$row_index][{$this->getName()}]");

        $view = new Engine();
        $view->assign([
            'field' => $user_field
        ]);

        return $view->fetch($user_field->getRenderTemplate());
    }
}