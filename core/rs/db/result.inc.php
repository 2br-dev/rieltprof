<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Db;

/**
* Класс содержит методы для работы с результатом SQL запроса.
*/
class Result
{
    public        
        $auto_free = true; //Автоматически освобождать память после получения результатов запроса.
    
    protected 
        /**
        * @var mysqli_result 
        */
        $resource,
        $res_type = MYSQLI_ASSOC;        
    
    function __construct($resource)
    {
        $this -> resource = $resource;
    }
    
    /**
    * Возвращает результат в виде объекта
    * 
    * @param string $class_name - имя класса возвращаемых объектов
    * @param array | null $params - дополнительные параметры для конструктора
    * @return mixed
    */
    function fetchObject($class_name = null, array $params = null)
    {
        if (is_bool($this->resource)) return false;
        if ($class_name === null) $class_name = 'stdClass';
        if ($params === null) {
            $row = mysqli_fetch_object($this->resource, $class_name);
        } else {
            $row = mysqli_fetch_object($this->resource, $class_name, $params);
        }
        if ($this->auto_free && $row === false) $this->free();
        return $row;
    }
    
    /**
    * Возвращает список строк результата
    * 
    * @return array
    */
    function fetchAll()
    {
        $res = [];
        while ($row = $this->fetchRow()) {
            $res[] = $row;
        }
        return $res;
    }
    
    /**
    * Возвращает одну строку результата
    * 
    * @return array
    */
    function fetchRow()
    {
        if (is_bool($this->resource)) return false;
        $row = mysqli_fetch_array($this -> resource, $this -> res_type);
        
        if ($this->auto_free && $row === false) $this->free();
        return $row ?: false;
    }
    
    /**
    * Возвращает количество строк в результате
    * 
    * @return integer
    */
    function rowCount()
    {
        if (is_bool($this->resource)) return 0;
        return mysqli_num_rows($this -> resource);
    }
    
    /**
    * Устанавливает тип возвращаемого массива функциями getRow
    * 
    * @param mixed MYSQL_ASSOC, MYSQL_NUM, MYSQL_BOTH
    * @return void
    */
    function setResultType($type)
    {
        $this -> res_type = $type;
    }
    
    /**
    * Освобождает память, связанную с результатом выполнения запроса.
    * 
    * @return void
    */
    function free()
    {
        return mysqli_free_result($this -> resource);
    }
    
    /**
    * Возвращает значение колонки field у первой строчки выборки или default, если строки или колонки нет.
    * 
    * @param string $field - имя поля, значние которого необходимо вернуть
    * @param mixed $default - значение по-умолчанию
    * @return mixed
    */
    function getOneField($field, $default = null)
    {
        $row = $this->fetchRow();
        if ($this->auto_free && $row !== false) $this->free();
        return isset($row[$field]) ? $row[$field] : $default;
    }
    
    /**
    * Возвращает результат в виде массива, подставляя в качестве ключа столбец key, а в значение столбцы value
    * 
    * @param mixed $key - столбец, значение которого пойдет в ключ. Если null, то результат будет нумерованным массивом
    * @param mixed $value - может быть строка с названием столбца или массив с названиями столбцов.
    * @param boolean $allow_sublist - Если true и задан $key, то будет возможность возвращать несколько значений на один и тот же $key
    * @return array
    */
    function fetchSelected($key, $value = null, $allow_sublist = false)
    {
        if ($is_array = is_array($value)) {
            $value = array_flip($value);
        }
        $tmp = [];
        while ($row = $this->fetchRow())
        {
            if ($value === null) {
                $val = $row;
            } else {
                $val = $is_array ? array_intersect_key($row, $value) : $row[$value];
            }

            if (is_null($key)) {
                $tmp[] = $val;
            } elseif ($allow_sublist) {
                $tmp[$row[$key]][] = $val;
            } else {
                $tmp[$row[$key]] = $val;
            }
        }
        return $tmp;
    }
    
    /**
    * Возвращает количество измененных строк
    * 
    * @return integer
    */
    function affectedRows()
    {
        return Adapter::affectedRows();
    }
    
}


