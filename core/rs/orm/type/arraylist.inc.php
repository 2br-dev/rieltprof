<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Orm\Type;

/**
* Тип - массив. пока только run-time тип.
*/
class ArrayList extends AbstractType
{
    protected
        $php_type = "array",
        $runtime = true;
    
    function getFormName()
    {
    	//из формы должно возвращаться значение - массив.
        if($this->form_name){
            return $this->form_name;
        }
        return $this->array_wrap_name ? $this->array_wrap_name.'['.$this->name.'][]' : $this->name.'[]';
	}
    
    /**
    * Проверяет значение $value и подставляет значение по-умолчанию, если таковое действие требуется
    * 
    * @param mixed $value
    * @return mixed $value
    */
    function checkDefaultRequestValue($value)
    {
        return ($value === null) ? [] : $value;
    }    
    
}

