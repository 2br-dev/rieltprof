<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Router;

/**
* Стандартный маршрут. Позволяет использовать упрощенный синтаксис правил.
*/
class Route extends RouteAbstract
{
    protected
        $cache = [],
        $keys_in_mask = [],
        $masks;
    
    protected static
        $cache_site_folder;
    
    /**
    * Конструктор абстрактного класса для маршрутов (тип: маска в формате ReadyScript)
    * 
    * Допустимые варианты спользования маски ReadyScript:
    * /product/{alias}/   - запишет вторую секцию в переменную alias
    * /product/{alias:[\d]+}/  - маршрут сработает, только если alias будет числом
    * Чтобы маршрут сработал, должно произойти соответствие по левой части маски.
    * Например: 
    * для url: http://domain.ru/product/phone-360gs/comments/
    * /product/ - сработает маршрут
    * /comments/ - НЕ сработает маршрут
    * 
    * @param string $id - идентификатор URI
    * @param string |array $masks - Маска для URI в формате ReadyScript.
    * @param array | null $defaults - значения по умолчанию для переменных из URI
    * @param string $description - Текстовое описание страницы по данному URI
    * @param boolean hide - Скрывать из списков в админ. панели
    * @param string $wrap_pattern - Обрамляющий шаблон для регулярного выражения
    */
    function __construct($id, $masks, $defaults, $description, $hide = false, $wrap_pattern = '^{pattern}$')
    {
        $this->masks = (array)$masks;
        $keys_in_mask = [];
        $patterns = [];
        
        if (!isset($defaults[self::CONTROLLER_PARAM])) {
            //Если отсутствует параметр self::CONTROLLER_PARAM, 
            //то считаем что в id задано сокращенное имя контроллера
            $defaults[self::CONTROLLER_PARAM] = $id;
        }
        
        foreach($this->masks as $key => $mask) {
            //Конвертируем маску в регулярное выражение
            $keys_in_mask[$key] = [];
            $patterns[$key] = preg_replace_callback('/(\{(.*?)(\:(.*?))?\})/', function($match) use(&$keys_in_mask, $key) {
                $mask = isset($match[4]) ? $match[4] : '[^\?]*?';
                $keys_in_mask[$key][$match[2]] = $mask;
                $replace = "(?P<{$match[2]}>{$mask})";
                return $replace;
            }, $mask);
            
            $use = str_replace('/', '\\/', $patterns[$key]);
            $patterns[$key] = str_replace('{pattern}', $use, $wrap_pattern);
        }
        $this->keys_in_mask = $keys_in_mask;
        parent::__construct($id, $patterns, $defaults, $description, $hide);
    }
    
    
    /**
    * Возвращает Uri с нужными параметрами
    * 
    * @param array $params параметры для uri
    * @param bool $absolute если true, то вернет абсолютный путь
    * @param mixed $mask_key индекс маски по которой будет строиться url, если не задан, то будет определен автоматически
    */
    public function buildUrl($params = [], $absolute=false, $mask_key = null)
    {
        if ($mask_key === null) {
            $mask_key = $this->findKey($params);
        }

        $mask = $this->masks[$mask_key];
        //Приписываем папку, если таковая задана у сайта
        if (!$this->isAdmin()) { //Не дополняем пути в администраторской панели
            if (self::$cache_site_folder === null) {
                $site = \RS\Site\Manager::getSite();
                self::$cache_site_folder = ($site['folder'] != '') ? '/'.trim($site['folder'], '/') : '';
            }
            $mask = self::$cache_site_folder.$mask;
        }
        
        //Приписываем папку, в которой установлена система
        $folder = rtrim(\Setup::$FOLDER, '/');
        if ($folder !== '') {
            $mask = $folder.$mask;
        }       
        
        $uri = preg_replace_callback('/(\{(.*?)(\:(.*?))?\})/', function($match) use($params) {
            return isset($params[$match[2]]) ? rawurlencode($params[$match[2]]) : '';
        }, $mask);
        
        $other = array_diff_key($params, $this->keys_in_mask[$mask_key]); 
        if (count($other) && $query_params = http_build_query($other)) {
            $uri .= '?'.$query_params;
        }
        if ($absolute) {
            $current_site = \RS\Site\Manager::getSite();
            $uri = $current_site ? $current_site->getAbsoluteUrl($uri) : \RS\Http\Request::commonInstance()->getSelfAbsoluteHost().$uri;
        }        
        return $uri;
    }
    
    /**
    * Возвращает ключ наиболее подходящего Uri маршрута для построения ссылки
    * 
    * @param array $params
    */
    protected function findKey($params)
    {
        //Будет использован тот маршрут, в котором наибольшее число переменных встречается в маске. 
        //При равных показателях у которого длинна меньше.            
        $key_hash = implode('.',array_keys($params));
        if (!isset($this->cache[$key_hash])) {
            if (count($this->masks) > 1) {
                $max = null;
                foreach($this->keys_in_mask as $index => $keys) {
                    $rating = count(array_intersect_key($keys, $params)).(10000-strlen($this->masks[$index]));
                    if ($max === null || $rating > $max) {
                        $max = $rating;
                        $mask_key = $index;
                    }
                }
            } else {
                reset($this->masks);
                $mask_key = key($this->masks);
            }
            $this->cache[$key_hash] = $mask_key;
        } else {
            $mask_key = $this->cache[$key_hash];
        }
        return $mask_key;
    }
    
    /**
    * Возвращает регулярные выражения, заданные для маршрута в читаемом виде
    * @return array
    */    
    public function getPatternsView()
    {
        return $this->masks;
    }

}

