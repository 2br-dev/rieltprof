<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Helper;

use RS\Http\Request;
use RS\Router\Manager;

/**
* Пагинатор
*/
class Paginator
{
    const
        PATTERN_TEMPLATE = -2, // Заменять в %PAGE% на номе страницы в строке $pattern
        PATTERN_KEYREPLACE = -1; //Заменять параметр $pattern в строке GET
        
    protected
        $paginator_len = 9,
        $pattern,
        $pattern_type,
        $route_params,
        $pagelist = null;
        
    public
        $total,
        $page,
        $per_page,        
        $total_pages;
    
    /**
    * Пагинатор
    * 
    * @param integer $page текущая страница
    * @param integer $total всего страниц
    * @param integer $per_page элементов на странице
    * @param string $pattern_type - 0 | -1 | id маршрута -  тип формирования ссылки на страницу
    * 0 - Заменять %PAGE% в строке на номер страницы
    * -1 - Заменять параметр $pattern в текущем маршруте
    * id маршрута - Заменять параметр $pattern в заданном маршруте
    * 
    * @param array $route_params - дополнительные параметры, которые будут добавлены в ссылку
    * @param string $pattern шаблон ссылки на страницы будет заменять %PAGE% на номер страницы или параметр в GET для замены
    * 
    * @return Paginator
    */
    function __construct(&$page, $total, $per_page, $pattern_type_or_routeid = self::PATTERN_KEYREPLACE, $route_params = [], $pattern = 'p')
    {
        $this->total = $total;
        $this->per_page = $per_page>0 ? $per_page : 1;
        $this->pattern = $pattern;
        $this->pattern_type = $pattern_type_or_routeid;
        $this->route_params = $route_params;
    
        $this->total_pages = ceil($this->total/$this->per_page);
        if ($page > $this->total_pages) $page = $this->total_pages;
        if ($page<1) $page = 1;
        
        $this->page = $page;        
    }
    
    /**
    * Возвращает страницы пагинатора в виде массива
    */
    function getPages()
    {
        $this->pagelist = [];
        $total_pages = $this->total_pages;
        
        //Если страница < длины пашинатора
        if ($this->page < $this->paginator_len)
        {
            $to = $this->paginator_len;
            if ($total_pages < $this->paginator_len) $to = $total_pages;
            for($n=1; $n <= $to; $n++)
            {
                $class = ($n == $to && $total_pages > $this->paginator_len) ? 'right' : 'page';
                $this->addPage($n, $class);
            }

        } else        
        //Если страница в конце списка страниц
        if ($this->page >= $this->paginator_len && (($total_pages-$this->page) < $this->paginator_len))
        {
            $from = $total_pages-$this->paginator_len;
            if ($from<1) $from=1;
            
            if ($from>1) $this->addPage($from-1, 'left');
            for($n=$from; $n <= $total_pages; $n++)
            {
                $this->addPage($n);
            }
        } else
        //Если страница посредине
        if ($this->page >= $this->paginator_len && (($total_pages-$this->page) >= $this->paginator_len))
        {
            $from = floor($this->page/$this->paginator_len)*$this->paginator_len;
            $this->addPage($from-1, 'left');
            for ($n=$from; $n<$from+$this->paginator_len; $n++)
            {
                $this->addPage($n);
            }
            $class = ($from+$this->paginator_len == $total_pages) ? 'page' : 'right';
            $this->addPage($from+$this->paginator_len, $class);
        }        
        return $this->pagelist;
    }
    
    /**
    * Устанавливает число страниц, одновременно отображаемых в пагинаторе
    * 
    * @param integer $len
    * @return self
    */
    function setPaginatorLen($len)
    {
        $this->paginator_len = $len;
        return $this;
    }
    
    /**
    * Возвращает текущее смещение элементов от 0
    * 
    * @return integer
    */
    function getOffset()
    {
        return ($this->page-1)*$this->per_page;
    }
    
    /**
    * Возвращает просчитанные ранее страницы
    */
    function getPageList()
    {
        if (!isset($this->pagelist)) $this->getPages();
        return $this->pagelist;
    }
    
    
    /**
    * Возвращает true, если необходимо отобразить ссылку на первую страницу
    */
    function showFirst()
    {
        $pages = $this->getPageList();
        return !($pages[0]['n'] == 1);
    }

    /**
    * Возвращает true, если необходимо отобразить ссылку на последнюю страницу
    */    
    function showLast()
    {
        $pages = $this->getPageList();
        foreach ($pages as $page) {
            if ($page['n'] == $this->total_pages) return false;
        }
        return true;
    }
    
    /**
    * Добавляет страницу к пагинатору
    */
    protected function addPage($page, $class = 'page')
    {
        $this->pagelist[] = [
            'n' => $page,
            'act' => ($page==$this->page),
            'class' => $class,
            'href' => $this->getPageHref($page)
        ];
    }
    
    /**
    * Возвращает ссылку на страницу с номером $page
    */
    function getPageHref($page)
    {
        switch($this->pattern_type) {
            case self::PATTERN_KEYREPLACE: {
                if ($page == 1){
                    $href = Request::commonInstance()->replaceKey([$this->pattern => null]);
                }else{
                    $href = Request::commonInstance()->replaceKey([$this->pattern => $page]);
                }
                break;
            }
            
            case self::PATTERN_TEMPLATE: {
                $href = str_replace('%PAGE%', $page, $this->pattern);
                break;
            }
            
            default: {
                $href = Manager::obj()->getUrl($this->pattern_type, $this->route_params + [$this->pattern => ($page != 1) ? $page : null]);
            }
        }
        return $href;
    }
    
    
}

