<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Event;

/**
* Результат выполнения события
*/
class Result
{
    private 
        $original_params,
        $result_params,
        $event;
    
    /**
    * Исходные параметры события
    * 
    * @param mixed $original_params
    * @param mixed $result_params
    * @param Event $event
    * @return Result
    */
    function __construct($original_params, $result_params, Event $event)
    {
        $this->original_params = $original_params;
        $this->result_params = $result_params;
        $this->event = $event;
    }
    
    /**
    * Возвращает массив со значениями в порядке, соответствующем исходному массиву параметров
    * @return array
    */
    function extract()
    {
        if (!is_array($this->original_params)) return $this->result_params;
        
        $result = [];
        foreach($this->original_params as $key => $val) {
            $result[] = isset($this->result_params[$key]) ? $this->result_params[$key] : null;
        }
        return $result;
    }
    
    /**
    * Возвращает событие с которым связан результат
    * 
    * @return Event
    */
    function getEvent()
    {
        return $this->event;
    }
    
    /**
    * Возвращает параметр, прошедший через все обработчики.
    */
    function getResult()
    {
        return $this->result_params;
    }
    
}

