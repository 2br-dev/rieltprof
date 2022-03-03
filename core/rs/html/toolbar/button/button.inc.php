<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Html\Toolbar\Button;

class Button extends AbstractButton
{
    protected
        $title,
        $href,
        $template = 'system/admin/html_elements/toolbar/button/button.tpl';

    
    function __construct($href, $title = null, $property = null)
    {
        parent::__construct($property);
        @$this->property['attr']['class'] = 'btn '.($this->property['attr']['class'] ?: 'btn-default');

        if (strpos($this->property['attr']['class'], 'btn-') === false) {
            //Для совместимости с предыдущими версиями RS
            $this->property['attr']['class'] .= ' btn-default';
        }
        
        $this->title = $title;
        $this->href = $href;
    }
    
    /**
    * Устанавливает название кнопки
    * 
    * @param string $title
    * @return Button
    */
    function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }
    
    /**
    * Устанавливает ссылку кнопки
    * 
    * @param string $href
    * @return Button
    */
    function setHref($href)
    {
        $this->href = $href;
        return $this;
    }
    
    /**
    * Возвращает название кнопки
    * 
    * @return string
    */
    function getTitle()
    {
        return $this->title;
    }
    
    /**
    * Возвращает ссылку кнопки
    * @return string
    */
    function getHref()
    {
        return $this->href;
    }
}

