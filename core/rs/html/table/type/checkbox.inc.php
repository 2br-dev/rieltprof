<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Html\Table\Type;

class Checkbox extends AbstractType
{
    const
        AUTO_WIDTH = 26,
        DEFAULT_CHECKBOX_NAME = 'chk[]',
        DEFAULT_HEAD_CLASS = ' chk';
        
    public 
        $property = [
            'ThAttr' => ['class' => 'chk'],
            'TdAttr' => ['class' => 'chk'],
            'customizable' => false
    ];
    
    protected
        $name,
        $head_template = 'system/admin/html_elements/table/coltype/checkbox_head.tpl',
        $body_template = 'system/admin/html_elements/table/coltype/checkbox.tpl';
        
    function __construct($field, $property = null)
    {
        parent::__construct($field, null, $property);
        $this->name = isset($this->property['CheckboxName']) ? $this->property['CheckboxName'] : self::DEFAULT_CHECKBOX_NAME;
        if (!isset($property['noAutoWidth'])) $this->setAutoWidth();
    }
    
    function setAutoWidth()
    {
        if (!isset($this->property['ThAttr']['style'])) {
            $this->property['ThAttr']['style'] = '';
        }
        if (!isset($this->property['TdAttr']['style'])) {
            $this->property['TdAttr']['style'] = '';
        }
        $this->property['ThAttr']['style'] .= "width:".self::AUTO_WIDTH."px;";        
        $this->property['TdAttr']['style'] .= "width:".self::AUTO_WIDTH."px;";        
    }
    
    /**
    * Устанавливает, отображать ли checkbox "выделить элементы на всех страницах"
    * 
    * @param bool $bool - если true, то отображать
    * @return Checkbox
    */
    function setShowSelectAll($bool)
    {
        $this->property['showSelectAll'] = $bool;
        return $this;
    }
    
    /**
    * Возвращает имя чекбоксов
    */
    function getName()
    {
        return $this->name;
    }
    
}

