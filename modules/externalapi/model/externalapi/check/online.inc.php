<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace ExternalApi\Model\ExternalApi\Check;
use \ExternalApi\Model\Exception as ApiException;
  
/**
* Проверяет доступность API и токен, если он передан
*/
class Online extends \ExternalApi\Model\AbstractMethods\AbstractMethod
{        
    /**
    * Возвращает какими методами могут быть переданы параметры для данного метода API
    * 
    * @return array
    */        
    public function getAcceptRequestMethod()
    {
        return [POST];
    }
    
    
    
    /**
    * Метод позволяет проверить доступность API и если передан токен, проверяет его на валидность 
    * 
    * @param string $token токен для проверки на валидность
    * @param string $hash уникальный код приложения
    * 
    * @example POST /api/methods/check.online?v=1&token=38b83885448a8ad9e2fb4f789ec6b0b690d50041
    * 
    * POST /api/methods/check.online?v=1
    * 
    * Ответ:
    * <pre>
    * {
    *      'response': {
    *            'success': true,
    *            'check_token': false|true
    *        }
    * }
    * </pre>
    * 
    * @return array Возвращает ответ доступности АПИ и если передан, токен, то его валидность
    * <b>response.success</b> - ответ о доступности
    * <b>response.check_token</b> - проверка валидности токена, если он передан
    */
    protected function process($token = null, $hash = null)
    {
        $params['response']['success']     = true;   
        $params['response']['check_token'] = !empty($token) ? true : false;
        $extend_api = new \MobileSiteApp\Model\ExtendApi();
        if (!empty($token) && !$extend_api->checkToken($token)){
            $params['response']['check_token'] = false;
        } 
        
        return $params;
    }    
}