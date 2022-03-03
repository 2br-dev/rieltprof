<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Html\Table\Type;

class Userfunc extends AbstractType
{
    protected $body_template = 'system/admin/html_elements/table/coltype/string.tpl';
    /** @var callable */
    protected $userfunc;

    function __construct($field, $title, $func, $property = null)
    {
        parent::__construct($field, $title, $property);
        $this->userfunc = $func;
    }

    /**
     * Возвращает результат выполнения пользовательской функции
     *
     * @return mixed
     */
    function getValue()
    {
        $userfunc = $this->userfunc;
        return $userfunc($this->value, $this);
    }
}
