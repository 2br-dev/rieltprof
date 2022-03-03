<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace PushSender\Model\ExternalApi\Push;
use \ExternalApi\Model\Exception as ApiException;

class RegisterToken extends \ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod
{
    const
        RIGHT_REGISTER = 1;
        
    protected
        $token_require = false,
        $client_id_validator,
        $device_validator;
        
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
            self::RIGHT_REGISTER => t('Регистрация Push токена')
        ];
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
        $validator = $this->getDeviceValidator();
        $text = preg_replace_callback('/\#data-device/', function() use($validator) {
            return $validator->getParamInfoHtml();
        }, $text);
        

        return $text;
    }


    /**
     * Возвращает валидатор для id клиентского приложения
     *
     * @return \ExternalApi\Model\Validator\ValidateArray
     */
    private function getClientIdValidator()
    {
        if ($this->client_id_validator === null){
            $this->client_id_validator = new \ExternalApi\Model\Validator\ValidateArray([
                '@validate' => function($client_id, $all_parameters) {
                    if (empty($client_id)){
                        return t("Не передан параметр client_id.");
                    }
                    return true;
                }
            ]);
        }
        return $this->client_id_validator;
    }

    /**
     * Возвращает валидатор для проверки полей со сведения обустройстве
     *
     * @return \ExternalApi\Model\Validator\ValidateArray
     */
    private function getDeviceValidator()
    {
        if ($this->device_validator === null){
            $this->device_validator = new \ExternalApi\Model\Validator\ValidateArray([
                'uuid' => [
                    '@require' => true,
                    '@title' => t('Уникальный id устройства. Обязателен, если отсутствует авторизационный токен'), 
                    '@type' => 'string', 
                    '@validate_callback' => function($uuid, $full_data) {
                        if (empty($uuid) && !$this->token){ //Если нет токен и уникальный идентификатор устройства не передан, то выдадим ошибку
                            return "Не передан уникальный идентификатор устройства (uuid).";
                        }
                        return true;
                    }
                ],
                'model' => [
                    '@title' => t('Модель устройства'), 
                    '@type' => 'string',
                ],
                'manufacturer' => [
                    '@title' => t('Производитель устройства'), 
                    '@type' => 'string',
                ],
                'platform' => [
                    '@title' => t('Платформа на которой работает устройство'), 
                    '@type' => 'string',
                ],
                'version' => [
                    '@title' => t('Версия платформы системы'), 
                    '@type' => 'string',
                ],
                'cordova' => [
                    '@title' => t('Версия плагина codova. Может отсутствовать.'), 
                    '@type' => 'string',
                ],
            ]);
        }
        return $this->device_validator;
    }

    /**
     * Регистрирует Push токен. С помощью данного токена потом можно будет отправлять пользователю Push уведомления.
     * Зарегистрировать push token можно также передав его в метод oauth.token в параметр &custom[push_token]=..... совместно с другими сведениями авторизации
     *
     * @param string $push_token Токен, полученный при регистрации устройства в одном из Push сервисов, например Firebase Cloud Messaging
     * @param string $token Авторизационный токен
     * @param string $client_id id клиентского приложения. Если авторизационный токен передан, то передавать его не нужно. Если нет. то он обязателен.
     * @param array $device массив сведений об устройстве. Если не передан авторизационный токен, то некоторые поля обязательны. #data-device
     * @param integer $update_user_id флаг означающий, что нужно принудительно перезаписать уже установленного пользователя.
     *
     * @example GET /api/methods/push.registerToken?token=f49d5fcd051aa917e8d3b37e112a6226d0bec863&push_token=2a6226d0bec863...
     *
     * GET /api/methods/push.registerToken?token=f49...6226d0bec863&push_token=2a6226d0bec863...&client_id=mobilesiteapp&device[uuid]=dfnm345...45656&device[model]=Galaxy Note&device[manufacturer]=Samsung&device[platform]=Android
     *
     * Ответ
     * <pre>
     * {
     *     "response": {
     *         "success": true
     *     }
     * }
     * </pre>
     * Возвращает информацию об успешной записи или ошибку
     * @return array
     * @throws \RS\Orm\Exception
     * @throws \ExternalApi\Model\Exception
     */
    protected function process($push_token, $token = null, $client_id = null, $device = [], $update_user_id = null)
    {
        $response['response']['success'] = true;
        $user_id = null;
        if ($this->token){ //Если токен передан
            $client_id = $this->token->app_type;
            $user_id   = $this->token->user_id;
        }else{
            if (empty($client_id)){
               $validator = $this->getClientIdValidator();
               $validator->validate('client_id', $client_id, $this->method_params); 
            }
            //Предварительные проверки параметров
            $validator = $this->getDeviceValidator();
            $validator->validate('device', $device, $this->method_params);
        }

        /**
         * @var \PushSender\Model\Orm\PushToken $token
         */
        $token = \PushSender\Model\PushTokenApi::registerUserToken($push_token, $client_id, $user_id, $device, $update_user_id);
        if (!$token['id']) {
            throw new ApiException($token->getErrorsStr(), ApiException::ERROR_WRONG_PARAM_VALUE);
        } 
        
        return $response;
    }
}