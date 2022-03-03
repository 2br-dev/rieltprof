<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
// Элемент - toolbar - список функций под таблицей
namespace RS\Html;

use RS\View\Engine;

class Toolbar extends AbstractHtml
{
    public    
        $default_buttons;
        
    protected
        $items;

    
    function setItems(array $items)
    {
        foreach($items as $key => $item) 
            $this->addItem($item, $key);
    }
    
    function getItems() 
    {
        return $this->items;
    }
    
    function addItem($item, $key = null)
    {
        if ($key) {
            $this->items[$key] = $item;
        } else {
            $this->items[] = $item;
        }
    }
    
    function removeItem($key)
    {
        unset($this->items[$key]);
    }
    
    function getView()
    {
        $view = new Engine();
        $view->assign('toolbar', $this);
        return $view->fetch('%system%/admin/html_elements/toolbar/toolbar.tpl');
    }
    
}

