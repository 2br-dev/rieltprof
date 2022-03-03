<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Event;

/**
* Объекты данного класса, содержат в себе подробности происходящего события
*/
class Event
{
    private
        $errors = [],
        $stopped = false,
        $event;
    
    /**
    * Конструктор объектов события
    * 
    * @param string $event - название события
    * @param string $module - имя модуля, который вызывает данное событие
    * @return Event
    */
    function __construct($event)
    {
        $this->event = $event;
    }
    
    /**
    * Возвращает назвние события
    * 
    * @return string
    */
    function getEvent()
    {
        return $this->event;
    }
    
    /**
    * Останавливает вызов последующего обработчика события
    * 
    * @return Event
    */
    function stopPropagation()
    {
        $this->stopped = true;
        return $this;
    }
    
    /**
    * Возвращает true, если обработка собития была прервана вызовом stopPropagation
    * 
    * @return bool
    */
    function isStopped()
    {
        return $this->stopped;
    }
    
    /**
    * Добавляет сообщение об ошибке в список
    * 
    * @param string $message
    * @return Event
    */
    function addError($message)
    {
        $this->errors[] = $message;
        return $this;
    }
    
    /**
    * Возвращает массив со списком ошибок, произошедших в данном событии
    * 
    * @return array
    */
    function getErrors()
    {
        return $this->errors;
    }

}

