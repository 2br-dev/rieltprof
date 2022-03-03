<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Html\Paginator;

use RS\Application\Application;
use RS\Exception;
use RS\html\AbstractHtml;
use RS\View\Engine;

/**
* Класс управления элементом пагинации
*/
class Control extends AbstractHtml
{
    public
        $pagesize_var = 'perpage',
        $page_var = 'p',
        $action,
        $element,
        $auto_fill = true,
        $hidden_fields = [];

    /**
     * Control constructor.
     * @param array $options
     * @throws Exception
     */
    function __construct($options = [])
    {        
        parent::__construct($options);
        if ($this->auto_fill) $this->fill();
    }
    
    /**
    * Устанавливает, заполнять ли элемент пагинатора значениями из GET при создании текущего объекта
    * Необходимо использовать через параметр в конструкторе
    * 
    * @param bool $autofill
    * @return $this
    */
    function setAutoFill($autofill)
    {
        $this->auto_fill = $autofill;
        return $this;
    }
        
    /**
    * Устанавливает подконтрольный элемент пагинатора
    * 
    * @param Element $paginator - пагинатор
    * @return $this
    */
    function setPaginator(Element $paginator)
    {
        $this->element = $paginator;
        return $this;
    }
    
    /**
    * Возвращает подконтрольный элемент пагинатора
    * 
    * @return Element
    */
    function getPaginator()
    {
        return $this->element;
    }
    
    /**
    * Добавляет параметры, которые будут добавлены в GET запрос, вместе с переключением страницы
    * 
    * @param array $hidden_fields
    * @return Control
    */
    function addHiddenFields(array $hidden_fields)
    {
        $this->hidden_fields += $hidden_fields;
        return $this;
    }
    
    /**
    * Возвращает номер текущей страницы пагинатора, начиная с 1
    * 
    * @return integer
    */
    function getPage()
    {
        return $this->element->page;
    }
    
    /**
    * Возвращает количество элементов на странице
    * 
    * @return integer
    */
    function getPageSize()
    {
        return $this->element->page_size;
    }

    /**
     * Заполняет пагинатор значениями из GET
     *
     * @return $this
     * @throws Exception
     */
    function fill()
    {
        $this->action = $_SERVER['REQUEST_URI'];
        $pageSize = $this->url->request( $this->pagesize_var, TYPE_INTEGER, false );
        $page = $this->url->request($this->page_var, TYPE_INTEGER, 1);
        
        if ($pageSize > 0) $this->element->setPageSize($pageSize);
        $this->element->setPage( $page );
        
        //Устанавливаем в cookie размер страниц
        Application::getInstance()->headers
            ->addCookie($this->pagesize_var, $pageSize, time()+3600*700);
        
        $this->saveGetParams();
        return $this;
    }

    /**
     * Добавляет текущие параметры из GET в форму пагинатора
     *
     * @return $this
     * @throws Exception
     */
    function saveGetParams()
    {
        foreach ($this->url->getSource(GET) as $key => $val) {
            if ($key != $this->pagesize_var && $key != $this->page_var) {
                if (is_array($val)) {
                    foreach($val as $k=>$v)
                        $this->hidden_fields["{$key}[{$k}]"] = $v;
                } else {
                    $this->hidden_fields[$key] = $val;
                }
            }
        }
        return $this;
    }

    /**
     * Возвращает HTML код формы пагинатора
     *
     * @param mixed $local_options - массив параметров, передаваемый в шаблон пагинатора
     * Поддерживаются ключи:
     * array(
     *   'short' => true //Не будет выводится надпись с общим количеством элементов
     * )
     * @return string
     * @throws \Exception
     * @throws \SmartyException
     */
    function getView($local_options = [])
    {
        $view = new Engine();
        $view->assign('pcontrol', $this);
        $view->assign('local_options', $local_options);
        
        return $view->fetch('%system%/admin/html_elements/paginator/control.tpl');
    }
}

