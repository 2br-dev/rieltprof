<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace ExternalApi\Model\ExternalApi\Oauth;

use ExternalApi\Model\AbstractMethods\AbstractMethod;
use ExternalApi\Model\Exception as ApiException;
use ExternalApi\Model\Orm\AuthorizationToken;
use ExternalApi\Model\TokenApi;
use RS\Application\Auth as AppAuth;
use RS\Config\Loader;
use RS\Orm\Type\Checker;
use Users\Config\File as UsersConfigFile;
use Users\Model\Api as UserApi;
use Users\Model\Orm\User;
use Users\Model\Orm\VerificationSession;
use Users\Model\Verification\Action\TwoStepAuthorize;
use Users\Model\Verification\Action\TwoStepRegisterByPhone;
use Users\Model\Verification\VerificationEngine;
use RS\RemoteApp\Manager as RemoteAppManager;

/**
 * Авторизация, поддерживающая однофакторный и двухфакторный формат,
 * по логину(Email или Логин или Телефон) + паролю или только по номеру телефона.
 */
class Login extends AbstractMethod
{
    public $remember = true;
    /**
     * @var \Users\Config\File
     */
    protected $users_module_config;

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
     * Однофакторная или двухфакторная авторизация. Метод позволяет авторизовать пользователя по логину и паролю или по номеру телефона, в зависимости от настроек авторизации в административной панели.
     *
     * В случае, если двухфакторная авторизация не включена, то успешным ответом данного метода будет
     * сразу авторизационный токен и сведения об авторизованном пользователе.
     *
     * В случае, если двухфакторная авторизация включена, то успешным ответом данного метода будет
     * токен верификационной сессии, а также сведения о данной сессии для верификации. В этом случае
     * далее необходимо выполнить запрос на verification.checkCode для завершения авторизации.
     * Для повторной отправки кода второго фактора можно выполнить запрос на verification.sendCode
     *
     * Метод поддерживает прием параметров только методом POST.
     *
     * @param string $client_id Уникальный идентификатор приложения, которое запрашивает авторизацию пользователя
     * @param string $client_secret Секретный ключ приложения, которое запрашивает авторизацию пользователя
     * @param string $login Логин или Email или Телефон пользователя (В зависимости от настроек авторизации). Обязателен, если не передан параметр phone.
     * @param string $password Пароль пользователя в открытом виде.
     * @param string $phone Номер телефона, в случае если разрешен тип авторизации "Только по номеру телефона". Обязателен, если не передан параметр login
     * В метод следует передавать либо параметры login и password (для классической авторизации), либо поле phone (для авторизации по номеру телефона, если разрешен данный тип авторизации)
     * @param string $grant_type Необходимо по спецификации
     *
     * @example Успешный ответ прохождения первого фактора при двухфакторной авторизации.
     * Возвращаются сведения по верификационной сессии, которые нужно использоать в запросах к verification.checkCode
     *
     * POST /api/methods/oauth.login
     * client_id=myapp&client_secret=myappsecret&grant_type=password&phone=+70000000000
     * <pre>
     * {
     *   "response": {
     *       "verification": {
     *           "session": {
     *               "token": "58da0ee2c45f94e1f2c8cac87af5e346a9435a0f",
     *               "error": "",
     *               "code_send_flag": true,
     *               "code_refresh_delay": 60,
     *               "code_send_phone_mask": "700000****0",
     *               "code_debug": "1718"
     *           }
     *       },
     *       "site_uid": "91d8b18c4cc70da5e0d4a0ab8eb6186bbd1418d3"
     *       }
     *   }
     * </pre>
     * Успешный ответ при однофакторной авторизации:
     *
     * POST /api/methods/oauth.login
     * client_id=myapp&client_secret=myappsecret&grant_type=password&login=mylogin&password=mypassword
     *
     * <pre>
     * {
     *      'response': {
     *            'auth': {
     *                'token' => '38b83885448a8ad9e2fb4f789ec6b0b690d50041',
     *                'expire' => '1504785044',
     *            },
     *            'user': {
     *                "id": "1",
     *                "name": "Супервизор тест тест",
     *                "surname": " Моя фамилия",
     *                "midname": " ",
     *                "e_mail": "admin3@admin.ru",
     *                "login": "admin3@admin.ru",
     *                "phone": "+7(xxx)xxx-xx-xx",
     *                "sex": "",
     *                "subscribe_on": "0",
     *                "dateofreg": "2016-03-14 19:58:58",
     *                "ban_expire": null,
     *                "last_visit": "2016-11-09 15:29:14",
     *                "is_company": "0",
     *                "company": "",
     *                "company_inn": "",
     *                "data": [],
     *                "push_lock": null,
     *                "user_cost": null,
     *                "birthday": null,
     *                "fio": "Моя фамилия Супервизор тест тест",
     *                "groups": [
     *                    "guest",
     *                    "clients",
     *                    "supervisor"
     *                ],
     *                "is_courier": false
     *            },
     *            'site_uid' : "38b83885448a8ad9e2fb4f789ec6b0b690d50041"
     *        }
     * }
     * </pre>
     *
     * @return array Возвращает информацию об авторизованном пользователе или информацию о верификационной сессии (при двухфакторной авторизации) или ошибку
     *
     * <b>response.verification.session.token</b> - token верификационной сессии, будет необходим при отправке кода второго фактора
     * <b>response.verification.session.error</b> - ошибка, которую нужно отобразить возле формы ввода второго фактора
     * <b>response.verification.session.code_send_flag</b> - флаг, говорящий о том, что код был отправлен
     * <b>response.verification.session.code_refresh_delay</b> - количесто секунд, через которое можно повторить отправку кода
     * <b>response.verification.session.code_send_phone_mask</b> - маска нмера телефона, на который был отправлен код
     * <b>response.verification.session.code_debug</b> - отладочный код второго фактора. (будет присутствовать, если включен "демо-режим" в настройках модуля Пользователи и группы)
     *
     * <b>response.auth.token</b> - авторизационный token
     * <b>response.auth.expire</b> - срок истечения токена
     *
     * <b>response.user.id</b> - ID Пользователя
     * <b>response.user.name</b> - Имя пользователя
     * <b>response.user.surname</b> - Фамилия пользователя
     * <b>response.user.midname</b> - Отчество пользователя
     * <b>response.user.full_name</b> - Полное имя пользователя
     * <b>response.user.groups</b> - Группы, в которых состоит пользователь
     *
     * <b>response.site_uid</b> - Уникальный идентификатор сайта
     *
     * @throws ApiException
     */
    protected function process($client_id, $client_secret, $login = null, $password = null, $phone = null, $grant_type = 'password')
    {
        if ($grant_type != 'password') {
            throw new ApiException(t('Параметр grant_type может принимать только значение password'), ApiException::ERROR_WRONG_PARAM_VALUE);
        }

        if ($login == '' && $phone == '') {
            throw new ApiException(t('Ожидается параметр login или phone'), ApiException::ERROR_WRONG_PARAM_VALUE);
        }

        $this->users_module_config = Loader::byModule('users');
        $app = $this->checkApp($client_id, $client_secret);
        $site_uid = \RS\Site\Manager::getSite()->getSiteHash();
        $response = [
            'response' => [
                'site_uid' => $site_uid
            ]
        ];

        if ($phone != '') {
            $result = $this->authByPhone($phone, $client_id);
        } else {
            $result = $this->authByLogin($login, $password, $client_id);
        }

        if ($result instanceof VerificationSession) {
            $verify_session = $result;
            //Автоматически отправляем код
            if ($verify_session->getRefreshCodeDelay() == 0) {
                $verify_session->sendVerificationCode();
            }

            $response['response'] += [
                'verification' => [
                    'session' => self::makeResponseVerificationSessionData($verify_session)
                ]
            ];

        } elseif ($result instanceof User) {

            $user = $result;
            //Проверяем группу пользователя, соответствует ли она требованиям приложения
            if (!array_intersect($app->getAllowUserGroup(), $user->getUserGroups())) {
                throw new ApiException(t('Пользователь не имеет права доступа к приложению'), ApiException::ERROR_APP_ACCESS_DENIED);
            }

            $token = TokenApi::createToken($user['id'], $client_id);
            $token_data = self::makeResponseAuthTokenData($token);
            $auth_user = self::makeResponseUserData($user);

            $response['response'] += [
                'auth' => $token_data,
                'user' => $auth_user
            ];
        }

        return $response;
    }

    /**
     * Проверяет корректность параметров client_id и client_secret.
     *
     * @param string $client_id Уникальный идентификатор приложения, которое запрашивает авторизацию пользователя
     * @param string $client_secret Секретный ключ приложения, которое запрашивает авторизацию пользователя
     * @return \RS\RemoteApp\AbstractAppType Возвращает объект приложения
     * @throws ApiException
     */
    protected function checkApp($client_id, $client_secret)
    {
        $app = RemoteAppManager::getAppByType($client_id);

        if (!$app || !($app instanceof \ExternalApi\Model\App\InterfaceHasApi)) {
            throw new ApiException(t('Приложения с таким client_id не существует или оно не поддерживает работу с API'), ApiException::ERROR_BAD_CLIENT_SECRET_OR_ID);
        }

        //Производим валидацию client_id и client_secret
        if (!$app || !$app->checkSecret($client_secret)) {
            throw new ApiException(t('Приложения с таким client_id не существует или неверный client_secret'), ApiException::ERROR_BAD_CLIENT_SECRET_OR_ID);
        }

        return $app;
    }

    /**
     * Пытается авторизовать пользователя по логину и паролю.
     * Возвращает объект пользователя или верификационную сессию, если включена друхфакторная авторизация
     *
     * @param string $login
     * @param string $password
     * @param string $client_id
     * @return User|VerificationSession
     * @throws ApiException
     */
    protected function authByLogin($login, $password, $client_id)
    {
        $user = AppAuth::login($login, $password, $this->remember, false, true);
        if ($user) {
            if ($this->users_module_config->isEnabledTwoFactorAuthorization($user)) {
                // Если включена двухэтапная авторизация, то инициализируем второй фактор

                $verification_action = new TwoStepAuthorize();
                $verification_action->setRememberUser($this->remember);
                $verification_action->setAppClientId($client_id);

                $verification_engine = (new VerificationEngine())
                    ->setCreatorUserId($user['id'])
                    ->setAction($verification_action)
                    ->setPhone($user['phone']);

                $verification_engine->initializeSession();

                if (!$verification_engine->getSession()->isResolved()) {
                    return $verification_engine->getSession();
                }
            }

            //Завершаем авторизацию
            AppAuth::setCurrentUser($user);
            $success = AppAuth::onSuccessLogin($user, $this->remember);
            if ($success) {
                return $user;
            }
        }

        throw new ApiException(AppAuth::getError(), ApiException::ERROR_BAD_AUTHORIZATION);
    }

    /**
     * Пытается авторизовать пользователя по номеру телефона
     *
     * @param string $phone
     * @param string $client_id
     * @return VerificationSession Возвращает верификационную сессию
     * @throws ApiException
     */
    protected function authByPhone($phone, $client_id)
    {
        if ($this->users_module_config->type_auth != UsersConfigFile::TYPE_AUTH_PHONE) {
            throw new ApiException(t('Данный тип авторизации отключен в настройках'), ApiException::ERROR_METHOD_ACCESS_DENIED);
        }

        $check_phone_result = Checker::chkPhone(null, $phone);
        if ($check_phone_result === true) {

            $phone = UserApi::normalizePhoneNumber($phone);
            $tmp_user = User::loadByWhere([
                'phone' => $phone
            ]);

            if ($tmp_user['id']) {
                //Пробуем авторизовать пользователя
                if (AppAuth::login($tmp_user['phone'], $tmp_user['pass'], false, true, true)) {
                    $verification_action = new TwoStepAuthorize();
                } else {
                    $error = AppAuth::getError();
                }
            } elseif ($this->users_module_config->register_by_phone) {
                //Регистрация и авторизация пользвателя
                $tmp_user['phone'] = $phone;
                $verification_action = new TwoStepRegisterByPhone();
            } else {
                //Ошибка
                $error = t('Пользователь с таким номером телефона не найден');
            }

            if (isset($verification_action)) {
                $verification_action->setAppClientId($client_id);
                $verification_action->setRememberUser($this->remember);

                $verification_engine = (new VerificationEngine())
                    ->setCreatorUserId($tmp_user['id'])
                    ->setAction($verification_action)
                    ->setPhone($tmp_user['phone']);

                $verification_engine->initializeSession();
                return $verification_engine->getSession();
            }

        } else {
            $error = $check_phone_result;
        }

        throw new ApiException($error, ApiException::ERROR_BAD_AUTHORIZATION);
    }

    /**
     * Подготавливает секцию верификационной сессии для возврата через API
     *
     * @param VerificationSession $verify_session объект верификационной сессии
     * @return array
     */
    public static function makeResponseVerificationSessionData(VerificationSession $verify_session)
    {
        $verify_session_data = [
            'token' => $verify_session->getToken(),
            'error' => $verify_session->getErrorsStr(),
            'code_send_flag' => ($verify_session['send_counter'] > 0 && $verify_session['code_expire'] > time()),
            'code_refresh_delay' => $verify_session->getRefreshCodeDelay(),
        ];

        if ($verify_session_data['code_send_flag']) {
            $verify_session_data['code_send_phone_mask'] = $verify_session->getPhoneMask();
        }

        if ($verify_session['code_debug']) {
            $verify_session_data['code_debug'] = $verify_session['code_debug'];
        }

        return $verify_session_data;
    }

    /**
     * Подготавливает секцию информации о пользователе для объекта пользователя
     *
     * @param User $user Пользователь
     * @return array
     */
    public static function makeResponseUserData(User $user)
    {
        $auth_user = \ExternalApi\Model\Utils::extractOrm($user);
        $auth_user['fio']        = $user->getFio();
        $auth_user['groups']     = $user->getUserGroups();

        return $auth_user;
    }

    /**
     * Подготавливает секцию информации об авторизационном токене
     *
     * @param AuthorizationToken $token Авторизационный токен
     * @return array
     */
    public static function makeResponseAuthTokenData(AuthorizationToken $token)
    {
        return [
            'token' => $token['token'],
            'expire' => $token['expire'],
        ];
    }
}