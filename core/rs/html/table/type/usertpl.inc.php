<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Html\Table\Type;

/**
* Тип данных - пользовательский шаблон, в который передается весь текущий объект со всеми параметрами
*/
class Usertpl extends AbstractType
{
    function __construct($field, $title, $tpl, $property = null)
    {
        parent::__construct($field, $title, $property);
        $this->body_template = $tpl;
    }
}

