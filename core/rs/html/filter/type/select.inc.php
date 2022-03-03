<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Html\Filter\Type;

class Select extends AbstractType
{
    public
        $tpl = 'system/admin/html_elements/filter/type/select.tpl';
        
    protected
        $list;
        
    function __construct($key, $title, $list, $options = [])
    {
        $this->list = $list;
        parent::__construct($key, $title, $options);
    }

    /**
     * Возвращает список возможных значений отображаемого списка
     *
     * @return array
     */
    function getList()
    {
        return $this->list;
    }

    /**
     * Возвращает текстовое значение выбранного элемента списка
     *
     * @return string
     */
    function getTextValue()
    {
        return $this->list[$this->getValue()];
    }
    
}