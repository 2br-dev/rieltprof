<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Html\Table\Type;

/**
 * Тип колонки - пользователь. Отображает ФИО пользователя и его ID
 */
class ObjectSelect extends AbstractType
{
    protected $body_template = 'system/admin/html_elements/table/coltype/object_select.tpl';

    /**
     * Возвращает наименование объекта
     *
     * @return string
     */
    function getObjectName(): string
    {
        $type_field = $this->getRow()['__'.$this->getField()];
        return $type_field->getPublicTitle();
    }
}
