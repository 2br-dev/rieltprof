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
class OrderProducts extends \RS\Csv\Preset\AbstractPreset
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
        * @var \Shop\Model\Orm\Order
        */
        $order    = $this->schema->rows[$n];
        $products = $order->getCart()->getCartItemsByType('product');
         
        $line = '';
        if (!empty($products)){
            $lines = [];
            
            foreach ($products as $uniq_id=>$product){
               $data   = [];
               $data[] = $product['title']; 
               if (!empty($product['model'])) {
                  $data[0] .= "(".$product['model'].")";  
               }
               $data[] = t("Артикул:").$product['barcode']; 
               $data[] = t("Кол-во:").$product['amount']; 
               $data[] = t("Сумма:").$product['price']; 
               $data[] = t("Вес одного товара:").$product['single_weight'];
               if (!empty($product['discount'])) {
                  $data[] = t("Скидка:").$product['discount']."";  
               }
               $lines[] = implode(' | ',$data);
            }
            $line = implode($this->delimiter."\n", $lines);         
        }
        return [
            $this->id.'-products' => $line
        ];
        
    }

    
    /**
    * Возвращает колонки, которые добавляются текущим набором 
    * 
    * @return array
    */
    function getColumns() {
        return [
            $this->id.'-products' => [
                'key' => 'products',
                'title' => t('Товары')
            ]
        ];
    }
    
}