<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Html\Toolbar;

use RS\Html\AbstractHtml;
use RS\View\Engine;

/**
* Панель с кнопками
*/
class Element extends AbstractHtml
{
    protected
        $items;

    /**
    * Добавляет список кнопок в ряд
    * 
    * @param Button\AbstractButton[] $items
    * @return Element
    */
    function setItems(array $items)
    {
        foreach($items as $key => $item) {
            $this->addItem($item, $key);
        }
        return $this;
    }
    
    /**
    * Возвращает список кнопок
    * 
    * @return Button\AbstractButton[]
    */
    function getItems() 
    {
        return $this->items;
    }
    
    /**
    * Возвращает кнопку по ключу
    * 
    * @param string $key идентификатор кнопки
    * @return Button\AbstractButton
    */
    function getItem($key)
    {
        return $this->items[$key];
    }
    
    /**
    * Добавляет кнопку 
    * 
    * @param Button\AbstractButton $item объект кнопки
    * @param string $key идентификатор кнопки
    * @return Element
    */
    function addItem(Button\AbstractButton $item, $key = null)
    {
        if ($key) {
            $this->items[$key] = $item;
        } else {
            $this->items[] = $item;
        }
        return $this;
    }
    
    /**
    * Удаляет кнопку из списка
    * 
    * @param mixed $key
    */
    function removeItem($key)
    {
        unset($this->items[$key]);
    }
    
    /**
    * Возвращает отображение кнопки
    * 
    * @return string
    */
    function getView()
    {
        $view = new Engine();
        $view->assign('toolbar', $this);
        return $view->fetch('%system%/admin/html_elements/toolbar/element.tpl');
    }
}

