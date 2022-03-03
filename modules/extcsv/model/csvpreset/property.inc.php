<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace ExtCsv\Model\CsvPreset;
use \Catalog\Model\Orm\Property\ItemValue;

class Property extends \RS\Csv\Preset\AbstractPreset
{
    protected static
        $props,
        $groups,       
        $index;
    
    protected
        $delimiter = ",",
        $value_delimiter = ",",
        $id_field = 'id',
        $link_id_field,
        $link_preset_id,
        $properties,           //Массив со свойствами для колонок
        $titles_prop_compire, //Массив с соотвествием имён и id 
        $mask,
        $mask_fields = [],
        $mask_pattern,
        $title,
        $array_field = 'prop',
        $manylink_foreign_id_field = 'prop_id',
        $manylink_id_field = 'product_id',
        $list_values_orm,
        $manylink_orm;
    
    function __construct($options)
    {
        $defaults = [
            'ormObject'   => new \Catalog\Model\Orm\Property\Item(),
            'manylinkOrm' => new \Catalog\Model\Orm\Property\Link(),
            'listValuesOrm' => new \Catalog\Model\Orm\Property\ItemValue(),
            'multisite'   => true
        ];
        parent::__construct($options + $defaults);
        $this->loadProperty();        
    }
    
    /**
    * Устанавливает ORM объект значения характеристики
    * 
    * @param \RS\Orm\AbstractObject $orm
    */
    protected function setListValuesOrm(\RS\Orm\AbstractObject $orm)
    {
        $this->list_values_orm = $orm;
    }    
    
    /**
    * Устанавливает разделитель между значениями в поле
    * 
    * @param string $delimeter - разделитель между значениями в колонке
    */
    function setDelimeter($delimeter)
    {
       $this->delimeter       = $delimeter; 
       $this->value_delimiter = $delimeter; 
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
    
    function loadProperty()
    {
        
        if (!isset(self::$props)) {
            self::$props = \RS\Orm\Request::make()
                ->from(new \Catalog\Model\Orm\Property\Item())
                ->where($this->getMultisiteExpr() ?: null)
                ->objects(null, 'id');
            
            self::$groups = \RS\Orm\Request::make()
                ->from(new \Catalog\Model\Orm\Property\Dir())
                ->where($this->getMultisiteExpr() ?: null)
                ->objects(null, 'id');
                
           
            
            foreach(self::$props as $prop) {
                $group_name = isset(self::$groups[$prop['parent_id']]) ? self::$groups[$prop['parent_id']]['title'] : '';
                self::$index["($group_name)".$prop['title']] = $prop['id'];
            }
        }
    }
            
    /**
    * Устанавливает ORM объект связки многие ко многим
    * 
    * @param \RS\Orm\AbstractObject $orm
    * @return void
    */
    protected function setManylinkOrm(\RS\Orm\AbstractObject $orm)
    {
        $this->manylink_orm = $orm;
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
    
    /**
    * Загружает связанные данные
    * 
    * @return void
    */
    function loadData()
    {
        $this->row = [];
        if ($this->schema->ids) {
            $this->rows = \RS\Orm\Request::make()
                ->select('L.*, V.value as val_list_id')
                ->from($this->manylink_orm, 'L')
                ->leftjoin($this->list_values_orm, 'V.id = L.val_list_id ', 'V')
                ->whereIn($this->manylink_id_field, $this->schema->ids)
                ->objects(null, $this->manylink_id_field, true);
        }
    }
    
    /**
    * Возвращает характеристики возможные вместе с группами в виде массива
    * с ключами group и properties
    * 
    */
    function getAllProperties()
    {
       if (empty($this->properties)){ //Запросим все свойства для текущей выбранной директории  
           $prop_api         = new \Catalog\Model\PropertyApi();
           $this->properties = $prop_api->getAllPropertiesAndGroups();
       }   
    }
    
    /**
    * Возвращает характеристики возможные у товаров лежащих в данной категории
    * 
    */
    function getAllPropertiesFromCategory()
    {
       if (empty($this->properties)){ //Запросим все свойства для текущей выбранной директории           
           $prop_api         = new \Catalog\Model\PropertyApi();
           $this->properties = $prop_api->getGroupProperty((int)$this->schema->getParamByKey('dir'),true);
       }   
    }
    
    /**
    * Получает колонки с которыми будет работать данный пресет
    * 
    */
    function getColumns()
    {
        //Получим колонки характеристик из характеристик для текущей директории
        
        if ($this->schema->getAction() == 'export'){
            $this->getAllPropertiesFromCategory();
        }else{
            $this->getAllProperties();
        } 
        
        $colums_propety = [];
        if (!empty($this->properties)){
           foreach ($this->properties as $group_key=>$propety_info){    
               $group      = $propety_info['group'];       //Сведения о группе характеристики
               $properties = $propety_info['properties'];  //Характеристки данной группы

               foreach($properties as $prop_key=>$property){ //Проходимся по характеристикам
                  $this->titles_prop_compire[$prop_key] = [ //Запишем в массив соотвествия свойства
                       'key'         => 'property_'.$group['id'].'_'.$property['id'],                                   //Ключ колонки
                       'title'       => t("Характеристика_(").$group['title'].")".$property['title'], //Имя колонки
                       'site_id'     => $property['site_id'],                                      //Id сайта
                       'property_id' => $prop_key,                                                  //Id свойства
                       'group_id'    => $group['id']                                                  //Id свойства
                  ];
                  $colums_propety[$this->id.'-property-'.$group['id']."_".$prop_key] = $this->titles_prop_compire[$prop_key];  
               }
            } 
        }
        
        return $colums_propety;
    }
    
    /**
    * Получение информации для экспорта
    * 
    * @param mixed $n - номер колонки
    * @return array
    */
    function getColumnsData($n)
    {
        $id = $this->schema->rows[$n][$this->link_id_field];
        $data         = [];
        $values_array = [];
        if (isset($this->rows[$id])) {
            foreach($this->rows[$id] as $property_link) { //Пройдёмся и получим все необходимые значения характеристик
                $property = isset(self::$props[ $property_link['prop_id'] ]) ? self::$props[ $property_link['prop_id'] ] : false;
                if ($property && isset($this->titles_prop_compire[$property['id']])) {
                    
                    
                    $value = $property_link[$property->getValueLinkField()];  //Получим значение характеристики
                    if ($value == '') continue;
                    if ($property['type'] == 'int'){
                        
                        $value = str_replace(".",",",$value);
                    }
                    
                    if (isset($data[$property['id']])) {
                        $data[$property['id']] .= $this->value_delimiter.$value;
                    } else {
                        $data[$property['id']] =  $value;
                    } 
                }
                 
            }
            
            if (isset($this->titles_prop_compire)){
                //Сопоставим с действующими характеристиками для колонок
                foreach($this->titles_prop_compire as $property) {
                   if (isset($data[$property['property_id']])){
                      $values_array[$this->id.'-property-'.$property['group_id']."_".$property['property_id']] = $data[$property['property_id']];   
                   } 
                } 
            }
            
            
                
        }
        return $values_array;
    }
    
    /**
    * Импортируем данные привязывая к объекту записывая в массив объект массив с ключом prop.
    * Который при сохранении объекта проимпортирует свойства
    * 
    */
    function importColumnsData()
    {
        if (isset($this->row)) { //Если есть что импортировать получим данные о группе и характеристиках
            $result_array = [];
            foreach($this->row as $column_title=>$val){
                $prop_data = explode("_",$column_title); 
                $prop_id   = $prop_data[2];             //id характеристики
                
                $property = isset(self::$props[ $prop_id ]) ? self::$props[ $prop_id ] : false;
                
                if (strpos($val, $this->value_delimiter) !== false) { //Если несколько значений
                    $val   = explode($this->value_delimiter, trim($val));
                }
                $value = $val;
                
                if ($property['type'] == 'int'){
                    $value = str_replace(",",".",$value);
                }     
                
                if ($property->isListType()) {
                    //Конвертируем списковые значения в ID
                    $value = array_map(function($value) use ($prop_id) {
                        return ItemValue::getIdByValue($prop_id, trim($value));
                    }, (array)$value);
                }
                
                if ($value != ''){
                   $result_array[$prop_id] = [
                        'id'    => $prop_id,
                        'is_my' => 1,
                        'value' => $value
                   ];
                }
                
            }
            if (!empty($result_array)){
                $this->schema->getPreset($this->link_preset_id)->row[$this->array_field] = $result_array;
            }
        }
    }
    
}