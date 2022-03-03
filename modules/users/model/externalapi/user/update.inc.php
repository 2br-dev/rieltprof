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
* Обновляет сведения о пользователе, перезаписывает значения полей
*/
class Update extends \ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod
{
    const RIGHT_LOAD_SELF = 1;
    const RIGHT_LOAD = 2;

    protected $user_validator;

    /** Поля, которые следует проверять из POST */
    public $use_post_keys = ['is_company', 'company', 'company_inn', 'name', 'surname', 'midname', 'sex', 'passport', 'phone', 'e_mail', 'openpass', 'captcha', 'data', 'changepass'];

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
            self::RIGHT_LOAD_SELF => t('Загрузка авторизованного пользователя')
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
    * Форматирует комментарий, полученный из PHPDoc
    * 
    * @param string $text - комментарий
    * @return string
    */
    protected function prepareDocComment($text, $lang)
    {
        $text = parent::prepareDocComment($text, $lang);
        
        //Валидатор для пользователя
        $validator = $this->getUserValidator();
        $text = preg_replace_callback('/\#data-user/', function() use($validator) {
            return $validator->getParamInfoHtml();
        }, $text);
        
        
        return $text;
    }
    
    /**
    * Возвращает валидатор для пользователя который отправляет поля для сохранения
    * 
    */
    private function getUserValidator()
    {
        if ($this->user_validator === null){
            $this->user_validator = \Users\Model\ApiUtils::getUserAddAndUpdateValidator();
        }
        return $this->user_validator;
    }

    /**
     * Обновляет сведения о пользователе, перезаписывает значения полей.
     * Данные можно обновить только у авторизованного пользователя, который получается из токена.
     *
     * @param string $token Авторизационный токен
     * @param string $client_id id клиентского приложения
     * @param string $client_secret пароль клиентского приложения
     * @param array $user поля пользователя для сохранения #data-user
     * @param array $regfields_arr поля пользователя из настроек модуля пользователь
     *
     * @example POST /api/methods/user.update?token=b45d2bc3e7149959f3ed7e94c1bc56a2984e6a86&user[name]=Супервизор%20тест%20тест&user[surname]=%20Моя%20фамилия&user[e_mail]=admin%40admin.ru&user[phone]=8(000)800-80-30&user[changepass]=0&user[is_company]=0
     *
     * <pre>
     *  {
     *      "response": {
     *            "success" : false,
     *            "errors" : ['Ошибка'],
     *            "errors_status" : 2 //Появляется, если присутствует особый статус ошибки (истекла сессия, ошибки в корзине, корзина пуста)
     *      }
     *   }</pre>
     * @return array Возращает, пустой массив ошибок, если успешно
     * @throws ApiException
     * @throws \RS\Exception
     */
    protected function process($token, $client_id, $client_secret, $user, $regfields_arr = [])
    {
        //Проверим предварительно приложение
        \ExternalApi\Model\Utils::checkAppIsRegistered($client_id, $client_secret);
        
        //Проверим поля пользователя
        $validator = $this->getUserValidator();
        $validator->validate('user', $user, $this->method_params);

        $response['response']['success'] = false; 
                                                      
        //Получим пользователя
        /**
         * @var \Users\Model\Orm\User $current_user
         */
        $current_user = $this->token->getUser();

        return \Users\Model\ApiUtils::getUserDataPostAddUpdateCheck($user, $current_user, $client_id, $this->use_post_keys, $response);
    }
}