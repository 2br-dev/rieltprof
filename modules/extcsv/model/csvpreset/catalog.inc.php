<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace ExtCsv\Model\CsvPreset;

/**
* Добавляет колонку описывающую связь многие ко многим с древовидным списком
*/
class Catalog extends \RS\Csv\Preset\AbstractPreset
{
    protected static 
        $cache_path = [];
        
    protected
        $tree_delimiter = '/',
        $delimiter = "/",
        $id_field,
        $link_id_field,
        $link_preset_id,
        $orm_object,
        $is_multisite,
        $range,         //Массив диапозона для обработки полей (Сколько одинаковых полей "Категория" включает)
        
        $title,
        $array_field,
        
        $tree_field,
        $tree_parent_field,
        $root_value,
        
        $manylink_foreign_id_field,
        $manylink_id_field,
        $manylink_orm;
    
    /**
    * Конструктор пресета
    * 
    * @param array $options - массив параметров передаваемых в пресет
    * @return Catalog
    */
    function __construct($options)
    {
       parent::__construct($options); 
       $this->range = range(0,4); //Диапозон выборки колонок с фото от 1 до 5 
    }
    
    /**
    * Устанавливает разделитель между значениями в поле
    * 
    * @param string $delimeter - разделитель между значениями в колонке
    */
    function setDelimeter($delimeter)
    {
       $this->delimeter      = $delimeter; 
       $this->tree_delimiter = $delimeter; 
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
        $colums_num = $this->range;
        $columns    = [];
        for($i=0;$i<count($colums_num);$i++){
            $columns[$this->id.'-manytree'.($i+1)] = [
                'key' => 'manytree'.($i+1),
                'title' => t('Категория'.($i+1))
            ];
        }
        
        return $columns;
    }
    

    /**
    * Получает данные для колонок
    * 
    * @param integer $n - номер строки
    * @return array
    */
    function  getColumnsData($n)
    {
        $this->row = [];
        $base_id = $this->schema->getPreset($this->link_preset_id)->rows[$n][$this->link_id_field];
        
        $values_array = [];
        for($i=0;$i<count($this->range);$i++){      //Проходимся по колонкам с категориями
           if (isset($this->rows[$base_id][$i])){
               $data      = $this->rows[$base_id][$i];
               $cat_id    = $data['id'];
               $values_array[$this->id.'-manytree'.($i+1)] = $this->getRecursiveField($cat_id);//Запишем значения для колонок 
           }
        }
        
        return $values_array;
    }
    
    /**
    * Проходится по массиву путей, к последнему элементу, возвращая id конечной директории
    * Если директорий не существует, то создаёт их
    * 
    * @param array $path_array - массив пути следования
    */
    function followPath($path_array){

       $parent_id = $this->root_value; //id корневой директории 
       foreach($path_array as $path){
          $dir = \RS\Orm\Request::make()
               ->from($this->getOrmObject()) 
               ->where([
                   'name'   => trim($path),
                   'parent' => $parent_id
               ])
               ->object();
               
          if (!$dir){ //Если такая директория не найдена, создадим её
             $dir = clone $this->getOrmObject(); 
             
             $dir['name']   = trim($path);
             $dir['parent'] = trim($parent_id);
             $dir->insert();
          }
          $parent_id = $dir['id'];
       }
       
       return $parent_id;
    }
    
    /**
    * Находит id директории по значению в ячейке,
    * Разбирает по дереву каталоги
    * 
    * @param string $dir_value - строкое представление дерева каталогов
    */
    function findDirIdByValue($dir_value){
       if (!isset(self::$cache_path[$dir_value])){
           $dirs = explode($this->delimiter,$dir_value);
           self::$cache_path[$dir_value] = $this->followPath($dirs);
       }
       
       return self::$cache_path[$dir_value];  
    }
    
    /**
    * Импортируем данные из колонок в объект
    * 
    */
    function importColumnsData()
    {
        $colums_num = $this->range;
        $insert_ids = [];
        if (isset($this->row)){
           foreach($this->row as $column=>$value){
                if (!empty($value)){
                   $insert_ids[] = $this->findDirIdByValue($value); 
                }
           } 
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