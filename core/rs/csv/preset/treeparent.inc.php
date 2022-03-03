<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Csv\Preset;

/**
* Экспортирует колонку "Родитель" у рекурсивно древовидного списка.
* Вместо цифры с id родителя поле представляется в наглядном виде:
* Название родительской категории\Название дочерней категории\....
*/
class TreeParent extends AbstractPreset
{
    protected
        $fields = [],
        $fields_map = [],
        
        $is_multisite,
        $tree_delimiter = '/',
        $tree_field,
        $parent_field,
        $root_value,
        $id_field,
        $orm_object,
        $titles,
        $null_sign_fields,
        $link_foreign_field,
        $link_preset_id,
        $link_default_value;
        
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
    * Устанавливает значение ID для корневого элемента
    * 
    * @param mixed $value
    * @return void
    */
    function setRootValue($value)
    {
        $this->root_value = $value;
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
    * Устанавливает поле, которое будет использоваться при формировании плоского списка
    * 
    * @param string $field
    * @return void
    */
    function setTreeField($field)
    {
        $this->tree_field = $field;
    }
    
    function setParentField($field)
    {
        $this->parent_field = $field;
    }
    
    /**
    * Задает названия колонок
    * 
    * @param array $titles - массив поле => новое название колонки
    * @return void
    */
    function setTitles($titles)
    {
        $this->titles = $titles;
    }
    
    /**
    * Загружает данные для текущего набора
    * 
    * @return void
    */
    function loadData()
    {
        $this->rows = $this->schema->rows;
        
    }    
    
    /**
    * Возвращает колонки, которые добавляются текущим набором 
    */
    function getColumns()
    {
        $fields = array_merge([$this->tree_field], $this->fields);
        $result = [];
        foreach($fields as $n => $key) {
            $property = $this->getOrmObject()->getProp( $this->getMappedField($key) );
            $title = isset($this->titles[$key]) ? $this->titles[$key] : $property->getTitle();
            
            $result[$this->id.'-'.$key] = [
                'key' => $key,
                'title' => $title
            ];
        }
        return $result;
    }
    
    function getColumnsData($n)
    {
        $this->row = [];
        foreach($this->getColumns() as $id => $column) {
            if ($column['key'] == $this->tree_field) {
                $load_id = $this->schema->getPreset($this->link_preset_id)->rows[$n][$this->link_foreign_field];
                $this->row[$id] = $this->getRecursiveField( $load_id );
            } else {
                $this->row[$id] = $this->rows[$n][$column['key']];
            }
        }
        return $this->row;
    }
    
    function importColumnsData()
    {
        $tree_field = $this->getMappedField($this->tree_field);
        
        $tree_value = isset($this->row[$tree_field]) ? $this->row[$tree_field] : '';
        $parent_id_value = $this->root_value;
        if (!empty($tree_value)) {
            $tree_value = str_replace('\\', $this->tree_delimiter, $tree_value); //Для совместимости
            $parts = explode($this->tree_delimiter, $tree_value);
            $parent = 0;
            foreach($parts as $n => $part) {
                $loaded_item = $this->loadItem($part, $parent);
                
                if ($loaded_item) {
                    $id = $loaded_item[$this->id_field];
                    //Обновляем элемент
                    if ($n == count($parts)-1 && $this->fields) {
                        $loaded_item->getFromArray($this->row);
                        $loaded_item[$tree_field] = $part;
                        $loaded_item->update();
                    }
                } else {
                    //Пытаемся создать элемент
                    $item = clone $this->getOrmObject();
                    if ($n == count($parts)-1) {
                        //Только крайний элемент создаем со всеми свойствами. 
                        //Промежуточные с минимальными свойствами.
                        $item->getFromArray($this->row);
                    }
                    $item[$tree_field] = $part;
                    $item[$this->parent_field] = $parent;
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
        $this->schema->getPreset($this->link_preset_id)->row[$this->link_foreign_field] = $parent_id_value;
    }
    
    function loadItem($title, $parent)
    {
        $q = \RS\Orm\Request::make()
            ->from($this->getOrmObject())
            ->where([
                $this->getMappedField($this->tree_field) => $title,
                $this->parent_field => $parent
            ]);
        if ($this->is_multisite) {
            $q->where([
                'site_id' => \RS\Site\Manager::getSiteId()
            ]);
        }        
        
        return $q->object();
    }
    
    
    /**
    * Возвращает древовидные данные в одну строку
    * 
    * @param mixed $current_id
    * @return array
    */
    protected function getRecursiveField($current_id)
    {
        $result = [];
        $id = $current_id;
        
        while($id && $id != $this->root_value) {
            $row = \RS\Orm\Request::make()
                ->select($this->getMappedField($this->tree_field), $this->parent_field)
                ->from($this->getOrmObject())
                ->where([
                    $this->id_field => $id
                ])->exec()->fetchRow();
            if ($row) {
                $id = $row[$this->parent_field];
                $result[] = $row[$this->getMappedField($this->tree_field)];
            } else {
                $id = false;
            }
        }
        return implode($this->tree_delimiter, array_reverse($result));
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
}