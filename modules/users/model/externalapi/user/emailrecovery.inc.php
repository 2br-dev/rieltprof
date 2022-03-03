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
class EmailRecovery extends \ExternalApi\Model\AbstractMethods\AbstractMethod
{
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
     * Делает запрос на восстановление пароля
     *
     * @param string $email E-mail для восстановления
     *
     * @example GET /api/methods/user.emailrecovery?email=test@test.ru
     *
     * <pre>
     *  {
     *      "response": {
     *          "success": 1,
     *          "text": "На указанный E-mail будет отправлено письмо с дальнейшими инструкциями по восстановлению пароля"
     *      }
     *  }</pre>
     *
     * @return array Возвращает объект пользователя
     * @throws ApiException
     */
    protected function process($email)
    {
        $user_api = new \Users\Model\Api();
        $success = $user_api->sendRecoverEmail($email);
        if (!$success) {
            throw new ApiException($user_api->getErrorsStr(), ApiException::ERROR_OBJECT_NOT_FOUND);
        }
        
        return [
          'response' => [
              'success' => 1,
              'text' => t('На указанный E-mail будет отправлено письмо с дальнейшими инструкциями по восстановлению пароля')
          ]
        ];
    }
}