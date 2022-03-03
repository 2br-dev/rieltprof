<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace ExternalApi\Model\ExternalApi\Oauth;
use \ExternalApi\Model\Exception as ApiException;
  
/**
* Авторизация.
*/
class Token extends \ExternalApi\Model\AbstractMethods\AbstractMethod
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
    * Метод позволяет авторизовать пользователя и получить авторизационный token.
    * Token будет обладать набором прав, необходимых для работы приложения client_id.
    * Метод поддерживает прием параметров только методом POST.
    * 
    * @param string $grant_type Необходимо по спецификации
    * 
    * @param string $client_id Уникальный идентификатор приложения, которое запрашивает авторизацию пользователя
    * @param string $client_secret Секретный ключ приложения, которое запрашивает авторизацию пользователя
    * @param string $username Логин пользователя
    * @param string $password Пароль пользователя в открытом виде 
    * 
    * @example POST /api/methods/oauth.token?grant_type=password&client_id=myapp&client_secret=myappsecret&username=demo_example.com&password=xxxxxxx
    * 
    * Ответ:
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
    * @throws ApiException
    * @return array Возвращает информацию об авторизованном пользователе или ошибку
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
    */
    protected function process($client_id, $client_secret, $username, $password, $grant_type = 'password')
    {
        if ($grant_type != 'password') {
            throw new ApiException(t('Параметр grant_type может принимать только значение password'), ApiException::ERROR_WRONG_PARAM_VALUE);
        }        
        
        $app = \RS\RemoteApp\Manager::getAppByType($client_id);
        
        if (!$app || !($app instanceof \ExternalApi\Model\App\InterfaceHasApi)) {
            throw new ApiException(t('Приложения с таким client_id не существует или оно не поддерживает работу с API'), ApiException::ERROR_BAD_CLIENT_SECRET_OR_ID);
        }
        
        //Производим валидацию client_id и client_secret
        if (!$app || !$app->checkSecret($client_secret)) {
            throw new ApiException(t('Приложения с таким client_id не существует или неверный client_secret'), ApiException::ERROR_BAD_CLIENT_SECRET_OR_ID);
        }
        
        
        
        //Авторизовываем пользователя
        if ($user = \RS\Application\Auth::login($username, $password, false, false, true)) {
            //Проверяем группу пользователя, соответствует ли она требованиям приложения
            if (!array_intersect($app->getAllowUserGroup(), $user->getUserGroups())) {
                throw new ApiException(t('Пользователь не имеет права доступа к приложению'), ApiException::ERROR_APP_ACCESS_DENIED);
            }
            
            $token = \ExternalApi\Model\TokenApi::createToken($user['id'], $client_id);
            
            $auth_user = \ExternalApi\Model\Utils::extractOrm($user);
            $auth_user['fio']        = $user->getFio();
            $auth_user['groups']     = $user->getUserGroups();
            
            return [
                'response' => [
                    'auth' => [
                        'token' => $token['token'],
                        'expire' => $token['expire'],
                    ],
                    'user' => $auth_user,
                    'site_uid' => \RS\Site\Manager::getSite()->getSiteHash()
                ]
            ];
            
        } else {
            //Возвращаем ошибку
            throw new ApiException(t(\RS\Application\Auth::getError()), ApiException::ERROR_BAD_AUTHORIZATION);
        }   
    }    
}