<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Html\Table\Type;

use RS\Orm\Request;
use RS\View\Engine;

abstract class AbstractType
{
    public 
        $property = [],
        $sorturl;
        
    protected
        $container,
        $can_modificate_query = false,
        $option_prefixes = ['set', 'add'],
        $field,
        $row,
        $title,
        $attr_callback,
        $value;
    
    protected
        $head_template = 'system/admin/html_elements/table/coltype/strhead.tpl',
        $body_template = '';

    function __construct($field, $title = null, $property = null)
    {
        $this->field = $field;
        $this->title = $title;
        if ($property !== null) {
          
            foreach($property as $option => $value)
                foreach($this->option_prefixes as $prefix) {
                    $method_name = $prefix.$option;
                    if (method_exists($this, $method_name)) {
                        $this->$method_name($value);
                        unset($property[$option]);
                        break;
                    }
                }        
            $this->property = array_replace_recursive($this->property, $property);
        }
        if (!isset($this->property['customizable']))  $this->property['customizable'] = true;
        if (isset($this->property['Value'])) $this->value = $this->property['Value'];
                
        $this->_init();
    }
    
    /** 
    * Вызывается сразу после конструктора.
    */
    function _init() {}
    
    /**
    * Возвращает true, если колонку можно включать/отключать в настройках таблицы
    */
    function isCustomizable()
    {
        return !empty($this->property['customizable']);
    }
    
    /**
    * Устанавливает, скрывать ли данный столбец по-умолчанию
    * 
    * @param bool $bool Если true - то столбец не будет отображен по-умолчанию
    * @return AbstractType
    */
    function setHidden($bool)
    {
        $this->property['hidden'] = $bool;
        return $this;
    }
    
    /**
    * Возвращает true, если поле не отображается
    * @return bool
    */
    function isHidden()
    {
        return !empty($this->property['hidden']);
    }
    
    /**
    * Устанавливает гиперссылку для ячейки
    * 
    * @param string $href
    * @return AbstractType
    */
    function setHref($href)
    {
        $this->property['href'] = $href;
        return $this;
    }
    
    /**
    * Устанавливает, какого типа сортировка может быть у данной колонки
    * 
    * @param string $sortable - Возможно использовать константы: SORTABLE_ASC, SORTABLE_DESC, SORTABLE_BOTH, SORTABLE_NONE
    * @return AbstractType
    */
    function setSortable($sortable)
    {
        $this->property['Sortable'] = $sortable;
        return $this;
    }
    
    /**
    * Устанавливает, какая сортровка в данный момент применена
    * 
    * @param string $sortable - Возможно использовать константы: SORTABLE_ASC, SORTABLE_DESC
    * @return AbstractType
    */
    function setCurrentSort($sort)
    {
        $this->property['CurrentSort'] = $sort;
        return $this;
    }
    
    /**
    * Возвращает поле данной колонки
    */
    function getField()
    {
        return $this->field;
    }
    
    /**
    * Устанавливает строку значений
    * 
    * @param array $row
    * @return AbstractType
    */
    function setRow($row)
    {
        $this->row = $row;
        return $this;
    }
    
    /**
    * Возвращает строку значений и значение колонки $key
    * 
    * @param mixed $key - ключ колонки
    */
    function getRow($key = null)
    {
        return ($key !== null) ? $this->row[$key] : $this->row;
    }
    
    /**
    * Устанавлвает название колонки
    * 
    * @param mixed $title
    * @return AbstractType
    */
    function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }
    
    /**
    * Устанавливает значение текущей ячейки
    * 
    * @param mixed $value
    * @return AbstractType
    */
    function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
    
    /**
    * Устанавливает аттрибуты для ячейки колонки
    * 
    * @param array $attributes
    * @return AbstractType
    */
    function setTdAttr($attributes)
    {
        $this->property['TdAttr'] = $attributes;
        return $this;
    }
    
    /**
    * Устанавливает аттрибуты для шапки колонки
    * 
    * @param mixed $attributes
    * @return AbstractType
    */
    function setThAttr($attributes)
    {
        $this->property['ThAttr'] = $attributes;
        return $this;
    }    
    
    
    /**
    * Возвращает название колонки
    */
    function getTitle()
    {
        return $this->title;
    }
        
    /**
    * Возвращает значение текущей ячейки
    */
    function getValue()
    {
        return $this->value;
    }
    
    /**
    * Возвращает аттрибуты в виде строки для элемента ячейки
    * 
    * @return string
    */
    function getCellAttr()
    {
        if (isset($this->property['cellAttrParam']) && isset($this->row[$this->property['cellAttrParam']])) {            
            return $this->abstractGetAttr($this->property['cellAttrParam'], [], $this->row);
        }
    }

    /**
     * Возвращает аттрибуты для элемента
     *
     * @param array $concat_arr массив, значения которого будут добавлены к атрибутам
     * @param array|null $source альтернативный источник массива с атрибутами
     * @return string
     */
    function getAttr(array $concat_arr, $source = null)
    {  
        return $this->abstractGetAttr('attr', $concat_arr, $source);
    }
            
    
    /**
    * Возвращает аттрибуты для шапки колонки
    */
    function getThAttr()
    {  
        return $this->abstractGetAttr('ThAttr');
    }
    
    /**
    * Возвращает аттрибуты для ячейки
    */
    function getTdAttr()
    {  
        return $this->abstractGetAttr('TdAttr');
    }

    /**
     * Возвращает строку из атрибутов Ключ = Значение
     *
     * @param string $index ключ в массиве property
     * @param array $concat_arr массив, значения которого будут добавлены к атрибутам
     * @param array|null $source альтернативный источник массива с атрибутами
     * @return string
     */
    protected function abstractGetAttr($index, array $concat_arr = [], $source = null)
    {
        $str = '';
        if ($source === null) {
            $source = $this->property;
        }

        if ($this->attr_callback) {
            $attributes = call_user_func($this->attr_callback, $this, $index, $source) ?: [];
        } else {
            $attributes = $source[$index] ?? [];
        }

        foreach($attributes as $key => $val) {
            if (isset($concat_arr[$key])) {
                $val .= $concat_arr[$key];
            }
            if ($key[0] == '@') {
                $val = $this->getHref($val);
                $key = substr($key, 1);
            }
            $str .= " $key=\"$val\"";
        }

        return $str;
    }

    /**
     * Устанавливает произвольный обработчик, который может вмешиваться в формирование атрибутов
     *
     * @param callback $callback в callback подаются аргументы: $this, $index, $source
     */
    function setAttrCallback($callback)
    {
        $this->attr_callback = $callback;
    }
    
    /**
    * Возвращает ссылку ячейки
    * 
    * @param string | Closure $href_pattern - шаблон для поставления значения реалной ссылки
    * @return string
    */
    function getHref($href_pattern = null)
    {
        if ($href_pattern === null) {
            $href_pattern = $this->property['href'];
        }
        
        if ($href_pattern instanceof \Closure) {
            return call_user_func($href_pattern, $this->row);
        }
        
        $href = $href_pattern;
        if (strpos($href, '~field~') !== false) {
            $href = str_replace('~field~', urlencode($this->getValue()), $href);            
        }
        if (strpos($href, '@') !== false) {
            $href = preg_replace_callback('/(@([^&\/]+))/', [$this, 'replaceCallback'], $href);
        }
        if (strpos($href, '{') !== false) {
            $href = preg_replace_callback('/({([^&\/]+)})/', [$this, 'replaceCallback'], $href);
        }
        return $href;        
    }
    
    protected function replaceCallback($matches)
    {
        return $this->row[$matches[2]];
    }
    
    /**
    * Возвращает аттрибуты для ссылки в формате строки
    */
    function getLinkAttr()
    {
        return $this->abstractGetAttr('LinkAttr');
    }
    
    /**
    * Устанавливает аттрибуты для обрамляющего элемента ссылки
    * 
    * @param array $link_attributes
    * @return AbstractType
    */
    function setLinkAttr(array $link_attributes)
    {
        $this->property['LinkAttr'] = $link_attributes;
        return $this;
    }
    
    function getHeadTemplate()
    {
        return $this->head_template;
    }
    
    function getBodyTemplate()
    {
        return $this->body_template;
    }
    
    /**
    * Возвращает шапку для колонки
    */
    function getHead()
    {
        $view = new Engine();
        $view->assign('cell', $this);        
        return $view->fetch($this->head_template);
    }

    /**
     * Устанавливает контейнер, в котором располагается ячейка,
     * например - объект таблицы.
     *
     * @param object $container
     */
    function setContainer($container)
    {
        $this->container = $container;
    }

    /**
     * Возвращает контейнер, в котором располагается ячейка
     *
     * @return object
     */
    function getContainer()
    {
        return $this->container;
    }

    /**
     * Вызывается в момент установки данных один раз для одной колонки
     */
    function onSetData($data)
    {}

    /**
     * Модифицирует запрос для установки сортировки
     * @param Request $q
     */
    function modificateSortQuery(Request $q)
    {}

    /**
     * Возвращат true сли данная колонка способна модифицировать запрос для установки сортировки, в противном случае false
     * @return bool
     */
    function canModificateSortQuery()
    {
        return $this->can_modificate_query;
    }
}

