<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Html\Filter\Type;

use RS\Http\Request;

/**
* Диапозон дат в фильтрах
*/ 
class DateRange extends AbstractType
{
    public 
        $tpl = 'system/admin/html_elements/filter/type/daterange.tpl';
        
    protected
        $search_type = 'daterange';    
        
    /**
    * Возвращает выражение для поиска по дате
    */
    protected function where_daterange()
    {
        $values = $this->getValue();//Значения фильтра
        $from = isset($values['from']) ? $values['from'] : ""; //Дата с   
        $to   = isset($values['to']) ? $values['to'] : "";   //Дата по
        
        if (!empty($from) && !empty($to)){
           return "DATE({$this->getSqlKey()})  >= '{$this->escape($from)}' AND DATE({$this->getSqlKey()})  <= '{$this->escape($to)}'"; 
        }elseif (!empty($from) && empty($to)){
           return "DATE({$this->getSqlKey()})  >= '{$this->escape($from)}'"; 
        }elseif (empty($from) && !empty($to)){
           return "DATE({$this->getSqlKey()})  <= '{$this->escape($to)}'";  
        }else{
            return false;
        }
    }
    
    /**
    * Возвращает массив с данными, об установленых фильтрах для визуального отображения частиц
    * 
    * @param array $exclude_keys массив ключей, которые необходимо исключить из ссылки на сброс параметра
    * @return array of array ['title' => string, 'value' => string, 'href_clean']
    */
    public function getParts($current_filter_values, $exclude_keys = [])
    {
        $parts = [];
        
        if ($this->getNonEmptyValue() !== null) {
            $values = $this->getValue();
            $from = isset($values['from']) ? $values['from'] : ""; //Дата с   
            $to   = isset($values['to']) ? $values['to'] : "";   //Дата по
            
            $exclude = array_combine($exclude_keys, array_fill(0, count($exclude_keys), null));
            
            if (!empty($from)){ //Если есть дата с
               $from_formated = date('d.m.Y', strtotime($from)); 
               $without_this = $current_filter_values;
               unset($without_this[$this->getKey()]['from']); 
               if (empty($values['to'])){
                   unset($without_this[$this->getKey()]['to']); 
               }  
               $parts[] = [
                    'title' => $this->getTitle().t(' с'),
                    'value' => $from_formated,
                    'href_clean' => Request::commonInstance()->replaceKey([$this->wrap_var => $without_this] + $exclude) //Url, для очистки данной части фильтра
               ];
            }
            if (!empty($to)){ //Если есть дата по
               $to_formated   = date('d.m.Y', strtotime($to)); 
               $without_this = $current_filter_values;
               unset($without_this[$this->getKey()]['to']); 
               if (empty($values['from'])){
                   unset($without_this[$this->getKey()]['from']); 
               }
               $parts[] = [
                    'title' => $this->getTitle().t(' до'),
                    'value' => $to_formated,
                    'href_clean' => Request::commonInstance()->replaceKey([$this->wrap_var => $without_this] + $exclude) //Url, для очистки данной части фильтра
               ];
            }
            
        }
        return $parts;
    }   
}
