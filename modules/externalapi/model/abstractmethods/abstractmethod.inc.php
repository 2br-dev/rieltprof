<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace ExternalApi\Model\AbstractMethods;
use \ExternalApi\Model\Exception as ApiException;

/**
* Базовый класс для всех публичных методов API.
* Данный класс можно использовать в качестве базового, если метод API не требует авторизационный token.
*/
abstract class AbstractMethod
{
    const
        ALLOW_ALL_METHOD = 'all',
        BASE_VERSION = 1, //Версия API, которой будет соответствовать функция "process" без уточнения версии
        DEFAULT_LANGUAGE = 'ru';
    
    protected
        $external_api_config,
        $default_version,
        $method_params = [],
        $version,
        $params;
    
    function __construct()
    {
        $this->external_api_config = \RS\Config\Loader::byModule('externalapi');
        $this->default_version = $this->external_api_config->default_api_version;        
    }
        
    /**
    * Запускает выполнение метода
    * 
    * @param array $params - параметры запроса
    * @param string $version - версия АПИ
    * @param string $lang - язык ответа
    * 
    * @return mixed
    */
    public function run($params, $version = null, $lang = 'ru')
    {
        $this->version = $version ?: $this->default_version;
        $this->lang = $lang;
        
        $this->params = $params; //Параметры без валидации
        $this->method_params = $this->validateParams($params, $this->version);
        $this->validateRights($params, $this->version);
        
        //Разрешаем корректировать результат выполнения метода другим модулям
        $event_result = \RS\Event\Manager::fire('api.'.strtolower($this->getSelfMethodName()).'.before', [
            'result' => [],
            'version' => $this->version,
            'lang' => $this->lang,
            'method' => $this,
            'method_params' => $this->method_params,
            'params' => $params,
        ]);
        
        list($response_before) = $event_result->extract();
        
        $response = array_merge_recursive($response_before, call_user_func_array([$this, $this->getProcessFunctionName($this->version)], $this->method_params));

        //Разрешаем корректировать результат выполнения метода другим модулям
        $event_result = \RS\Event\Manager::fire('api.'.strtolower($this->getSelfMethodName()).'.success', [
            'result' => $response,
            'version' => $this->version,
            'lang' => $this->lang,
            'method' => $this,
            'method_params' => $this->method_params,
            'params' => $params,
        ]);
        list($response) = $event_result->extract();
        
        return $response;
    }
    
    /**
    * Возвращает имя метода(функции) соответствующего указанной версии, если таковой метод существует (processVerX), 
    * иначе возвращает имя метода предыдущей версии или имя функции по умолчанию (process)
    * 
    * @param string $version версия
    * @return string - имя метода
    * @throws \ExternalApi\Model\Exception - в случае если метод process не определен
    */
    public function getProcessFunctionName($version)
    {
        $default_func_name = 'process';
        
        //Получаем все возможные версии API (методом обхода всех методов)
        $versions = array_reverse(\ExternalApi\Model\ApiRouter::getMethodsVersions(), true);
        if (!in_array($version, $versions)) {
            throw new ApiException(t("API указанной версии '%0' не существует. Допустимы версии: %1", [$version, implode(',', $versions)]), ApiException::ERROR_WRONG_PARAMS);
        }
        
        foreach($versions as $one_version) {
            if (version_compare($one_version, $version, '<=')) {
                //Выполняем либо запрошенную версию, либо предыдущую версию
                $func_name = $default_func_name.'Ver'.str_replace('.', '_', $one_version);
                if (is_callable([$this, $func_name])) {
                    return $func_name;
                }
            }
        }
        
        //Выполняем дефолтную версию
        if (is_callable([$this, $default_func_name])) {
            return $default_func_name;
        } else {
            throw new ApiException(t('Отсутствует метод process в классе %0', [get_class($this)]), ApiException::ERROR_INSIDE);
        }
    }
    
    /**
    * Возвращает какими методами могут быть переданы параметры для данного метода API
    * 
    * @return array
    */
    public function getAcceptRequestMethod()
    {
        return [POST, GET, FILES];
    }    
    
    /**
    * Проверяет права на выполнение данного метода
    * 
    * @param mixed $version
    */
    public function validateRights($params, $version) 
    {
        if (!in_array(self::ALLOW_ALL_METHOD, $this->external_api_config->allow_api_methods)
            && !in_array(strtolower($this->getSelfMethodName()), $this->external_api_config->allow_api_methods)) 
        {
            throw new ApiException(t('Метод отключен администратором'), ApiException::ERROR_METHOD_ACCESS_DENIED);
        }
        
        return true;
    }
    
    /**
    * Проверяем, все ли обязательные параметры присутствуют. Если да, то возвращает массив параметров для вызова call_user_func_array
    * 
    * @param array $params
    * @param array $version
    * @return array
    * 
    * @throws \ExternalApi\Model\Exception
    */
    public function validateParams($params, $version)
    {
        $result = [];
        
        $info = $this->getInfo();
        $use_version = isset($info[$version]) ? $version : self::BASE_VERSION;
        $method_allow_params = $info[$use_version]['params'];

        //Проверяем обязательные параметры
        foreach($method_allow_params as $name => $info) {
            if (!$info['is_optional'] && !isset($params[$name])) {
                $error = new ApiException(t('Передан неверный набор параметров. Не найден параметр %0', [$name]), ApiException::ERROR_WRONG_PARAMS);
                $this->addMethodHelpUrlToException($error);
                
                throw $error;
            }
            
            if (isset($params[$name])) {
                $param_value = $params[$name];
                if (!empty($info['type']) && $info['type'] != 'mixed') {
                    settype($param_value, $info['type']);
                }
            } else {
                $param_value = $info['default_value'];
            }
            $result[$name] = $param_value;
            
            if (!$info['is_disabled']) {
                unset($params[$name]);
            }
        }
        
        //Проверяем, нет ли лишних параметров, убираем стандартные параметры
        unset($params['method']);
        unset($params['v']);
        unset($params['lang']);
        unset($params['format']);
        unset($params['client_version']);
        unset($params['client_name']);
        
        //Разрешаем передавать в переменной custom - любые частные данные, 
        //которые могут затем могут отлавливать любые сторонние модули
        unset($params['custom']); 
        
        if (count($params)) {
            $error = new ApiException(t("Передан неверный набор параметров. Обнаружены неизвестные параметры '%0'", [implode(', ', array_keys($params))]), ApiException::ERROR_WRONG_PARAMS);
            $this->addMethodHelpUrlToException($error);            
            
            throw $error;
        }
        
        return $result;
    }    
    
    /**
    * Добавляет в сведения, отображаемые клиенту ссылку на документацию к текущему методу
    * 
    * @param \ExternalApi\Model\AbstractException $api_exception
    * @return \ExternalApi\Model\AbstractException
    */
    protected function addMethodHelpUrlToException(\ExternalApi\Model\AbstractException $api_exception)
    {
        //Добавим ссылку на документацию по методу, если help включен
        if (\RS\Config\Loader::byModule($this)->enable_api_help) {
            $api_exception->addExtraApiData('help_url', \RS\Router\Manager::obj()
                                                   ->getUrl('externalapi-front-apigate-help', 
                                                            ['method' => $this->getSelfMethodName()], true));
        }
        return $api_exception;
    }
    
    /**
    * Возвращает информацию об имеющихся методах и их параметрах
    * 
    * @param mixed $version
    * @return [
    *   "1.0": [
    *       [
    *           "name" => '',
    *           "type" => '',
    *           "default_value" => '',
    *           "comment" => ''
    *        ]
    * ]
    */
    public function getInfo($lang = null)
    {
        $result = [];
        
        $reflection = new \ReflectionClass($this);
        $methods = $reflection->getMethods();
        
        foreach($methods as $method) {
            if (preg_match('/^process(Ver(.*?))?$/', $method->name, $match)) {
                $v = isset($match[2]) ? str_replace('_', '.', $match[2]) : self::BASE_VERSION;
                
                $method_comment = $method->getDocComment();
                
                $params = [
                    'class' => get_class($this),
                    'method' => $this->getSelfMethodName(),
                    'comment' => $this->getMethodComment($method_comment, $lang),
                    'example' => $this->getMethodExample($method_comment, $lang),
                    'return' => $this->getMethodReturnComment($method_comment, $lang),
                    'params' => []
                ];
                
                foreach ($method->getParameters() as $param) {
                    $name = $param->getName();
                    
                    //Игнорируем параметры с символом подчеркивания в начале 
                    $clean_name = $name[0] == '_' ? substr($name, 1) : $name;
                    
                    $params['params'][$clean_name] = [
                        'name' => $clean_name,
                        'type' => $this->getParamType($method_comment, $param),
                        'is_optional' => $param->isDefaultValueAvailable(),
                        'default_value' => $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null,
                        'comment' => $this->getParamComment($method_comment, $param, $lang),
                        'is_disabled' => $name[0] == '_'
                    ];
                }
                $result[$v] = $params;
            }
        }
        
        ksort($result); //Более ранние версии всегда должны быть выше
        return $result;
    }
    
    /**
    * Возвращает комментарий к параметру API в формате HTML.
    * Данный комментарий должен быть подписан 
    * 
    * @param string $comment - полный PHPDoc комментарий к функции process...
    * @param \ReflectionParameter $param_name - параметр функции process...
    * @return string | null
    */
    protected function getParamComment($comment, $param, $lang)
    {
        if (preg_match('/@param\s+(mixed|bool|boolean|integer|int|float|string|array)\s+\$'.$param->name.' (.*?)(\*\s?\@|\*\/)/msu', $comment, $match)) {
            return $this->prepareDocComment($match[2], $lang);
        }
    }
    
    /**
    * Форматирует комментарий, полученный из PHPDoc
    * 
    * @param string $text - комментарий
    * @return string
    */
    protected function prepareDocComment($text, $lang)
    {
        $text = preg_replace('/\r/', '', $text);
        $text = preg_replace('/\n\s*\*/', "\n", $text);
                
        //Парсим языковые версии
        if ($lang !== null) {
            if (preg_match('/\#lang-'.$lang.'\:(.*?)(\#lang-.*)?$/s', $text, $match)) {
                $text = trim($match[1]);
            } else {
                if (preg_match('/^(.*?)(\#lang-.*)?$/s', $text, $match)) {
                    $text = trim($match[1]);
                }
            }
        }
        
        //Экранируем данные в тегах <pre>...</pre>
        $text = preg_replace_callback('/\<pre\>(.*?)\<\/pre\>/sm', function($match) {
            return '<pre>'.htmlspecialchars($match[1]).'</pre>';
        }, $text);
        
        $text = preg_replace('/\n/', '<br>', trim($text));
        
        return $text;
    }
    
    /**
    * Возвращает ожидаемый тип параметра API, исходя из комментария PHPDoc
    * 
    * @param string $comment - полный PHPDoc комментарий к функции process...
    * @param \ReflectionParameter $param_name - параметр функции process...
    * @return string | null
    */
    protected function getParamType($comment, $param)
    {
        if (preg_match('/@param\s+(mixed|bool|boolean|integer|int|float|string|array)\s+\$'.$param->name.'/', $comment, $match)) {
            return $match[1];
        }
    }
    
    /**
    * Возвращает описание метода API, исходя из PHPDoc описания
    * 
    * @param string $comment полный PHPDoc комментарий к функции process...
    * @return string | null
    */
    protected function getMethodComment($comment, $lang)
    {
        if (preg_match('/\/\*\*(.*?)(\*\s?\@|\*\/)/msu', $comment, $match)) {
            return $this->prepareDocComment($match[1], $lang);
        }
    }
    
    /**
    * Возвращает комментарий к результату метода API
    * 
    * @param string $comment полный PHPDoc комментарий к функции process...
    * @return string
    */
    protected function getMethodReturnComment($comment, $lang)
    {
        if (preg_match('/@return\s+(.*?)\s+(.*?)(\*\s?\@|\*\/)/msu', $comment, $match)) {
            return $this->prepareDocComment($match[2], $lang);
        }
    }
    
    /**
    * Возвращает пример вызова метода API
    * 
    * @param string $comment полный PHPDoc комментарий к функции process...
    * @return string
    */
    protected function getMethodExample($comment, $lang)
    {
        if (preg_match('/@example\s+(.*?)(\*\s?\@|\*\/)/msu', $comment, $match)) {
            return $this->prepareDocComment($match[1], $lang);
        }
    }
    
    /**
    * Возвращает идентификатор текущего метода API вместе с группой
    * 
    * @return string
    */
    public function getSelfMethodName()
    {
        preg_match('/([^\\\\]+?)\\\\([^\\\\]+?)$/', get_class($this), $match);
        return lcfirst($match[1]).'.'.lcfirst($match[2]);
    }
}