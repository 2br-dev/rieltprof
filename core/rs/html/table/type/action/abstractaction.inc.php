<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Html\Table\Type\Action;
use \RS\Html\Table\Type\Actions;

abstract class AbstractAction 
{
    public
        $options = [
            'noajax' => false,
            'attr' => []
    ];
    
    protected
        $container,
        $class_ajax = '',
        $class_action = '',
        $class_icon = '',
    
        $href_pattern,
        $title,
        $hidden,
        $body_template = 'system/admin/html_elements/table/coltype/action/abstract.tpl';
        
    function __construct($href_pattern, $title = null, array $options = null)
    {
        $this->options = $options;
        if ($this->options) {
            foreach($this->options as $option => $value)
                foreach(['add', 'set'] as $prefix)
                {
                    $method_name = $prefix.$option;
                    if (method_exists($this, $method_name)) {
                        $this->$method_name($value);
                        break;
                    }
                }
        }
        
        $this->href_pattern = $href_pattern;
        $this->title = $title;
    }

    function setContainer(Actions $container)
    {
        $this->container = $container;
    }
    
    function getContainer()
    {
        return $this->container;
    }
    
    function getTitle()
    {
        return $this->title;
    }
    
    function getTemplate()
    {
        return $this->body_template;
    }
    
    function getHrefPattern()
    {
        return $this->href_pattern;
    }
    
    function setClass($action_class)
    {
        $this->class_action = $action_class;
    }
    
    function setAttr(array $attr)
    {
        $this->options['attr'] = $attr;
    }
    
    function setDisableAjax($bool)
    {
        $this->options['noajax'] = $bool;
    }
    
    function setHidden($bool)
    {
        $this->hidden = $bool;
    }
    
    function isHidden()
    {
        if (is_callable($this->hidden)) {
            return call_user_func($this->hidden, $this);
        }
        return $this->hidden;
    }
    
    function getClass()
    {
        return $this->class_action.(empty($this->options['noajax']) ? ' '.$this->class_ajax : '' );
    }

    /**
     * Устанавливает класс для иконки
     *
     * @param string $icon_class
     */
    function setIconClass($icon_class)
    {
        $this->class_icon = $icon_class;
    }

    /**
     * Возвращает установленный ранее класс для иконки
     *
     * @return string
     */
    function getIconClass()
    {
        return $this->class_icon;
    }
}

