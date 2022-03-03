<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Orm\Type;

class Decimal extends AbstractType
{
    protected 
        $max_len = 10,
        $decimal = 0,
        $php_type = 'float',
        $sql_notation = "decimal";

    /**
     * Возвращает значение поля в базе по-умолчанию
     *
     * @param bool $db_format - привести результат к хранимому в БД виду
     * @return mixed
     */
    public function getDefault($db_format = false)
    {
        $result = $this->default;
        if ($db_format && !in_array(gettype($result), ['NULL', 'bool', 'string'])) {
            $result = number_format($result, $this->decimal, '.', '');
        }
        return $result;
    }
}
