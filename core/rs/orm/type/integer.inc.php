<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Orm\Type;

class Integer extends AbstractType
{
    protected
        $php_type = 'integer',
        $sql_notation = 'int',
        $max_len = 11;
}  


