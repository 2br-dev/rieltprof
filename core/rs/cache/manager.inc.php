<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Cache;

use RS\File\Tools as FileTools;

/**
* Класс, содержащий api функции для работы с кэшем
*/
class Manager
{
    protected
        $cache_folder = CACHE_FOLDER, //Определено в \Setup;
        $cache_table_folder = CACHE_TABLE_FOLDER,
        
        $tags = [],
        $before_expire,
        $watch_tables = null;
    
    public
        $enabled = CACHE_ENABLED, //Определено в \Setup
        $expire = CACHE_TIME; //в секундах, срок в течение котороо кэш считается валидным. 0 - бессрочно (для ручной инвалидации)
        
    private static 
        $instance;
        
    function __construct()
    {
        //Создаем папку, если ее не существует
        FileTools::makePath($this->cache_folder);
        FileTools::makePrivateDir($this->cache_folder);
        FileTools::makePath($this->cache_table_folder);
    }
    
    /**
    * Возвращает экземпляр текущего объекта
    * @return Manager
    */
    public static function obj()
    {
        return new self();
    }
    
    
    /**
    * Устанавливает срок в секундах в течение котороо кэш считается валидным. Сбрасывается после вызова метода request
    * 
    * @param integer $time - количество секунд хранения кэша
    * @return Manager
    */
    function expire($time)
    {
        $this->before_expire = $this->expire;
        $this->expire = (int)$time;
        return $this;
    }    
    
    /**
    * Задает теги, по которым далее можно будет сбросить кэш
    * 
    * @param string | array $tag тег или список тегов
    * @param string $tag тег
    * ...
    * @return Manager
    */
    function tags($tags = null)
    {
        if (!is_array($tags)) {
            $tags = func_get_args();
        }
        $this->tags = $tags;
        return $this;
    }
    
    /**
    * Выполняет функцию и кэширует ее результаты или возвращает результаты из кэша
    * 
    * @param callback $callback
    * @param mixed параметр
    * @param mixed параметр
    * ...
    * @return mixed результат вызова $callback
    */
    function request($callback, ...$params)
    {
        $key = $this->keyByCallback($callback, $params);
        $validate = $this->validate($key);

        if ($validate) {
            $result = $this->read($key);
        }

        if (!$validate || $result === false) {
            $result = call_user_func_array($callback, $params);
            $this->write($key, $result);
            if (isset($this->watch_tables)) {
                $this->tablesIsActual($this->watch_tables, $key);
            }
        }
        
        if (isset($this->before_expire)) {
            $this->expire = $this->before_expire;
            $this->before_expire = null;
        }
        
        $this->watch_tables = null;
        
        return $result;        
    }
    
    /**
    * Сбрасывает кэш по заданным параметрам
    * 
    * @param string | array $callback. В качестве имени метода допустимо использовать '*', что будет означать - удаление кэша всех методов класса.
    * @param параметр
    * @param параметр
    * ...
    * @return void
    */
    function invalidate($callback, ...$params)
    {
        if (is_array($callback)) {
            $class = is_object($callback[0]) ? get_class($callback[0]) : ltrim($callback[0], '\\');
            $method = $callback[1];
        } 
        elseif (is_object($callback)) {
            $class = get_class($callback);
            $method = '*';
        } else {
            $class = 'func';
            $method = $callback;
        }
        $mask = $this->prepareClass($class).'_'.$method;

        if ($method != '*') {
            if (!empty($params)) {
                $mask .= '_'.sprintf('%u', crc32(serialize($params))).'*';
            } else {
                $mask .= '_*';
            }
        }    
        if ($list = glob($this->cache_folder.'/'.$mask)) {
            foreach($list as $filename) {
                unlink($filename);
            }
        }
    }
    
    /**
    * Сбрасывает абсолютно весь кэш
    * @return void
    */
    function invalidateAll()
    {
        FileTools::deleteFolder($this->cache_folder, false);
    }
    
    
    /**
    * Возвращает уникальный идентификатор для функции callback и параметорв
    * 
    * @param mixed $callback
    * @param mixed $params
    * @return string
    */
    protected function keyByCallback($callback, $params = [])
    {        
        if (is_array($callback)) {
            $class = is_object($callback[0]) ? get_class($callback[0]) : $callback[0];
            $method = $callback[1];
        } else {
            $class = 'func';
            $method = $callback;
        }
        
        $tags = '';
        if (!empty($this->tags)) {
            foreach($this->tags as $tag) {
                $tags .= '_tag'.sprintf('%u', crc32($tag));
            }
        }
        
        return $this->prepareClass($class).'_'.$method.'_'.sprintf('%u', crc32(serialize($params))).$tags;
    }

    /**
     * Возвращает строку из добавленных ранее тегов, которая может использоваться в имени файла кэш файла
     *
     * @return string
     */
    function getTagsKey()
    {
        $tags = '';
        if (!empty($this->tags)) {
            foreach($this->tags as $tag) {
                $tags .= '_tag'.sprintf('%u', crc32($tag));
            }
        }

        return $tags;
    }
    
    /**
    * Генерирует имя ключа с учетом заданных раннее тегов, которое можно использовать в методе read, write.
    * 
    * @param mixed $user_key - произвольный ключ
    * @return string
    */
    function generateKey($user_key)
    {
        return 'userkey_'.sprintf('%u', crc32($user_key)).$this->getTagsKey();
    }
    
    /**
    * Сбрасывает кэш по тегам
    * 
    * @param string | array $tags - тег или массив тегов
    * @return void
    */
    function invalidateByTags($tags)
    {
        $tags = (array)$tags;
        foreach($tags as $tag) {
            $mask = '*tag'.sprintf('%u', crc32($tag)).'*';
            $filelist = glob($this->cache_folder.'/'.$mask);
            if ($filelist) {
                foreach($filelist as $filename) {
                    unlink($filename);
                }
            }
        }
    }
    
    /**
    * Проверяет, актуален ли кэш.
    * @param mixed $key ключ
    * @return boolean True - да, кэш можно использовать, False - Кэш не атуален
    */
    function validate($key)
    {
        $filename = $this->cache_folder.'/'.$key;
        if (!$this->enabled || !file_exists($filename)) return false;
        if ($this->expire>0 && filemtime($filename) < time()-$this->expire) return false;
        if ($this->watch_tables !== null && !$this->checkTableActual($this->watch_tables, $key)) return false;
        return true;
    }
    
    /**
    * Проверяет, существует ли кэш
    * 
    * @param mixed $key ключ
    * @return bool - если true, то кэш с таким key существует
    */
    function exists($key)
    {
        $filename = $this->cache_folder.'/'.$key;
        return file_exists($filename);
    }
    
    /**
    * Делает пометку, что таблица изменена
    * @param string $table Имя таблицы
    * @return void
    */
    function tableIsChanged($table, $db = DB_NAME)
    {
        $file = $this->tableFilename($table, $db);
        if (file_exists($file)) unlink($file);
    }
    
    /**
    * Делает пометку, что таблица находится в актуальном состоянии
    * @param string $table Имя таблицы
    * @return void
    */
    function tablesIsActual(array $tables, $key)
    {
        foreach($tables as $table)        
        {
            list($db, $table) = $this->normalizeTable($table);
            $file = $this->tableFilename($table, $db);
            file_put_contents($file, $key."\n", FILE_APPEND);
        }
    }
    
    /**
    * Возвращает массив с именем базы данных и именем таблицы.
    * 
    * @param mixed $table
    * @return array of string 
    */
    function normalizeTable($table)
    {
        if (is_array($table)) {
            $db = $table[0];
            $table = $table[1];
        } else {
            $db = \Setup::$DB_NAME;
        }
        return [$db, $table];
    }

    /**
    * Возвращает true - если все таблицы актуальны
    * @param array $tables список таблиц
    * @return boolean
    */
    protected function checkTableActual(array $tables, $key)
    {
        $result = true;
        foreach($tables as $table) {
            list($db, $table) = $this->normalizeTable($table);
            $file = $this->tableFilename($table, $db);
            $result = $result 
                && (file_exists($file) 
                && (strpos(file_get_contents($file), $key."\n") !== false));
        }
        return $result;
    }
    
    /**
    * Устанавливает какие таблицы отвечают за актуальность кэша для следующего вызова request
    * 
    * @param string|\RS\Orm\AbstractObject|array $tables
    * @return Manager
    */
    function watchTables($tables)
    {
        if ($tables instanceof \RS\Orm\AbstractObject) $tables = [$tables->_getTableArray()];
        if (!is_array($tables)) $tables = [$tables];
        $this->watch_tables = $tables;
        return $this;
    }
    
    /**
    * Возвращает специально экранированное имя класса
    * 
    * @param mixed $class_name
    * @return string
    */
    function prepareClass($class_name)
    {
        return str_replace('\\', '_', $class_name);
    }
    
    /**
    * Возвращает имя файла для пары база данных, таблица
    * 
    * @param mixed $table
    * @param mixed $db
    * @return string
    */
    function tableFilename($table, $db)
    {
        return $this->cache_table_folder.'/'.$db.'-'.$table.'.tmp';
    }
        
    /**
    * Производит запись данных на диск
    * 
    * @param mixed $key - ключ
    * @param mixed $value - значение
    * @return integer - количество записанных байт
    */
    function write($key, $value)
    {
        $data = serialize($value);
        return file_put_contents($this->cache_folder.'/'.$key, $data);
    }
    
    /**
    * Производит чтение данных с диска
    * 
    * @param string $key - ключ
    * @return mixed
    */
    function read($key)
    {
        $data = file_get_contents($this->cache_folder.'/'.$key);
        return @unserialize($data);
    }
}
