<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Debug\Action;

/**
* Абстрактный класс объектов одного действия контекстного меню в режиме отладки
*/
class AbstractAction
{
    protected
        $attr = [],
        $href,
        $title;
    
    /**
    * Конструктор объектов действия контекстного меню в режиме отладки
    * 
    * @param string $href - ссылка действия
    * @param string $title - название действия
    * @param array $attr - ассоциативный массив атрибутов HTML элемента контекстного меню
    * @return AbstractAction
    */
    function __construct($href, $title, $attr = [])
    {
        $this->href = $href;
        $this->title = $title;
        if (!empty($attr)) {
            $this->attr = array_replace_recursive($this->attr, $attr);
        }
    }
    
    /**
    * Возвращает ссылку с подставленными значениями
    * 
    * @param string $href_pattern - шаблон ссылки
    * @param mixed $data - массив или объект с interface ArrayAccess, с параметрами объекта действия
    * @return string
    */
    protected function getHref($href_pattern = null, $data)
    {
        $this->data = $data;        
        $href = $href_pattern;
        if (strpos($href, '{') !== false) {
            $href = preg_replace_callback('/({([^&\/]+)})/', [$this, 'replaceCallback'], $href);
        }
        unset($this->data); //Удаляем ненужную ссылку на объект
        return $href;        
    }    

    /**
    * Подставляет значения из data, callback для preg_replace_callback
    * 
    * @param array $matches
    * @return string
    */
    protected function replaceCallback($matches)
    {
        return $this->data[$matches[2]];
    }    
    
    /**
    * Возвращает массив с параметрами пункта контекстного меню
    * @param mixed $data - массив или объект с interface ArrayAccess, с параметрами объекта действия
    * @return array
    */
    function getData($data)
    {
        $attr = $this->attr;
        $attr['@href'] = $this->href;
        
        foreach($attr as $key=>$val) {
            if ($key[0] == '@') {
                $val = $this->getHref($val, $data);
                unset($attr[$key]);
                $attr[substr($key, 1)] = $val;
            }
        }
        
        return ['title' => $this->title, 'attributes' => $attr];
    }
    
    
}

