<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\CsvPreset;

/**
* Добавляет колонки, отвечающие за связь с характеристикой
*/
class PropertyId extends \RS\Csv\Preset\AbstractPreset
{
    protected static
        $props,
        $groups,
        $index_group,
        $index;
    
    function __construct($options)
    {
        $defaults = [
            'ormObject' => new \Catalog\Model\Orm\Property\Item(),
            'multisite' => true
        ];
        parent::__construct($options + $defaults);
        $this->loadProperty();        
    }
    
    /**
    * Устанавливает связываемое поле в источнике данных
    * 
    * @return void
    */
    function setLinkIdField($field)
    {
        $this->link_id_field = $field;
    }
    
    /**
    * Устанавливает номер пресета - источника данных
    * 
    * @param integer $id - номер пресета
    * @return void
    */
    function setLinkPresetId($id)
    {
        $this->link_preset_id = $id;
    }
    
    /**
    * Устанавливает название экспортной колонки с характеристикой
    * 
    * @param string $title
    * @return void
    */
    function setTitle($title)
    {
        $this->title = $title;
    }    
    
    /**
    * Устанавливает название для экспортной колонки с группой характеристики
    * 
    * @param string $title
    * @return void
    */
    function setTitleGroup($title)
    {
        $this->title_group = $title;
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
            
            foreach(self::$groups as $group) {
                self::$index_group[$group['title']] = $group['id'];
            }
        }
    }
           
        
    /**
    * Возвращает колонки, которые обслуживает данный пресет
    * 
    * @return array
    */
    function getColumns()
    {
        return [
            $this->id.'-property_group' => [
                'key' => 'property_group',
                'title' => $this->title_group
            ],
            $this->id.'-property_title' => [
                'key' => 'property_title',
                'title' => $this->title
            ]
        ];
    }
    
    /**
    * Возвращает значение колонок для экспорта 
    * 
    * @param mixed $n
    */
    function getColumnsData($n)
    {
        $index_reverse = array_flip(self::$index);
        $property_id = $this->schema->rows[$n][$this->link_id_field];
        if (isset(self::$props[$property_id])) {
            
            $group = isset(self::$groups[self::$props[$property_id]['parent_id']]) 
            ? self::$groups[self::$props[$property_id]['parent_id']]['title']
            : '';
            
            return [
                $this->id.'-property_group' => $group,
                $this->id.'-property_title' => self::$props[$property_id]['title']
            ];
        }
        
        return [
                $this->id.'-property_group' => '',
                $this->id.'-property_title' => ''
        ];
    }
    
    /**
    * put your comment there...
    * 
    */
    function importColumnsData()
    {
        if (isset($this->row['property_title'])) {
            $key = "({$this->row['property_group']}){$this->row['property_title']}";
            
            if (isset(self::$index[$key])) {
                $this->schema->getPreset($this->link_preset_id)->row[$this->link_id_field] = self::$index[$key];
            } else {
                //Создаем группу и характеристику, если её не было
                if (!isset(self::$index_group[$this->row['property_group']])) {
                    $group = new \Catalog\Model\Orm\Property\Dir();
                    $group['title'] = $this->row['property_group'];
                    $group->insert();
                    self::$index_group[$this->row['property_group']] = $group['id'];
                }
                
                $property = new \Catalog\Model\Orm\Property\Item();
                $property['title'] = $this->row['property_title'];
                $property['type'] = \Catalog\Model\Orm\Property\Item::TYPE_LIST;
                $property['parent_id'] = self::$index_group[$this->row['property_group']];
                $property->insert();
                
                self::$index[$key] = $property['id'];
                
                $this->schema->getPreset($this->link_preset_id)->row[$this->link_id_field] = $property['id'];
            }
        }
    }
    
}