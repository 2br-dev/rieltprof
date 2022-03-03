<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Html\Table;

use RS\Html\AbstractHtml;
use RS\Html\Table\Type\Actions;
use RS\Orm\Request;

/**
* Класс управления таблицей
*/
class Control extends AbstractHtml
{
    protected $id;
    protected $auto_fill = true;
    protected $table_var = 'table';
    protected $sort_column_var = 'sort';
    protected $sort_direction_var = 'direction';
    /**
     * @var Element
     */
    protected $table;
        
    function __construct(array $options)
    {
        parent::__construct($options);
        if ($this->auto_fill) $this->fill();
    }    
    
    /**
     * Устанавливает уникальный идентификатор таблицы, чтобы сохранять для неё параметры.
     *
     * @param integer $id Уникальный идентификатор таблицы
     * @return void
     */
    function setId($id)
    {
        $this->id = $id;
    }
    
    /**
     * Возвращает уникальный идентификатор таблицы
     *
     * @return string
     */
    function getId()
    {
        return 'table-'.$this->id;
    }

    /**
     * Устанавливает, нужно ли автоматически (в конструкторе) устанавливать
     * параметры таблицы, исходя из настроек клиента
     *
     * @param bool $autofill
     * @return void
     */
    function setAutoFill($autofill)
    {
        $this->auto_fill = $autofill;
    }        
    
    /**
     * Устанавливает параметры таблицы, исходя из настроек пользователя
     *
     * @return void
     */
    function fill()
    {
        $my_cookie = $this->url->cookie($this->getId(), TYPE_ARRAY, false);
        $params = $this->url->request($this->table_var, TYPE_ARRAY, []);
        if (isset($params[ $this->sort_column_var ])) {
            //Устанавливаем текущую сортировку
            $this->table->setSortColumn( $params[ $this->sort_column_var ], $params[ $this->sort_direction_var ] );
        } else {
            //Устанавливаем сортировку, выставленную в настройках
            if (isset($my_cookie['sort'])) {
                list($column, $direction) = explode('=', $my_cookie['sort']);
                $direction = ($direction === 'asc' ? 'asc' : 'desc');
                $this->table->setSortColumn( (int)$column, strtoupper($direction) );
            }
        }
        
        $hidden = [];
        if ($this->getId() !== null && $my_cookie) {
            if (isset($my_cookie['columns'])) {
                foreach(explode(',', $my_cookie['columns']) as $column_value) {
                    list($column, $value) = explode('=', $column_value);
                    $hidden[(int)$column] = ($value === 'N');
                }
            }
        }

        
        foreach($this->table->getColumns() as $n => $obj)
        {
            //Устанавливаем url для сортировки            
            if (!empty($obj->property['Sortable']))
            {
                if (isset($obj->property['CurrentSort'])) {
                    $direction =  ($obj->property['CurrentSort'] == SORTABLE_ASC) ? SORTABLE_DESC : SORTABLE_ASC;
                } else {
                    $direction = ($obj->property['Sortable'] == SORTABLE_BOTH) ? SORTABLE_ASC : $obj->property['Sortable'];
                }
                
                $obj->sorturl = $this->url->replaceKey([
                    $this->table_var => [
                        $this->sort_column_var => $n,
                        $this->sort_direction_var => $direction
                    ]
                ]);
            }
            
            //Скрываем колонки, которые отключены пользователем.
            if (isset($hidden[$n]) && !($obj instanceof Actions)) {
                $obj->property['hidden'] = $hidden[$n];
            }
            
            //Снимаем флаг сортировки, со скрытых полей
            if (!empty($obj->property['hidden']) && isset($obj->property['CurrentSort'])) {
                unset($obj->property['CurrentSort']);
            }
        }
    }

    /**
     * Возвращает колонку, по которой установлена сортировка
     *
     * @return bool|string
     */
    function getSqlOrderBy()
    {
        $orderby = false;
        if (($obj = $this->table->getSortColumn()) && !$obj->canModificateSortQuery()) {
            $orderby = $obj->getField().' '.$obj->property['CurrentSort'];
        }
        return $orderby;
    }

    /**
     * Модифицирует объект запроса, так, чтобы применялась необходимая сортировка данных
     *
     * @param Request $q
     * @return void
     */
    function modificateSortQuery(Request $q)
    {
       if (($obj = $this->table->getSortColumn()) && $obj->canModificateSortQuery() ) {
           return $obj->modificateSortQuery($q);
       }
    }

    /**
     * Устанавливает объект таблицы, которым необходимо управлять
     *
     * @param Element $table
     * @return void
     */
    function setTable(Element $table)
    {
        $this->table = $table;
    }

    /**
     * Возвращает объект управляемой таблицы
     *
     * @return Element
     */
    function getTable()
    {
        return $this->table;
    }

    /**
     * Возвращает HTML таблицы
     *
     * @return string
     */
    function getView()
    {
        return $this->table->getView();
    }
}

