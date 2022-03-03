<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
// HTML компонент - таблица
namespace RS\Html\Table;

use RS\Html\AbstractHtml;
use RS\Orm\AbstractObject;
use RS\View\Engine;

class Element extends AbstractHtml
{
    protected
        $data,
        $table_attr = ['class' => 'rs-table'],
        $row_attr_param,
        $tr_param = [],
        
        $columns = [],
        $rows = [],
        $anyrows = [];
    
    function __construct(array $options, $data = null)
    {
        parent::__construct($options);
        $this->data = $data;
    }
    
    /**
    * Устанавливает колонки, которые должны присутствовать в таблице
    * 
    * @param array of Type\AbstractType $columns
    * @return Element
    */
    function setColumns(array $columns) 
    {
        $this->columns = [];

        foreach($columns as $column) {
            $this->addColumn($column);
        }

        return $this;
    }
    
    /**
    * Добавляет колонку в отображение таблицы
    * 
    * @param Type\AbstractType $column
    * @param integer $pos - порядковый номер колонки слева направо. Допустимо отрицательное число, тогда справа налево.
    * @return Element
    */
    function addColumn(Type\AbstractType $column, $pos = null) 
    {
        $column->setContainer($this);
        if ($pos !== null) {
            $this->columns = array_merge(array_slice($this->columns, 0, $pos), [$column], array_slice($this->columns, $pos));
        } else {
            $this->columns[] = $column;
        }
        return $this;
    }
    
    /**
    * Возвращает список колонок таблицы
    * 
    * @return Type\AbstractType[]
    */
    function getColumns() 
    {
        return $this->columns;
    }
    
    /**
    * Возвращает одну колонку
    * 
    * @param integer $n номер колонки
    * @return Type\AbstractType
    */
    function getColumn($n)
    {
        return $this->columns[$n];
    }
    
    /**
    * Устанавливает ключ в наборе данных, в котором ожидать параметры для строки таблицы
    * 
    * @param string $field - имя поля
    * @return Element
    */
    function setRowAttrParam($field)
    {
        $this->row_attr_param = $field;
        return $this;
    }
    
    /**
    * Возвращает список колонок, которые пользователь может включить/отключить
    * 
    * @return array
    */
    function getCustomizableColumns()
    {
        $columns = $this->getColumns();
        foreach($columns as $n => $column) {
            if (!$column->isCustomizable()) {
                unset($columns[$n]);
            }
        }
        return $columns;
    }
    
    /**
    * Устанавливает аттрибуты для Dom элемента таблицы
    * 
    * @param array $attr
    * @return Element
    */
    function setTableAttr(array $attr)
    {
        $this->table_attr = array_merge($this->table_attr, $attr);
        return $this;
    }
    
    /**
    * Возвращает аттрибуты элемента таблицы
    * 
    * @return string
    */
    function getTableAttr()
    {
        $str = '';
            foreach($this->table_attr as $key=>$val) {
                $str .= " $key=\"$val\"";
            }
        return $str;
    }
    
    /**
    * Устанавливает аттрибуты для элемента строки таблицы
    * 
    * @param integer $n - номер строки, начиная с нуля.
    * @param array $attr - аттрибуты
    * @return Element
    */
    function setRowAttr($n, array $attr)
    {
        $this->row_attr[$n] = $attr;
        return $this;
    }
    
    /**
    * Возвращает аттрибуты для строки
    * 
    * @param integer $n - номер строки
    * @return string
    */
    function getRowAttr($n)
    {
        $str = '';
        if (isset($this->row_attr[$n]))
            foreach($this->row_attr[$n] as $key=>$val) {
                $str .= " $key=\"$val\"";
            }
        return $str;
    }
    
    /**
    * Устанавливает аттрибуты для вставленных вручную строк
    * 
    * @param integer $n - номер строки
    * @param array $attr
    */
    function setAnyRowAttr($n, array $attr)
    {
        $this->any_row_attr[$n] = $attr;
    }
    
    /**
    * Возвращает аттрибуты для вставленных вручную строк
    * 
    * @param integer $n - номер строки
    * @return string
    */
    function getAnyRowAttr($n)
    {
        $str = '';
        if (isset($this->any_row_attr[$n]))
            foreach($this->any_row_attr[$n] as $key=>$val) {
                $str .= " $key=\"$val\"";
            }
        return $str;
    }    
    
    
    /**
    * Устанавливает колонку, по которой сортируются строки
    * 
    * @param mixed $column номер колонки
    * @param string ASC|DESC $direction направление сортировки
    */
    function setSortColumn($column, $direction)
    {
        $direction = (strtoupper($direction) == 'ASC') ? 'ASC' : 'DESC';
        foreach($this->columns as $n=>$obj)
        {
            if ($n == $column) $obj->property['CurrentSort'] = $direction; 
                else unset($obj->property['CurrentSort']);
        }
    }
    
    /**
    * Возвращает объект колонки, по которой установлена сортировка или null
    *
    * @return Type\AbstractType|null
    */
    function getSortColumn() 
    {
        foreach($this->columns as $n=>$obj)
        {
            if (isset($obj->property['CurrentSort'])) return $obj;
        }
    }
    
    /**
    * Устанавливает набор данных для таблицы
    * 
    * @param array $data
    * @return Element
    */
    function setData($data)
    {
        $this->data = $data;

        foreach ($this->columns as $coltype_obj) {
            $coltype_obj->onSetData($data);
        }

        return $this;
    }

    /**
     * Возвращает установленный ранее набор данных
     *
     * @return array
     */
    function getData()
    {
        return $this->data;
    }

    
    /**
    * Загружает сведения из набора данных
    * 
    * @param array $data
    * @return void
    */
    function loadFromArray($data)
    {        
        $this->rows = [];
        if (is_array($data)) {
            foreach($data as $row => $val)  {
                foreach ($this->columns as $col => $coltype_obj) {
                    if (empty($coltype_obj->property['hidden'])) {
                        $value = null;
                        $property = null;
                        $field = $coltype_obj->getField();
                        if (isset($field))
                        {
                            if ($val instanceof AbstractObject && isset($val['__'.$field])) {
                                $property = $val['__'.$field];
                                $value = $property->textView();
                            } else {
                                $property = null;
                                $value = isset($val[$field]) ? $val[$field] : '';
                            }
                	        
				        }
                        
                        $item = clone $coltype_obj;
                        $item->setValue($value);
                        $item->setRow($val);

                        $this->rows[$row][$col] = $item;
                    }
                }
                if ($this->row_attr_param !== null && !empty($val[$this->row_attr_param])) {
                    $this->setRowAttr($row, $val[$this->row_attr_param]);
                }
            }
        }
    }
    
    /**
    * Вставляет произвольную строку в таблицу
    * 
    * @return Element
    */
    function insertAnyRow(array $columns, $numrow = null)
    {
        $this->anyrows[$numrow] = $columns;
        return $this;
    }

        
    /**
    * Возвращает значение опции key
    * 
    * @param string $key
    * @param mixed $default
    * @return mixed
    */
    function getOptions($key = null, $default = null) {
        if ($key !== null) {
            return isset($this->options[$key]) ? $this->options[$key] : $default;
        } else {
            return $this->options;
        }
    }
    
    /**
    * Возвращает произвольные строки
    * 
    * @param integer $n - номер строки
    * @return array | false
    */
    function getAnyRows($n = null)
    {
        if ($n !== null) {
            return isset($this->anyrows[$n]) ? $this->anyrows[$n] : false;
        } else {
            return $this->anyrows;
        }        
    }
    
    /**
    * Возвращает строки, подготовленные для отображения таблицы
    * 
    * @return array
    */
    function getRows()
    {
        return $this->rows;
    }
    
    /**
    * Возвращает HTML для текущей таблицы
    * 
    * @return string
    */
    function getView()
    {        
        $this->loadFromArray($this->data);
        
        $view = new Engine();
        $view->assign('table', $this);
        
        $view->assign('options', $this->getOptions());
        $view->assign('anyrows', $this->getAnyRows());
        $view->assign('rows', $this->getRows());
        
        return $view->fetch('%system%/admin/html_elements/table/table.tpl');
    }
    
    /**
    * Удаляет колонку из списка
    * @param integer $n номер колонки
    * @return Element
    */
    function removeColumn($n)
    {
        array_splice($this->columns, $n, 1);
        return $this;
    }
}

