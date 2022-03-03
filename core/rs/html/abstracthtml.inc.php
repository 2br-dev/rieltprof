<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Html;

use RS\Http\Request;

/**
* Базовый абстрактный класс всех html элементов, 
* у которых через конструктор можно устанавливать все основные свойства.
* т.е. если у класса есть метод setTitle($title), то его можно задать в момент создания объекта, 
* передав в конструктор в параметре options - array('Title' => $title);
*/
abstract class AbstractHtml implements ElementInterface
{
    public
        $options = [];
    
    protected
        $url,    
        $option_prefixes = ['set', 'add']; //Искать методы со следующими префиксами для установки свойств
    
    function __construct(array $options = [])
    {
        $this->url = Request::commonInstance();
        $this->options = $options;
        foreach($options as $option => $value)
            foreach($this->option_prefixes as $prefix)
            {
                $method_name = $prefix.$option;
                if (method_exists($this, $method_name)) {
                    $this->$method_name($value);
                    break;
                }
            }
    }
    
    /**
    * Устанавливает параметры для объекта
    * 
    * @param array $options - Ассоциативный массив с параметрами
    * @return AbstractHtml
    */
    function setOptions(array $options)
    {
        $this->options = $options + $this->options;
        return $this;
    }
    
    /**
    * Устанавливает параметр
    * 
    * @param string $key - ключ
    * @param mixed $value - значение
    * @return AbstractHtml
    */
    function setOption($key, $value)
    {
        $this->options[$key] = $value;
        return $this;
    }
    
    /**
    * Возвращает значение параметра
    * 
    * @param string $key - ключ
    * @param mixed $default - значение по умолчанию
    * @return mixed
    */
    function getOption($key, $default = null)
    {
        return isset($this->options[$key]) ? $this->options[$key] : $default;
    }
}

