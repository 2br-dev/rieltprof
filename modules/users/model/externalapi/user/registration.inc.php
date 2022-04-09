<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Model\ExternalApi\User;
use ExternalApi\Model\AbstractMethods\AbstractMethod;
use \ExternalApi\Model\Exception as ApiException;
use ExternalApi\Model\TokenApi;
use ExternalApi\Model\Utils;
use RS\Application\Auth as AppAuth;
use RS\Config\Loader;
use Site\Model\Orm\Config;
use Users\Model\Orm\User;
use Users\Model\Orm\VerificationSession;
use Users\Model\Verification\Action\TwoStepAuthorize;
use Users\Model\Verification\VerificationEngine;

/**
* Регистрация пользователя в системе
*/
class Registration extends AbstractMethod
{
    /** Поля, которые следует ожидать из POST */
    public $use_post_keys = ['is_company', 'company', 'company_inn', 'fio', 'phone', 'e_mail', 'openpass', 'openpass_confirm', 'captcha', 'data'];

    /**
     * Возвращает какими методами могут быть переданы параметры для данного метода API
     *
     * @return array
     */
    /*public function getAcceptRequestMethod()
    {
        return [POST];
    }*/

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
        $validator = \Users\Model\ApiUtils::getUserRegistrationValidator();
        $text = preg_replace_callback('/\#data-user/', function() use($validator) {
            return $validator->getParamInfoHtml();
        }, $text);


        return $text;
    }

    /**
     *  Регистрирует нового пользователя в системе
     *
     *  Метод поддерживает прием параметров только методом POST.
     *
     * @param string $client_id id клиентского приложения
     * @param string $client_secret пароль клиентского приложения
     * @param array $user поля пользователя для сохранения #data-user
     *
     * @example POST /api/methods/user.registration?client_id=myapp&client_secret=myappsecret&user[fio]=Иванов Иван Иванович&user[phone]=880008008030&user[e_mail]=admin@admin.ru&user[openpass]=pass123&user[openpass_confirm]=pass123...
     *
     * В случае успешной регистрации:
     *
     * <pre>
     *   {
     *       "response": {
     *           "success": true,
     *           "auth": {
     *               "token": "ff22086b9e8c2c2fad406e04265df1c5a8ddc124",
     *               "expire": 1678458003
     *           },
     *           "user": {
     *               "id": 9,
     *               "name": "Иван",
     *               "surname": "Иванов",
     *               "midname": "Иванович",
     *               "e_mail": "admin@admin.ru",
     *               "login": null,
     *               "phone": "880008008030",
     *               "sex": null,
     *               "subscribe_on": null,
     *               "dateofreg": "2022-03-10 17:20:03",
     *               "ban_expire": null,
     *               "last_visit": null,
     *               "last_ip": null,
     *               "is_enable_two_factor": null,
     *               "is_company": null,
     *               "company": null,
     *               "company_inn": null,
     *               "data": [],
     *               "desktop_notice_locks": null,
     *               "user_cost": null,
     *               "allow_api_methods": null,
     *               "push_lock": null,
     *               "manager_user_id": null,
     *               "basket_min_limit": null,
     *               "source_id": null,
     *               "date_arrive": null,
     *               "openpass_confirm": "pass123",
     *               "fio": "Иванов Иван Иванович",
     *               "groups": [
     *                   "guest",
     *                   "clients"
     *               ]
     *           }
     *       }
     *   }</pre>
     *
     * В случае ошибки:
     *
     *  <pre>
     *  {
     *      "response": {
     *          "success": false,
     *          "errors": [
     *              "Такой e-mail уже занят",
     *              "Такой телефон уже занят"
     *          ]
     *      }
     *  }</pre>
     *
     * @return array Возвращает информацию об авторизованном пользователе или ошибку
     * @throws ApiException
     * @throws \RS\Exception
     */
    protected function process($client_id, $client_secret, $user)
    {
        Utils::checkAppIsRegistered($client_id, $client_secret);

        $current_user = $this->getUserForRegistration();

        $current_user->checkData($user);
        $current_user['changepass'] = 1;

        if (!$current_user->hasError() && $current_user->save($current_user['id'])) {
            $response['response']['success'] = true;

            //Выпишем новый токен под пользователя
            $token = TokenApi::createToken($current_user['id'], $client_id);

            $auth_user           = Utils::extractOrm($current_user);

            //Не передаем пароль в открытом виде в ответе
            $auth_user['openpass'] = $auth_user['openpass_confirm'] = '';

            $auth_user['fio']    = $current_user->getFio();
            $auth_user['groups'] = $current_user->getUserGroups();

            $response['response']['auth']['token']  = $token['token'];
            $response['response']['auth']['expire'] = $token['expire'];
            $response['response']['user']           = $auth_user;
        }else{
            $response['response']['success'] = false;
            $response['response']['errors'] = $current_user->getErrors();;
        }


        return $response;
    }

    /**
     * Возвращает объект пользователя с включенными необходимыми чекерами для валидации при регистрации
     *
     * @param User $user
     * @return User
     */
    private function getUserForRegistration()
    {
        $user = new User();
        $user->usePostKeys($this->use_post_keys);

        //Включаем капчу
        if (!$user['__phone']->isEnabledVerification()) {
            $user['__captcha']->setEnable(true);

            //Установит капчу по умолчанию "ReadyScript "Стандарт""
            $site_config = Loader::getSystemConfig();
            $site_config['captcha_class'] = 'RS-default';
        }

        $user->enableOpenPassConfirm();
        $user['__fio']->setChecker([User::class, 'checkFioField']);
        $user['__name']->removeAllCheckers();
        $user['__surname']->removeAllCheckers();
        $user['__midname']->removeAllCheckers();

        return $user;
    }

}