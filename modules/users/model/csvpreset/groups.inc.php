<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Model\CsvPreset;

class Groups extends \RS\Csv\Preset\AbstractPreset
{
    protected static     
        $index;
    
    protected
        $delimiter = ",",
        $value_delimiter = ",",
        $id_field = 'id',
        $array_field = 'groups',
        $link_id_field,
        $link_preset_id,
        $many_link_ormobject,
        $loaded_groups = [], //Группы пользователей
        $loaded_groups_by_name = [], //Группы пользователей к ключом по name
        $title;
        
    function __construct($options)
    {
        $defaults = [
            'ormObject'   => new \Users\Model\Orm\UserGroup(),
            'manyLinkOrmObject' => new \Users\Model\Orm\UserInGroup(),
        ];
        parent::__construct($options + $defaults);     
        $this->loadUserGroups(); 
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
    * Загружает все группы пользователей сайта с ключом по alias
    * 
    */
    function loadUserGroups()
    {
       if (empty($this->loaded_groups)){
           $this->loaded_groups = \RS\Orm\Request::make()
                        ->from($this->getOrmObject())
                        ->objects(null,'alias'); 
       } 
       return $this->loaded_groups;
    }
    
    /**
    * Загружает все группы пользователей сайта с ключом по названию
    * 
    */
    function loadUserGroupsByName()
    {
       if (empty($this->loaded_groups_by_name)){
           
           $items = \RS\Orm\Request::make()
                        ->from($this->getOrmObject())
                        ->objects(null,'name');
           //Уменьшим имена для последующей проверки 
           if (!empty($items)){
              foreach ($items as $key=>$item){
                  $this->loaded_groups_by_name[mb_strtolower($key)] = $item;
              } 
           }
       } 
       return $this->loaded_groups_by_name;
    }
     
            
    /**
    * Устанавливает поле по которому будет привязыватся объект к пользователю
    * 
    * @param string $field - имя поля
    */
    function setLinkIdField($field)
    {
        $this->link_id_field = $field;
    }
    
    function setLinkPresetId($id)
    {
        $this->link_preset_id = $id;
    }
    
    /**
    * Устанавливает поля объекта пользователя куда будут помещены группы
    * 
    * @param string $array_field - имя поля пользователя
    */
    function setArrayField($array_field)
    {
        $this->array_field = $array_field;
    }
    
    function getArrayField()
    {
        return $this->array_field;
    }
    
    /**
    * Устанавливает объект для линковки таблицы
    * 
    * @param \RS\Orm\AbstractObject $orm_object
    */
    function setManyLinkOrmObject(\RS\Orm\AbstractObject $orm_object)
    {
        $this->many_link_ormobject = $orm_object;
    }
    
    function getManyLinkOrmObject()
    {
        return $this->many_link_ormobject;
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
    * Загружает связанные данные
    * 
    * @return void
    */
    function loadData()
    {
        $this->row = [];
        if ($this->schema->ids) {
            $this->rows = \RS\Orm\Request::make()
                 ->from($this->getManyLinkOrmObject())
                 ->whereIn($this->link_id_field, $this->schema->ids)
                 ->objects(null, $this->link_id_field, true);
        }
        
    }

    
    /**
    * Получает колонки с которыми будет работать данный пресет
    * 
    */
    function getColumns()
    {        
        $colums_propety = [$this->id.'-usergroups' => [
            'key'  => 'usergroups',
            'title'  => t('Группы пользователя'),
        ]];
        
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
        $id = $this->schema->rows[$n][$this->id_field];
        $values_array = [];
        if (isset($this->rows[$id])) {
            $groups = []; //Имена групп в которых состоит пользователь
            $user_group_links = $this->rows[$id];
            
            foreach($user_group_links as $group_link){
               $groups[] = $this->loaded_groups[$group_link['group']]['name']; 
            }
            
            $values_array[$this->id.'-usergroups'] = implode($this->delimiter,$groups);
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
        if (isset($this->row['usergroups'])) { 
            $this->loadUserGroupsByName();
            $groups  = explode($this->delimiter,$this->row['usergroups']);
            
            $result_array = [];
            foreach($groups as $group){
               $group_title = trim(mb_strtolower($group)); //Имя группы
               //Если такая группа для пользователя существует
               if (isset($this->loaded_groups_by_name[$group_title])){
                  $result_array[] = $this->loaded_groups_by_name[$group_title]['alias'];
               }
            }

            
            if (!empty($result_array)){
                $this->schema->getPreset($this->link_preset_id)->row[$this->array_field] = $result_array;
            }
        }
    }    
    
}
