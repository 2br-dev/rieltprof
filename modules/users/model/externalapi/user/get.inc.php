<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Model\ExternalApi\User;
use \ExternalApi\Model\Exception as ApiException;
  
/**
* Загружает объект пользователя
*/
class Get extends \ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod
{
    const
        RIGHT_LOAD_SELF = 1,
        RIGHT_LOAD = 2;
    
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
            self::RIGHT_LOAD_SELF => t('Загрузка авторизованного пользователя'),
            self::RIGHT_LOAD => t('Загрузка пользователя')
        ];
    }
    
    /**
    * Возвращает список прав, требуемых для запуска метода API
    * По умолчанию для запуска метода нужны все права, что присутствуют в методе
    * 
    * @return [код1, код2, ...]
    */
    public function getRunRights()
    {
        return []; //Проверка прав будет непосредственно в теле метода
    }    
    
    /**
    * Возвращает ORM объект, который следует загружать
    */
    public function getOrmObject()
    {
        return new \Users\Model\Orm\User();
    }

    /**
     * Загружает объект пользователя
     *
     * @param string $token Авторизационный токен
     * @param integer $user_id ID пользователя
     *
     * @example GET /api/methods/user.get?token=b45d2bc3e7149959f3ed7e94c1bc56a2984e6a86&user_id=1
     *
     * <pre>
     *  {
     *      "respone": {
     *          "user": {
     *              "name": "Артем",
     *              "surname": "Иванов",
     *              "midname": "Петрович",
     *              "e_mail": "mail@readyscript.ru",
     *              "login": "demo@example.com",
     *              "phone": "+700000000000",
     *              "sex": "",
     *              "subscribe_on": "0",
     *              "dateofreg": "0000-00-00 00:00:00",
     *              "ban_expire": null,
     *              "last_visit": "2016-09-07 18:51:20",
     *              "is_company": 1,
     *              "company": "ООО Ромашка",
     *              "company_inn": "1234567890",
     *              "data": {}
     *          }
     *      }
     *   }</pre>
     *
     * @return array Возвращает объект пользователя
     * @throws ApiException
     */
    protected function process($token, $user_id)
    {
        //Проверяем права на доступ к загрузке своего объекта        
        if ($error = $this->checkAccessError($user_id == $this->token['user_id'] ? self::RIGHT_LOAD_SELF : self::RIGHT_LOAD)) {
            throw new ApiException(t('Недостаточно прав для доступа к данному пользователю'), ApiException::ERROR_METHOD_ACCESS_DENIED);
        }
        
        $object = $this->getOrmObject();
        if ($object->load($user_id)) {
            
            $user = \ExternalApi\Model\Utils::extractOrm($object);
            return [
                'response' => \ExternalApi\Model\Utils::extractOrm($user)
            ];
        }
        
        throw new ApiException(t('Пользователь с таким ID не найден'), ApiException::ERROR_OBJECT_NOT_FOUND);
    }
}