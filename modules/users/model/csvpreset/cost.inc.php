<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Model\CsvPreset;

class Cost extends \RS\Csv\Preset\AbstractPreset
{
    protected static     
        $index;
    
    protected
        $id_field = 'id',
        $link_id_field,
        $link_preset_id,
        $array_field,     //Поле для объекта пользователя
        $user_object,     //Объект пользователя
        $sites,           //Массив сайтов системе
        $current_site,    //Текущего сайта
        $costs,           //Цены по id
        $costs_by_title,  //Цены по заголовку
        $title;
        
    function __construct($options)
    {
        $defaults = [
            'ormObject'   => new \Catalog\Model\Orm\Typecost(),       
            'userObject'   => new \Users\Model\Orm\User(),
        ];
        parent::__construct($options + $defaults); 
        $this->current_site = \RS\Site\Manager::getSiteId();   
        $this->loadCosts(); 
        $this->loadSites(); 
    }
    
    /**
    * Загружает существующие сайты
    * 
    */
    function loadSites()
    {
       $api = new \RS\Site\Manager(); 
       $this->sites = $api->getSiteList();
    }
    
    
    /**
    * Загружает все цены по id
    * 
    */
    function loadCosts()
    {
       if (empty($this->costs)){
           $this->costs = \RS\Orm\Request::make()
                        ->from($this->getOrmObject())
                        ->where([
                            'site_id' => $this->current_site
                        ])
                        ->objects(null,'id'); 
       } 
       return $this->costs;
    }
    
    /**
    * Загружает все цены по названию
    * 
    */
    function loadCostsByTitle()
    {
       if (empty($this->costs_by_title)){
           $this->costs_by_title = \RS\Orm\Request::make()
                        ->from($this->getOrmObject())
                        ->where([
                            'site_id' => $this->current_site
                        ])
                        ->objects(null,'title'); 
       } 
       return $this->costs_by_title;
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
    * Установка поля пользователя куда запишется результат
    * 
    * @param string $field
    */
    function setArrayField($field)
    {
        $this->array_field = $field;
    }
    
    /**
    * Возвращает поле пользователя куда запишется результат
    *
    */
    function getArrayField()
    {
        return $this->array_field;
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
    * Устанавливает объект пользователя
    * 
    * @param mixed $orm_object
    */
    function setUserObject(\RS\Orm\AbstractObject $orm_object)
    {
        $this->user_object = $orm_object;
    }
    
    
    /**
    * Возвращает объект пользователя
    * 
    * @return \RS\Orm\AbstractObject
    */
    function getUserObject()
    {
        return $this->user_object;
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
            $items = \RS\Orm\Request::make()
                 ->from($this->getUserObject())
                 ->whereIn($this->link_id_field, $this->schema->ids)
                 ->objects(null, $this->link_id_field);
            $users = [];
            if (!empty($items)){
                foreach($items as $id=>$user){
                    $costs = unserialize($user['cost_id']);
                    
                    if (is_array($costs) && !empty($this->sites)){
                       foreach ($this->sites as $site){
                          $users[$id][$site['id']]['cost_id']    = 0; 
                          $users[$id][$site['id']]['cost_title'] = t('По умолчанию'); 
                          if (isset($costs[$site['id']])){
                             //id цены в зависимости от сайта   
                             $users[$id][$site['id']]['cost_id']    = $costs[$site['id']];  
                             //Название цены в зависимости от сайта
                             if (isset($this->costs[$costs[$site['id']]])){
                                $users[$id][$site['id']]['cost_title'] = $this->costs[$costs[$site['id']]]['title'];
                             }
                             
                          } 
                       } 
                    }
                    
                    
                    
                    
                }
            }
            

            $this->rows = $users;
        }
        
    }

    
    /**
    * Получает колонки с которыми будет работать данный пресет
    * 
    */
    function getColumns()
    {        
        $colums_propety = [];
        if (!empty($this->sites)){
           foreach ($this->sites as $site){
              $colums_propety[$this->id.'-usercosts'.$site['id']] = [
                    'key'  => 'usercosts'.$site['id'],
                    'title'  => t('Тип цены(%0)', [$site['title']]),
                    'site_id'  => $site['id'],
              ];
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
        $id = $this->schema->rows[$n][$this->id_field];
        $values_array = [];
        if (isset($this->rows[$id]) && !empty($this->rows[$id])) {       
            foreach($this->rows[$id] as $site_id=>$cost){
                $values_array[$this->id.'-usercosts'.$site_id] = $cost['cost_title'];
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
        if (!empty($this->sites)){
           foreach($this->sites as $site){
               $result_array = [];
               if (isset($this->row['usercosts'.$site['id']])) { 
                    $this->loadCostsByTitle();
                    $user_cost_title = $this->row['usercosts'.$site['id']];
                    
                    $result_array[$site['id']] = 0; //По умолчанию
                    if (!empty($user_cost_title) && isset($this->costs_by_title[$user_cost_title]['id'])){ //Если задана и существует
                        $result_array[$site['id']] = $this->costs_by_title[$user_cost_title]['id']; 
                    }
                    
                    if (!empty($result_array)){
                        $this->schema->getPreset($this->link_preset_id)->row[$this->array_field] = $result_array;
                    }
               }
           } 
            
        }
    }    
}
