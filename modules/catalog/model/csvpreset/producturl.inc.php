<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\CsvPreset;

/**
* Пресет для экспорта данных (полный url товара)
*/ 
class ProductUrl extends \RS\Csv\Preset\AbstractPreset
{
    /**
    * Устанавливает название экспортной колонки
    * 
    * @param mixed $title
    */
    function setTitle($title)
    {
        $this->title = $title;
    }
    
    function getColumns()
    {
        return [
            $this->id.'-producturl' => [
                'key' => 'producturl',
                'title' => $this->title
            ]
        ];
    }
    
    /**
    * Получает данные для колонки
    * 
    * @param integer $n - номер колонки
    * @return array
    */
    function getColumnsData($n)
    {           
        /**
        * @var \Catalog\Model\Orm\Product
        */
        $product = $this->schema->rows[$n]; 
                         
        return [$this->id.'-producturl' => $product->getUrl(true)];
    }
    
    /**
    * Пустой метод, т.к. в импорте не участвует поле, только в экспорте
    * 
    */
    function importColumnsData()
    {}    
    
}