<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Orm\Type;

class Timestamp extends AbstractType
{
    protected
        $has_len = false,
        $php_type = 'integer',
        $sql_notation = 'timestamp';
}  


