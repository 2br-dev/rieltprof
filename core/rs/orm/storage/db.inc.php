<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Orm\Storage;
use \RS\Db\Adapter;

/**
* Класс обеспечивающий хранение ORM объекта в базе данных
*/
class Db extends AbstractTableStorage
{
    /**
    * Загружает объект по первичному ключу
    * 
    * @param mixed $primaryKey - значение первичного ключа
    * @return object
    */
    public function load($primaryKeyValue = null)
    {
        if ($primaryKeyValue !== null) {
            $primary = $this->orm_object->getPrimaryKeyProperty();
            $dbresult = Adapter::sqlExec("SELECT * FROM {$this->table} WHERE " . $this->getPrimaryKeyExprStr($primaryKeyValue));
            $row = $dbresult->fetchRow();
            if ($row !== false) {
                $this->orm_object->getFromArray($row, null, false, true);
                return true;
            }
        }
        return false;
    }
    
    /**
    * Возвращает true, если объект существует в базе
    * 
    * @param mixed $primaryKeyValue - значение первичного ключа
    * @return bool
    */
    public function exists($primaryKeyValue)
    {
        $primary = $this->orm_object->getPrimaryKeyProperty();
        $dbresult = Adapter::sqlExec("SELECT COUNT(*) as cnt FROM {$this->table} 
                                                WHERE ".$this->getPrimaryKeyExprStr($primaryKeyValue));
        $row = $dbresult -> fetchRow();
        return ($row["cnt"] == "1");
    }
    
    /**
    * Возвращает подготовленные данные для сохранения в БД
    * 
    * @param bool $only_modified - если true, то будут возвращены только 
    * измененные данные, иначе - все.
    * @return array
    */
    protected function prepareForDB($only_modified = false)
    {
        $query = [];
        $properties = $this->orm_object->getProperties();
        
        foreach ($properties as $key=>$property) {
            if ($property->beforesave()) {
                $this->orm_object[$key] = $property->get();
            }
            
            if ($only_modified && !$this->orm_object->isModified($key)) continue;
            if (!$property->isUseToSave()) continue;
            
            if (!$property->isRuntime() && ($this->orm_object->isModified($key) || $this->orm_object->offsetExists($key)) ) {
                if (is_null($property->get()) && $property->isAllowEmpty()) {
                    $query[] = "`$key` = NULL";
                } else {
                    $query[] = "`$key` = '".Adapter::escape($property->get())."'";
                }
            }
        }
        return $query;
    }

    /**
    * Вставляет объект в БД
    * 
    * @param string $type - тип вставки insert или replace
    * @param array $on_duplicate_update_keys - поля, которые необходимо обновить в случае если запись уже существует
    * @param array $on_duplicate_uniq_fields - поля, которые точно идетифицируют текущаю запись, для подгрузки id объекта при обновлении
    * @return bool
    */
    public function insert($type = 'INSERT', $on_duplicate_update_keys = [], $on_duplicate_uniq_fields = [])
    {
        $sql = "$type INTO {$this->table} SET ";
        $query = $this->prepareForDB();
        
        if (empty($query)) return true; //Ни одно свойство не изменилось, запрос выполнять не нужно
        
        $sql .= implode(",",$query);
        
        if ($on_duplicate_update_keys) {
            if (!$on_duplicate_uniq_fields) {
                throw new \RS\Orm\Exception(t('Не задан параметр on_duplicate_uniq_fields'));
            }
            $on_duplicate = [];
            foreach($on_duplicate_update_keys as $field) {
                $on_duplicate[] = "`$field` = VALUES(`$field`)";
            }
            $sql .= ' ON DUPLICATE KEY UPDATE '.implode(',', $on_duplicate);
        }
        
        try {
            $dbresult = Adapter::sqlExec($sql);
        } catch (\RS\Db\Exception $e) {
            if ($e->getCode() == 1062) {
                $index_fields = $this->parseDuplicateIndexName($e->getMessage());
                $this->orm_object->addError(t('Запись с таким уникальным идентификатором уже присутствует %0', [$index_fields]));
            } else {
                throw new \RS\Db\Exception($e->getMessage(), $e->getCode());
            }
          return false;
        }
        $primary_key = $this->orm_object->getPrimaryKeyProperty();
            
        if ($on_duplicate_update_keys) {
            //Сообщаем ORM объекту, что он был обновлен а не создан.
            $updated = $dbresult->affectedRows() != 1;
            $this->orm_object->setLocalParameter('duplicate_updated', $updated );
            
            if ($updated && $primary_key !==false) {
                //Если произошло обновление объекта, то устанавливаем объекту значение первичного ключа
                $expr = [];
                foreach($on_duplicate_uniq_fields as $field) {
                    $expr[] = "`$field` = '".Adapter::escape($this->orm_object[$field])."'";
                }
            
                $sql = "SELECT ".implode(',', (array)$primary_key)." FROM {$this->table} WHERE ".implode(' AND ', $expr)." LIMIT 1";
                if ($row = Adapter::sqlExec($sql)->fetchRow()) {
                    foreach($row as $key => $value) {
                        $this->orm_object[$key] = $value;
                    }
                }
            }
        } 
        
        if (is_string($primary_key) && empty($this->orm_object[$primary_key]) && empty($updated)) {
            //Устанавливаем объекту автоинкрементный ID
            $this->orm_object[$primary_key] = Adapter::lastInsertId();
        }
        
        return true;
    }
    
    /**
    * Обновляет объект в хранилище
    * 
    * @param $primaryKey - значение первичного ключа
    * @return bool
    */
    public function update($primaryKey = null)
    {
        $sql = "UPDATE {$this->table} SET ";
        $query = $this->prepareForDB(true);
        if (empty($query)) return true; //Ни одно свойство не изменилось, запрос выполнять не нужно
        
        $sql .= implode(",",$query) . " WHERE ".$this->getPrimaryKeyExprStr($primaryKey);
        
        try {
            Adapter::sqlExec($sql);
        } catch (\RS\Db\Exception $e) {
            if ($e->getCode() == 1062) {
                $index_fields = $this->parseDuplicateIndexName($e->getMessage());
                $this->orm_object->addError(t('Запись с таким уникальным идентификатором уже присутствует %0', [$index_fields]));
            } else {
                throw new \RS\Db\Exception($e->getMessage(), $e->getCode());
            }                
            return false;
        }        
        return true;
    }
    
    /**
    * Перезаписывает объект в хранилище
    * 
    * @return bool
    */
    public function replace()
    {
        return $this->insert('REPLACE');
    }
    
    /**
    * Удаляет объект из хранилища
    * 
    * @return bool
    */
    public function delete()
    {
        $sql = "DELETE FROM {$this->table} WHERE ".$this->getPrimaryKeyExprStr();
        return Adapter::sqlExec($sql)->affectedRows();
    }
    
    /**
    * Возвращает условие для выборки по первичному ключу в виде для SQL Where
    * 
    * @param mixed $primaryKey - значение первичного ключа
    * @return string
    */
    protected function getPrimaryKeyExprStr($primaryKey = null)
    {
        $pk = $this->getPrimaryKeyExpr($primaryKey);
        $expr = [];
        foreach($pk as $key => $value) {
            $expr[] = "`$key` = '".Adapter::escape($value)."'";
        }
        return implode(' AND ', $expr);
    }
    
    /**
    * Парсит ошибку о дублировании записи и возвращает названия полей, 
    * по которым произошло дублирование
    * 
    * @param string $error_text - текст ошибки, который возвращает Mysql
    * @return string
    */
    protected function parseDuplicateIndexName($error_text)
    {
        if (preg_match('/for key \'(.*?)\'/', $error_text, $match)) {
            $result = [];
            $index_name = $match[1];
            $indexes = $this->orm_object->getIndexes();
            if (isset($indexes[$index_name])) {
                foreach($indexes[$index_name]['fields'] as $field) {
                    $result[] = $this->orm_object["__{$field}"]->getDescription();
                }
            }
            return '('.implode(',', $result).')';
        }
    }
    
}
