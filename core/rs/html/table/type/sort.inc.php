<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Html\Table\Type;

class Sort extends AbstractType
{
    protected
        $head_template = 'system/admin/html_elements/table/coltype/sorthead.tpl',
        $body_template = 'system/admin/html_elements/table/coltype/sort.tpl';
    
    function _init()
    {
        
        @$this->property['TdAttr']['class'] .= ' drag-handle';
        @$this->property['ThAttr']['width'] = '20';
    }
    
    /**
    * Возвращает значение поля сортировки
    * 
    */
    function getSortValue()
    {
        if (isset($this->property['sortField'])){
           return $this->row[$this->property['sortField']]; 
        }
        return $this->getValue();
    }

}