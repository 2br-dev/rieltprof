<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Behavior;

/**
* Интерфейс классов, поддерживающих внешнюю модификацию поведения.
*/
interface AcceptBehaviorInterface {
    
    /**
    * Добавляет поведение (новые методы) ко всем объектам текущего класса.
    * 
    * @param BehaviorAbstract $behavior_class - библиотека методов
    * @return void
    */
    public static function attachClassBehavior(\RS\Behavior\BehaviorAbstract $behavior_class);
    
    /**
    * Исключает поведение (новые методы) из всех объектов текущего класса
    * 
    * @param BehaviorAbstract $behavior_class - библиотека методов
    */
    public static function detachClassBehavior(\RS\Behavior\BehaviorAbstract $behavior_class = null);
    
    /**
    * Добавляет поведение (новые методы) к текущему объекту
    * 
    * @param BehaviorAbstract $behavior_class
    * @return void
    */
    public function attachInstanceBehavior(\RS\Behavior\BehaviorAbstract $behavior_class);
    
    /**
    * Исключает поведение (подключенные методы) из текущего объекта
    * 
    * @param BehaviorAbstract $behavior_class
    * @return void
    */
    public function detachInstanceBehavior(\RS\Behavior\BehaviorAbstract $behavior_class = null);
    
        /**
    * Возвращает массив со списком классов подключенных библиотек методов
    * 
    * @return [
    *   'класс библиотеки подключения' => тип подключения (class | instance),
    *   ...
    * ]
    */
    public function getAttachedBehaviors();
}