<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Catalog\Model\ExternalApi\Favorite;

use Catalog\Model\FavoriteApi;
use \ExternalApi\Model\Exception as ApiException;

/**
* Добавляет товар в избранное
*/
class Add extends \ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod
{
    
    const
        RIGHT_FAVORITE = 1;
        
    protected
        $token_require = false,
        $token_error = [];
    
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
            self::RIGHT_FAVORITE => t('Доступ к функционалу "Избранного"')
        ];
    }
    
    /**
    * Проверяет права на выполнение данного метода
    * 
    * @param mixed $version
    */
    public function validateRights($params, $version) 
    {
        try{
            parent::validateRights($params, $version);
        }catch(ApiException $e){
            if ($e->getCodeString() == ApiException::ERROR_METHOD_ACCESS_DENIED || $e->getCodeString() == ApiException::ERROR_WRONG_PARAM_VALUE){
                $this->token_error['code']    = $e->getCodeString();
                $this->token_error['message'] = $e->getMessage();
            }else{
                throw $e;
            }
        }
    }

    
    /**
    * Добавляет товар в избранное. Для незарегестрированных пользователей передавать токен не нужно, а для присуствующих в системе обязательно.
    * Если токен не указан, или указан неправильно, то товар будет добавляен для неавторизованного пользователя.
    * 
    * @param string $token Авторизационный токен
    * @param integer $product_id ID товара
    * @example GET api/methods/favorite.add?token=2bcbf947f5fdcd0f77dc1e73e73034f5735de486&product_id=1
    * Ответ
    * <pre>
    * {
    *     "response": {
    *         "summary": {
    *             "token_used": "false", //Флаг использовался ли токен. Для получения избранного зарегестрированнного пользователя передавать обязательно.
    *             "token_error": { //Присутствует если есть ошибки переданного токена.
    *                "code" : "wrong_param_value',
    *                "message" : "Сообщение об ошибке'
    *             },
    *             "total": 2 //Количество добавленных в избранные товаров
    *     }
    * }
    * </pre>
    */
    function process($token = null, $product_id)
    {
        $favoriteapi = FavoriteApi::getInstance();
        if (!$token){ //Если токен не использовали.
            $response['response']['summary']['token_used'] = false; 
            $favoriteapi->setGuestId(session_id());
        }else{ //Если токен использовали.
            $response['response']['summary']['token_used'] = true; 
            $favoriteapi->setUserId($this->token->getUser()->id); //Установим пользователя из токена
        }
        
        //Если есть секция с ошибками токена
        if (!empty($this->token_error)){
            $response['response']['summary']['token_error'] = $this->token_error;
        }
        
        $favoriteapi->addToFavorite($product_id);
        $response['response']['summary']['total'] = $favoriteapi->getFavoriteCount();
        return $response;
    }
}