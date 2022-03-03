<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Orm;

class OrmObject extends AbstractObject
{
    protected static 
        $self_singleton_cache = [],
        $self_cache = []; //Здесь будут кэшироваться данные объектов, загруженные через конструктор
        
    /**
    * Объявляет поле ID у объектов
    * @return \RS\Orm\PropertyIterator
    */
    protected function _init() //инициализация полей класса. конструктор метаданных
    {
        return $this->getPropertyIterator()->append([
            'id' => new Type\Integer([
                'description' => t('Уникальный идентификатор (ID)'),
                'autoincrement' => true,
                'allowEmpty' => false,
                'primaryKey' => true,
                'visible' => false,
                'appVisible' => true
            ])
        ]);
    }
    
    /**
    * При передаче в конструктор id объекта, загружает его. 
    * Если объекта с таким id не найдено, объект остается пустым.
    * 
    * При загрузке объекта через конструктор используется простая система 
    * кэширования сохраняющая в статической переменной загруженные данные для объекта.
    * При следующей попытке загрузить такой же объект с таким же id будет, данные будут взяты из кэша.
    * 
    * @param mixed $id
    * @param bool $cache - Если задано true, то будет использоваться кэширование при загрузке объекта
    */
    function __construct($id = null, $cache = true)
    {
        parent::__construct();
        if ($id !== null && (!$cache || !$this->loadFromCache($id))) {
            $this->load($id);
            $this->saveInCache($id);
        }
    }
    
    /**
    * Возвращает имя свойства, которое помечено как первичный ключ.
    * 
    * @return string
    */
    public function getPrimaryKeyProperty()
    {
        return 'id';
    }
    
    /**
    * Сохраняет в статической переменной значение свойств объекта
    * 
    * @param mixed $id
    * @return void
    */
    function saveInCache($id)
    {
        self::$self_cache[$this->_self_class][self::getIdHash($id)] = $this->_values;
    }
    
    /**
    * Загружает из статической переменной(кэша) значения свойств для текущего объекта
    * 
    * @param mixed $id
    * @return bool Возвращает true, если данные были в кэше, иначе false
    */
    function loadFromCache($id)
    {
        $id = self::getIdHash($id);
        if (isset(self::$self_cache[$this->_self_class][$id])) {
            $this->getFromArray(self::$self_cache[$this->_self_class][$id], null, false);
            return true;
        }
        return false;
    }
    
    /**
    * Имплементирует pattern SingleTon
    * В отличие от загрузки объекта из кэша через конструктор по id данные метод всегда возвращает один и тот же объект.
    * 
    * @param mixed $id
    */
    public static function loadSingle($id)
    {
        $self_class = get_called_class();
        $id_hash = self::getIdHash($id);
        if (!isset(self::$self_singleton_cache[$self_class][$id_hash])) {
            self::$self_singleton_cache[$self_class][$id_hash] = new $self_class($id, false);
        }
        return self::$self_singleton_cache[$self_class][$id_hash];
    }
    
    /**
    * Возвращает хэш от ID
    * 
    * @param mixed $id
    * @return string
    */
    protected static function getIdHash($id)
    {
        return is_scalar($id) ? (string)$id : md5(json_encode($id));
    }
    
}