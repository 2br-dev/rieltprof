<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Application;

/**
* Класс, отвечающий за мета теги в head части страницы
*/
class Meta
{
    protected
        $meta_vars = [
        				'Content-type' => ['http-equiv' => 'Content-type', 'content' => 'text/html; Charset=utf-8'],
                        'keywords' => ['name' => 'keywords', 'content' => ''],
                        'description' => ['name' => 'description', 'content' => '']
    ];
    
    /**
    * Очищает значения мета-тегов
    * 
    * @param string $key - идентификатор мета-тега
    * @return Meta
    */
    function cleanMeta($key)
    {
        if (isset($this->meta_vars[$key]['content'])) {
            $this->meta_vars[$key]['content'] = '';
        } else {
            unset($this->meta_vars[$key]);
        }
        return $this;
    }
    
    /**
    * Добавить произвольные ключевые слова в meta keywords
    * 
    * @param string $value теги
    * @param string $sep разделитель, если уже присутствуют другие теги
    * @param string $pos указание, куда в начало или в конец добавлять теги
    * @return Meta
    */
    function addKeywords($value, $sep = ',',$pos = 'before')
    {
        if ($value != '' && $pos =='before') {
            if (!empty($this->meta_vars['keywords']['content'])) $value = $value.$sep;
            $this->meta_vars['keywords']['content'] = $value.$this->meta_vars['keywords']['content'];
        } elseif ($value != '' && $pos =='after'){
            if (!empty($this->meta_vars['keywords']['content'])) $value = $sep.$value;
            $this->meta_vars['keywords']['content'] = $this->meta_vars['keywords']['content'].$value;
        }

        return $this;
    }
    
    /**
    * Добавить описание страницы в meta description
    * 
    * @param string $value описание
    * @param string $sep разделитель, если уже присутствует другое описание
    * @param string $pos указание, куда в начало или в конец добавлять теги
    * @return Meta
    */
    function addDescriptions($value, $sep = ',',$pos = 'before')
    {
        if ($value != ''&& $pos =='before') {
            if (!empty($this->meta_vars['description']['content'])) $value = $value.$sep;
            $this->meta_vars['description']['content'] = $value.$this->meta_vars['description']['content'];
        } elseif ($value != '' && $pos =='after') {
            if (!empty($this->meta_vars['description']['content'])) $value = $sep.$value;
            $this->meta_vars['description']['content'] = $this->meta_vars['description']['content'].$value;
        }
        return $this;
    }
    
    /**
    * Добавить произвольный meta тег
    * 
    * @param array $tagparam - массив с аттрибутами
    * @param mixed $key - идентификатор мета-тега
    * @return Meta
    */
    function add(array $tagparam, $key = null)
    {
        $is_unshift = !empty($tagparam['unshift']);
        unset($tagparam['unshift']);
        
        $meta = ($key === null) ? [$tagparam] : [$key => $tagparam];
        if ($is_unshift) {
            $this->meta_vars = array_merge($meta, $this->meta_vars);
        } else {
            $this->meta_vars = array_merge($this->meta_vars, $meta);
        }
        
        return $this;
    }
    
    /**
    * Возвращает HTML код блока мета тегов
    * @return string
    */
    function get()
    {
        $view = new \RS\View\Engine();
        $view->assign('meta_vars', $this->meta_vars);
        return $view->fetch('%system%/meta.tpl');
    }
    
    /**
    * Возвращает мета данные по ключу, или весь массив
    * @param string $key - ключ
    * @return array
    */
    function getData($key = null)
    {
        $view = new \RS\View\Engine();
        return ($key) ? $this->meta_vars[$key] : $this->meta_vars;
    }

    /**
     * Возвращает установленные в текущий момент мета-теги
     *
     * @param null $key - Ключ мета-тега, если null, то будет возвращен весь массив с мета-тегами
     * @param null $default - значение по-умолчанию на случай, если мета-тега с ключем $key не существует
     * @return mixed
     */
    function getMetaVars($key = null, $default = null)
    {
        if ($key !== null) {
            return isset($this->meta_vars[$key]) ? $this->meta_vars[$key] : $default;
        } else {
            return $this->meta_vars;
        }
    }
}

