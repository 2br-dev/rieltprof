<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Html\Filter;

use RS\Html\AbstractHtml;
use RS\View\Engine;

/**
* Линия форм, отвечает за отображение одной строки форм в фильтре
*/
class Line extends AbstractHtml
{
    protected
        $tpl = 'system/admin/html_elements/filter/line.tpl',
        $item_list = [];

    /**
    * Добавляет элементы форм в линию
    * 
    * @param array of Type\AbstractType $items
    * @return Line
    */
    function addItems(array $items)
    {
        foreach($items as $item) {
            $this->addItem($item);
        }
            
        return $this;
    }
        
    /**
    * Добавляет один элемент формы в линию
    * 
    * @param Type\AbstractType $item
    * @param integer | null $position - позиция элемента
    * @return Line
    */
    function addItem(Type\AbstractType $item, $position = null)
    {
        if ($position !== null) {
            array_splice( $this->item_list, $position, 0, [$item]);
        } else {
            $this->item_list[] = $item;
        }
        
        return $this;
    }
    
    /**
    * Возвращает все элементы форм Линии
    * 
    * @return array of Type\AbstractType
    */
    function getItems()
    {
        return $this->item_list;
    }


    /**
    * Удаляет элемент из списка по индексу
    *
     * @param integer $key - идентификатор позиции фильтра
    *
    * @return $this
    */
    function removeItem($key)
    {
        if (isset($this->item_list[$key])){
            unset($this->item_list[$key]);
        }

        return $this;
    }

    /**
    * Возвращает HTML код одной линии
    * 
    * @return string
    */
    function getView()
    {
        $tpl = new Engine();
        $tpl->assign('fline', $this);
        return $tpl->fetch($this->tpl);
    }
        
}

