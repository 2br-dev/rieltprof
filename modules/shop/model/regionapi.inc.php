<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model;

use RS\Module\AbstractModel\TreeList;
use RS\Site\Manager as SiteManager;
use Shop\Model\Orm\Region;

class RegionApi extends TreeList
{
    const DEFAULT_REGION_NAME = "Россия";
    
    static protected $cache_default_region;
    protected $enable_tree_preload = false;
    protected $delete_child = false;
    
    function __construct()
    {
        parent::__construct(new Region, [
            'parentField' => 'parent_id',
            'nameField' => 'title',
            'defaultOrder' => 'parent_id, sortn, title',
            'multisite' => true
        ]);
    }

    /**
     * Возвращает список с регионами и город. Города добавляются по условию.
     *
     * @param boolean $only_regions - ограничить список только до региона, без городов. По умолчанию true.
     * @return array
     * @throws \RS\Db\Exception
     */
    static function selectList($only_regions = true)
    {                         
        $_this = new self();
        if ($only_regions){
            $_this->setFilter('is_city', 0);    
        }
        $list  = $_this->getSelectList(0);
        
        $arr = [
            0 => t('Верхний уровень')
        ];
        foreach($list as $k=>$item){
           $arr[$k] = $item; 
        }
        return $arr;
    }

    /**
     * @deprecated (19.03)
     * Возвращает список городов для выбора, сгруппированных по странам и регионам
     *
     * @param string $order - сортировка
     * @return array
     */
    static function selectListByOnlyCityGroupTree($order = 'title')
    {                         
        $_this = new self();
        //Отберём сначала страны
        $_this->setFilter('parent_id', 0);
        $list  = $_this
                    ->setOrder($order)
                    ->getList(0);
        
        $arr = [];
        if (!empty($list)){
            foreach($list as $k=>$item){ 
               $regions = $_this->clearFilter()     
                   ->setFilter('parent_id', $item['id'])
                   ->setFilter('is_city', 0)
                   ->setOrder($order)
                   ->getList(0); 
               
               if (!empty($regions)){
                   foreach ($regions as $region){
                       
                       $cities = $_this->clearFilter()     
                           ->setFilter('parent_id', $region['id'])
                           ->setFilter('is_city', 1)
                           ->setOrder($order)
                           ->getList(0); 
                       
                       if (!empty($cities)){
                           foreach ($cities as $city){
                              $arr[$item['title']][$region['title']][$city['id']] = $city['title'];
                           }     
                       }
                   }
               }
                
            }    
        }
        
        return $arr;
    }

    /**
     * @deprecated (19.03)
     * Возвращает общий список со странами, регионами и городами
     *
     * @return array
     */
    static function selectListAll()
    {
        $top_level_group_title = t('Страны');
        $ret                         = [];
        $ret[$top_level_group_title] = [];
        $_this = new self();
        $_this->setFilter('parent_id', 0);
        $_this->setOrder('title');
        $country_list  = $_this->getListAsArray();
        
        if (!empty($country_list)){
            //Страны
            foreach($country_list as $country) {
                $ret[$top_level_group_title][$country['id']] = $country['title'];
            }
            
            //Регионы по странам
            foreach($country_list as $country) {
                $_this->clearFilter();
                $_this->setFilter('parent_id', $country['id']);
                $states_list  = $_this->getListAsArray();
                
                if (!empty($states_list)){
                    foreach ($states_list as $state){
                       $ret[$country['title']][$state['id']] = $state['title']; 
                    }
                }
                
                //Город по регионам и странам
                if (!empty($states_list)){
                    foreach($states_list as $state) {
                        $_this->clearFilter();
                        $_this->setFilter('parent_id', $state['id']);
                        $cities_list  = $_this->getListAsArray();
                        
                        if (!empty($cities_list)){
                            foreach($cities_list as $city) {
                                $ret[t("Страна")." ".$country['title']][$state['title']][$city['id']] = $city['title']; 
                            }
                        }
                    }
                }
            }
        }
        return $ret;
    }
    
    /**
    * Возвращает список только стран
    *
    * @return array
    */
    static function countryList()
    {
        $_this = new self();
        $_this->setFilter('parent_id', 0);
        return $_this -> getAssocList($_this->id_field, $_this->name_field);
    } 
    
    /**
    * Возвращает список только регионов
    *
    * @return array
    */
    static function regionsList()
    {
        $_this = new self();
        $_this->setFilter('parent_id', 0, '>');
        $_this->setFilter('is_city', 0);
        $_this->setOrder('title');
        return $_this -> getList();
    }  
    
    /**
    * Возвращает список только городов
    *
    * @return array
    */
    static function citiesList()
    {
        $_this = new self();
        $_this->setFilter('parent_id', 0, '>');
        $_this->setFilter('is_city', 1);
        $_this->setOrder('title');
        return $_this -> getList();
    }

    /**
     * Возвращает список только города в виде ассоциативного массива
     *
     * @return array
     */
    static function citiesListAssoc()
    {
        $_this = new self();
        $_this->setFilter('parent_id', 0, '>');
        $_this->setFilter('is_city', 1);
        $_this->setOrder('title');
        return $_this -> getAssocList($_this->id_field, $_this->name_field);
    }

    /**
     * Возращает регион по умолчанию - Россию
     * Если такого региона в справочнике нет - создает его
     *
     * @return Region
     */
    static function getDefaultRegion()
    {
        if(self::$cache_default_region != null){
            return self::$cache_default_region;
        }
        
        $def_reg = Region::loadByWhere([
            'site_id'   => SiteManager::getSiteId(),
            'title' => self::DEFAULT_REGION_NAME
        ]);
        
        if(!$def_reg->id){
            $def_reg->parent_id = 0;
            $def_reg->title     = self::DEFAULT_REGION_NAME;
            $def_reg->insert();
        }
        
        self::$cache_default_region = $def_reg;
        return self::$cache_default_region;
    }
    
}
