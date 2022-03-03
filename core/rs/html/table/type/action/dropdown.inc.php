<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Html\Table\Type\Action;

/**
* Тип инструмента - выпадающий список со значениями
*/
class DropDown extends AbstractAction
{
    protected
        $items = [],
        $body_template = 'system/admin/html_elements/table/coltype/action/dropdown.tpl';
    
    function __construct(array $items)
    {
        $this->setItems($items);
    }
    
    /**
    * Возвращает все пункты выпадающего меню
    * @return array
    */
    function getItems()
    {
        return $this->items;
    }

    /**
    * Добавляет пункты выпадающего меню
    * 
    * @param array $items - пункты меню
    * @return DropDown
    */    
    function setItems(array $items)
    {
        foreach($items as $key => $item) {
            $this->addItem($item, $key);
        }
        return $this;
    }
    
    /**
    * Добавляет один пункт к выпадающему списку
    * 
    * @param array $item - пункт списка
    * Формат:
    * array(
        'title' => t('удалить'),  //title - Текст ссылки
        'attr' => array(          //attr - Атрибуты
            'class' => 'crud-get',
            'data-confirm-text' => t('Вы действительно хотите удалить данный товар?'),
            '@href' => $this->router->getAdminPattern('del', array(':chk[]' => '@id')),    //атрибут, начинающийся на @ будет дополнительно обработан функией автозамены
        )
      ),
    *
    *
    * @return DropDown
    */
    function addItem($item, $key = null)
    {
        if ($key === null) {
            $this->items[] = $item;
        } else {
            $this->items[$key] = $item;
        }
        return $this;
    }
    
    /**
    * Удаляет пункт из выпадающего списка
    * 
    * @param mixed $key - порядковый номер элемента
    * @return DropDown
    */
    function removeItem($key)
    {
        unset($this->items[$key]);
        return $this;
    }
    
    /**
    * Возвращает true, если элемент является скрытым
    * 
    * @param array $item
    * @return bool
    */
    function isItemHidden($item)
    {
        if (isset($item['hidden'])) {
            if (is_callable($item['hidden'])) {
                return call_user_func($item['hidden'], $this);
            }
            return $item['hidden'];
        }
        return false;
    }
}

