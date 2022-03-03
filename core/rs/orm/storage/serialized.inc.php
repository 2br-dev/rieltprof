<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Orm\Storage;
use \RS\Orm\Request;

/**
* Класс обеспечивающий хранение orm объекта в базе данных в сериализованном виде
*/
class Serialized extends AbstractTableStorage
{
    /**
    * Загружает объект по первичному ключу
    * 
    * @param mixed $primaryKey - значение первичного ключа
    * @return object
    */    
    public function load($primaryKeyValue = null)
    {        
        $row = Request::make()
            ->from($this->table)
            ->where($this->getPrimaryKeyExpr($primaryKeyValue))
            ->exec()->fetchRow();
        
        if ($row === false) return false;
        
        $data = @unserialize($row[$this->getOption('data_field', 'data')]);

        $this->orm_object->getFromArray($data, null, false, true);
        return true;
    }
    
    /**
    * Возвращает true, если объект с таким первичным ключем существует в хранилище
    * 
    * @param mixed $primaryKeyValue - значение первичного ключа
    * @return boolean
    */
    public function exists($primaryKeyValue)
    {
        $row = Request::make()
            ->from($this->table)
            ->where($this->getPrimaryKeyExpr($primaryKeyValue))
            ->count();
        return ($row > 0);
    }
    
    /**
    * Подготавливает сериализованные данные для сохранения
    * 
    * @return string
    */
    protected function prepareForDB()
    {
        $query = [];
        $properties = $this->orm_object->getProperties();
        $pk_field = (array)$this->orm_object->getPrimaryKeyProperty();
        $data = [];
        foreach ($properties as $key => $property)
        {
            //Не сохраняем PrimaryKey в data
            if (in_array($key, $pk_field)) continue;
            
            if ($property->beforesave()) {
                $this->orm_object[$key] = $property->get();
            }            
            if (!$property->isUseToSave()) continue;
            if (!$property->isRuntime()) {
                $data[$key] = $property->get();
            }
        }
        return serialize($data);
    }

    /**
    * Добавляет объект в хранилище
    * 
    * @return bool
    */   
    public function insert()
    {
        return $this->replace();
    }
    
    /**
    * Обновляет объект в хранилище
    * 
    * @param $primaryKey - значение первичного ключа
    * @return bool
    */    
    public function update($primaryKeyValue = null)
    {
        return $this->insert();
    }
    
    /**
    * Перезаписывает объект в хранилище
    * 
    * @return bool
    */    
    public function replace()
    {
        $sql = "replace into {$this->table} set ";
        
        $arr_data = $this->getPrimaryKeyExpr();
        $arr_data[$this->getOption('data_field', 'data')] = $this->prepareForDB();

        $fields = [];
        foreach($arr_data as $key => $value) {
            $fields[] = "`$key` = '".\RS\Db\Adapter::escape($value)."'";
        }
        
        $sql .= implode(",", $fields);
        try {
            $dbresult = \RS\Db\Adapter::sqlExec($sql);
        } catch (\RS\Db\Exception $e) {
            if ($e->getCode() == 1062) {
                $this->orm_object->addError(t('Запись с таким уникальным идентификатором уже присутствует'));
            } else {
                throw new \RS\Db\Exception($e->getMessage(), $e->getCode());
            }
          return false;
        }
        
        return true;
    }
    
    /**
    * Удаляет объект из хранилища
    * 
    * @return bool
    */    
    public function delete()
    {
        return Request::make()
            ->delete()
            ->from($this->table)
            ->where($this->getPrimaryKeyExpr())
            ->exec()->affectedRows();
    }    
    
    /**
    * Возвращает условие для выборки по первичному ключу
    * 
    * @param array | string $primaryKeyValue - первичный ключ. Если первичный ключ простой, то ожидается скалярный тип, 
    * если составной, то массив [поле1 => 'значение', поле2 => 'значение']
    * @return array массив с парами ключ => значение
    */
    protected function getPrimaryKeyExpr($primaryKeyValue = null)
    {
        $result = parent::getPrimaryKeyExpr($primaryKeyValue);
        return $this->getOption('primary', []) + $result;
    }
}