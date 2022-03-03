<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace ExtCsv\Model\CsvPreset;

/**
* Добавляет к экспорту колонки соответствующие свойствам ORM объекта. 
* Самый простой набор колонок. В качестве названия колонок выступают названия свойств Orm объекта
*/
class Base extends \RS\Csv\Preset\AbstractPreset
{
    protected
        $fields = [],
        $select_request,
        $id_field = 'id',
        $saved_request = null, //Объект запроса из сессии с параметрами текущего просмотра списка
        $select_order,
        $exclude_fields = [],
        $titles = [],
        $search_fields = [],
        $load_expression,
        $is_multisite,
        $use_cache = true,
        $cache = [],
    
        $null_fields = [],
        $replace_mode = false,
        $use_temporary_id,
        $uniq_fields;
    
    /**
    * Устанавливает использовать ли REPLACE вместо INSERT и UPDATE, при вставке в базу
    * 
    * @param bool $bool
    * @return void
    */
    function setReplaceMode($bool)
    {
       $this->replace_mode = $bool; 
    }
    
    /**
    * Указывает какое поле является уникальным идентификатором объекта
    * 
    * @param mixed $field
    */
    function setIdFIeld($field)
    {
        $this->id_field = $field;
    }
    
    /**
    * Устанавливает колонки, которые в случае пустоты будут записаны в базу как NULL
    * 
    * @param mixed $fields
    */
    function setNullFields(array $fields)
    {
        $this->null_fields = $fields;
    }
    
    /**
    * Устнавливает запрос, который был взят из сессии с установленными параметрами просмотра списка
    * 
    * @param \RS\Orm\Request|null $request - объект из сессии
    */
    function setSavedRequest($request)
    {
        $this->saved_request = $request ? clone $request : null;
    }  
    
    /**
    * Загружает данные перед экспортом
    * 
    * @return void
    */
    function loadData()
    {
        $this->rows = $this->schema->rows;
    }
    
    /**
    * Возвращает данные для вывода в CSV
    * 
    * @return array
    */
    function getColumnsData($n)
    {
        $this->row = [];
        foreach($this->getColumns() as $id => $column) {
            $value          = $this->rows[$n][$column['key']];
            $this->row[$id] = trim($value);
        }
        
        return $this->row;
    }
    
    /**
    * Подготавливает для объекта товара, характеристики для вставки
    * обновляя или добавляя новые
    * 
    * @param \Catalog\Model\Orm\Product $product  - объект товара, который будет обновлён или вставлен
    */
    function prepareProductProperties($product)
    {
       $product->fillProperty();
       
       
       
       $prop_insert = $this->row['prop'];
       // Сформируем свой список 
       $arr = [];
       if (!empty($product['properties'])){
          foreach($product['properties'] as $key=>$properties){
              
              if (!empty($properties) && isset($properties['properties'])){
                  $props = $properties['properties'];
                  
                  foreach ($props as $key=>$property){
                      $arr[$key] = [
                         'id'    => $key,
                         'is_my' => true,
                         'value' => $property['value'],
                      ];
                  }
              }
          } 
       }
       
       $this->row['prop'] = $prop_insert+$arr;
    }
    
    /**
    * Импортирует данные одной строки текущего пресета в базу
    * 
    * @return void
    */
    function importColumnsData()
    {
        foreach($this->row as $field => $value) {
            if ($value === '' && in_array($field, $this->null_fields)) {
                unset($this->row[$field]);
            }
        }

        if(isset($this->row['recommended'])){
            $this->row['recommended_arr'] = unserialize($this->row['recommended']);
        }
        if(isset($this->row['concomitant'])){
            $this->row['concomitant_arr'] = unserialize($this->row['concomitant']);
        }

        if ($this->replace_mode) {
            $orm_object = clone $this->getOrmObject();
            $orm_object->getFromArray($this->row);
            $orm_object->replace();            
        } else {
            
            /**
            * @var \Catalog\Model\Orm\Product
            */
            $orm_object = $this->loadObject();
            
            //Ставим доп. условие для товара, чтобы подгрузить характеристики
            if (!empty($orm_object) && $orm_object instanceof \Catalog\Model\Orm\Product && isset($this->row['prop'])){
                $this->prepareProductProperties($orm_object);
            }
            
            if ($orm_object) {
                //Обновление
                unset($this->row[$this->id_field]);
                $orm_object->getFromArray($this->row);
                $orm_object->update();
            } else {
                //Создание
                $orm_object = clone $this->getOrmObject();
                $orm_object->getFromArray($this->row);
                unset($orm_object['id']);
                $orm_object->insert();
            }
        }
        
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
    * Возвращает какие поля следует исключить из выгрузки
    * 
    * @return array
    */
    function getExcludeFields()
    {
        return $this->exclude_fields;
    }
    
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
    * Возвращает массив c условиями для поиска
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
    * Возвращает объект Orm\Request для стартовой выборки элементов
    * 
    * @return \RS\Orm\Request
    */
    function getSelectRequest()
    {
        if (!$this->select_request) {
            if (!$this->saved_request){ //Если нет запроса сохранённого в сессии
                $this->select_request = \RS\Orm\Request::make()->from($this->getOrmObject());
                if ($this->is_multisite) {
                    $this->select_request->where(['site_id' => \RS\Site\Manager::getSiteId()]);
                }
                
                if ($this->select_order) {
                    $this->select_request->orderby($this->select_order);
                } 
            }else{ //Если есть запрос сохранённый в сессии
                $this->saved_request->limit(null);
                $this->select_request = $this->saved_request;
            }
        }
        return $this->select_request;
    }
    
    /**
    * Устанавливает порядок сортировки выборки для выгрузки
    * 
    * @param string $order - сортировка выборки
    * @return AbstractPreset
    */
    function setSelectOrder($order)
    {
        $this->select_order = $order;
        return $this;
    }
    
    /**
    * Устанавливает объект запроса для стартовой выборки
    * 
    * @param \RS\Orm\Request $q
    */
    function setSelectRequest(\RS\Orm\Request $q)
    {
        $this->select_request = $q;
        return $this;
    }    
    
}