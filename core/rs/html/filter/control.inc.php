<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Html\Filter;

use RS\Html\AbstractHtml;
use RS\Html\Filter\Type\AbstractType;
use RS\Orm\Request;
use RS\View\Engine;

/**
* Класс управления фильтром. Класс умеет заполнять формы контейнера данными,
* получать итоговый SQL запрос для применения фильтров, возвращать визуальные частицы установленных фильтров.
*/
class Control extends AbstractHtml
{
    public $uniq;
    public $auto_fill = true;
    public $filter_var = 'f'; //GET-параметр, содержащий сведения об установленных фильтрах


    protected $tpl = 'system/admin/html_elements/filter/control.tpl';
    protected $parts_tpl = 'system/admin/html_elements/filter/parts.tpl';
    /** @var Container */
    protected $container;
    protected $caption = ''; //Название кнопки фильтра
    protected $attr_class = ''; //Аттрибут класса
    protected $update_container = ''; //Контейнер который надо будет обновить
    protected $exclude_get_params = ['p'];
    protected $before_sql_where_callback;
    protected $add_param = [];
    
    protected static $inc = 0; //Уникальный порядковый номер экземпляра класса
        
    function __construct(array $options = [])
    {
        $this->uniq = 'filter'.self::getNextInc();
        parent::__construct($options);
    }
    
    /**
    * Устанавливает массив ключей GET переменных, которые не должны присутствовать в форме фильтра
    * в виде hidden полей
    * 
    * @param array $keys массив ключей GET переменных
    * @return Control
    */
    function setExcludeGetParams(array $keys)
    {
        $this->exclude_get_params = $keys;
        return $this;
    }
    
    /**
    * Возвращаем массив ключей GET параметров, которые не должны присутствовать в форме фильтра
    * в виде hidden полей
    * 
    * @return array
    */
    function getExcludeGetParams()
    {
        return $this->exclude_get_params;
    }
    
    /**
    * Устанавливает GET переменную, в которой будут находиться сведения о примененных фильтрах
    * 
    * @param string $var
    * @return Control
    */
    function setFilterVar($var)
    {
        $this->filter_var = $var;
        return $this;
    }
    
    /**
    * Возвращает имя GET переменной, в которой будут находиться сведения о примененных фильтра
    * 
    * @return string
    */
    function getFilterVar()
    {
        return $this->filter_var;
    }

    function setAutoFill($autofill)
    {
        $this->auto_fill = $autofill;
        return $this;
    }    
    
    /**
    * Устанавливает опции для всех форм контейнеров
    * 
    * @return Control
    */
    function setToAllItems($options) 
    {
        $items = $this->container->getAllItems(); //Получаем полный список элементов формы
        foreach($items as $item) {
            foreach($options as $opt_key => $opt_val) {
                $method = 'set'.$opt_key;
                if (is_callable([$item, $method])) {
                    $item->$method($opt_val);
                }
            }
        }
        return $this;
    }
    
    /**
    * Устанавливает название поиска
    * 
    * @param string $text
    * @return Control
    */
    function setCaption($text)
    {
        $this->caption = $text;
        return $this;
    }
    
    /**
    * Возвращает подпись для поиска
    * 
    * @return string
    */
    function getCaption()
    {
        return $this->caption ?: t('Поиск');
    }

    /**
     * Добавляет дополнительный класс
     * @param string $class
     */
    function setAddClass($class = '')
    {
        $this->attr_class .= " " . $class;
    }

    /**
     * Вовзращает дополнительные классы
     *
     * @return string
     */
    function getAddClass()
    {
        return $this->attr_class;
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
    * Добавляет произвольные дополнительные параметры
    * 
    * @param array $params
    * @return Control
    */
    function setAddParam(array $params)
    {
        $this->add_param += $params;
        return $this;
    }
    
    /**
    * Возвращает произвольные дополнительные параметры
    * 
    * @param string $key
    * @return mixed
    */
    function getAddParam($key = null) 
    {
        return isset($key) ? $this->add_param[$key] : $this->add_param;
    }
    
    /**
    * Возвращает объект элемента формы по имени формы
    * 
    * @param string $key
    * @return Type\AbstractType | null
    */
    function getItemByKey($key)
    {
        $items = $this->container->getAllItems();
        foreach ($items as $item){
            if ($item->getKey() == $key) return $item;
        }
    }
    
    /**
    * Возвращает URL текущей страницы со сброшенными фильтрами
    * 
    * @return string
    */
    function getCleanFilterUrl()
    {
        $exclude_keys = $this->getExcludeGetParams();
        $exclude = array_combine($exclude_keys, array_fill(0, count($exclude_keys), null));
                
        return \RS\Http\Request::commonInstance()->replaceKey([$this->getFilterVar() => null] + $exclude );
    }
    
    /**
    * Возвращает - какие элементы были закрыты. берет информацию из cookie
    * 
    * @return array
    */
    function getElementsStatus()
    {
        if (!isset($_COOKIE[$this->uniq])) return [];
        
        $estatus = [];
        $cookie = explode(',', $_COOKIE[$this->uniq]);
        foreach($cookie as $str) {
            $keyval = explode('=', $str);
            if (count($keyval) == 2) 
                $estatus[$keyval[0]] = (int)$keyval[1];
        }
        return $estatus;
    }    
    
    /**
    * Возвращает следующий уникальный идентификатор экземпляра класса
    * 
    * @return integer
    */
    protected static function getNextInc()
    {
        self::$inc++;
        return self::$inc;
    }
    
    /**
    * Устанавливает контейнер, над которым устанавливается управление
    * 
    * @param Container $container
    * @return Control
    */
    function setContainer(Container $container)
    {
        $this->container = $container;
        return $this;
    }
    
    /**
    * Возвращает контейнер, которым управляет текущий экземпляр класса
    * 
    * @return Container
    */
    function getContainer()
    {
        return $this->container;
    }
    
    /**
    * Возвращает HTML код контейнера
    * 
    * @return string
    */
    function getContainerView()
    {
        return $this->container->getView();
    }    
    
    /**
    * Заполняет формы текущего фильтра значениями, если они присутствуют в GET параметрах
    *
    * @param array $vars Значения фильтров, который необходимо применить
    * @return Control
    */
    function fill($vars = null)
    {
        if ($vars === null) {
            $vars = $this->url->get($this->filter_var, TYPE_ARRAY);
        }
        $items = $this->container->getAllItems();
        
        //Заполняем формы значениями
        $issetfilter = false;
        foreach ($items as $item) {
            $item->wrap_var = $this->filter_var;
            if ( isset($vars[$item->getKey()]) ) $item->setValue($vars[$item->getKey()]);
        }
        
        //Определяем активен ли сейчас поиск (заполнена ли хотя бы одна форма)
        $values = $this->getKeyVal(false);
        $this->container->work = !empty($values);
        
        //Устанавливам ссылку на очищение всех фильтров.
        $get = $this->url->getSource(GET);
        if (isset($get[$this->filter_var])) unset($get[$this->filter_var]);
        $pu = parse_url($_SERVER['REQUEST_URI']);
        $this->container->clearlink =  $pu['path']. (empty($get) ? '' : '?'.http_build_query($get));

        //Раскрываем контейнеры, которые должны быть открыты по данным из cookie        
        $statuses = $this->getElementsStatus();
        foreach($this->getAllContainers() as $container) {
            if (isset($statuses[$container->uniq])) $container->open = $statuses[$container->uniq];
            //in_array($container->uniq, $opened)) $container->open = true;
        }
        $this->saveGetParams();
        return $this;
    }
    
    /**
    * Сохраняет текущие GET параметры. Они будут добавлены к URL при применении фильтра
    * 
    * @return Control
    */
    function saveGetParams()
    {
        $exclude = $this->getExcludeGetParams();
        foreach ($this->url->getSource(GET) as $key => $val) {
            if ($key != $this->filter_var && !in_array($key, $exclude)) {
                if (is_array($val)) {
                    foreach($val as $k=>$v)
                        $this->add_param['hiddenfields']["{$key}[{$k}]"] = $v;
                } else {
                    $this->add_param['hiddenfields'][$key] = $val;
                }
            }
        }
        return $this;
    }
    
    /**
    * Возвращает основной контейнер и вложенные в него в виде массива.
    * 
    * @return Container[]
    */
    function getAllContainers()
    {
        return array_merge([$this->container], $this->container->getSecContainers());
    }
    
    /**
    * Возвращает установленные фильтры в виде ассоциативного массива
    * 
    * @return array
    */
    function getKeyVal($with_prefilters = true)
    {
        $keyval = [];
        $items = $this->container->getAllItems($with_prefilters);
        foreach ($items as $item) {
            $keyval += $item->getKeyVal();
        }
        return $keyval;
    }
    
    /**
    * Возвращает части установленного фильтра
    * 
    * @return array
    */
    function getParts()
    {
        $parts = [];
        $current_filter_values = $this->url->get($this->filter_var, TYPE_ARRAY);
        $exclude_keys = $this->getExcludeGetParams();

        $items = $this->container->getAllItems(false);
        foreach ($items as $item) {
            $parts = array_merge($parts, $item->getParts($current_filter_values, $exclude_keys));
        }
        return $parts;
    }
    
    /**
    * Возвращает готовую SQL строку для подстановки в условие where sql запроса
    * 
    * @return string
    */
    function getSqlWhere()
    {
        $sql_where = [];
        $items = $this->container->getAllItems(false);
        
        $exclude_items = [];
        if ($this->before_sql_where_callback) {
            //Обработчик может вернуть элементы, которые необходимо исключить из 
            //естественной генерации sql условий для фильтрации
            $exclude_items = call_user_func($this->before_sql_where_callback, $items, $this);
        }
        
        foreach ($items as $item) {
            if (!is_array($exclude_items) || !in_array($item->getKey(), $exclude_items)) {
                $item_where = $item->getWhere();
                if (!empty($item_where)) $sql_where[] = $item_where;
            }
        }

        return implode(' AND ', $sql_where);
    }
    
    /**
    * Применяется для более сложной трансформации запросов
    * 
    * @param Request $q исходный объект запроса
    * @return Request исходный объект запроса с установленными фильтрами
    */
    function modificateQuery(Request $q)
    {
        $items = $this->container->getAllItems(false);

        foreach ($items as $item) {
            /**
             * @var AbstractType $item
             */
            $q = $item->modificateQuery($q);
        }        
        return $q;
    }
    
    /**
    * Возвращает HTML код формы с фильтрами
    * 
    * @return string
    */
    function getView()
    {
        $tpl = new Engine();
        $tpl->assign('fcontrol', $this);
        return $tpl->fetch($this->tpl);
    }
    
    /**
    * Устанавливает обработчик, который будет выполняться перед выполнением метода getSqlWhere
    * 
    * @param callback $callback - callback для вызова 
    * @return Control
    */
    function setBeforeSqlWhere($callback)
    {
        $this->before_sql_where_callback = $callback;
        return $this;
    }

    /**
     * Возвращает HTML с установленными в настоящее время фильтрами
     * @return string
     */
    function getPartsHtml()
    {
        $tpl = new Engine();
        $tpl->assign('fcontrol', $this);
        return $tpl->fetch($this->parts_tpl);
    }
}

