<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Csv\Preset;

/**
* Формирует колонку со списком товаров и категорий, хранящихся в базе в сериализованном виде. 
*/
class ProductsSerialized extends AbstractPreset
{
    protected static
        $cache_groups = [],
        $cache_products = [];
    
    protected
        $export_pattern,
        $import_pattern,
        $delimeter = ';',             //Разделитель между значениями
        $loaded_products = [],
        $loaded_groups = [],
        $is_multisite = true,

        $export_product_field = 'title',
        $export_group_field   = 'name',
        $link_foreign_field,
        $link_id_field,
        $link_preset_id,    
        $title;
    
    function __construct($options)
    {
        $this->setTitle(t('Связанные товары и категории'));
        $this->setExportPattern('groups:<{groups}>, products:<{products}>');
        parent::__construct($options);
    }
    
    /**
    * Устанавливает какую колонку будем экспортировать
    * 
    * @param string $field - колонка в БД которая будет задействована для вывода в значение
    */
    function setExportProductField($field)
    {
       $this->export_product_field  = $field;
    }
    
    /**
    * Устанавливает разделитель между значениями в поле
    * 
    * @param string $delimeter - разделитель между значениями в колонке
    */
    function setDelimeter($delimeter)
    {
       $this->delimeter = $delimeter; 
    }
    
    
    function setExportPattern($pattern)
    {
        $this->export_pattern = $pattern;
        $this->import_pattern = str_replace(['{products}', '{groups}'], ['(?P<products>.*?)', '(?P<groups>.*?)'], quotemeta($pattern));
    }
    
    /**
    * Устанавливает название экспортной колонки
    * 
    * @param string $title
    */
    function setTitle($title)
    {
        $this->title = $title;
    }
    
    /**
    * Возвращает колонки, которые добавляются текущим набором 
    * 
    * @return array
    */
    function getColumns()
    {
        return [
            $this->id.'-products' => [
                'key' => 'products',
                'title' => $this->title
            ]
        ];
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
    * Устанавливает название поля id основного объекта
    * 
    * @param string $id_field
    * @return void
    */
    function setLinkIdField($id_field)
    {
        $this->link_id_field = $id_field;
    }    
    
    /**
    * Устанавливает в каком поле основного объекта находятся сериализованные данные с перечнем товаров и групп
    * 
    * @param string $field
    */
    function setLinkForeignField($field)
    {
        $this->link_foreign_field = $field;
    }

    
    /**
    * Загружает из базы данные, необходимые для экспорта текущего набора колонок
    * 
    * @return void
    */        
    function loadData()
    {
        $products_all = [];
        $groups_all = [];
        $this->row = [];
        foreach($this->schema->rows as $n => $row) {
            $list = (array)@unserialize($row[$this->link_foreign_field]);
            $this->row[ $n ] = $list;
            $products_all = array_merge($products_all, (array)@$list['product']);
            $groups_all = array_merge($groups_all, (array)@$list['group']);
        }
        $this->loaded_products = [];
        if ($products_all) {
            $this->loaded_products = \RS\Orm\Request::make()
                ->from(new \Catalog\Model\Orm\Product)
                ->whereIn('id', $products_all)
                ->objects(null, 'id');
        }
        
        if ($groups_all) {
            $this->loaded_groups = \RS\Orm\Request::make()
                ->from(new \Catalog\Model\Orm\Dir)
                ->whereIn('id', $groups_all)
                ->objects(null, 'id');
        }
    }    
    
    /**
    * Возвращает набор колонок с данными для одной строки
    * 
    * @param mixed $n
    */
    function getColumnsData($n)
    {
        $export_str = '';
        $products   = [];
        $groups     = [];
        
        $data = $this->row[$n];
        if (isset($data['product'])) {
            foreach($data['product'] as $product_id) {
                if (isset($this->loaded_products[$product_id])) {
                    $products[] = $this->loaded_products[$product_id][$this->export_product_field];
                }
            }
        }
        
        if (isset($data['group'])) {
            foreach($data['group'] as $group_id) {
                if (isset($this->loaded_groups[$group_id])) {
                    $groups[] = $this->loaded_groups[$group_id][$this->export_group_field];
                }
            }
        }
        
        $export_str = str_replace(['{groups}', '{products}'], [implode($this->delimeter, $groups), implode($this->delimeter, $products)], $this->export_pattern);
        return [
            $this->id.'-products' => $export_str
        ];
    }
    
    
    
    /**
    * Импортирует данные одной строки текущего пресета в базу
    */
    function importColumnsData()
    {
        $groups_ids = [];
        $products_ids = [];
        
        if ($this->row) {//Только, если колонка присутствует
        
            if (preg_match("/^{$this->import_pattern}$/", $this->row['products'], $match)) {
                $match += ['groups' => '', 'products' => ''];
                $groups   = explode($this->delimeter, $match['groups']);
                $products = explode($this->delimeter, $match['products']);
                
                foreach($groups as $group) {
                    if (!isset(self::$cache_groups[$group])) {
                        $item = \Catalog\Model\Orm\Dir::loadByWhere([
                            $this->export_group_field => $group
                            ] + $this->getMultisiteExpr());
                        self::$cache_groups[$group] = $item['id'] ?: false;
                    }
                    if (self::$cache_groups[$group] !== false) {
                        $groups_ids[] = self::$cache_groups[$group];
                    }
                }
                
                foreach($products as $product) {
                    if (!isset(self::$cache_products[$product])) {
                        $item = \Catalog\Model\Orm\Product::loadByWhere([
                            $this->export_product_field => $product
                            ] + $this->getMultisiteExpr());
                        self::$cache_products[$product] = $item['id'] ?: false;
                    }
                    
                    if (self::$cache_products[$product] !== false) {
                        $products_ids[] = self::$cache_products[$product];
                    }
                }
            }
            
            $preset = $this->schema->getPreset($this->link_preset_id);
            $result = [];
            if ($products_ids) {
                $result['product'] = $products_ids;
            }
            if ($groups_ids) {
                $result['group'] = $groups_ids;
            }
            
            $preset->row[$this->link_foreign_field] = serialize($result);
        }
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