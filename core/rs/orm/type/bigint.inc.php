<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Orm\Type;

class Bigint extends AbstractType
{
    protected
        $php_type = 'integer',
        $sql_notation = 'bigint',
        $max_len = 11;
}  


