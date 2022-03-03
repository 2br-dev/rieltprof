<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Behavior;

/**
* Абстрактный класс объектов, поддерживающих расширение списка методов
* за счет подключения сторонних библиотек методов.
*/
abstract class AcceptBehavior implements AcceptBehaviorInterface
{
    public static 
        /**
        * Список подключенных к классу библиотек методов
        * 
        * @var BehaviorAbstract[]
        */
        $class_behaviors = [];
        
    private 
        /**
        * Список подключенных к текущему инстансу библотек методов
        * 
        * @var BehaviorAbstract[]
        */
        $cache_behavior_instances = [],
        /**
        * Список инициализированных инстансов библиотек для текущего класса
        * 
        * @var BehaviorAbstract[]
        */
        $instance_behaviors = [];
    
    /**
    * Добавляет поведение (новые методы) ко всем объектам текущего класса.
    * 
    * @param BehaviorAbstract $behavior_class - библиотека методов
    * @return void
    */
    static function attachClassBehavior(BehaviorAbstract $behavior_class)
    {
        if (!isset(self::$class_behaviors[get_called_class()])) {
            self::$class_behaviors[get_called_class()] = [];
        }
        self::$class_behaviors[get_called_class()][get_class($behavior_class)] = $behavior_class;
    }
    
    /**
    * Исключает поведение (новые методы) из всех объектов текущего класса
    * 
    * @param BehaviorAbstract $behavior_class - библиотека методов
    */
    static function detachClassBehavior(BehaviorAbstract $behavior_class = null)
    {
        if ($behavior_class === null) {
            self::$class_behaviors[get_called_class()] = [];
        } else {
            unset(self::$class_behaviors[get_called_class()][get_class($behavior_class)]);
        }
    }
    
    /**
    * Добавляет поведение (новые методы) к текущему объекту
    * 
    * @param BehaviorAbstract $behavior_class
    * @return void
    */
    public function attachInstanceBehavior(BehaviorAbstract $behavior_class)
    {
        $this->instance_behaviors[get_class($behavior_class)] = $behavior_class;
    }
    
    /**
    * Исключает поведение (подключенные методы) из текущего объекта
    * 
    * @param BehaviorAbstract $behavior_class
    * @return void
    */
    public function detachInstanceBehavior(BehaviorAbstract $behavior_class = null)
    {
        if ($behavior_class === null) {
            $this->instance_behaviors = [];
        } else {
            unset($this->instance_behaviors[get_class($behavior_class)]);
        }        
    }
    
    /**
    * Возвращает массив со списком классов подключенных библиотек методов
    * 
    * @return [
    *   'класс библиотеки подключения' => тип подключения (class | instance),
    *   ...
    * ]
    */
    public function getAttachedBehaviors()
    {
        $result = [];
        if (isset(self::$class_behaviors[get_called_class()])) {
            foreach(self::$class_behaviors[get_called_class()] as $class => $instance) {
                $result[$class] = 'class';
            }                
        }
        foreach($this->instance_behaviors as $class => $instance) {
            $result[$class] = 'instance';
        }
        return $result;
    }
    
    /**
    * Возвращает true, если запрашиваемый метод добавлен к текущему классу
    * 
    * @param string $method - имя метода
    * @return bool
    */
    public function behaviorMethodExists($method_name)
    {
        $behaviors = $this->instance_behaviors + 
                        (isset(self::$class_behaviors[get_called_class()]) ? self::$class_behaviors[get_called_class()] : []);
        
        foreach($behaviors as $class => $behavior_template) {
            if (is_callable([$behavior_template, $method_name])) {
                return true;
            }
        }
        return false;
    }
    
    
    /**
    * Обрабатывает вызов необъявленных методов с целью выполнения таких методов 
    * с помощью подключенных библиотек методов
    * 
    * @throws \RS\Behavior\Exception
    * @param string $method_name имя вызываемого метода
    * @param array $arguments массив с аргументами вызываемого метода
    */
    function __call($method_name, $arguments)
    {                    
        $behaviors = $this->instance_behaviors + 
                        (isset(self::$class_behaviors[get_called_class()]) ? self::$class_behaviors[get_called_class()] : []);
        
        foreach($behaviors as $class => $behavior_template) {
            if (is_callable([$behavior_template, $method_name])) {
                
                if (!isset($this->cache_behavior_instances[$class])) {
                    $this->cache_behavior_instances[$class] = clone $behavior_template;
                    $this->cache_behavior_instances[$class]->_init($this);
                }
                
                return call_user_func_array([$this->cache_behavior_instances[$class], $method_name], $arguments);
            }
        }
        
        throw new Exception(t('Метод %0 не найден в классе %1', [$method_name, $this->_self_class]));
    }
}
