<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Html\Paginator;

use RS\Html\AbstractHtml;
use RS\View\Engine;

class Element extends AbstractHtml
{

    public $tpl = 'system/admin/html_elements/paginator/paginator.tpl';
    public $page_key = 'p';
    public $pagesize_key = 'perpage';
    public $page_count;
    public $total;
    public $page_size = 20;
    public $url_pattern;
    public $page;
    public $update_container = ''; //Контейнер который надо будет обновить

    
    function __construct($total = null, $urlPattern = null, $options = [])
    {
        parent::__construct($options);
        $this->total = $total;
        $this->url_pattern = $urlPattern;
    }
    
    /**
    * Устанавливает имя переменной, в которой будет содержаться текущая страница
    * 
    * @param string $page_key
    * @return void
    */
    function setPageKey($page_key)
    {
        $this->page_key = $page_key;
    }
    
    /**
    * Устанавливает имя переменной, в которой будет содержаться количество элементов на странице
    * 
    * @param string $pagesize_key
    * @return void
    */
    function setPageSizeKey($pagesize_key)
    {
        $this->pagesize_key = $pagesize_key;
    }
    
    /**
    * Устанавливает размер элементов на странице
    * 
    * @param string $pageSize
    * @return void
    */
    function setPageSize($pageSize)
    {
        $this->page_size = $pageSize;
    }
    
    /**
    * Устанавливает общее число элементов на всех страницах для рассчета количества страниц
    * 
    * @param integer $total
    * @return void
    */
    function setTotal($total)
    {
        $this->total = $total;
    }
    
    /**
    * Устанавливает шаблон отображения пагинатора
    * 
    * @param string $template
    * @return void
    */
    function setTemplate($template)
    {
        $this->tpl = $template;
    }

    /**
     * Возвращает контейнер который надо обновить (Например .updateForm или #updateForm)
     */
    function getUpdateContainer()
    {
        return $this->update_container;
    }


    /**
     * Устанавливает контейнер который надо обновить (Например .updateForm или #updateForm)
     *
     * @param string $container_selector - селектор контейнера
     */
    function setUpdateContainer($container_selector = "")
    {
        $this->update_container = $container_selector;
    }

    
    /**
    * Устанавливает текущую страницу.
    * 
    * @param integer $page - номер страницы, минимально: "1"
    * @return void
    */
    function setPage($page)
    {
        if (isset($this->total) && !empty($this->page_size))
        {
            $maxpage = ceil($this->total/$this->page_size);
            $this->page = ($page > $maxpage) ? $maxpage : $page;
        } else {
            $this->page = $page;
        }
        if ($this->page<1) $this->page = 1;        
    }
       
    /**
    * Возвращает HTML-код пагинатора
    * 
    * @param mixed $local_options
    */
    function getView($local_options = [])
    {        
        $this->page_count = ceil($this->total/$this->page_size);
        if ($this->page_count<1) $this->page_count = 1;
        
        $leftpage = ($this->page == 1) ? 1 : $this->page-1;
        $rightpage = ($this->page >= $this->page_count) ? $this->page_count : $this->page+1;
        
        if (isset($this->url_pattern)) {
            $this->left = str_replace('%PAGE%', $leftpage, $this->url_pattern);
            $this->right = str_replace('%PAGE%', $rightpage, $this->url_pattern);
            $this->perPageUrl = str_replace('%PERPAGE%', $rightpage, $this->url_pattern);
        } else {
            $this->left = $this->url->replaceKey([$this->page_key => $leftpage]);
            $this->right = $this->url->replaceKey([$this->page_key => $rightpage]);
            $this->perPageUrl = str_replace('%PERPAGE%', $rightpage, $this->url_pattern);
        }        
        
        $view = new Engine();
        $view->assign('paginator', $this);
        $view->assign('local_options', $local_options);
        
        return $view->fetch($this->tpl);
    }
    
}
