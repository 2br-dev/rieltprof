<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Html\Filter\Type;

class Date extends AbstractType
{
    public 
        $tpl = 'system/admin/html_elements/filter/type/date.tpl';
        
    protected
        $search_type = 'date';    
        
    /**
    * Возвращает выражение для поиска по дате
    */
    protected function where_date()
    {
        $compare = '=';
        if (!empty($this->prefilter_list)) 
        {
            foreach($this->prefilter_list as $prefilter){
                if ($prefilter->getKey() == 'type_'.$this->key) 
                {
                    $compare = isset($this->type_array_sql[$prefilter->getValue()]) ? $this->type_array_sql[$prefilter->getValue()] : '=';
                    break;
                }
            }
                
        }
        return "DATE({$this->getSqlKey()}) $compare  '{$this->escape($this->getValue())}'";
    }        
}