<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Orm\Type;

class Double extends AbstractType
{
    protected
        $has_len = false,
        $php_type = 'double',
        $sql_notation = "double";
    
}  


