<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\HtmlFilterType;

/**
* Класс поиска по товару в заказе
*/
class Product extends \RS\Html\Filter\Type\AbstractType
{
    public 
        $tpl = 'system/admin/html_elements/filter/type/string.tpl';
        
    protected
        $search_type = 'eq';
        
        
    /**
    * Модифицирует запрос
    * 
    * @param \RS\Orm\Request $q
    * @return \RS\Orm\Request
    */
    function modificateQuery(\RS\Orm\Request $q)
    {
        //Если указано значение и таблица ещё не присоединена
        if (!empty($this->value) && !$q->issetTable(new \Shop\Model\Orm\OrderItem())){
            $q->select = 'A.*';
            $q->join(new \Shop\Model\Orm\OrderItem(), 'A.id=PRODUCT.order_id', 'PRODUCT');
            $q->groupby('A.id');
        }
       return $q;
    }
}