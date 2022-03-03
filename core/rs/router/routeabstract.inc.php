<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Router;
use \RS\Http;

abstract class RouteAbstract
{
    const
        CONTROLLER_PARAM = 'controller', //GET параметр, который задает имя контроллера
        ACTION_PARAM = 'Act', //GET параметр, который задает имя действия контроллера
        DEFAULT_ACTION = 'index'; //Действие контроллера по-умолчанию
    
    public
        $match;
        
    protected
        $id,
        $patterns = [],
        $defaults = [],
        $is_admin = false,
        $hide = false,
        $description,
        $extra = [];
    
    protected static
        $http_request;
    
    /**
    * Конструктор абстрактного класса для маршрутов (тип: регулярные выражения)
    * 
    * @param string $id - идентификатор URI
    * @param string |array $patterns - Регулярное выражение для URI
    * @param array | null $defaults - значения по умолчанию для переменных из URI
    * @param string $description - Текстовое описание страницы по данному URI
    * @param bool $hide - скрывать маршрут в списках в административной панели. Рекомендуется для системных маршрутов.
    */
    function __construct($id, $patterns, $defaults, $description, $hide = false)
    {
        $this->id = $id;
        foreach((array)$patterns as $key=>$pattern) {
            $this->patterns[$key] = '/'.$pattern.'/i';
        }
        if ($defaults !== null) {
            $this->defaults = $defaults;
        }
        $this->description = $description;
        if (!self::$http_request) {
            self::$http_request = Http\Request::commonInstance();
        }
        $this->hide = $hide;
    }
    
    /**
    * Возвращает идентификатор маршрута
    */
    function getId()
    {
        return $this->id;
    }
    
    /**
    * Возвращает true в случае если маршрут соответствует текущему URL
    * 
    * @param string $host - хост
    * @param string $uri - REQUEST_URI
    * @param boolean $autoset - устанавливать автоматически, параметры из GET запроса 
    *
    * @return bool
    */
    function match($host, $uri, $autoset = true)
    {
        //Исключаем каталог, в которой установлена система
        $folder = str_replace('/','\\/', \Setup::$FOLDER);
        $uri = preg_replace('/^'.$folder.'/', '', $uri);
        
        foreach($this->patterns as $pattern) {
            if (preg_match($pattern, $uri, $match)) {
                foreach($match as $key => $value) {
                    if (is_numeric($key)) {
                        unset($match[$key]);
                    }
                }
                $this->match = $match;
                if ($autoset) {
                    self::$http_request->setFromRouter($this->match, $this->defaults);
                }
                return true;
            }
        }
        return false;
    }
    
    /**
    * Возвращает объект URL
    * 
    * @return \RS\Http\Request
    */
    function getHttpRequest()
    {
        return self::$http_request;
    }
    
    /**
    * Возвращает имя класса контроллера, который соответствует данному URI
    */
    function getController()
    {
        $minifed_str = $this->getHttpRequest()->get(self::CONTROLLER_PARAM, TYPE_STRING);
        //Генерируем имя класса контроллера
        if (preg_match('/^([^\-]+?)\-(.*)$/', $minifed_str, $match)) {
            return str_replace('-','\\', "-{$match[1]}-controller-{$match[2]}");
        }
    }
    
    /**
    * Возвращает имя метода контроллера, который соответствует данному URI
    */
    function getAction()
    {
        return $this->getHttpRequest()->get(self::ACTION_PARAM, TYPE_STRING, self::DEFAULT_ACTION);
    }
    
    /**
    * Возвращает описание страницы для данного маршрута
    */
    function getDescription()
    {
        return $this->description;
    }
    
    /**
    * Возвращает построенный URL по данному маршруту
    * 
    * @param array $params - параметры запроса
    * @param boolean $absolute - абсолютный или относительный путь строить
    * @return string
    */
    function buildUrl($params, $absolute = false)
    {
        $uri = '?'.http_build_query($params);
        
        if ($absolute) {
            $current_site = \RS\Site\Manager::getSite();
            $uri = $current_site ? $current_site->getAbsoluteUrl($uri) : \RS\Http\Request::commonInstance()->getSelfAbsoluteHost().$uri;
        }        
        return $uri;
    }
    
    /**
    * Возвращает регулярные выражения, заданные для маршрута
    * @return array
    */
    function getPatterns()
    {
        return $this->patterns;
    }
    
    /**
    * Возвращает регулярные выражения, заданные для маршрута в читаемом виде
    * @return array
    */
    function getPatternsView()
    {
        return $this->patterns;
    }    
    
    /**
    * Возвращает true, если данный url принадлежит административной панели 
    * 
    * @param mixed $bool
    * @return boolean
    */
    function isAdmin($bool = null)
    {
        if ($bool !== null) $this->is_admin = $bool;
        return $this->is_admin;
    }    
    
    /**
    * Возвращает true, если этот маршрут - заглушка, иначе - false
    */
    function isUnknown()
    {
        return false;
    }
    
    /**
    * Возвращает true, если маршрут скрытый
    */
    function isHidden()
    {
        return $this->hide;
    }
    
    /**
    * Добавляет произвольные данные в секцию extra
    * 
    * @param string $key - ключ
    * @param mixed $value - значение
    */
    function addExtra($key, $value)
    {
        $this->extra[$key] = $value;
    }
    
    /**
    * Возвращает данные из секции extra по ключу
    * 
    * @param string $key - ключ
    * @param mixed $default - значение по умолчанию
    * @return mixed
    */
    function getExtra($key, $default = null)
    {
        return (isset($this->extra[$key])) ? $this->extra[$key] : $default;
    }
}
