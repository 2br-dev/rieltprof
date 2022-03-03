<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace ExternalApi\Model\AbstractMethods;
use \ExternalApi\Model\Exception as ApiException;
use ExternalApi\Model\Orm\AuthorizationToken;
use RS\Site\Manager;

/**
* Метод API, требующий авторизационный токен token с 
* необходимым набором прав для выполнения
*/
abstract class AbstractAuthorizedMethod extends AbstractMethod
{    
    /**
    * Если указать false, то token можно принимать опционально, 
    * чтобы давать в определенных случаях больше прав для вызова данного метода
    */
    protected $token_require = true;
    protected $token_param_name = 'token';
    /**
     * @var AuthorizationToken
     */
    protected $token;
    
    public $token_is_invalid = false; //Флаг отвечает за показ, того действителен токен или нет
        
    /**
    * Проверяет права на выполнение данного метода.
    * Метод может быть вызван, если на него есть права у пользователя (чей токен передается) или в настройках модуля ExternalAPI,
    * также приложение, к которому привязан токен должно обладать правами на запрашиваемый метод API
    * 
    * @param array $params - параметры запроса
    * @param string $version - версия приложения
    * @throws ApiException
    * @return void
    */
    public function validateRights($params, $version) 
    {
        $self_method_name = strtolower($this->getSelfMethodName());
        $is_allow_in_config_method = in_array(self::ALLOW_ALL_METHOD, $this->external_api_config->allow_api_methods)
            || in_array($self_method_name, $this->external_api_config->allow_api_methods);
        
        if ($this->token_require || isset($params[$this->token_param_name])) {
            if (!isset($params[$this->token_param_name])) {
                throw new ApiException(t('Не указан обязательный параметр token'), ApiException::ERROR_WRONG_PARAM_VALUE);
            }
            
            //Загружаем token
            $this->token = new \ExternalApi\Model\Orm\AuthorizationToken();
            
            if (!$this->token->load($params[$this->token_param_name]) || $this->token['expire'] < time()) {
                if ($this->token_require){ //Бросаем только, если токен обязателен
                    throw new ApiException(t('Неверно указан авторизационный токен'), ApiException::ERROR_METHOD_ACCESS_DENIED);    
                }else{
                    $this->token_is_invalid = true;
                }
            }

            if (!$is_allow_in_config_method) { //Если метод запрещен для вызова в настройках модуля
                $site_id = Manager::getSiteId();

                //Проверяем, если метод еще и не разрешен у пользователя, чей токен подается, то бросаем исключение
                $user_allow_methods = $this->token->getUser()->getExternalApiAllowMethods($site_id);
                if ($this->token_is_invalid
                    || (!in_array(self::ALLOW_ALL_METHOD, $user_allow_methods)
                        &&  !in_array($self_method_name, $user_allow_methods)) )
                {
                    throw new ApiException(t('Метод отключен для пользователя'), ApiException::ERROR_METHOD_ACCESS_DENIED);
                }
            }

            if (!$this->token_is_invalid){
                //Проверяем права на запуск метода API
                $app_rights = $this->token->getApp()->getAppRights();
                $current_method = $this->getSelfMethodName();
                if (!isset($app_rights[$current_method])
                    || ($app_rights[$current_method] != \ExternalApi\Model\App\AbstractAppType::FULL_RIGHTS 
                        && array_diff($this->getRunRights(), (array)$app_rights[$current_method])))
                {
                    //Формируем права, которые нужны
                    if (isset($app_rights[$current_method])) {
                        $need_rights = implode(',', array_diff_key($this->getRightTitles(), $app_rights[$current_method]));
                    } else {
                        $need_rights = 'доступ к методу '.$current_method;
                    }
                    
                    throw new ApiException(t('Недостаточно прав для запуска метода API. Требуются права на: %0', [$need_rights]), ApiException::ERROR_METHOD_ACCESS_DENIED);
                }
                \RS\Application\Auth::setCurrentUser($this->token->getUser());    
            }
        }  else {
            //Если пользователь неизвестен, то проводим обычную проверку на возможность вызова метода
            if (!$is_allow_in_config_method) {
                throw new ApiException(t('Метод отключен'), ApiException::ERROR_METHOD_ACCESS_DENIED);
            }
        }
    }
    
    /**
    * Проверяет наличие у token'а отдельных необходимых прав
    * 
    * @param integer | array $rights - Одно или несколько прав. Проверка будет происходить с помощью ИЛИ
    * @return string | false
    */
    public function checkAccessError($rights)
    {
        if (empty($this->token['token'])) {
            return t('Токен не загружен');
        }
        $rights = (array)$rights;
        
        $app_rights = $this->token->getApp()->getAppRights();

        if (isset($app_rights[$this->getSelfMethodName()]) && $app_rights[$this->getSelfMethodName()] == \ExternalApi\Model\App\AbstractAppType::FULL_RIGHTS) {
            return false;
        }
        
        if (isset($app_rights[$this->getSelfMethodName()]) && array_intersect($rights, $app_rights[$this->getSelfMethodName()])) {
            return false;
        }
        
        $actions = implode(', ', array_intersect_key($this->getRightTitles(), array_flip($rights)));
        return t('Недостаточно прав для выполнения действий: %0', [$actions]);
    }
    
    /**
    * Возвращает список прав, требуемых для запуска метода API
    * По умолчанию для запуска метода нужны все права, что присутствуют в методе
    * 
    * @return [код1, код2, ...]
    */
    public function getRunRights()
    {
        return array_keys($this->getRightTitles());
    }
    
    /**
    * Возвращает комментарии к кодам прав доступа
    * 
    * @return [
    *     КОД => КОММЕНТАРИЙ,
    *     КОД => КОММЕНТАРИЙ,
    *     ...
    * ]
    */
    abstract public function getRightTitles();
    
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
        $response = parent::run($params, $version, $lang); 
        if ($this->token_is_invalid){ //Если есть и устновлен флаг, что токен не действителен
           $response['response']['token_is_invalid'] = true;  
        }
        return $response;
    }
    
}