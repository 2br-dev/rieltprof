<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Html\Toolbar\Button;

class Dropup extends AbstractButton
{
    const
        ITEM_TYPE_COMMON = 'common',
        ITEM_TYPE_TOGGLE = 'toggle',
        ITEM_TYPE_LISTITEM = 'listitem';

    protected
        $extra_class = 'btn-group dropup',
        $drop_items = [],
        $template = 'system/admin/html_elements/toolbar/button/dropup.tpl';
    
    function __construct(array $drop_items, $property = null)
    {
        parent::__construct($property);
        @$this->property['attr']['class'] = $this->extra_class.' '.$this->property['attr']['class'];
        $this->setItems($drop_items);
    }
    
    /**
    * Возвращает выпадающие элементы
    */
    function getDropItems()
    {
        return array_slice($this->drop_items, 1);
    }
    
    /**
    * Возвращает элемент, который будет виден по-умолчанию
    */
    function getFirstItem()
    {
        return reset($this->drop_items);
    }

    /**
     * Возвращает все элементы выпадающего списка и заголовочный элемент
     *
     * @return array
     */
    function getAllItems()
    {
        return $this->drop_items;
    }

    /**
     * Устанавливает элементы в список
     *
     * @param array $drop_items - массив элементов на установку
     */
    function setItems(array $drop_items)
    {
        foreach($drop_items as $key => $item) {
            if ($item) {
                $this->addItem($item, $key);
            }
        }
    }

    /**
     * Добавляет элемент в список
     *
     * @param $item
     * @param null|string|int $key - ключ массива куда будет добавлен элемент. Если не указан, то добавится в конец
     */
    function addItem($item, $key = null)
    {
        if ($key === null) {
            $this->drop_items[] = $item;
        } else {
            $this->drop_items[$key] = $item;
        }
    }
    
    /**
     * Возвращает строку с атрибутами элемента списка, кроме атрибута class
     * Атрибут class следует получать через метод getItemClass
     *
     * @return string
     */
    function getItemAttrLine(array $item)
    {
        $line = '';
        if (!empty($item['attr'])) {
            foreach($item['attr'] as $key => $value) {
                if ($key == 'class') continue;
                $quote = $value[0] == '{' ? "'" : '"';
                $line .= $key.'='.$quote.$value.$quote.' ';
            }
        }
        return $line;
    }

    /**
     * Возвращает значение для атрибута class
     *
     * @param array $item
     * @param $item_type
     */
    function getItemClass(array $item, $item_type = self::ITEM_TYPE_COMMON)
    {
        $class = isset($item['attr']['class']) ? $item['attr']['class'] : '';
        $base =  isset($item['class_common']) ? $item['class_common'] : 'btn'.(strpos($class, 'btn-') === false ? ' btn-default' : '');
        $class_element = isset($item['class_'.$item_type]) ? $item['class_'.$item_type] : 'dropdown-toggle';

        if ($item_type == self::ITEM_TYPE_COMMON) {
            //Возвращаем классы для главной кнопки "по умолчанию" выпадающего списка
            return $base.' '.$class;
        }
        elseif ($item_type == self::ITEM_TYPE_TOGGLE) {
            //Удаляем из вспомогательных элементов выпадающего списка микроразметку crud-...,
            // но оставляем стилевые классы кнопки
            // Сделано для совместимости с предыдущими версиями ReadyScript
            return $base.' '.$class_element.' '.preg_replace('/crud-(\w+)/', '', $class);
        } else {
            return $class;
        }
    }
}