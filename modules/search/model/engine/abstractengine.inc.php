<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Search\Model\Engine;

use RS\Orm\Request as OrmRequest;

/**
* Абстрактный класс поискового сервиса
*/
abstract class AbstractEngine
{
    const
        ORDER_RELEVANT = 'relevant',
        ORDER_FIELD = 'field';
        
    protected
        $config,
        $order,
        $order_type = self::ORDER_RELEVANT,
        $errors = [],
        $query,
        $page_size,
        $filters,
        $total = 0;
        
    function __construct()
    {
        $this->config = \RS\Config\Loader::byModule($this);
    }
    
    /**
    * Возвращает название поискового сервиса
    * 
    * @return string
    */
    abstract public function getTitle();    
    
    /**
    * Выполняет поиск по заранее заданным параметрам. Возвращает массив индексных объектов.
    * 
    * @return \Search\Model\Orm\Index[] | false - если поиск выполнен, в случае ошибки - false
    */
    abstract public function search(OrmRequest $q = null);


    /**
     * Модифицирует объект запроса $q, добавляя в него условия для поиска.
     *
     * @param OrmRequest $q - объект запроса
     * @param mixed $alias_product - псевдоним для таблицы товаров
     * @param mixed $alias - псевдоним для индексной таблицы
     * @return OrmRequest
     */
    abstract public function joinQuery(OrmRequest $q, $alias_product = 'A', $alias = 'B');


    /**
    * Устанавливает сортировку по релевантности
    * 
    * @return self
    */    
    function setOrderByRelevant()
    {
        $this->order_type = self::ORDER_RELEVANT;
        return $this;
    }
    
    /**
    * Устанавливает сортировку по полю $field
    * 
    * @param string $field
    * @return self
    */
    function setOrderByField($field)
    {
        $this->order_type = self::ORDER_FIELD;
        $this->order = $field;
        return $this;
    }
    
    /**
    * Устанавливает поисковый запрос для поиска
    * 
    * @param string $query
    * @return self
    */
    function setQuery($query)
    {
        $this->query = trim($query);
        return $this;
    }
    
    /**
    * Возвращает поисковый запрос, подготовленный для отображения в HTML
    * 
    * @return string
    */
    function getQueryView()
    {
        return htmlspecialchars($this->query);
    }
    
    /**
    * Устанавливает страницу для результатов поиска
    * 
    * @param integer $page
    * @return self
    */
    function setPage($page)
    {
        $this->page = $page;
        return $this;
    }
    
    /**
    * Устанавливает количество результатов на странице
    * 
    * @param integer $page_size
    * @return self
    */
    function setPageSize($page_size)
    {
        $this->page_size = $page_size;
        return $this;
    }
    
    /**
    * Устанавливает дополнительные фильтры, которые будут применены к поисковому индексу
    * 
    * @param string $key
    * @param mixed $value
    * @return self
    */
    function setFilter($key, $value)
    {
        if ($value === null) unset($this->filters[$key]);
            else $this->filters[$key] = $value;
        return $this;
    }
    
    /**
    * Возвращает общее количество результатов поиска
    * 
    * @return integer
    */
    function getTotal()
    {
        return $this->total;
    }    
    
        
    /**
    * Добавляет сведения об ошибке
    * 
    * @param string $errorText текст ошибки
    * @return self
    */
    function addError($errorText)
    {
        $this->errors[] = $errorText;
        return $this;
    }
    
    /**
    * Возвращает ошибки, произошедшие во время поиска
    * 
    * @return array
    */
    function getErrors()
    {
        return $this->errors;
    }    
    
    /**
    * Модифицирует индексную таблицу
    * 
    * @param mixed $search_item
    */
    function onUpdateSearch($search_item)
    {}
}
