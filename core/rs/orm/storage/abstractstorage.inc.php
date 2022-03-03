<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Orm\Storage;

/**
* Базовый класс хранилища ORM объектов.
* ORM объект использует хранилище, при загрузке, сохранении, 
* изменении, удалении ORM объекта.
*/
abstract class AbstractStorage
{
    protected
        $orm_object,
        $options;
    
    function __construct(\RS\Orm\AbstractObject $orm_object, $options = [])
    {
        $this->orm_object = $orm_object;
        $this->options = $options;
        $this->_init();
    }
    
    /**
    * Инициализирует хранилище
    * 
    * @return void
    */
    function _init() 
    {}

    /**
    * Загружает объект по первичному ключу
    * 
    * @param mixed $primaryKey - значение первичного ключа
    * @return object
    */
    public function load($primaryKey = null)
    {}
    
    /**
    * Добавляет объект в хранилище
    * 
    * @return bool
    */    
    public function insert() 
    {}
    
    /**
    * Обновляет объект в хранилище
    * 
    * @param $primaryKey - значение первичного ключа
    * @return bool
    */
    public function update($primaryKey = null) 
    {}

    /**
    * Перезаписывает объект в хранилище
    * 
    * @return bool
    */    
    public function replace()
    {}
    
    /**
    * Удаляет объект из хранилища
    * 
    * @return bool
    */
    public function delete() 
    {}    
    
    /**
    * Возвращает параметр хранилища
    * 
    * @param mixed $key - имя параметра
    * @param mixed $default - возвращаемое значение, если данный ключ не задан
    * @return mixed
    */
    function getOption($key, $default = null)
    {
        return isset($this->options[$key]) ? $this->options[$key] : $default;
    }    
}

