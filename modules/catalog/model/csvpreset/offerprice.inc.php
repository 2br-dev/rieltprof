<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\CsvPreset;
use \Catalog\Model\Orm;

/**
* Набор колонок описывающих связь комплектации с ценами
*/
class OfferPrice extends \Catalog\Model\CsvPreset\Cost
{
    protected static
        $type_cost = [],
        $type_cost_by_title = [],
        $default_currency = null, //Валюта по умолчанию
        $currencies = [],
        $currencies_by_title = [];
        
    protected
        $delimiter = ';',
        $id_field,
        $link_preset_id,
        $link_id_field,
        $manylink_orm,
        $orm_object;
        
    function __construct($options)
    {
        $this->array_field = 'excost';
        $this->manylink_foreign_id_field = 'cost_id';
        $this->manylink_id_field = 'product_id';
        $this->manylink_orm = new Orm\Xcost();
        $this->orm_object = new Orm\Typecost();
        $this->id_field = 'id';
        $this->link_id_field = 'id';
        parent::__construct($options);
        
        $this->link_preset_id = 0;
        $this->loadCurrencies();
    }
    
    
    /**
    * Устанавливает поле с массивом сведений о ценах
    * 
    * @param string $field - поле с ценой
    */
    function setArrayField($field)
    {
        $this->array_field = $field;
    }
    
    
    /**
    * Подгрузка сведений о валютах и ценах присутствующих в системе
    * 
    */
    function loadCurrencies()
    {
        $api = new \Catalog\Model\CurrencyApi();
        $api->setOrder('`default` DESC');
        $list = $api->getList();
        foreach($list as $cost) {
            self::$currencies[$cost['id']] = $cost['title'];
        }
        self::$currencies_by_title = array_flip(self::$currencies);
        //Валюта по умолчанию
        self::$default_currency = current(self::$currencies);
        
        $type_api = new \Catalog\Model\CostApi();
        $type_api->setFilter('type', 'manual');
        $list = $type_api->getList();
        foreach($list as $typecost) {
            self::$type_cost[$typecost['id']] = $typecost['title'];
        }
        self::$type_cost_by_title = array_flip(self::$type_cost);
    }  


    /**
    * Загружает связанные данные
    * 
    * @return void
    */
    function loadData()
    {
        $ids = [];
        foreach($this->schema->rows as $row) {
            $ids[] = $row[$this->link_id_field];
        }
        
        $this->row = [];
        if ($ids) {
            $this->row = \RS\Orm\Request::make()
                ->from($this->orm_object, 'OFFER')
                ->whereIn($this->link_id_field, $ids)
                ->objects(null, $this->link_id_field, true);
        }
    }        
    
    
    /**
    * Возвращает ассоциативный массив с одной строкой данных, где ключ - это id колонки, а значение - это содержимое ячейки
    * 
    * @param integer $n - индекс в наборе строк $this->rows
    * @return array
    */
    function getColumnsData($n)
    {
        /** 
        * @var \Catalog\Model\Orm\Offer
        */
        $offer = $this->schema->rows[$n];   
        $id    = $this->schema->rows[$n][$this->link_id_field];
        
        $values_array = [];
        if (isset($this->row[$id])) {
            foreach($this->row[$id] as $n => $item) {          
                
                $price_arr = $offer['pricedata_arr'];
                //Разберём цены в зависимости от типа заданных параметров цены
                if (isset($price_arr['oneprice']) && $price_arr['oneprice']['use']) { //Если единая цена всех типов цен
                    foreach (self::$type_cost_by_title as $title => $cost_id){
                        $znak     = ($price_arr['oneprice']['znak'] == "=") ? "" : "(".$price_arr['oneprice']['znak'].")"; 
                        
                        $currency = "%";
                        if ($price_arr['oneprice']['unit'] != "%"){ //Если числовое значение
                            $currency = isset(self::$currencies[$price_arr['oneprice']['unit']]) ? self::$currencies[$price_arr['oneprice']['unit']] : ''; 
                        }
                        $values_array[$this->id.'-costlistname_'.$cost_id]     = $znak.$price_arr['oneprice']['original_value']; 
                        $values_array[$this->id.'-costlistcurrency_'.$cost_id] = $currency;
                    }
                }
                elseif (!isset($price_arr['oneprice']) && isset($price_arr['price'])) { //Если цены на комплектацию разные
                    foreach ($price_arr['price'] as $cost_id=>$price_data) {
                        if (isset($price_data['znak'])){
                            $znak     = ($price_data['znak'] == "=") ? "" : "(".$price_data['znak'].")";
                        }
                        if (!isset($znak)){                            //на случай если цены для комплектации не были заданы изначально по какой то причине
                            $znak = '(+)';
                        }
                        if (!isset($price_data['original_value'])){
                            $price_data['original_value'] = 0;
                        }
                        $currency = '';
                        if (isset($price_data['unit'])) {
                            if ($price_data['unit'] == "%") {
                                $currency = "%";
                            } elseif (isset(self::$currencies[$price_data['unit']])) {
                                $currency = self::$currencies[$price_data['unit']];
                            }
                        }

                        $values_array[$this->id.'-costlistname_'.$cost_id]     = $znak.$price_data['original_value'];
                        $values_array[$this->id.'-costlistcurrency_'.$cost_id] = $currency;

                    }
                }  
            }
        }
        return $values_array;        
        
    }
    
    /**
    * Возвращает колонки, которые добавляются текущим набором 
    * 
    * @return array
    */
    function getColumns() {        
        $columns = [];
        if (!empty(self::$type_cost)){
           foreach (self::$type_cost as $cost_id => $cost_title){
               $columns[$this->id.'-costlistname_'.$cost_id] = [
                    'key'   => 'costname_'.$cost_id,
                    'title' => t('Цена').'_'.$cost_title
               ];
               $columns[$this->id.'-costlistcurrency_'.$cost_id] = [
                    'key'   => 'costlistcurrency_'.$cost_id,
                    'title' => t('Цена').'_'.$cost_title.'_'.t('Валюта')
               ];
           } 
        }
        
        return $columns;
    }
    
    /**
    * Добавляет дополнительный 
    * 
    * @param array $pricedata_arr - массив цены для комплектации
    */
    function addOnePriceIfNeeded($pricedata_arr)
    {
        if (!$pricedata_arr) return $pricedata_arr;
        
        $one_price       = true;
        $last_seriallize = ''; //Сериализованная строка для проверки 
        foreach ($pricedata_arr['price'] as $cost_id=>$info){
          if (empty($last_seriallize)){
              $last_seriallize = serialize($info);
          }else{
              if ($last_seriallize!=serialize($info)){
                 $one_price = false;
                 break; 
              }
          } 
        }
        if ($one_price){ //Если "Для всех типов цен" признак найден
          $first = reset($pricedata_arr['price']);
          $pricedata_arr['oneprice']['use']            = 1; 
          $pricedata_arr['oneprice']['znak']           = $first['znak']; 
          $pricedata_arr['oneprice']['original_value'] = $first['original_value']; 
          $pricedata_arr['oneprice']['unit']           = $first['unit']; 
        }
        return $pricedata_arr;
    }
    
    /**
    * Добавляет данные цены для массива цены комплектации
    * 
    * @param array $pricedata_arr - массив в данными о ценах комплектации 
    * @param integer $cost_id - id цены
    * @param string $value - значение цены
    */
    function addCostInPriceArray($pricedata_arr, $cost_id, $value)
    {
        if ($value === ''){
            return $pricedata_arr;
        }
        $znak = "=";
        if (!is_numeric($value)){ //Если это строка
            if (preg_match('/\(([\+|=])\)?([\d|.]+)?/', $value, $matches)){
               $znak  = $matches[1];
               $value = $matches[2];
            }    
        }
        $pricedata_arr['price'][$cost_id]['znak']           = $znak;
        $pricedata_arr['price'][$cost_id]['original_value'] = $value;
        
        return $pricedata_arr;
    }
    
    /**
    * Импортирует одну строку данных
    * 
    * @return void
    */
    function importColumnsData()
    {
        if (isset($this->row)) {  
            $pricedata_arr = [];
            foreach($this->row as $key_info=>$item) {
                $item     = trim($item);                  //Значение ячейки
                $key_info = explode("_",trim($key_info)); //Получим информацию из поля
                $cost_id  = $key_info[1];
                
                switch($key_info[0]){  //Пройдёмся по типу поля 
                    case "costname": //Название цены
                            $value = str_replace([","," "], [".",""], $item);
                            $pricedata_arr = $this->addCostInPriceArray($pricedata_arr, $cost_id, $value); //Разложим для импорта не многомерной комлектации 
                            break;
                    case "costlistcurrency": //Валюта цены
                            $currency_id = $item;
                            if ($currency_id != "%"){
                               $currency_id = isset(self::$currencies_by_title[$currency_id]) ? self::$currencies_by_title[$currency_id] : 0; 
                            }
                            if (isset($pricedata_arr['price'][$cost_id])) {
                                $pricedata_arr['price'][$cost_id]['unit']   = $currency_id; 
                            }
                            break;
                }
            }
            
            if (!empty($pricedata_arr)){ //Если данные есть, то проверим, нужно ли объединять и добавлять признак "Для всех типов цен"
                $pricedata_arr = $this->addOnePriceIfNeeded($pricedata_arr);
            }
           
            $this->schema->getPreset($this->link_preset_id)->row[$this->array_field] = $pricedata_arr;
        }
    }
}