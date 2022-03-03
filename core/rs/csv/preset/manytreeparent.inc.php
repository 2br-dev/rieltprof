<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Csv\Preset;

/**
* Добавляет колонку описывающую связь многие ко многим с древовидным списком
*/
class ManyTreeParent extends AbstractPreset
{
    protected static 
        $cache_path = [];
        
    protected
        $tree_delimiter = '/',
        $delimiter = ";",
        $id_field,
        $link_id_field,
        $link_preset_id,
        $orm_object,
        $is_multisite,
        
        $title,
        $array_field,
        
        $tree_field,
        $tree_parent_field,
        $root_value,
        
        $manylink_foreign_id_field,
        $manylink_id_field,
        $manylink_orm;

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
    * Устанавливает название экспортной колонки
    * 
    * @param mixed $title
    */
    function setTitle($title)
    {
        $this->title = $title;
    }
    
    function setTreeField($field)
    {
        $this->tree_field = $field;
    }
    
    function setTreeParentField($field)
    {
        $this->tree_parent_field = $field;
    }
    
    function setRootValue($value)
    {
        $this->root_value = $value;
    }
    
    /**
    * Загружает связанные данные
    * 
    * @return void
    */
    function loadData()
    {
        $this->rows = [];
        $ids = [];
        foreach($this->schema->rows as $row) {
            $ids[] = $row[$this->link_id_field];
        }
        if ($ids) {
            $this->rows = \RS\Orm\Request::make()
                ->select("O.*, X.{$this->manylink_foreign_id_field} as foreignId")
                ->from($this->orm_object, 'O')
                ->join($this->manylink_orm, "X.{$this->manylink_id_field} = O.{$this->id_field}", 'X')
                ->where("X.{$this->manylink_foreign_id_field} in (".implode(',', $ids).")")
                ->objects(null, 'foreignId', true);
        }
    }
    
    function setArrayField($field)
    {
        $this->array_field = $field;
    }
        
    function getColumns()
    {
        return [
            $this->id.'-manytree' => [
                'key' => 'manytree',
                'title' => $this->title
            ]];
    }
    
    function getColumnsData($n)
    {
        $this->row = [];
        $base_id = $this->schema->getPreset($this->link_preset_id)->rows[$n][$this->link_id_field];

        $parts = [];
        foreach($this->getColumns() as $id => $column) {
            if (isset($this->rows[$base_id])) {
                foreach($this->rows[$base_id] as $linked_tree_item) {
                    $parts[] = $this->getRecursiveField($linked_tree_item[$this->link_id_field]);
                }
            }
            
            $this->row[$id] = implode($this->delimiter."\n", $parts);
        }
        return $this->row;
    }
    
    function importColumnsData()
    {
        $tree_values = explode($this->delimiter, $this->row['manytree']);
        $insert_ids = [];
        
        foreach($tree_values as $tree_value) {
            $tree_value = trim($tree_value);
            $parent_id_value = $this->root_value;
            if (!empty($tree_value)) {
                $tree_value = str_replace('\\', $this->tree_delimiter, $tree_value); //Для совместимости
                $parts = explode($this->tree_delimiter, $tree_value);
                $parent = $this->root_value;
                foreach($parts as $n => $part) {
                    $loaded_item = $this->loadItem($part, $parent);
                    if ($loaded_item) {
                        $id = $loaded_item[$this->id_field];
                    } else {
                        //Пытаемся создать элемент
                        $item = clone $this->getOrmObject();
                        $item[$this->tree_field] = $part;
                        $item[$this->tree_parent_field] = $parent;
                        if ($item->insert()) {
                            $id = $item[$this->id_field];
                        } else {
                            $id = $this->root_value;
                            break;
                        }
                    }
                    $parent = $id;
                }
                $parent_id_value = $id;
            }
            $insert_ids[] = $parent_id_value;
        }
        $this->schema->getPreset($this->link_preset_id)->row[$this->array_field] = $insert_ids;
    }
    
    function loadItem($title, $parent)
    {
        $q = \RS\Orm\Request::make()
            ->from($this->getOrmObject())
            ->where([
                $this->getMappedField($this->tree_field) => $title,
                $this->tree_parent_field => $parent
                ] + $this->getMultisiteExpr());
        
        return $q->object();
    }    
    
    protected function getRecursiveField($current_id)
    {
        if (!isset(self::$cache_path[$current_id])) {
            $result = [];
            $id = $current_id;
            while($id && $id != $this->root_value) {
                $row = \RS\Orm\Request::make()
                    ->select($this->getMappedField($this->tree_field), $this->tree_parent_field)
                    ->from($this->getOrmObject())
                    ->where([
                        $this->id_field => $id
                    ])->exec()->fetchRow();
                if ($row) {
                    $id = $row[$this->tree_parent_field];
                    $result[] = $row[$this->getMappedField($this->tree_field)];
                } else {
                    $id = false;
                }
            }
            self::$cache_path[$current_id] = implode($this->tree_delimiter, array_reverse($result));
        }
        
        return self::$cache_path[$current_id];
    }    
}