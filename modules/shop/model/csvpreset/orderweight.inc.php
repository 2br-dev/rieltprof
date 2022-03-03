<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\CsvPreset;
use \Catalog\Model\Orm;

/**
* Набор колонок описывающих связь товара с заказами
*/
class OrderWeight extends \RS\Csv\Preset\AbstractPreset
{
    protected static
        $type_cost = [],
        $type_cost_by_title = [],
        $currencies = [],
        $currencies_by_title = [];
        
    protected
        $delimiter = ';',
        $id_field,
        $link_preset_id,
        $link_id_field,
        $orm_object;
        
    
    function __construct($options)
    {
        parent::__construct($options);
        
        $this->link_preset_id = 0;
    }

    /**
    * Загружает связанные данные
    * 
    * @return void
    */
    function loadData()
    {}
    
    /**
    * Импортирует связанные данные
    * 
    */
    function importColumnsData()
    {}    
    
    /**
    * Возвращает ассоциативный массив с одной строкой данных, где ключ - это id колонки, а значение - это содержимое ячейки
    * 
    * @param integer $n - индекс в наборе строк $this->rows
    * @return array
    */
    function getColumnsData($n)
    {
        /**
        * @var \Shop\Model\Orm\Order $order
        */
        $order  = $this->schema->rows[$n];
        $weight = $order->getWeight();
        unset($order->order_cart); // чтобы не было утечки памяти
         
        return [
            $this->id.'-orderweight' => $weight
        ];
    }

    
    /**
    * Возвращает колонки, которые добавляются текущим набором 
    * 
    * @return array
    */
    function getColumns() {
        return [
            $this->id.'-orderweight' => [
                'key' => 'orderweight',
                'title' => t('Общий вес заказа')
            ]
        ];
    }
    
}