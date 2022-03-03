<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Orm\Type;

class Mediumblob extends AbstractType
{
    protected
        $has_len = false,
        $php_type = 'string',
        $sql_notation = 'mediumblob',
        $auto_increment = false;

	public function validate($value)
    {
        return true;
    }
}  


