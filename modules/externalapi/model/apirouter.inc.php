<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace ExternalApi\Model;
use ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod;
use \ExternalApi\Model\Exception as ApiException;

class ApiRouter
{    
    public static
        $api_method_folder = '/model/externalapi';
        
    private
        $version,
        $lang;
        
    function __construct($version, $lang)
    {
        $this->version = $version;
        $this->lang = $lang;
    }
    
    /**
    * Выполняет один метод API
    * 
    * @param string $method
    * @param array $http_request
    */
    public function runMethod($method, $params)
    {
        $method_instance = self::getMethodInstance($method);
        
        if ($method_instance) {
            return $method_instance->run($params, $this->version, $this->lang);
        } else {
            throw new ApiException(t('Метод API не найден'), ApiException::ERROR_METHOD_NOT_FOUND);
        }
    }
    
    /**
    * Возвращает POST И GET параметры, которые будут переданы в API метод
    * 
    * @param string $method 
    * @param \RS\Http\Request $http_request
    * @return array
    */
    public function makeParams($method, \RS\Http\Request $http_request)
    {
        $params = [];
        
        if ($method_instance = self::getMethodInstance($method)) {
            $allow_request_methods = $method_instance->getAcceptRequestMethod();
            
            if (in_array(POST, $allow_request_methods)) {
                foreach($http_request->getSource(POST) as $key => $value) {
                    $params[$key] = $http_request->post($key, is_array($value) ? TYPE_ARRAY : TYPE_STRING);
                }
            }

            if (in_array(GET, $allow_request_methods)) {
                foreach($http_request->getSource(GET) as $key => $value) {
                    $params[$key] = $http_request->get($key, is_array($value) ? TYPE_ARRAY : TYPE_STRING);
                }
            }
            
            if (in_array(FILES, $allow_request_methods)) {
                foreach($http_request->getSource(FILES) as $key => $value) {
                    $params[$key] = $http_request->files($key);
                }
            }
        }
        
        return $params;
    }
    
    /**
    * Возвращает все возможные версии методов API, от самой нижней до верхней
    * 
    * @param mixed $cache
    */
    public static function getMethodsVersions($cache = true)
    {
        if ($cache) {
            return \RS\Cache\Manager::obj()
                ->request([__CLASS__, __FUNCTION__], false);
        } else {
            $versions = [];
            foreach(self::getGroupedMethodsInfo() as $methods) {
                foreach($methods as $method) {
                    foreach($method['versions'] as $version) {
                        $versions[$version] = $version;
                    }
                }
            }
            natsort($versions);
            return $versions;            
        }
    }
    
    
    /**
    * Возвращает все существующие языки, для которых есть описания API
    * 
    * @param bool $cache
    * @return array
    */
    public static function getMethodsLanguages($cache = true)
    {
        if ($cache) {
            return \RS\Cache\Manager::obj()
                ->request([__CLASS__, __FUNCTION__], false);
                
        } else {
            $languages = [\ExternalApi\Model\AbstractMethods\AbstractMethod::DEFAULT_LANGUAGE => 1];
            
            foreach(self::getGroupedMethodsInfo() as $methods) {
                foreach($methods as $method) {
                    $languages += self::findLanguages($method['comment']);
                    $languages += self::findLanguages($method['example']);
                    $languages += self::findLanguages($method['return']);
                    
                    foreach($method['params'] as $param_data) {
                        $languages += self::findLanguages($param_data['comment']);
                    }
                }
            }
            return array_keys($languages);
        }
    }
    
    
    /**
    * Ищет языки, используемые в комментариях к методам
    * 
    * @param string $text
    * @return array
    */
    private static function findLanguages($text)
    {
        if (preg_match_all('/\#lang-(\w+)\:/', $text, $match)) {
            return array_flip(array_map('strtolower', $match[1]));
        }
        return [];
    }
    
    /**
    * Возвращает информацию о всех методах API, присутствующих в системе
    * 
    * @param string $lang - двухсимвольный идентификатор
    * @param bool $cache - использовать кэширование
    * 
    * @return array
    */
    public static function getGroupedMethodsInfo($lang = null, $cache = true)
    {
        $result = [];
        foreach(self::getApiMethods($cache) as $method) {
            $method_instance = new $method['class']();
            $info = $method_instance->getInfo($lang);
            
            $versions = [];
            foreach($info as $version => $method_info) {
                $result[$method['method_group']][$method['method']] = $method_info;
                $versions[] = $version;
            }
            
            $result[$method['method_group']][$method['method']]['versions'] = $versions;
        }
        
        return $result;
    }
    
    /**
    * Возвращает инстанс класса, который обрабатывает метод API
    * 
    * @param string $method - Имя метода, например oauth.authorize
    * @param bool $only_allowable - Если true, то будут возвращены инстансы только включенных в настройках модуля методов
    * @return \ExternalApi\Model\AbstractMethod
    */
    public static function getMethodInstance($method, $only_allowable = false)
    {
        $method = strtolower($method);
        $methods_list = self::getApiMethods(true, $only_allowable);
        
        if (isset($methods_list[$method])) {
            return new $methods_list[$method]['class']();
        }
        return false;
    }
    
    /**
    * Возвращает список методов, имеющихся в системе для отображения в элементе select
    * @return [
    *     'id метода' => '(id метода) описание метода',
    *     'id метода' => '(id метода) описание метода',
    *     ....  
    * ]
    */
    public static function getApiMethodsSelectList(array $root_item = [], $only_allowable = false, $lang = null, $cache = true)
    {
        $result = [];
        foreach(self::getApiMethods($cache, $only_allowable) as $method) {
            $method_instance = new $method['class']();
            $info = $method_instance->getInfo($lang);
            
            $description = '';
            foreach($info as $version => $method_info) {
                $description = $method_info['method']." <i><small>({$method_info['comment']})</small></i>";
            }
            
            $result[strtolower($method['method'])] = $description;
        }
        
        return $root_item + $result;
    }

    /**
     * Возвращает список методов, поддерживающих авторизационный токен, имеющихся в системе для отображения в элементе select
     *
     * @param array $root_item
     * @param bool $only_allowable - Если true, то будут возвращены только разрешенные в настройках модуля методы
     * @param null $lang - Язык для справки
     * @param bool $cache - Если true, то будет использоваться кэширование
     */
    public static function getAuthorizedApiMethodsSelectList(array $root_item = [], $only_allowable = false, $lang = null, $cache = true)
    {
        $result = [];
        foreach(self::getApiMethods($cache, $only_allowable) as $method) {
            $method_instance = new $method['class']();
            if ($method_instance instanceof AbstractAuthorizedMethod) {
                $info = $method_instance->getInfo($lang);

                $description = '';
                foreach ($info as $version => $method_info) {
                    $description = $method_info['method'] . " <i><small>({$method_info['comment']})</small></i>";
                }

                $result[strtolower($method['method'])] = $description;
            }
        }

        return $root_item + $result;
    }
    
    /**
    * Возвращает полный список методов API, которые существуют во всех включенных модулях текущего сайта.
    * Классы с обработчиками методов должны находиться в папке /ИМЯ МОДУЛЯ/model/externalapi/ИМЯ ГРУППЫ/ИМЯ МЕТОДА
    * 
    * @param bool $cache - Если true, то будет использоваться кэширование
    * @param bool $only_allowable - Если true, то будут возвращены только разрешенные в настройках модуля методы
    * @return [
    *   [
    *       'method' => 'oauth.token', //группа.метод
    *       'class' => '\ExternalApi\Model\ExternalApi\OAuth\Token' //Имя класса, выполняющего метод
    *   ],
    *   ...
    * ]
    */
    public static function getApiMethods($cache = true, $only_allowable = true)
    {
        static
            //Локальный уровень кэширования
            $scope_cache = [];
            
        $site_id = \RS\Site\Manager::getSiteId();            
        if (!isset($scope_cache[$site_id]) || !$cache) {
        
            if ($cache) {
                //Кэширование результата на уровне файлов
                $scope_cache[$site_id] = \RS\Cache\Manager::obj()
                    ->request([__CLASS__, __FUNCTION__], false, $only_allowable, $site_id);
                    
            } else {
                $exists_methods = [];
                
                $module_api = new \RS\Module\Manager();
                $modules = $module_api->getActiveList($site_id);

                foreach($modules as $module_item) {
                    $exists_methods += self::getModuleApiMethods($module_item);
                }
                
                $allowable_methods = \RS\Config\Loader::byModule('externalapi')->allow_api_methods;                
                if ($only_allowable && !in_array(AbstractMethods\AbstractMethod::ALLOW_ALL_METHOD, $allowable_methods)) {                    
                    $exists_methods = array_intersect_key($exists_methods, array_flip($allowable_methods));
                }
                
                return $exists_methods;
            }
        }
        
        return $scope_cache[$site_id];        
    }
    
    /**
    * Возвращает список методов API, присутствующих в модуле
    * Классы с обработчиками методов должны находиться в папке /ИМЯ МОДУЛЯ/model/externalapi/ИМЯ ГРУППЫ/ИМЯ МЕТОДА
    * 
    * @param \RS\Module\Item $module - объект одного модуля
    * @return [
    *   [
    *       'method' => 'oauth.token', //группа.метод
    *       'class' => '\ExternalApi\Model\ExternalApi\OAuth\Token' //Имя класса, выполняющего метод
    *   ],
    *   ...
    * ]
    */
    private static function getModuleApiMethods(\RS\Module\Item $module_item)
    {
        $exists_methods = [];
        $folder = $module_item->getFolder().self::$api_method_folder; //Папка, где находятся группы методов
        if (file_exists($folder)) {
            foreach(new \DirectoryIterator($folder) as $dir) {
                if (!$dir->isDot() && $dir->isDir()) {
                    $files_iterator = new \FilesystemIterator($dir->getRealPath(), 
                                                              \FilesystemIterator::KEY_AS_PATHNAME 
                                                              | \FilesystemIterator::SKIP_DOTS);
                    
                    $files_iterator = new \RegexIterator($files_iterator, '/(.inc.php|.my.inc.php)$/');
                    
                    foreach($files_iterator as $filename => $file) {
                        $method = strtok($file->getFilename(), '.');
                        $class = str_replace('/', '\\', $module_item->getName()
                                 .self::$api_method_folder
                                 .'/'.$dir->getFilename()
                                 .'/'.$method);
                        
                        if (is_subclass_of($class, '\ExternalApi\Model\AbstractMethods\AbstractMethod')) {
                            $instance = new $class();
                            
                            //Получаем названия методов с учетом регистра букв в названии
                            $class_name = str_replace('\\', '/', get_class($instance));
                            $method_group = lcfirst(basename(dirname($class_name)));
                            $method_name = lcfirst(basename($class_name));
                            
                            $method_fullname = $method_group.'.'.$method_name;
                            $exists_methods[strtolower($method_fullname)] = [
                                'method_group' => $method_group,
                                'method_name' => $method_name,
                                'method' => $method_fullname,
                                'class' => $class
                            ];
                        }
                    }
                }
            }
        }
        return $exists_methods;
    }

    /**
     * Возвращает значение заголовка Origin для ответа на запросы
     *
     * @param string $client_name - имя приложения для подключения
     * @param string $client_version - версия приложения для подключения
     *
     * @return string
     */
    public static function getOriginForRequest($client_name = "", $client_version = "")
    {
        $origin = "*";
        if ($client_name == 'MobileSiteApp'){
            $origin = 'http://localhost:8080';
        }
        //Заглушка для локальной разработки
        if (mb_stripos($_SERVER['HTTP_HOST'], "192.168.1") !== false){
            $origin = 'http://localhost:8100';
        }

        //Дополнительная заглушка для проверок
        if (!function_exists('getallheaders')) {
            $headers = [];
            foreach ($_SERVER as $name => $value)
            {
                if (substr($name, 0, 5) == 'HTTP_')
                {
                    $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                }
            }
        }else{
            $headers = getallheaders();
        }

        $header_origin = null;
        if (isset($headers['Origin'])){
            $header_origin = $headers['Origin'];
        }

        if (isset($headers['origin'])){
            $header_origin = $headers['origin'];
        }

        if ($header_origin && (in_array($header_origin, [
                'http://localhost:8100',
                'http://localhost:8080',
                'http://localhost',
                'ionic://localhost',
            ])) && $client_name == 'MobileSiteApp'){
            $origin = $header_origin;
        }

        return $origin;
    }
}
