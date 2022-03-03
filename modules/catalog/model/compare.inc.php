<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model;
/**
* Класс определяет объект - сравнение товаров
*/
class Compare
{
    const 
        COMPARE_SESS_VAR = 'COMPARE';
    
    protected
        $items = [];
    
    /**
    * Возвращает экземпляр текущего класса с загруженными данными
    * 
    * @return Compare
    */
    public static function currentCompare()
    {
        $site_id = \RS\Site\Manager::getSiteId();
        if (!isset($_SESSION[self::COMPARE_SESS_VAR][$site_id])) {
            $_SESSION[self::COMPARE_SESS_VAR][$site_id] = new self();
        }
        return $_SESSION[self::COMPARE_SESS_VAR][$site_id];
    }
    
    /**
    * Добавляет продукт для сравнения
    * 
    * @param integer $id
    * @return bool
    */
    function addProduct($id)
    {
        $id = (int)$id;
        $this->items[$id] = $id;
        return true;        
    }
    
    /**
    * Исключает продукт из сравнения
    * 
    * @param integer $id
    * @return bool
    */
    function removeProduct($id)
    {
        if (isset($this->items[$id])) {
            unset($this->items[$id]);
            return true;
        }
        return false;
    }
    
    /**
    * Удаляет все товары из списка сравнения
    * 
    * @return bool
    */
    function removeAll()
    {
        $this->items = [];
        return true;
    }
    
    /**
    * Возвращает список товаров для сравнения
    * 
    * @return array of Orm\Product
    */
    function getCompareList()
    {
        $items = [];
        
        if (count($this->items)) {
            $api = new Api();
            $api->setFilter('id', $this->items, 'in');
            $items = $api->getAssocList('id');
            $items = $api->addProductsPhotos($items);
            $items = $api->addProductsDynamicNum($items);
        }
        
        return $items;
    }
    
    /**
    * Возвращает количество добавленных элементов к сравнению
    * @return integer
    */
    function getCount()
    {
        return count($this->items);
    }
    
    /**
    * Возвращает true, если товар присутствует в списке для сравнения
    * 
    * @param integer $id
    * @return bool
    */
    function inList($id)
    {
        return isset($this->items[$id]);
    }

    /**
     * Возвращает список id товаров, которые добавлены к сравнению
     *
     * @return array
     */
    function getList()
    {
       return $this->items;
    }
    
    /**
    * Возвращает массив с данными для сравнения
    * 
    * @return array
    */
    function getCompareData()
    {
        $result = [];
        $groups = [];
        
        $items = $this->getCompareList();
        
        $api = new Api();
        $items = $api->addProductsProperty($items);
        $items = $api->addProductsOffers($items);
        $items = $api->addProductsMultiOffers($items);
        
        $empty_fill = [];
        foreach($items as $k=>$v) {
            $empty_fill[$k] = false;
        }
        
        foreach($items as $key => $item) {
            $result['items'][$key] = $item;
            
            $properties = $item['properties'];
            
            foreach($properties as $data) {
                if (empty($data['group']['hidden'])) {
                    $group_id = $data['group']['id'];
                    $result['groups'][$group_id] = $data['group'];
                    foreach($data['properties'] as $alias => $prop) {
                        
                        //Создаем пустой список, для случая если у другого товара нет такого свойства
                        if (!isset($result['values'][$group_id][$alias])) {
                            $result['values'][$group_id][$alias] = $empty_fill;
                        }
                        
                        $result['values'][$group_id][$alias][$key] = $prop;
                        
                        if (!isset($result['props'][$alias])) {
                            $result['props'][$alias] = $prop;
                        }
                    }
                }
            }
        }
        return $result;
    }
    
}