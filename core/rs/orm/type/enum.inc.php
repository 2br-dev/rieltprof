<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Orm\Type;

class Enum extends AbstractType
{
    protected
        $value_list,
        $has_len = false,
        $php_type = 'string',
        $sql_notation = 'enum',
        $max_len = 255;
        
    function __construct(array $value_list, array $options = null)
    {        
        $this->setValueList($value_list);
        parent::__construct($options);
    }
    
    /**
    * Устанавливает список значений для enum поля
    * 
    * @param array $value_list
    * @return void
    */
    function setValueList($value_list)
    {
        $this->value_list = $value_list;
    }
    
    /**
    * Возвращает список значений для enum поля
    * 
    * @return array
    */
    function getValueList()
    {
        return $this->value_list;
    }

    /**
    * Возвращает строковое значение параметра, которое подставляется после SQL - типа
    * Для большинства типов это его длина, например INT(11), VARCHAR(255), 
    * но для некоторых типов это могут быть другие значения, например: ENUM('Y', 'N') или DECIMAL(10,2)
    * 
    * @return string 
    */    
    function getSQLTypeParameter()
    {
        return "(".implode(',', \RS\Helper\Tools::arrayQuote($this->getValueList())).")";
    }
    
    /**
    * Проверяет значение $value и подставляет значение по-умолчанию, если таковое действие требуется
    * 
    * @param mixed $value
    * @return mixed $value
    */
    function checkDefaultRequestValue($value)
    {
        if (in_array($value, $this->value_list)) {
            return $value;
        } elseif ($this->default !== null && !$this->is_default_func) {
            return $this->default;
        } else {
            return null;
        }
    }     
        
}

