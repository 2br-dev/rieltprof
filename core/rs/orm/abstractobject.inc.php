<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Orm;

use RS\AccessControl\Rights;
use RS\AccessControl\DefaultModuleRights;
use RS\Behavior\AcceptBehavior;
use RS\Event\Manager as EventManager;
use RS\Exception as RSException;
use RS\Orm\Request as OrmRequest;

/**
* Базовый абстрактный класс объектов ORM
*/
abstract class AbstractObject extends AcceptBehavior implements \ArrayAccess, \Iterator
{
    const INSERT_FLAG = 'insert';
    const UPDATE_FLAG = 'update';
    const REPLACE_FLAG = 'replace';
    const INDEX_PRIMARY = 'primary key';
    const INDEX_UNIQUE = 'unique';
    const INDEX_KEY = 'index';
    const INDEX_FULLTEXT = 'fulltext';

    protected $_values = []; //Значения свойств
    protected $_self_class;       //имя класса ORM объекта
    protected $_local_id;         //внутренний ID объекта

    //Невидимые для print_r параметры
    protected static $db = DB_NAME; //Имя базы данных
    protected static $table = null; //Имя таблицы

    protected static $iterator = 0;
    protected static $init_default_method = '_initDefaults'; //Имя метода, который вызывается во время создания объекта
    protected static $local = [];
    protected static $default_local_parameters = [
        'modified' => [],        //Измененные свойства
        'properties' => null,         //Свойства объекта
        'errorlist' => [],       //Список ошибок в формах
        'formerror' => [],       //Список форм с ошибками
        'non_form_errors' => [], //Список ошибок не в формах
        'checkRights' => true,        //Проверять права на запись во время проверки данных (CheckData)
        'escape_fields' => [],   //Перевести в entity следующие поля
        'escape_all' => false         //Перевести в entity все значения при записи            
    ];
    protected static $class = [];
    protected static $default_class_parameters = [
        'property_template' => '%system%/coreobject/prop_form.tpl',
        'properties' => null, //Свойства объекта
        'indexes' => [],
    ];

    public function __construct()
    {
        $this->_self_class = get_class($this);
        $this->_local_id = self::$iterator++;
        self::$local[$this->_local_id] = self::$default_local_parameters;
        $this->{static::$init_default_method}();        
    }

    function __destruct()
    {
        unset(self::$local[$this->_local_id]);
    }    
    
    /**
    * Инициирует поля ORM Объекта
    * @return void
    */
    protected function initProperties()
    {
        if (!$this->issetClassParameter()) {
            self::$class[$this->_self_class] = self::$default_class_parameters;
            $newIterator = new PropertyIterator();
            $this->setClassParameter('properties', $newIterator);
            if (!isset(self::$local[$this->_local_id]['properties'])) {
                $this->setLocalParameter('properties', $newIterator);
            }
            $this->_init();
            $this->afterInit();
        } 
        else {
            if (!isset(self::$local[$this->_local_id]['properties'])) {
                self::$local[$this->_local_id]['properties'] = self::$class[$this->_self_class]['properties'];
            }
        }
    }

    /**
    * В данном методе должны быть заданы поля объекта. 
    * Вызывается один раз для одного класса объектов в момент первого обращения к свойству
    */
    abstract protected function _init();

    /**
    * Внутри данной функции нужно объявлять значения свойств по умолчанию.
    * Вызывается после конструктора.
    */
    protected function _initDefaults()
    {}

    /**
     * Производит внутреннюю инициализацию объекта. Вызывается один раз для одного имени класса
     */
    protected function afterInit()
    {
        //Сообщаем свойствам их ключи.
        $properties = self::$local[$this->_local_id]['properties'];

        /**
         * Event: orm.init
         * paramtype \RS\Orm\AbstractObject
         */
        EventManager::fire('orm.init.' . $this->getShortAlias(), $this);

        if ($properties !== null) {
            $default_values = [];
            foreach ($properties as $property => $value) {
                $properties[$property]->setName($property);
            }
        }
    }

    /**
     * Присваивает не измененным полям значение по-умолчанию
     * 
     * @return void
     */
    public function fillDefaults()
    {
        foreach ($this->getProperties() as $key => $property) {
            $default_value = $property->getDefault();
            if (!$this->isModified($key) && $default_value !== null) {
                $this[$key] = $default_value;
            }
        }
    }

    /**
    * @deprecated
     * Возвращает параметры, заданные для всех объектов данного класса
     * 
     * @param string $key имя параметра
     */
    public function getClassParameter($key = null)
    {
        return $key ? self::$class[$this->_self_class][$key] : self::$class[$this->_self_class];
    }

    /**
     * @deprecated
     * Устанавливает параметр, заданные для всех объектов данного класса
     * 
     * @param string | array $key имя параметра или ассоциативный массив параметров и значений
     * @param mixed $value значение
     */
    public function setClassParameter($key, $value = null)
    {
        if (is_array($key)) {
            self::$class[$this->_self_class] = $key + self::$class[$this->_self_class];
        } else {
            self::$class[$this->_self_class][$key] = $value;
        }
    }

    /**
     * @deprecated
     * Возвращает true, если задан $key и параметр $key существует.
     * Возвращает true, если не задан $key и задан хотя бы один параметр для даннго класса
     * 
     * @param string | null $key - имя параметра
     * @return bool
     */
    public function issetClassParameter($key = null)
    {
        if ($key === null) {
            return isset(self::$class[$this->_self_class]);
        } else {
            return isset(self::$class[$this->_self_class][$key]);
        }
    }

    /**
     * Устанавливает параметр для текущего объекта.
     * 
     * @param string | array $key - имя параметра или ассоциативный массив имя => значение
     * @param mixed $value - значение параметра
     */
    public function setLocalParameter($key, $value = null)
    {
        if (is_array($key)) {
            self::$local[$this->_local_id] = $key + self::$local[$this->_local_id];
        } else {
            self::$local[$this->_local_id][$key] = $value;
        }
    }

    /**
     * Возвращает параметр, заданный для текущего объекта
     * 
     * @param mixed $key - имя параметра
     * @param mixed $default - значение по-умолчанию
     * @return mixed
     */
    public function getLocalParameter($key, $default = null)
    {
        return isset(self::$local[$this->_local_id][$key]) ? self::$local[$this->_local_id][$key] : $default;
    }
    
    /**
    * Возвращает параметр, заданный для текущего объекта, а если такой параметр не задан, заданный для текущего класса объектов
    * 
    * @param mixed $key - имя параметра
    * @param mixed $default - значение по-умолчанию
    * @return mixed
    */
    public function getParameter($key, $default = null)
    {
        if (isset(self::$local[$this->_local_id][$key])) {
            return self::$local[$this->_local_id][$key];
        } else {
            return isset(self::$class[$this->_self_class][$key]) ? self::$class[$this->_self_class][$key] : $default;
        }
    }

    /**
     * Задает новые свойства для текущего объекта.
     * Применение данного метода накладывает ряд ограничений на последующую работу объекта.
     * К объекту невозможно будет применить сериализацию.
     * 
     * @param PropertyIterator $iterator
     */
    public function setPropertyIterator(PropertyIterator $iterator)
    {
        $this->setLocalParameter('properties', $iterator);
        $this->afterInit();
    }

    /**
     * Устанавливает, проверять ли права на запись при попытке изменить объект
     * 
     * @param boolean $bool
     */
    function checkRights($bool)
    {
        $this->setLocalParameter('checkRights', $bool);
    }

    /**
     * Очищает все значения свойств
     */
    function clear()
    {
        $this->_values = [];
        self::$local[$this->_local_id]['modified'] = [];
    }

    /**
     * Возвращает загруженный объект по условию в случае успеха, иначе вернет пустой экземпляр объекта.
     *
     * @param array|string $expr выражение WHERE.
     * @param array $values массив со значениям, заменит "-КЛЮЧ-" из expr на ЗНАЧЕНИЕ
     * @param string $prefix будет подставлено перед текущим выражением AND, OR,...
     * @param string $in_prefix будет подставлено между выражениями, в случае если expr - массив AND, OR,...
     * @return static
     * Исключение \RS\Orm\Exception не может быть брошено
     */
    public static function loadByWhere($expr, array $values = null, $prefix = 'AND', $in_prefix = 'AND')
    {
        $called_class = get_called_class();
        $result = OrmRequest::make()
                ->from(new $called_class)
                ->where($expr, $values, $prefix, $in_prefix)
                ->limit(1)
                ->object();
        return $result ? $result : new $called_class;
    }

    /**
     * Проверяет наличие значения по ключу (ArrayAccess)
     */
    public function offsetExists($offset)
    {
        if ($offset[0] == '_' && $offset[1] == '_') {
            $this->initProperties();
            return isset(self::$local[$this->_local_id]['properties'][substr($offset, 2)]);
        }
        return isset($this->_values[$offset]);
    }

    /**
     * Возвращает значение по ключу (имени свойства) (ArrayAccess)
     * Если $offset начинается с "__" (двойное подчеркивание), то возвращает объект \RS\Orm\Type\.....
     *
     * @param string $offset - имя свойства.
     * @return mixed|Type\AbstractType|null
     */
    public function offsetGet($offset)
    {
        if ($offset[0] == '_' && $offset[1] == '_') {
            //Запрос объекта 
            $offset = substr($offset, 2);
            $value = $this->getProp($offset);
        } else {
            //Запрос значения объекта
            $value = isset($this->_values[$offset]) ? $this->_values[$offset] : null;
        }
        return $value;
    }

    /**
     * Устанавливает значение в свойство (ArrayAccess)
     *
     * @param string $offset - ключ( имя свойства )
     * @param mixed $value - значение
     * @return mixed
     */
    public function offsetSet($offset, $value)
    {
        $this->_values[$offset] = $value;
        self::$local[$this->_local_id]['modified'][$offset] = true;

        return $value;
    }

    /**
     * Ограниченная функция установки 
     * 
     * @param mixed $offset
     * @param mixed $value
     * @return mixed
     */
    public function __set($offset, $value)
    {
        return $this->offsetSet($offset, $value);
    }

    public function __get($offset)
    {
        return $this->offsetGet($offset);
    }

    /**
     * Очищает значение свойства
     * 
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->_values[$offset]);
        unset(self::$local[$this->_local_id]['modified'][$offset]);
    }

    /**
     * Возвращает объект хранилища
     * @return \RS\Orm\Storage\AbstractStorage
     */
    protected function getStorageInstance()
    {
        return new \RS\Orm\Storage\Db($this);
    }

    /**
     * Возвращает сокращенное наименование orm объекта. 
     * Данное имя используется в названиях событий
     * @return string
     */
    public function getShortAlias()
    {
        $name = str_replace('\\', '-', strtolower($this->_self_class));
        return trim(str_replace('model-orm-', '', $name), '-');
    }

    /**
     * Загружает объект из хранилища
     *
     * @param mixed $primaryKeyValue - уникальный идентификатор
     * @return object
     */
    public function load($primaryKeyValue = null)
    {
        return $this->getStorageInstance()->load($primaryKeyValue);
    }

    /**
     * Добавляет объект в хранилище
     *
     * @param bool $ignore - Если true, то ошибки вставки будут игнорироваться
     * @param array $on_duplicate_update_keys - поля, которые необходимо обновить в случае если запись уже существует
     * @param array $on_duplicate_uniq_fields - поля, которые точно идетифицируют текущаю запись, для подгрузки id объекта при обновлении
     * @return boolean - true в случае успеха, иначе - false
     */
    public function insert($ignore = false, $on_duplicate_update_keys = [], $on_duplicate_uniq_fields = [])
    {
        /**
         * Event: orm.beforewrite
         * paramtype array
         * index 'orm' => \RS\Orm\AbstractObject OrmObject
         * index 'flag' => string "insert" | "replace" | "update"
         * index 'on_duplicate_update_keys' => array of string
         */
        $can_do = $this->beforeWrite(self::INSERT_FLAG, $on_duplicate_update_keys) !== false && !EventManager::fire('orm.beforewrite.' . $this->getShortAlias(), [
                    'orm' => $this,
                    'flag' => self::INSERT_FLAG,
                    'on_duplicate_update_keys' => $on_duplicate_update_keys
            ])->getEvent()->isStopped();

        $ret = false;
        $storage = $this->getStorageInstance();
        $type = $ignore ? 'insert ignore' : 'insert';
        
        if ($can_do && $ret = $storage->insert($type, $on_duplicate_update_keys, $on_duplicate_uniq_fields)) {
            $this->afterWrite(self::INSERT_FLAG, $on_duplicate_update_keys);
            /**
             * Event: orm.afterwrite
             * paramtype array
             * index 'orm' => \RS\Orm\AbstractObject OrmObject
             * index 'flag' => string "insert" | "replace" | "update"
             */
            EventManager::fire('orm.afterwrite.' . $this->getShortAlias(), [
                'orm' => $this,
                'flag' => self::INSERT_FLAG,
                'on_duplicate_update_keys' => $on_duplicate_update_keys
            ]);
        }
        return $ret;
    }

    /**
     * Обновляет объект в хранилище
     *
     * @param mixed $primaryKey - уникальный идентификатор. Необходимо указывать, если у объекта меняется уникальный идентификатор
     * @return boolean - true в случае успеха
     */
    public function update($primaryKey = null)
    {
        /**
         * Event: orm.beforewrite
         * paramtype array
         * index 'orm' => \RS\Orm\AbstractObject OrmObject
         * index 'flag' => string "insert" | "replace" | "update"
         */
        $can_do = $this->beforeWrite(self::UPDATE_FLAG) !== false && !EventManager::fire('orm.beforewrite.' . $this->getShortAlias(), [
                'orm' => $this,
                'flag' => self::UPDATE_FLAG
            ])->getEvent()->isStopped();

        $ret = false;
        $storage = $this->getStorageInstance();
        if ($can_do && $ret = $storage->update($primaryKey)) {
            $this->afterWrite(self::UPDATE_FLAG);
            /**
             * Event: orm.afterwrite
             * paramtype array
             * index 'orm' => \RS\Orm\AbstractObject OrmObject
             * index 'flag' => string "insert" | "replace" | "update"
             */
            EventManager::fire('orm.afterwrite.' . $this->getShortAlias(), [
                'orm' => $this,
                'flag' => self::UPDATE_FLAG
            ]);
        }
        return $ret;
    }

    /**
     * Заменяет объект в хранилище, если таковой уже имелся, в противном случае вставляет новый объект
     *
     * @return boolean - true, в случае успеха
     */
    public function replace()
    {
        /**
         * Event: orm.beforewrite
         * paramtype array
         * index 'orm' => \RS\Orm\AbstractObject OrmObject
         * index 'flag' => string "insert" | "replace" | "update"
         */
        $can_do = $this->beforeWrite(self::REPLACE_FLAG) !== false && !EventManager::fire('orm.beforewrite.' . $this->getShortAlias(), [
                    'orm' => $this,
                    'flag' => self::REPLACE_FLAG
            ])->getEvent()->isStopped();

        $ret = false;
        $storage = $this->getStorageInstance();
        if ($can_do && $ret = $storage->replace()) {
            $this->afterWrite(self::REPLACE_FLAG);
            /**
             * Event: orm.afterwrite
             * paramtype array
             * index 'orm' => \RS\Orm\AbstractObject OrmObject
             * index 'flag' => string "insert" | "replace" | "update"
             */
            EventManager::fire('orm.afterwrite.' . $this->getShortAlias(), [
                'orm' => $this,
                'flag' => self::REPLACE_FLAG
            ]);
        }
        return $ret;
    }

    /**
     * Удаляет объект из хранилища
     * @return boolean - true, в случае успеха
     */
    public function delete()
    {
        //Проверяем права на запись для модуля
        $check_rights = $this->getLocalParameter('checkRights');
        if ($check_rights && ($error = Rights::CheckRightError($this, $this->getRightDelete(), true))) {
            $this->addError($error);
            return false;
        }
        //Конец проверки прав на запись для модуля
        
        $eresult = EventManager::fire('orm.delete.' . $this->getShortAlias(), [
            'orm' => $this,
        ]);

        $delete_result = !$eresult->getEvent()->isStopped() && $this->getStorageInstance()->delete();

        if ($delete_result) {
            EventManager::fire('orm.afterdelete.' . $this->getShortAlias(), [
                'orm' => $this,
            ]);
        }

        return $delete_result;
    }

    /**
     * Возвращает true, если объект с указанным Уникальным идентификатором существует
     * 
     * @param mixed $primaryKeyValue - уникальный идентификатор
     * @return boolean
     */
    public function exists($primaryKeyValue)
    {
        return $this->getStorageInstance()->exists($primaryKeyValue);
    }

    /**
     * Загружает данные в объект из массива
     * 
     * @param array $data - массив ключ => значение
     * @param string $remove_prefix - префикс, который будет удален перед ключем
     * @param bool $mark_modify - True - отмечать поле измененным, после загружки данных
     * @param bool $call_afterload_event - True - вызывать обработчик загрузки данных
     * @return AbstractObject
     */
    public function getFromArray(array $data, $remove_prefix = null, $mark_modify = true, $call_afterload_event = false)
    {
        foreach ($data as $key => $value) {
            $propname = $key;
            if ($remove_prefix !== null) {
                if (strpos($key, $remove_prefix) === 0) {
                    $propname = str_replace($remove_prefix, '', $propname);
                } else {
                    continue;
                }
            }
            $this->_values[$propname] = $value;
            if ($mark_modify) {
                self::$local[$this->_local_id]['modified'][$propname] = true;
            }
        }

        if ($call_afterload_event) {
            $this->afterObjectLoad();
            EventManager::fire('orm.afterload.' . $this->getShortAlias(), [
                'orm' => $this,
            ]);
        }
        
        return $this;
    }

    /**
     * Возвращает ассоциативный массив свойств и значений текущего объекта
     * @return array
     */
    public function getValues()
    {
        return (array)$this->_values;
    }

    /**
     * Возвращает true, если свойство $property изменено
     * 
     * @param string $property - имя свойства
     * @return boolean
     */
    public function isModified($property)
    {
        if (isset(self::$local[$this->_local_id]['modified'][$property])) {
            return true;
        } else {
            return $this->getProp($property)->isAlwaysModify();
        }
    }

    /**
     * Возвращает объект PropertyIterator, который используется в текущем объекте
     * @return \RS\Orm\PropertyIterator
     */
    public function getPropertyIterator()
    {
        $this->initProperties();
        return self::$local[$this->_local_id]['properties'];
    }

    /**
     * Дополняет текущий класс объектов новыми полями(свойствами)
     * 
     * @param array $properties - массив со свойствами, аналогично тому, что задается в _init
     * @return static
     */
    public function appendProperty(array $properties)
    {
        $this->getPropertyIterator()->append($properties);
        $this->afterInit();
        return $this;
    }

    /**
     * Возвращает имя таблицы текущего объекта
     *
     * @param boolean $with_quotes - если true, то оборачивать в апострофами имя таблицы
     * @return string
     * Исключение \RS\Exception оставлено на ручной контроль
     */
    protected static function _tableName($with_quotes = true)
    {
        $called_class = get_called_class();

        if (isset(\Setup::$TABLE_MAPPING[$called_class])) {
            $table_name = \Setup::$TABLE_MAPPING[$called_class];
        } elseif (isset(static::$table)) {
            $table_name = static::$table;
        } else {
            throw new RSException(t('Не задано имя таблицы в ORM объекте %0', $called_class));
        }

        $table = \Setup::$DB_TABLE_PREFIX . $table_name;
        return ($with_quotes) ? "`$table`" : $table;
    }

    /**
     * Возвращает имя базы данных текущего объекта
     *
     * @param bool $with_quotes - если true, то оборачивать в апострофами имя базы данных
     * @return string
     */
    protected static function _dbName($with_quotes = true)
    {
        $called_class = get_called_class();

        if (isset(\Setup::$DB_MAPPING[$called_class])) {
            $db_name = \Setup::$DB_MAPPING[$called_class];
        } else {
            $db_name = static::$db;
        }

        return ($with_quotes) ? "`$db_name`" : $db_name;
    }

    /**
     * Возвращает базу данных и имя таблицы
     *
     * @return string
     * Исключение \RS\Exception оставлено на ручной контроль
     */
    public static function _getTable()
    {
        return static::_dbName()."." . static::_tableName();
    }

    /**
     * Возвращает базу данный и имя таблицы в виде массива
     *
     * @return array - где [0] => имя базы, [1] => имя таблицы
     * @throws RSException
     */
    public function _getTableArray()
    {
        return [static::_dbName(false), static::_tableName(false)];
    }

    /**
     * Возвращает имя свойства, которое помечено как первичный ключ.
     * Для совместимости с предыдущими версиями, метод ищет первичный ключ в свойствах. 
     * 
     * С целью увеличения производительности необходимо у наследников реализовать явное
     * возвращение свойств, отвечающих за первичный ключ.
     * 
     * @return string | array | false - false в случае отсутствия такого свойства
     */
    public function getPrimaryKeyProperty()
    {
        foreach ($this->getPropertyIterator() as $key => $prop)
            if ($prop->isPrimaryKey())
                return $key;

        return false;
    }

    /**
     * Возвращает список объектов свойств с установленными значениями
     * 
     * @return PropertyIterator
     */
    final public function getProperties()
    {
        $this->initProperties();
        $properties = self::$local[$this->_local_id]['properties'];
        $properties->setValues($this->_values);
        return $properties;
    }

    /**
     * Возвращает объект свойства
     * @return Type\AbstractType
     */
    function getProp($offset)
    {
        $this->initProperties();
        
        $properties = self::$local[$this->_local_id]['properties'];
        $value = isset($this->_values[$offset]) ? $this->_values[$offset] : null;

        if (isset($properties[$offset])) {
            $property = $properties[$offset];
        } else {
            $property = new Type\MixedType();
        }

        $property->set($value);
        if (isset(self::$local[$this->_local_id]['formerror'][$offset])) {
            $property->setErrors(self::$local[$this->_local_id]['formerror'][$offset]);
        } else {
            $property->setErrors([]);
        }
        return $property;
    }

    /**
     * Сохраняет объект
     * Если передан $primaryKeyValue, то обновляет его свойства, если нет то вставляет новую запись
     *
     * @param mixed $primaryKeyValue
     * @return bool
     * @throws RSException
     */
    public function save($primaryKeyValue = null, $user_post = [], $post_var = null, $files_var = null)
    {
        $flag = ($primaryKeyValue > 0) ? self::UPDATE_FLAG : self::INSERT_FLAG;
        if ($this->checkData($user_post, $post_var, $files_var, null, null, $flag)) {
            $usekeys = $this->getLocalParameter('use_keys');
            $exclude = $this->getLocalParameter('exclude_keys');

            foreach ($this->getProperties() as $key => $property) {
                if (isset($usekeys) && !in_array($key, $usekeys))
                    continue;

                if (isset($exclude) && in_array($key, $exclude))
                    continue;

                $property->selfSave(); //Если это сложный объект, то пусть он сам себя сохраняет(например делает запись в промежуточной таблице)
                $this->_values[$key] = $property->get();
            }

            if ($this->getLocalParameter('replaceOn')) {
                return $this->replace();
            } else {
                if (isset($primaryKeyValue)) {
                    return $this->update($primaryKeyValue);
                } else {
                    return $this->insert();
                }
            }
        }
        return false;
    }

    /**
     * Устанавливает режим вставки
     * 
     * @param boolean $bool - если true, то метод save будет использовать для вставки вместо insert - replace.
     * @return void
     */
    function replaceOn($bool)
    {
        $this->setLocalParameter('replaceOn', $bool);
    }

    /**
     * @deprecated (08.18)
     * Устанавливает номер бита, который следует проверять при вызове CheckData (вызывается перед записью)
     * 
     * @param integer $n - номер бита
     * @return void
     */
    function setWriteBit($n)
    {
        $this->setLocalParameter('write_bit', $n);
    }

    /**
     * Устанавливает, какие ключи должны приниматься из POST для сохранения объекта
     * 
     * @param array $keys
     */
    public function usePostKeys(array $keys)
    {
        $this->setLocalParameter('use_keys', $keys);
    }

    /**
     * Устанавливает, какие ключи должны исключаться из POST при сохранении объекта
     *
     * @param array $keys
     */
    public function excludePostKeys(array $keys)
    {
        $this->setLocalParameter('exclude_keys', $keys);
    }

    /**
     * Проверка на возможность сохранения данных. при этом объект заполняется из POST. насколько это возможно.
     *
     * @param array $user_post - дополнительные данные, которые будут добавлены к post_var
     * @param array $post_var - если передан, то будет использован вместо $_POST
     * @param array $files_var - если передан, то будет использован вместо $_FILES
     * @param array $usekeys - массив с ключами, которые нужно исползовать для заполнения объекта
     * @param array $exclude - массив с ключами, которые нужно исключить при заполнении объекта
     * @param string $flag - флаг опреации с объектом (self::INSERT_FLAG|self::UPDATE_FLAG)
     * @return bool
     * @throws RSException
     */
    public function checkData($user_post = [], $post_var = null, $files_var = null, $usekeys = null, $exclude = null, $flag = self::UPDATE_FLAG)
    {
        $this->fillFromPost($user_post, $post_var, $files_var, $usekeys, $exclude);
        return $this->validateData($flag);
    }

    /**
     * Заполняет объект из POST. насколько это возможно.
     *
     * @param array $user_post - дополнительные данные, которые будут добавлены к post_var
     * @param array $post_var - если передан, то будет использован вместо $_POST
     * @param array $files_var - если передан, то будет использован вместо $_FILES
     * @param array $usekeys - массив с ключами, которые нужно исползовать для заполнения объекта
     * @param array $exclude - массив с ключами, которые нужно исключить при заполнении объекта
     * @return void
     */
    public function fillFromPost($user_post = [], $post_var = null, $files_var = null, $usekeys = null, $exclude = null): void
    {
        if (!isset($post_var))
            $post_var = $_POST;
        if (!isset($files_var))
            $files_var = $_FILES;
        if (!isset($usekeys))
            $usekeys = $this->getLocalParameter('use_keys');
        if (!isset($exclude))
            $exclude = $this->getLocalParameter('exclude_keys');

        $post_var = array_merge($post_var, $user_post);

        //Заполняем свойства
        $request_data = $post_var + $files_var;
        foreach ($this->getProperties() as $key => $property) {
            //Пропускаем заполнение свойства, если необходимо
            if (isset($usekeys) && !in_array($key, $usekeys))
                continue;
            if (isset($exclude) && in_array($key, $exclude))
                continue;
            if (!$property->isListenPost() && !isset($user_post[$key]))
                continue;

            $value = $property->getFromRequest($request_data);

            if (isset($value)) {
                $this[$key] = $value;
            }
        }
    }

    /**
     * Проверяет возможность сохранения данных
     *
     * @param string $flag - флаг опреации с объектом (self::INSERT_FLAG|self::UPDATE_FLAG)
     * @return bool
     * @throws RSException
     */
    public function validateData($flag = self::UPDATE_FLAG): bool
    {
        switch ($flag) {
            case self::INSERT_FLAG:
                $checking_right = $this->getRightCreate();
                break;
            case self::UPDATE_FLAG:
                $checking_right = $this->getRightUpdate();
                break;
            default:
                $checking_right = $this->getRightUpdate();
        }

        $check_rights = $this->getLocalParameter('checkRights');
        if ($check_rights && $this->noWriteRights($checking_right)) {
            return false;
        }

        $this->validate();

        return !$this->hasError();
    }

    /**
     * Производит валидацию текущих данных в свойствах
     * 
     * @return bool Возвращает true, если нет ошибок, иначе - false
     */
    function validate()
    {
        //Валидация свойств
        $result = true;
        $check_fields = $this->getLocalParameter('check_fields', null);
        foreach ($this->getProperties() as $key => $property) {
            if ($check_fields === null || in_array($key, $check_fields)) {

                $err = $this->checkField($property);  //Пропускаем через валидаторы форм
                if ($err !== true) {
                    if ($err !== null) { //На случае, если внутри checker'а был вызван addError()
                        $this->addError($err, $key);
                        $property->setErrors([$err]);
                    }
                    $result = false;
                }
            }
        }
        return $result;
    }

    /**
     * Устанавливает, какие поля проверять при вызове checkData
     * 
     * @param array $fields
     * @return void
     */
    function setCheckFields(array $fields)
    {
        $this->setLocalParameter('check_fields', $fields);
    }

    /**
     * При заполнении данными с помощью метода check(), 
     * указанные свойства будут пропущены через функцию htmlspecialchars.
     * 
     * @param array $escape_fields - массив с названиями свойств, которые нужно перевести в entity
     */
    function escapeFields(array $escape_fields)
    {
        $this->setLocalParameter('escape_fields', $escape_fields);
    }

    /**
     * Переводить в entity все значения свойств перед записью?
     * 
     * @param boolean $bool - если true, то будут переведены в entity все свойства
     */
    function escapeAll($bool)
    {
        $this->setLocalParameter('escape_all', $bool);
    }

    /**
     * Возвращает результат проверки поля $key
     * 
     * @param string | \RS\Orm\Type\AbstractType $key
     * @return boolean | string - true, если все успешно иначе текст ошибки
     */
    public function checkField($property)
    {
        if (!($property instanceof Type\AbstractType)) {
            $property = $this->getProp($property);
        }

        foreach ($property->getCheckers() as $checker) {
            $param = array_merge([$this, $property->get()], $checker['param']);
            if (is_string($checker['callmethod'])) {
                $callback = ['\RS\Orm\Type\Checker', $checker['callmethod']];
            } else {
                $callback = $checker['callmethod'];
            }
            /* Передает checker'у параметры:
              1.$this - текущий объект
              2.$value - значение поля на проверку
              3.$errortext - текст ошибки
              4.... произвольные параметры, переданные в setChecker у свойства
              5....
              ... */
            $result = call_user_func_array($callback, $param);
            
            
            
            if ($result !== true)
                return $result;
        }
        return true;
    }

    /**
     * Проверяет поле в зависимости от значений других полей.
     * Вызывает реальные checker'ы, если соблюдены conditions,
     * иначе не вызывает checker
     */
    public static function chkDepend($coreobj, $value, $real_errtext, $real_chk, array $conditions, $real_params = [])
    {
        $flag = true;
        foreach ($conditions as $key => $val) {
            if (is_array($val)) {
                if (!in_array($coreobj[$key], $val))
                    $flag = false;
            } else {
                if ($coreobj[$key] != $val)
                    $flag = false;
            }
        }
        if (!$flag)
            return true; //Сообщаем, что поле прошло прверку, если условия использования чекера не подходят

        $param = array_merge([$coreobj, $value, $real_errtext], $real_params);

        if (is_array($real_chk)) {
            $callback = $real_chk;
        } else {
            $chk = new Type\Checker();
            $callback = [$chk, $real_chk];
        }
        return call_user_func_array($callback, $param);
    }

    /**
     * Дабавляет ошибку
     * 
     * @param string $string - текст ошибки
     * @param string $form - свойство, в котором произошла ошибка
     * @return bool(false)
     */
    public function addError($string, $form = null)
    {
        $local = &self::$local[$this->_local_id];
        $local['errorlist'] [] = $string;

        if ($form !== null) {
            $local['formerror'] [$form][] = $string;
        } else {
            $local['non_form_errors'] [] = $string;
        }
        return false;
    }

    /**
     * Добавляет ошибки
     * 
     * @param mixed $array - массив с ошибками
     * @param mixed $form - свойство в котором произошли ошибки
     * @return bool(false)
     */
    public function addErrors($array, $form = null)
    {
        $local = &self::$local[$this->_local_id];
        $local['errorlist'] = array_merge($local['errorlist'], $array);

        if ($form !== null) {
            if (!isset($local['formerror'][$form]))
                $local['formerror'][$form] = $array;
            else
                $local['formerror'][$form] = array_merge($local['formerror'][$form], $array);
        } else {
            $local['non_form_errors'] = array_merge($local['non_form_errors'], $array);
        }
        return false;
    }

    /**
     * @deprecated - вместо данного метода следует использовать getErrors()
     */
    public function getLastError()
    {
        return $this->getErrors();
    }

    /**
     * Возвращает массив с текстами всех ошибок.
     * @return array
     */
    public function getErrors()
    {
        return $this->getLocalParameter('errorlist');
    }
    
    /**
    * Возвращает ошибки в виде строки
    * 
    * @return string
    */
    function getErrorsStr()
    {
        return implode(', ', $this->getErrors());
    }    

    /**
     * Возвращает true, если есть ошибки.
     * @return boolean
     */
    public function hasError()
    {
        return !empty(self::$local[$this->_local_id]['errorlist']);
    }

    /**
     * Возвращает массив форм, в которых есть ошибки
     * @return array
     */
    public function getFormError()
    {
        return array_keys(self::$local[$this->_local_id]['formerror']);
    }

    /**
     * Возвращает ассоциативный массив с ошибками в формах, если $key = null
     * Возвращает массив с ошибками, если $key != null и $sep = null
     * Возвращает строку с ошибками, с разделителем $sep, если $key != null и $sep != null
     * 
     * @param string | null $key - имя свойство
     * @param string | null $sep - разделитель
     * @return array | string
     */
    public function getErrorsByForm($key = null, $sep = null)
    {
        $local = &self::$local[$this->_local_id];
        if ($key === null)
            return $local['formerror'];
        if ($sep === null) {
            return isset($local['formerror'][$key]) ? $local['formerror'][$key] : [];
        } else {
            return isset($local['formerror'][$key]) ? implode($sep, $local['formerror'][$key]) : '';
        }
    }

    /**
     * Возвращает массив с ошибками со сведениями о поле
     * @return array
     */
    public function getDisplayErrors()
    {
        $errors_by_form = $this->getErrorsByForm();
        $non_form_errors = $this->getNonFormErrors();
        $errors = [];
        if (count($non_form_errors)) {
            $errors['@system'] = [
                'class' => 'system',
                'fieldname' => t('Системное сообщение'),
                'errors' => $non_form_errors
            ];
        }

        foreach ($errors_by_form as $key => $error_list) {
            if (self::$local[$this->_local_id]['properties'][$key]) {
                $prop = $this->getProp($key);
                $fieldname = $prop->getDescription() != '' ? $prop->getDescription() : $prop->getName();
            } else {
                $fieldname = $key;
            }
            $errors[$key] = [
                'class' => 'field',
                'fieldname' => $fieldname,
                'errors' => $error_list
            ];
        }
        return $errors;
    }

    /**
     * Возвращает true, если недостаточно прав на изменение данного объекта.
     *
     * @return bool
     * @throws RSException
     */
    public function noWriteRights($right = DefaultModuleRights::RIGHT_UPDATE)
    {
        $error = \RS\AccessControl\Rights::CheckRightError($this, $right, true);
        if ($error !== false) {
            $this->addError($error);
            return true;
        }
        return false;
    }

    /**
     * Возвращает ошибки не привязанные к формам
     * @return array
     */
    public function getNonFormErrors()
    {
        return $this->getLocalParameter('non_form_errors');
    }

    /**
     * Очищает все ошибки в объекте
     * @return void
     */
    public function clearErrors()
    {
        $this->setLocalParameter('errorlist', []);
        $this->setLocalParameter('formerror', []);
        $this->setLocalParameter('non_form_errors', []);
    }

    /**
     * Устанавливает объекту отрицательный id
     */
    function setTemporaryId()
    {
        return $this[$this->getPrimaryKeyProperty()] = -time();
    }

    /**
     * Устанавливает шаблон, который будет использоваться для создания формы
     * 
     * @param string $template - путь к шаблону
     */
    function setFormTemplate($template)
    {
        $this->setLocalParameter('form_template', $template);
    }

    /**
     * Приводит базу данных в соответствие со структурой объекта
     * 
     * @return bool
     */
    function dbUpdate()
    {
        if ($this->getStorageInstance() instanceof \RS\Orm\Storage\Db) {
            $map = new DbMap($this->getProperties(), $this->getIndexes(), static::_dbName(false), static::_tableName(false));
            return $map->sync();
        }
        return true;
    }

    /**
     * Добавляет описание индекса для данного объекта
     * 
     * @param array | string $fields - поля, которые должны войти в индекс
     * @param string $type - тип индекса. Используйте константы:
     *    self::INDEX_PRIMARY - первичный ключ
     *    self::INDEX_UNIQUE - уникальный индекс
     *    self::INDEX_KEY - неуникальный индекс
     *    self::INDEX_FULLTEXT - полнотекстовый индекс
     * @param string $name - идентификатор индекса
     * @param string $using - тип индекса BTREE | HASH
     * @return AbstractObject
     */
    function addIndex($fields, $type = self::INDEX_KEY, $name = null, $using = null)
    {
        $fields = (array) $fields;
        if ($name === null) {
            $name = str_replace(' ', '', strtolower(implode('_', $fields)));
        }
        if ($type == self::INDEX_PRIMARY) {
            $name = 'PRIMARY';
        }

        self::$class[$this->_self_class]['indexes'][$name] = [
            'fields' => $fields,
            'type' => $type,
            'name' => $name,
            'using' => $using
        ];

        //Если полю присваевается первичный ключ, то возможность установить NULL запрещается
        if ($type == self::INDEX_PRIMARY) {
            foreach ($fields as $field) {
                $this->getProp($field)->setAllowEmpty(false);
            }
        }
        return $this;
    }

    function getIndexes()
    {
        //Добавляем индексы, установленные у полей
        foreach ($this->getProperties() as $key => $property) {
            if ($property->isPrimaryKey()) {
                $this->addIndex($key, self::INDEX_PRIMARY);
            }
            if ($property->isUnique()) {
                $this->addIndex($key, self::INDEX_UNIQUE);
            }
            if ($property->isIndex()) {
                $this->addIndex($key, self::INDEX_KEY);
            }
        }

        return $this->getClassParameter('indexes');
    }

    /**
     * Возвращает HTML форму объекта, если её не существует по указанному пути, то создает её.
     *
     * @param array $tpl_vars - дополнительные параметры, передаваемые в шаблон
     * @param mixed $switch - контекст, в котором будет генерироваться форма. Позволяет скрывать какие-либо поля в зависимости от контекста
     * @param bool $is_multiedit - Если true, то это форма мультиредактирования
     * @param mixed $template - имя файла генерируемого шаблона
     * @param mixed $tpl_maker - имя шаблона, по которому будет произведена генерация
     * @param mixed $tpl_folder - каталог для генерации шаблона
     * @return string
     * @throws \SmartyException
     */
    function getForm(array $tpl_vars = null, $switch = null, $is_multiedit = false, $template = null, $tpl_maker = null, $tpl_folder = null)
    {
        if ($tpl_maker === null) {
            $tpl_maker = $is_multiedit ? '%system%/coreobject/multiedit_form.tpl' : '%system%/coreobject/src_form.tpl';
        }

        if ($tpl_folder === null) {
            $module = \RS\Module\Item::nameByObject($this);
            $tpl_folder = \Setup::$PATH . \Setup::$MODULE_FOLDER . '/' . $module . \Setup::$MODULE_TPL_FOLDER;
        }

        if ($template === null) {
            $default_filename = strtolower(str_replace('\\', '_', $this->_self_class));
            $filename = $this->getLocalParameter('form_template', $default_filename); //Получаем имя файла
            $me = $is_multiedit ? 'me_' : ''; //Префикс шаблона мультиредактирования
            $hash = $this->getPropertyHash($is_multiedit, $switch);
            $template = $tpl_folder . '/form/' . $me . $filename . '_' . strtolower($switch) . $hash . '.auto.tpl';
        }

        if (!file_exists($template)) {
            //Нужно создать шаблон исходя из полей объекта
            $make_form = new \RS\View\Engine();

            $properties = $this->getPropertyIterator();
            $make_form->assign([
                'prop' => $properties,
                'elem' => $this,
                'switch' => $switch
            ]);

            $content = $make_form->fetch($tpl_maker);

            \RS\File\Tools::makePath($template, true);
            file_put_contents($template, $content);
        }

        $inputs = new \RS\View\Engine();
        if ($tpl_folder) {
            $inputs->setTemplateDir($tpl_folder);
        }

        if ($tpl_vars) {
            $inputs->assign($tpl_vars);
        }

        $inputs->assign('elem', $this);
        return $inputs->fetch($template);
    }

    /**
     * Возвращет HTML форму одного свойства объекта.
     *
     * @param string $key - имя свойства
     * @param array $attributes - массив с атрибутами для формы
     * @param array $view_params - массив с атрибутами для формы [form => true, errors => true], form - только сама форма, errors - форма с ошибками
     * @return string
     * @throws \SmartyException
     */
    public function getPropertyView($key, $attributes = [], $view_params = [])
    {
        $sm = new \RS\View\Engine();
        $property = $this->getProp($key);
        $property->setAttr($attributes);

        $sm->assign([
            'prop' => $property,
            'view_params' => $view_params,
            'object' => $this
        ]);
        return $sm->fetch($this->getClassParameter('property_template'));
    }

    /**
     * Возвращает Хэш от списка свойств объекта
     *
     * @param bool $is_multiedit
     * @param string | null $switch
     * @return string
     */
    protected function getPropertyHash($is_multiedit, $switch = null)
    {
        $groups = $this->getPropertyIterator()->getGroups($is_multiedit, $switch);
        $str = '';
        foreach ($groups as $data) {
            $str .= $data['group'] . implode('', array_keys($data['items']));
        }

        return sprintf('%u', crc32($str));
    }

    /**
     * Возвращает ключ => значение всех полей типа Type/Hidden
     * @return array
     */
    public function getHiddenKeyVal()
    {
        $result = [];
        foreach (self::$local[$this->_local_id]['properties'] as $key => $prop) {
            if ($prop instanceof Type\Hidden) {
                $result[$key] = $this[$key];
            }
        }
        return $result;
    }

    /**
     * Добавляет скрытые поля
     * 
     * @param string|array $keys
     * @param mixed $value
     * @return void
     */
    public function addHiddenFields($keys, $value = null)
    {
        if (!is_array($keys)) {
            $keys = [$keys => $value];
        }
        foreach ($keys as $key => $value) {
            self::$local[$this->_local_id]['properties'][$key] = new Type\Hidden();
            $this[$key] = $value;
        }
    }

    /**
    * @deprecated
    * Необходимо реализовывать метод getDebugActions()
    */
    public function addDebugActions(array $actions, $local = false)
    {}

    /**
    * @deprecated
    * Необходимо реализовывать метод getDebugActions()
    */
    public function addDebugAction(\RS\Debug\Action\AbstractAction $action, $local = false)
    {}

    /**
     * Возвращает строку с необходимыми атрибутами блочного элемента для вставки в html
     * @return string | null
     */
    public function getDebugAttributes()
    {
        if ($actions = $this->getDebugActions()) {
            return \RS\Debug\Group::getContextAttributes($actions, $this);
        }
    }

    /**
     * Возвращает клон текущего объекта
     *
     * @return static
     */
    public function cloneSelf()
    {
        /** @var static $clone */
        $clone = new $this->_self_class;
        $clone->getFromArray($this->getValues());
        if (isset($clone['sortn'])){ //Если есть индекс сортировки то очистим его
           unset($clone['sortn']); 
        }
        $pk = $this->getPrimaryKeyProperty();
        if ($pk) {
            $clone[$pk] = null; //Очищаем первичный ключ объекта
        }
        
        EventManager::fire('orm.clone.' . $this->getShortAlias(), $clone);
        
        return $clone;
    }

    /**
     * Уничтожает класс объектов, в следующем конструкторе будет вызван _init
     * @return void
     */
    public static function destroyClass()
    {
        unset(self::$class[get_called_class()]);
    }

    /**
     * Вызывается перед сохранением объекта в storage
     * Если возвращено false, то сохранение не произойдет
     *
     * @param string $save_flag - тип операции (insert|update|replace)
     * @return void|false
     */
    public function beforeWrite($save_flag)
    {}

    /**
     * Вызывается после сохранения объекта в storage
     *
     * @param string $save_flag - тип операции (insert|update|replace)
     * @return void
     */
    public function afterWrite($save_flag)
    {}

    /**
     * Вызывается после загрузки объекта
     * 
     * @return void
     */
    public function afterObjectLoad()
    {}
    
    /**
    * Возвращает отладочные действия, которые можно произвести с объектом
    * 
    * @return \RS\Debug\Action[]
    */
    public function getDebugActions()
    {
        return [];
    }

    /**
     * Удаляет таблицу для данного объекта
     * 
     * @return bool
     */
    public function dropTable()
    {
        if ($this->getStorageInstance() instanceof \RS\Orm\Storage\Db) {
            try {
                $sql = "DROP TABLE IF EXISTS " . $this->_getTable();
                \RS\Db\Adapter::sqlExec($sql);
            } catch (\RS\Db\Exception $e) {
                return false;
            }
        }
        return true;
    }

    /**
     * Экспортирует все данные по ошибкам
     *
     * @return array
     */
    function exportErrors()
    {
        $local = &self::$local[$this->_local_id];

        return [
            'errors_by_form' => $local['formerror'],
            'errors_non_form' => $local['non_form_errors'],
            'errors' => $local['errorlist']
        ];
    }

    //начало свойств интерфейса итератора
    public function current()
    {
        $property = key($this->_values);
        return $this->getProp($property);
    }

    public function key()
    {
        return key($this->_values);
    }

    public function next()
    {
        next($this->_values);
        $valid = $property = key($this->_values);
        return ($valid && $this->getProp($property));
    }

    public function rewind()
    {
        reset($this->_values);
        $valid = $property = key($this->_values);
        return ($valid && $this->getProp($property));
    }

    public function valid()
    {
        return key($this->_values) !== null;
    }

    //конец свойств интерфейса итератора

    /**
     * При сериализации объекта - достаточно сохранить массив его значений.
     */
    function __sleep()
    {
        return ['_values'];
    }

    /**
     * Позволяет сконструировать объект заново после рассериализации
     */
    function __wakeup()
    {
        $this->__construct();
    }
    
    function __clone()
    {
        $this->__construct();
    }

    /**
     * Возвращает идентификатор права на чтение для данного объекта
     *
     * @return string
     */
    public function getRightRead()
    {
        return DefaultModuleRights::RIGHT_READ;
    }

    /**
     * Возвращает идентификатор права на создание для данного объекта
     *
     * @return string
     */
    public function getRightCreate()
    {
        return DefaultModuleRights::RIGHT_CREATE;
    }

    /**
     * Возвращает идентификатор права на изменение для данного объекта
     *
     * @return string
     */
    public function getRightUpdate()
    {
        return DefaultModuleRights::RIGHT_UPDATE;
    }

    /**
     * Возвращает идентификатор права на удаление для данного объекта
     *
     * @return string
     */
    public function getRightDelete()
    {
        return DefaultModuleRights::RIGHT_DELETE;
    }

    /**
     * Возвращает массив полей, которые лежат закодированными в базе данных
     *
     * @return array|null
     */
    function getHtmlEncodedFields()
    {
        static $html_encoded_fields = null;

        if($html_encoded_fields === null) {
            foreach ($this->getPropertyIterator() as $item) {
                if($item->isHtmlEncodedField()) {
                    $html_encoded_fields[] = $item->getName();
                }
            }
        }

        return $html_encoded_fields;
    }
}