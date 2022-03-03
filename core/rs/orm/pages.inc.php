<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Orm;

class Pages implements \ArrayAccess, \Iterator, \Countable
{
    private 
        $data = [],
        $query,
        $pagecount = 0,
        $current_page,
        $total_count,
        $page_size = 0,
        $valid = false;
    
    public 
        $auto_clear = true; //Удалять из памяти не текущие страницы. Позволяет держать в памяти только одну страницу
        
    
    public function __construct(\RS\Orm\Request $query, $page_size)
    {
        $this->query = $query;
        $this->page_size = $page_size;
        $qc = clone $query;
        $this->total_count = $qc->count();
        $this->pagecount = ceil( $this->total_count / $page_size);
    }
    
    
    public function offsetExists($offset)
    {
        return (is_int($offset)&&$offset<$this->page_size);
    }

    public function offsetGet($offset)
    {
        if (!isset($this->data[$offset]))
        {
            $this->current_page = $offset;
            if ($this->auto_clear) $this->data = []; //Очищаем массив с остальными страницами.
            $this->data[$offset] = $this->query->limit($offset*$this->page_size,$this->page_size)->objects();
        }
        return $this->data[$offset];
    }

    public function offsetSet($offset, $value)
    {
        throw new RSException(t("Нельзя менять значения свойств"));
    }

    public function offsetUnset($offset)
    {
        throw new RSException(t("Нельзя менять значения свойств"));
    }
    
    public function totalCount() {return $this->total_count;}
    
    public function count() {return $this->pagecount;}
    
    public function current() {return $this->data[$this->current_page];}
    
    public function key() {return $this->current_page;}
        
    public function next() 
    {
        if ( $this->current_page < ($this->pagecount - 1) )
        {
            return $this->valid = $this->offsetGet($this->current_page + 1);
        }
        else return $this->valid = false;
    }

    public function rewind() {return $this->valid = $this->offsetGet(0);}

    public function valid() {return $this->valid;}

    
    
    
}
