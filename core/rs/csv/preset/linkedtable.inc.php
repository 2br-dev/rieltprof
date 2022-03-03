<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Csv\Preset;

/**
* Экспортирует колонки из присоедененной таблицы
*/
class LinkedTable extends AbstractPreset
{
    protected
        $use_cache = true,
        $cache = [],
        $search_fields = [],
        $orm_object,
        $is_multisite,
        $load_expression,
        $titles = [],
        
        $fields = [],
        $exclude_fields = [],
        $save = true,
        $id_field,
        $null_sign_fields,
        $link_foreign_field,
        $link_preset_id,
        $link_default_value;
        
    /**
    * Устанавливает какие поля следует исключить из выгрузки
    * 
    * @param array $fields
    * @return void
    */
    function setExcludeFields($fields)
    {
        $this->exclude_fields = $fields;
    }
    
    /**
    * Устанавливает пользовательские названия для колонок
    * 
    * @param array $titles
    * @return void
    */
    function setTitles(array $titles)
    {
        $this->titles = $titles;    
    }
    
    
    /**
    * Устанавливает свойства, которые должны появиться в экспорте
    * 
    * @param array $fields
    */
    function setFields(array $fields)
    {
        $this->fields = array_combine($fields, $fields);
    }    
        
        
    /**
    * Устанавливает объект, связанный с данным набором колонок
    * 
    * @param mixed $orm_object
    */
    function setOrmObject(\RS\Orm\AbstractObject $orm_object)
    {
        $this->orm_object = $orm_object;
    }
    
    
    /**
    * Возвращает объект, связанный с данным набором колонок
    * 
    * @return \RS\Orm\AbstractObject
    */
    function getOrmObject()
    {
        return $this->orm_object;
    }          
        
    /**
    * Возвращает колонки, которые добавляются текущим набором 
    */
    function getColumns()
    {
        $result = [];
        foreach($this->orm_object->getProperties() as $key => $property) {
            
            if (!in_array($key, $this->exclude_fields) && (!$this->fields || isset($this->fields[$key])) && (!$property->isRuntime() || isset($this->fields[$key]))) {
                $title = isset($this->titles[$key]) ? $this->titles[$key] : $property->getTitle();
                
                $result[$this->id.'-'.$key] = [
                    'key' => $key,
                    'title' => $title
                ];
            }
        }
        
        return $result;
    }        
    
    /**
    * Устанавливает по какому полю текущий объект связан с другим объектом
    * 
    * @param string $field
    * @return void
    */
    function setIdField($field)
    {
        $this->id_field = $field;
    }
    
    /**
    * Определяет foreign key другого объекта
    * 
    * @param string $field
    * @return void
    */
    function setLinkForeignField($field)
    {
        $this->link_foreign_field = $field;
    }
    
    /**
    * Устанавливает номер пресета, к которому линкуется текущий пресет
    * 
    * @param integer $n - номер пресета
    * @return void
    */
    function setLinkPresetId($n)
    {
        $this->link_preset_id = $n;
    }
    
    /**
    * Устанавливает пустые значения каких полей будут являться поводом считать, что данный объект создавать не нужно
    * 
    * @param mixed $fields
    */
    function setNullSign($fields)
    {
        $this->null_sign_fields = $fields;
    }
    
    /**
    * Проверяет, подходит ли текущие значения под определения NULL. (Если такие признаки имеются, то это значит, что этот объект создавать не нужно)
    * 
    * @return bool Возвращает true, если null был обнаружен
    */
    function checkNullSign()
    {
        if (!$this->null_sign_fields) {
            $this->null_sign_fields = $this->getFields();
        }
        $is_null = true;
        foreach($this->null_sign_fields as $null_fields) {
            if ($this->row[$null_fields] !== '') $is_null = false;
        }
        return $is_null;
    }
    
    /**
    * Устанавливает какое значение нужно подставить в линковочный класс, если будет ясно, что текущий объект - null
    * 
    * @param mixed $value
    */
    function setLinkDefaultValue($value)
    {
        $this->link_default_value = $value;
    }
    
    function loadData()
    {
        $ids = [];
        foreach($this->schema->rows as $row) {
            $ids[] = $row[$this->link_foreign_field];
        }
        
        if (count($ids)) {
            $fields = $this->fields;
            $fields[] = $this->id_field;
            
            $this->rows = \RS\Orm\Request::make()
                ->select(implode(',', $fields))
                ->from($this->getOrmObject())
                ->whereIn($this->id_field, $ids)
                ->exec()->fetchSelected($this->id_field);
        }
    }
    
    /**
    * Устанавливает, создавать или обновлять объект в базе.
    * 
    * @param bool $save
    * @return void
    */
    function setSave($save)
    {
        $this->save = $save;
    }
    
    
    /**
    * Возвращает набор колонок с данными для одной строки
    * 
    * @param mixed $n
    */
    function getColumnsData($n)
    {
        $my_id = $this->schema->rows[$n][$this->link_foreign_field];
        
        $this->row = [];
        foreach($this->getColumns() as $id => $column) {
            $value = isset($this->rows[$my_id][$column['key']]) ? $this->rows[$my_id][$column['key']] : '';
            $this->row[$id] = $value;
        }
        return $this->row;
    }
    
    
    
    /**
    * Импортирует данные одной строки текущего пресета в базу
    */
    function importColumnsData()
    {
        $orm_object = $this->loadObject();
        $my_id = null;
        
        if ($orm_object) {
            //Обновление
            if ($this->save) {
                $orm_object->update();
            }
            $my_id = $orm_object[$this->id_field];
        } else {
            //Создание
            if ($this->save) {
                if ($this->checkNullSign()) {
                    $my_id = $this->link_default_value;
                } else {
                    $class = get_class($this->getOrmObject());
                    $orm_object = new $class;                            
                    $orm_object->getFromArray($this->row);
                    $orm_object->insert();
                    $my_id = $orm_object[$this->id_field];
                }
            }
        }
        
        $preset = $this->schema->getPreset($this->link_preset_id);
        $preset->row[$this->link_foreign_field] = $my_id;
    }    
    
    /**
    * Загружает объект из базы по имеющимся данным в row или возвращает false
    * 
    * @return \RS\Orm\AbstractObject
    */
    function loadObject()
    {
        $cache_key = implode('.', array_keys($this->getSearchExpr())).implode('.', $this->getSearchExpr());
        
        if (!$this->use_cache || !isset($this->cache[$cache_key])) {
            $q = \RS\Orm\Request::make()
                    ->from($this->getOrmObject())
                    ->where($this->getSearchExpr())
                    ->where($this->getMultisiteExpr());
            
            if ($this->load_expression) {
                $q->where($this->load_expression);
            }

            $object = $q->object();
            if ($object) {
                $this->cache[$cache_key] = $object;
            } else {
                return false;
            }
        }
        return $this->cache[$cache_key];
    }        
    
    
    /**
    * Поля для поиска
    * 
    * @param array $fields
    * @return void
    */
    function setSearchFields(array $fields)
    {
        $this->search_fields = $fields;
    }
    
    
    /**
    * Возвращает поля, которые будут участвовать в выгрузке
    */
    function getFields()
    {
        if (!$this->fields) {
            $this->fields = [];
            foreach($this->getColumns() as $column) {
                    $this->fields[] = $column['key'];
            }
        }
        return $this->fields;
    }    
    
    /**
    * Возвращает массив для условий для поиска
    * 
    * @return array | null
    */
    function getSearchExpr()
    {
        if (!$this->search_fields) {
            $this->search_fields = $this->getFields();
        }
            
        $search_expr = [];
        foreach($this->search_fields as $field) {
            $search_expr[$field] = isset($this->row[$field]) ? $this->row[$field] : '';
        }
        return $search_expr;
    }     
    
    
    /**
    * Устанавливает дополнительное условие для поиска уже имеющегося элемента в базе во время импорта.
    * 
    * @param array | string $expr
    * @return void
    */
    function setLoadExpression($expr)
    {
        $this->load_expression = $expr;
    }    
    
    /**
    * Добавляет дополнительное условие в виде site_id = ТЕКУЩИЙ САЙТ, если задано true
    * 
    * @param bool $bool
    * @return void
    */
    function setMultisite($bool)
    {
        $this->is_multisite = $bool;
    }
    
    /**
    * Возвращает условие для добавления к Where, если установлено свойство multisite => true
    * 
    * @return array
    */
    function getMultisiteExpr()
    {
        return $this->is_multisite ? ['site_id' => \RS\Site\Manager::getSiteId()] : [];
    }        
        
}
