<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Html\Toolbar\Button;

abstract class AbstractButton
{
    protected
        $class_ajax = '',
        $property = [],
        $template;
    
    function __construct(array $property = null)
    {
        if ($property !== null) {
            $this->property = array_replace_recursive($this->property, $property);
        }
    }
    
    /**
    * Возвращает шаблон кнопки
    */
    function getTemplate()
    {
        return $this->template;
    }
    
    /**
    * Устанавливает дополнительные атрибуты элементу кнопки в HTML
    * 
    * @param array $attr - список Аттрибут => значение
    * @return AbstractButton
    */
    function setAttr(array $attr)
    {
        $this->property['attr'] = $attr;
        return $this;
    }
    
    /**
    * Возвращает строку с атрибутами для вставки в HTML
    * 
    * @return string
    */
    function getAttrLine()
    {
        $line = '';
        if (!empty($this->property['attr'])) {
            foreach($this->property['attr'] as $key => $value) {
                if ($key == 'class'
                    && empty($this->property['noajax'])
                    && !empty($this->class_ajax))
                {
                    $value .= ' '.$this->class_ajax;
                }

                $quote = $value[0] == '{' ? "'" : '"';
                $line .= $key.'='.$quote.$value.$quote.' ';
            }
        }
        return $line;
    }
}

