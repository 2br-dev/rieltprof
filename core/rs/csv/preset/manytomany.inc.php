<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Csv\Preset;

/**
* Набор колонок описывающих связь многие ко многим с другим объектом
*/
class ManyToMany extends AbstractPreset
{
    protected
        $search_fields = [],
        $delimiter = ";",
        $orm_object,
        $id_field,
        $link_id_field,
        $link_preset_id,
        $mask,
        $mask_fields = [],
        $mask_pattern,
        $title,
        $array_field,
        $array_key,
        $array_value,
        $manylink_foreign_id_field,
        $manylink_id_field,
        $manylink_orm;
        
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
        
    function setIdField($field)
    {
        $this->id_field = $field;
    }
    
    /**
    * Устанавливает ORM объект связки многие ко многим
    * 
    * @param \RS\Orm\AbstractObject $orm
    * @return void
    */
    function setManylinkOrm(\RS\Orm\AbstractObject $orm)
    {
        $this->manylink_orm = $orm;
    }
    
    /**
    * Устанавливает поле связываемое с основным объектом в объекте связки
    * 
    * @param string $field
    * @return void
    */
    function setManylinkIdField($field)
    {
        $this->manylink_id_field = $field;
    }
    
    /**
    * Устанавливает поле, связываемое с другим объектом в объекте связки
    * 
    * @param string $field
    * @return void
    */
    function setManylinkForeignIdField($field)
    {
        $this->manylink_foreign_id_field = $field;
    }
    
    function setLinkIdField($field)
    {
        $this->link_id_field = $field;
    }
    
    function setLinkPresetId($id)
    {
        $this->link_preset_id = $id;
    }
    
    /**
    * Устанавливает маску для формирования строки из данных в CSV файле
    * 
    * @param string $mask
    * @return void
    */
    function setMask($mask)
    {
        $this->mask = $mask;
        $this->mask_fields = [];
        if (preg_match_all('/\{(.*?)\}/', $this->mask, $match)) {
            foreach($match[1] as $field) {
                $this->mask_fields[] = $field;
            }
        }
        $pattern = preg_replace_callback('/(\{.*?\})/', function($match) {
                    $field = trim($match[1], '{}');
                    return "(?P<{$field}>.*?)";
                }, $this->mask);
        $this->mask_pattern = '/^'.$pattern.'$/';        
    }
    
    /**
    * Устанавливает название экспортной колонки
    * 
    * @param mixed $title
    */
    function setTitle($title)
    {
        $this->title = $title;
    }
    
    /**
    * Загружает связанные данные
    * 
    * @return void
    */
    function loadData()
    {
        $ids = [];
        foreach($this->schema->rows as $row) {
            $ids[] = $row[$this->link_id_field];
        }
        $this->row = [];
        if ($ids) {
            $this->row = \RS\Orm\Request::make()
                ->from($this->manylink_orm, 'X')
                ->join($this->orm_object, "X.{$this->manylink_foreign_id_field} = M.{$this->id_field}", 'M')
                ->whereIn($this->manylink_id_field, $ids)
                ->objects(null, $this->manylink_id_field, true);
        }
    }
    
    function setArrayField($field)
    {
        $this->array_field = $field;
    }
    
    function setArrayKey($field)
    {
        $this->array_key = $field;
    }
    
    function setArrayValue($field)
    {
        $this->array_value = $field;
    }
    
    function getColumns()
    {
        return [
            $this->id.'-manydata' => [
                'key' => 'manydata',
                'title' => $this->title
            ]
        ];
    }
    
    function getColumnsData($n)
    {
        $id = $this->schema->rows[$n][$this->link_id_field];
        $line = '';
        if (isset($this->row[$id])) {
            $lines = [];
            foreach($this->row[$id] as $n => $item) {
                $lines[$n] = $this->mask;
                foreach($this->mask_fields as $replace_field) {
                    $lines[$n] = str_replace("{".$replace_field."}", $item[$replace_field], $lines[$n]);
                }
            }
            $line = implode($this->delimiter."\n", $lines);
        }
        return [
            $this->id.'-manydata' => $line
        ];
    }
    
    function importColumnsData()
    {
        if (isset($this->row['manydata'])) {
            $items = explode($this->delimiter, $this->row['manydata']);
            $result_array = [];
            foreach($items as $item) {
                $item = trim($item);
                if (preg_match($this->mask_pattern, $item, $values_arr)) {
                    
                    $expr = [];
                    foreach($this->search_fields as $search_field) {
                        $expr[$search_field] = $values_arr[$search_field];
                    }
                    $loaded_object = \RS\Orm\Request::make()
                        ->from($this->orm_object)
                        ->where($expr)
                        ->exec()->fetchRow();
                    if ($loaded_object) {
                        $values_arr = $loaded_object + $values_arr;
                        if ($this->array_key === null) {
                            $result_array[] = $values_arr[$this->array_value];
                        } else {
                            $result_array[$values_arr[$this->array_key]] = $values_arr[$this->array_value];
                        }
                    }
                }
            }
            
            $this->schema->getPreset($this->link_preset_id)->row[$this->array_field] = $result_array;
        }
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
    
}