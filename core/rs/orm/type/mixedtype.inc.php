<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Orm\Type;

/**
* Тип - любое значение. только run-time тип.
*/
class MixedType extends AbstractType
{
    protected
        $php_type = '',
        $vis_form = false,
        $runtime = true;
}
