<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Http;

use RS\Application\Application;
use RS\Exception as RSException;
use RS\Helper\IdnaConvert;
use RS\Helper\Tools as HelperTools;
use RS\Router\Manager as RouterManager;

/**
 * Класс содержит методы по отдаче значений переменных из глобальных массивов.
 */
class Request
{
    const CSRF_INPUT_NAME = 'csrf_protection'; //имя поля, в котором будет приходить код защиты вместе с POST запросом

    private static $instance;

    private $get;
    private $post;
    private $request;
    private $cookie;
    private $server;
    private $files;
    private $stream_input;
    private $parsed_url;
    private $router_defaults;
    private $session_save_url_var = 'SAVEDURL_';

    public $to_entity = true;

    function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->request = $_GET + $_POST + $_COOKIE;
        $this->cookie = $_COOKIE;
        $this->files = $_FILES;
        $this->stream_input = file_get_contents('php://input');
        $this->server = $_SERVER;
    }

    /**
     * Возвращает инстанс текущего класса с параметрами текущего запроса
     *
     * @return static
     */
    public static function commonInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Добавляет список ключ => значение в один из массивов GET, POST, COOKIE, SERVER (не изменяет суперглобальные массивы)
     *
     * @param array $array - массив со значениями
     * @param mixed $storage - константа GET, POST, COOKIE, SERVER
     * @return void
     */
    public function addFromArray(array $array, $storage)
    {
        foreach ($array as $key => $value) {
            $this->set($key, $value, $storage);
        }
    }

    /**
     * Устанавливает в локальные копии суперглобальных массивов значения
     *
     * @param mixed $key - ключ
     * @param mixed $value - значение
     * @param mixed $storage - тип хранилища GET, POST, COOKIE, SERVER
     * @return void
     */
    public function set($key, $value, $storage)
    {
        switch ($storage) {
            case POST:
                $this->post[$key] = $value;
                break;
            case GET:
                $this->get[$key] = $value;
                break;
            case COOKIE:
                $this->cookie[$key] = $value;
                break;
            case SERVER:
                $this->server[$key] = $value;
                break;
        }
        if ($storage != SERVER) {
            $this->request[$key] = $value;
        }
    }

    /**
     * Удаляет запись из копии суперглобального массива
     *
     * @param mixed $key - ключ
     * @param mixed $storage - тип хранилища
     * @return void
     */
    public function remove($key, $storage)
    {
        switch ($storage) {
            case POST:
                unset($this->post[$key]);
                break;
            case GET:
                unset($this->get[$key]);
                break;
            case COOKIE:
                unset($this->cookie[$key]);
                break;
        }
        unset($this->request[$key]);
    }

    /**
     * Парсит текущий URL и возвращает его частицу или весь массив частиц
     *
     * @param string | null $key ключ scheme, host, port, user, pass, path, query, fragment
     * @return array | string
     */
    public function getParsedUrl($key = null)
    {
        if ($this->parsed_url === null) {
            $this->parsed_url = parse_url($this->getSelfAbsoluteHost() . $this->server('REQUEST_URI'));
        }
        return $key === null ? $this->parsed_url : $this->parsed_url[$key];
    }

    /**
     * Возвращает текущий протокол HTTP или HTTPS
     *
     * @return string
     */
    public function getProtocol()
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off')
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
        || (isset($_SERVER['HTTP_X_REQUEST_SCHEME']) && $_SERVER['HTTP_X_REQUEST_SCHEME'] == 'https')
        || (isset($_SERVER['HTTP_CF_VISITOR']) && ($visitor = json_decode($_SERVER['HTTP_CF_VISITOR'])) && $visitor->scheme == 'https')
            ? 'https' : 'http';
        return $protocol;
    }

    /**
     * Возвращает строку вида http://текущий домен
     *
     * @return string
     */
    public function getSelfAbsoluteHost()
    {
        return $this->getProtocol() . '://' . $this->server('HTTP_HOST');
    }

    /**
     * Возвращает абсолютный URL текущей страницы
     *
     * @return string
     */
    public function getSelfUrl()
    {
        return $this->getSelfAbsoluteHost() . $this->server('REQUEST_URI');
    }

    /**
     * Возвращает содержимое потока php://input (необработанные данные из тела запроса)
     *
     * @return string|false
     */
    public function getStreamInput()
    {
        return $this->stream_input;
    }

    /**
     * Возвращает значение из суперглобального массива $_GET.
     *
     * @param string $key - имя ключа
     * @param string $type - тип ожидаемых данных.
     *   Используйте константы: TYPE_STRING, TYPE_INTEGER, TYPE_ARRAY, TYPE_BOOLEAN, TYPE_FLOAT
     * @param mixed $default - значение по-умолчанию, которое возвращается в случае, если параметра с заданным ключем не существует
     * @param mixed $strip - Какие теги не вырезать? Если false, то теги не будут вырезаться, иначе можно указать теги, которые нужно оставить.
     * Например: '<br><p>'
     * @return mixed
     */
    public function get($key, $type, $default = null, $strip = "")
    {
        return $this->_returner($this->get, $key, $type, $default, $strip);
    }

    /**
     * Возвращает значение из суперглобального массива $_POST.
     *
     * @param string $key - имя ключа
     * @param string $type - тип ожидаемых данных.
     *   Используйте константы: TYPE_STRING, TYPE_INTEGER, TYPE_ARRAY, TYPE_BOOLEAN, TYPE_FLOAT
     * @param mixed $default - значение по-умолчанию, которое возвращается в случае, если параметра с заданным ключем не существует
     * @param mixed $strip - Какие теги не вырезать? Если false, то теги не будут вырезаться, иначе можно указать теги, которые нужно оставить.
     * Например: '<br><p>'
     * @return mixed
     */
    public function post($key, $type, $default = null, $strip = "")
    {
        return $this->_returner($this->post, $key, $type, $default, $strip);
    }

    /**
     * Возвращает значение из суперглобального массива $_REQUEST.
     *
     * @param string $key - имя ключа
     * @param string $type - тип ожидаемых данных.
     *   Используйте константы: TYPE_STRING, TYPE_INTEGER, TYPE_ARRAY, TYPE_BOOLEAN, TYPE_FLOAT
     * @param mixed $default - значение по-умолчанию, которое возвращается в случае, если параметра с заданным ключем не существует
     * @param mixed $strip - Какие теги не вырезать? Если false, то теги не будут вырезаться, иначе можно указать теги, которые нужно оставить.
     * Например: '<br><p>'
     * @return mixed
     */
    public function request($key, $type, $default = null, $strip = "")
    {
        return $this->_returner($this->request, $key, $type, $default, $strip);
    }

    /**
     * Возвращает значение из суперглобального массива $_COOKIE.
     *
     * @param string $key - имя ключа
     * @param string $type - тип ожидаемых данных.
     *   Используйте константы: TYPE_STRING, TYPE_INTEGER, TYPE_ARRAY, TYPE_BOOLEAN, TYPE_FLOAT
     * @param mixed $default - значение по-умолчанию, которое возвращается в случае, если параметра с заданным ключем не существует
     * @param mixed $strip - Какие теги не вырезать? Если false, то теги не будут вырезаться, иначе можно указать теги, которые нужно оставить.
     * Например: '<br><p>'
     * @return mixed
     */
    public function cookie($key, $type, $default = null, $strip = "")
    {
        return $this->_returner($this->cookie, $key, $type, $default, $strip);
    }

    /**
     * Возвращает значение из суперглобального массива $_FILES.
     *
     * @param string $key - имя ключа
     * @param string $type - тип ожидаемых данных.
     *   Используйте константы: TYPE_STRING, TYPE_INTEGER, TYPE_ARRAY, TYPE_BOOLEAN, TYPE_FLOAT
     * @param mixed $default - значение по-умолчанию, которое возвращается в случае, если параметра с заданным ключем не существует
     * @param mixed $strip - Какие теги не вырезать? Если false, то теги не будут вырезаться, иначе можно указать теги, которые нужно оставить.
     * Например: '<br><p>'
     * @return mixed
     */
    public function files($key, $type = TYPE_ARRAY, $default = null, $strip = null)
    {
        return $this->_returner($this->files, $key, $type, $default, $strip);
    }

    /**
     * Возвращает значение из суперглобального массива $_SERVER.
     *
     * @param string $key - имя ключа
     * @param string $type - тип ожидаемых данных.
     *   Используйте константы: TYPE_STRING, TYPE_INTEGER, TYPE_ARRAY, TYPE_BOOLEAN, TYPE_FLOAT
     * @param mixed $default - значение по-умолчанию, которое возвращается в случае, если параметра с заданным ключем не существует
     * @param mixed $strip - Какие теги не вырезать? Если false, то теги не будут вырезаться, иначе можно указать теги, которые нужно оставить.
     * Например: '<br><p>'
     * @return mixed
     */
    public function server($key, $type = TYPE_STRING, $default = null, $strip = null)
    {
        return $this->_returner($this->server, $key, $type, $default, $strip);
    }

    /**
     * Возвращает значение из локального хранилища
     *
     * @param string $key - имя ключа
     * @param mixed $default - значение по-умолчанию, которое возвращается в случае, если параметра с заданным ключем не существует
     * @return mixed
     */
    public function parameters($key, $default = null)
    {
        return isset($this->router_defaults[$key]) ? $this->router_defaults[$key] : $default;
    }

    /**
     * Возвращает метод, с которым была загружена страница
     * @return string POST, GET,...
     */
    public function getMethod()
    {
        return $this->server('REQUEST_METHOD', TYPE_STRING);
    }

    /**
     * Возвращает true, если страница была загружена методом POST
     *
     * @return bool
     */
    public function isPost()
    {
        return $this->getMethod() == 'POST';
    }

    /**
     * Возвращает true, если в суперглобальном массиве присутствует параметр
     *
     * @param string $key - параметр
     * @param mixed $from - источник GET, POST, SERVER, ....
     * @return bool
     * Исключение \RS\Exception оставлено на ручной контроль
     */
    public function isKey($key, $from = REQUEST)
    {
        $src = $this->getSource($from);
        return isset($src[$key]);
    }

    /**
     * Возвращает true, если страница загружена с помощью ajax
     *
     * @return bool
     */
    public function isAjax()
    {
        return ($this->isKey('HTTP_X_REQUESTED_WITH', SERVER) && $this->server('HTTP_X_REQUESTED_WITH', TYPE_STRING) == 'XMLHttpRequest') || $this->request('ajax', TYPE_INTEGER);
    }

    /**
     * Возвращает локальную копию суперглобального массива
     *
     * @param mixed $storage - источник POST, GET, FILES,....
     * @return array
     * @throws RSException
     */
    public function getSource($storage)
    {
        switch ($storage) {
            case POST:
                return $this->post;
            case GET:
                return $this->get;
            case FILES:
                return $this->files;
            case COOKIE:
                return $this->cookie;
            case SERVER:
                return $this->server;
            case REQUEST:
                return $this->request;
            case PARAMETERS:
                return $this->router_defaults;
            default:
                throw new RSException(t('Неизвестный Storage'));
        }
    }

    /**
     * Устанавливает значение локальной копии суперглобального массива
     *
     * @param array $array - Массив с данными
     * @param mixed $storage - тип хранилища POST, GET, ...
     * @return void
     * @throws RSException
     */
    public function setSource(array $array, $storage)
    {
        switch ($storage) {
            case POST:
                $this->post = $array;
                break;
            case GET:
                $this->get = $array;
                break;
            case FILES:
                $this->files = $array;
                break;
            case COOKIE:
                $this->cookie = $array;
                break;
            case SERVER:
                $this->server = $array;
                break;
            case REQUEST:
                $this->request = $array;
                break;
            default:
                throw new RSException(t('Неизвестный Storage'));
        }
    }

    /**
     * Возвращает экранированное значение из одного источника
     *
     * @param mixed $src - массив с данных источника
     * @param mixed $keys - ключ или ключи
     * @param mixed $type - ожидаемый тип данных.
     * @param mixed $default - значение по-умолчанию
     * @param mixed $strip - теги, которые не нужно вырезать.
     * Если false, то теги strip_tags не будет вызываться
     * Если null, то данные вообще не будут экранироваться
     * @return mixed
     */
    protected function _returner(array $src, $keys, $type, $default, $strip)
    {
        $_keys = (array)$keys;
        $result = [];
        foreach ($_keys as $key) {
            $isset_key = false;
            $local_src = $src;
            if (isset($src[$key]) && $src[$key] != '') {
                $isset_key = true;
            } elseif (isset($this->router_defaults[$key])) {
                $isset_key = true;
                $local_src = $this->router_defaults;
            }

            $var = $isset_key ? $local_src[$key] : $default;
            if ($isset_key || $default === null) {
                if ($isset_key && $strip !== false && $type != TYPE_ARRAY && !is_array($var)) {
                    $var = strip_tags($var, $strip);
                }
                if ($type != TYPE_MIXED) {
                    settype($var, $type);
                }
                if ($type == TYPE_STRING && $this->to_entity && $strip !== null) {
                    $var = htmlspecialchars($var, ENT_QUOTES);
                }
                if ($type == TYPE_ARRAY && $this->to_entity && $strip !== null) {
                    $var = HelperTools::escapeArrayRecursive($var);
                }
            }
            $result[$key] = $var;
        }
        return is_array($keys) ? $result : $result[$keys];
    }

    /**
     * Возвращает URL с замещенными или добавленными параметрами
     *
     * @param array $new_keys - добавить/заменить параметры
     * @param array $search_keys - полный список параметров, которые нужно оставить
     * @param mixed $prefix - добавлять префикс перед $new_keys параметрами
     * @return string
     * @throws RSException
     */
    public function replaceKey($new_keys, $search_keys = [], $prefix = '')
    {
        $get = $this->getSource(GET);
        foreach ($new_keys as $key => $value) {
            $key = $prefix . $key;
            if (is_null($value)) {
                unset($get[$key]);
            } else {
                $get[$key] = $value;
            }
        }

        if (!empty($search_keys)) {
            $get = array_intersect_key($get, array_flip($search_keys));
        }
        array_walk_recursive($get, function (&$item) {
            $item = rawurldecode($item);
        });

        $route = RouterManager::getCurrentRoute();
        return $route ? $route->buildUrl($get) : '?' . http_build_query($get);
    }

    /**
     * Добавляет параметры, полученные маршрутизатором
     *
     * @param array $parameters параметры, которые были найдены в URL
     * @param array $defaults параметры по умолчанию, которые заданы в маршруте
     */
    public function setFromRouter(array $parameters, array $defaults)
    {
        $this->addFromArray($parameters, GET);
        $this->router_defaults = $defaults;
    }

    /**
     * выбирает из списка values значение, которое соответствует ключу var.
     * если таковое не найдено, то возвращается первое значение values
     *
     * @param mixed $var
     * @param array $values
     * @param bool $is_assoc - true означает, что values - это ассоциативный массив, значение $var будет проверяться в ключах
     *
     * @return boolean
     */
    public function convert($var, array $values, $is_assoc = false)
    {
        if ($is_assoc) {
            return isset($values[$var]) ? $values[$var] : reset($values);
        } else {
            return in_array($var, $values) ? $var : reset($values);
        }
    }

    /**
     * Сохраняет в сессии Url
     *
     * @param mixed $key - ключ
     * @param string $url - сохраняемый url, если не указан то текущий url
     * @return void
     */
    public function saveUrl($key, $url = null)
    {
        if ($url === null) {
            $url = $this->server('REQUEST_URI', TYPE_STRING);
        }
        if (!isset($_SESSION[$this->session_save_url_var]) || !is_array($_SESSION[$this->session_save_url_var])) {
            $_SESSION[$this->session_save_url_var] = [];
        }
        $_SESSION[$this->session_save_url_var][$key] = $url;
    }

    /**
     * Возвращает сохраненный раннее Url
     *
     * @param mixed $key - ключ
     * @param mixed $default - значение по-умолчанию
     * @return mixed
     */
    public function getSavedUrl($key, $default = '?')
    {
        return isset($_SESSION[$this->session_save_url_var][$key]) ? $_SESSION[$this->session_save_url_var][$key] : $default;
    }

    /**
     * Возвращает код, который необходимо добавить в форму для CSRF защиты
     *
     * @param string $form_name имя формы. Зарезервировано
     * @return string
     */
    public function setCsrfProtection($form_name = '')
    {
        if (!isset($_SESSION['csrf_protection'])) {
            $_SESSION['csrf_protection'] = uniqid();
        }
        return $_SESSION['csrf_protection'];
    }


    /**
     * Возвращает true, если проверка CSRF прошла успешно
     * Бросает исключение, если CSRF токен некорректный
     *
     * @param string $form_name имя формы. Зарезервировано
     * @return bool
     */
    public function checkCsrf($form_name = '')
    {
        $code = $this->request(self::CSRF_INPUT_NAME, TYPE_STRING);
        $server_code = isset($_SESSION['csrf_protection']) ? (string)$_SESSION['csrf_protection'] : null;

        if (!isset($_SESSION['csrf_protection']) || strcmp($code, $server_code)) {
            Application::getInstance()->showException(503, t('Данные устарели. Повторите отправку данных еще раз.'));
        }
        return true;
    }

    /**
     * Возвращает uri текущей страницы
     *
     * @return string
     */
    public function selfUri()
    {
        return $this->server('REQUEST_URI', TYPE_STRING);
    }

    /**
     * Возвращает доменное имя, в кодировке utf8. В том числе и интернациональные домены
     * @return string
     */
    public function getDomainStr()
    {
        $domain = $this->server('HTTP_HOST', TYPE_STRING);
        $int_domain = IdnaConvert::getInstance()->decode($domain);
        if ($int_domain !== false) {
            $domain = $int_domain;
        }
        return $domain;
    }

    /**
     * Возвращает исходное доменное имя
     *
     * @param bool $with_protocol - приписывать
     * @return string
     */
    public function getDomain($with_protocol = false)
    {
        $domain = $this->server('HTTP_HOST', TYPE_STRING);
        if ($with_protocol) {
            $domain = $this->getProtocol() . '://' . $domain;
        }
        return $domain;
    }
}
