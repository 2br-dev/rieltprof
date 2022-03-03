<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Helper;

use RS\Orm\AbstractObject;
use RS\View\Engine;

/**
* Класс абстрактный генератора seo текста 
*/
class AbstractSeoGen
{
    
    protected
        $real_replace,
        $values         = [],  //Массив значений автозамены для свойство
        $hints          = [],  //Массив значений автозамены для подсказок
        $struct         = [],  //Массив структуры автозамены
        $template_admin = '%system%/admin/seohint.tpl',       //Шаблон для подписи полей в админке 
        $hint_fields   = [],  //Массив имён свойств которым будет обновлена подсказка (hint)
        $include_array  = []; //Массив с ключами включения, какие элементы в массиве автозамены участвуют
    
    /**
    * Конструктор класса, при создании необходимо передать массив для автозамены
    * Ключи массива с "|" например name|100 при замене возвратят 100 первых сиволов необходимого поля 
    * 
    * @return SeoGenerator
    */
    function __construct(array $real_replace = [])
    {
        $this->real_replace = $real_replace;        
    }
    
    /**
    * Подготавливает значения для автозамены, возвращает подготовленный массив
    * 
    * @param array $values - массив со значниями автозамены
    * @return array
    */
    function prepairValues($values)
    {
        foreach ($values as $k=>$v){
           if (is_numeric($k) && is_object($v)) { 
               //Если передаём просто объект
               $this->values += $this->getValuesFromORMObject($v);
           } elseif (is_string($k) && is_object($v)) { 
               //Если объект, а в качестве аргумента префикс
               $this->values += $this->getValuesFromORMObject($v, $k);
           }else{
               $this->values += [$k => $v];
           }
       }
    }
    
    /**
    * Получает значения автозамены из ORM объекта 
    * 
    * @param AbstractObject $object - объект из которого будет доставатся массив
    * @return array
    */
    function getValuesFromORMObject(AbstractObject $object, $prefix = false)
    {
        $newArray = [];
        foreach($this->include_array as $field) {
            if (!$prefix || strpos($field, $prefix) !== false) {
                $key = str_replace($prefix, '', $field);
                if (isset($object['__'.$key])) {
                    $newArray[$field] = $object[$key];
                }
            }
        }

        return $newArray;
    }
    
    /**
    * Подготавливает значения для подсказок, возвращает подготовленный массив для свойств
    * 
    * @param array $values - массив со значниями автозамены 
    * @return array
    */
    function prepairHints($values)
    {        
       foreach ($values as $k=>$v){
           if (is_numeric($k) && is_object($v)) { 
               //Если передаём просто объект
               $this->hints += $this->getHintsFromORMObject($v);
           } elseif (is_string($k) && is_object($v)) { 
               //Если объект, а в качестве аргумента префикс
               $this->hints += $this->getHintsFromORMObject($v,$k);
           }else{
               $this->hints += [$k=>$v];
           }
       }
    }
    
    
    /**
    * Получает значения автозамены из ORM объекта для подсказок
    * 
    * @param AbstractObject $object - объект из которого будет доставатся массив
    * @return array
    */
    function getHintsFromORMObject(AbstractObject $object, $prefix = false)
    {
        $newArray = [];
        foreach($this->include_array as $field) {
            if (!$prefix || strpos($field, $prefix) !== false) {
                $key = str_replace($prefix, '', $field);
                if (isset($object['__'.$key])) {
                    $newArray[$field] = $object['__'.$key]->getTitle();
                }
            }
        }
        
        return $newArray;
    }
    
    
    
    /**
    * Возвращает массив для автозамены.
    * 
    * @return array
    */
    function getValues()
    {
       return $this->values; 
    }
    
    /**
    * Заменяет подсказочные надписи в ORM объекте
    * 
    * @param AbstractObject $object - объект ORM
    * @param string $template - пользовательский шаблон подсказки вместо установленного
    * @return AbstractObject
    */
    function replaceORMHint(AbstractObject $object, $template = false)
    {
       $this->prepairHints($this->struct);
       
       $view = new Engine();
       $view->assign([
          'hints' => $this->hints
       ]);
       $hint_template = $view->fetch(!$template ? $this->template_admin : $template);
       
       foreach ($this->hint_fields as $hint_key) {
           $object["__".$hint_key]->setHint($hint_template);
       }
        
       return $object; 
    }
    
    /**
    * Возвращает заменённое значение переданного текста в соотвествии с массивом 
    * Ключи массива с "|" например name|100 при замене возвратят 100 первых сиволов необходимого поля  
    * 
    * @param string $text - текст в котором будет замена
    * @return string
    */
    function replace($text)
    {
       $real_struct = array_intersect_key($this->real_replace, $this->struct);
       $this->prepairValues($real_struct);
       
       if (is_callable([$this,'beforeReplace'])){
           $text = $this->beforeReplace($text,$real_struct);
       }
           
       //найдём значения, когда задано ограничение на сиволы
       $values = $this->values;
       $text = preg_replace_callback('/\{(.*?)}/si', function($match) use ($values) {
           $parts = explode('|',$match[1]);
           
           
           if (isset($values[$parts[0]])) {
               if (count($parts)>1) {
                   $str = strip_tags($values[$parts[0]]);
                   if (intval($parts[1])){ //Если число, значит обрезаем от начала
                      return mb_substr($str, 0, $parts[1]);  
                   }else{ 
                       //Если не число, то обрезатся будет по фрагменту из второго 
                       //элемента массива $parts[1] и по элементу $parts[2] - числу вхождений этого элемента
                       //Например, если нужно обрезать после 3 точки в предложении то {title|.|3}.
                       if ( mb_substr_count($str,$parts[1]) >= $parts[2] ) { //Если количество подстрок равное или больше нужного
                          $pos = -1;
                          for($m=0;$m<=$parts[2];$m++){
                             $pos = mb_strpos($str,$parts[1],$pos+1);  
                          }
                          $str = mb_substr($str, 0, $pos+1);  
                       }
                   } 
                   return $str;
               } else {
                   return strip_tags($values[$parts[0]]);
               }
           }
           
       }, $text);
       
       return $text; 
    }
}
