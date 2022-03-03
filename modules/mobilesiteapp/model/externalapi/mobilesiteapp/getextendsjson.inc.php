<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace MobileSiteApp\Model\ExternalApi\MobileSiteApp;

/**
* Возвращает JSON для расширения функционала мобильного приложения. 
* Метод собирает javascript с разных модулей и объединяет в один JSON
*/
class GetExtendsJSON extends \ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod
{
    const
        RIGHT_LOAD = 1;
        
    protected
        $token_require = false; //Токен не обязателен
    
    /**
    * Возвращает комментарии к кодам прав доступа
    * 
    * @return [
    *     КОД => КОММЕНТАРИЙ,
    *     КОД => КОММЕНТАРИЙ,
    *     ...
    * ]
    */
    public function getRightTitles()
    {
        return [
            self::RIGHT_LOAD => t('Получение данных приложением')
        ];
    }
    

    /**
    * Возвращает JSON для расширения функционала мобильного приложения. 
    * Метод собирает javascript с разных модулей и объединяет в один JSON, который встраивается в классы JS приложения
    * 
    * @example GET /api/methods/mobilesiteapp.getextendsjson
    * 
    * GET /api/methods/mobilesiteapp.getextendsjson?token=8439f9jf034jf089jsduihf3240fj34fj
    * 
    * Ответ:
    * <pre>
    * {
    *  "response": {
    *     "MainPage" : {
    *       "openConsole" : "function(){console.log(777)}"
    *     } 
    *  }  
    *}
    * </pre>
    * 
    * @return array Возращает, пустой массив ошибок, если всё успешно
    */
    protected function process($token = null)
    {
        $extend_api           = new \MobileSiteApp\Model\ExtendApi(); 
        $response['response'] = $extend_api->getExtendsJSON();
                  
        return $response;
    }
}