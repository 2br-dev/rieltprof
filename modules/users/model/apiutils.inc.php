<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Model;
use \RS\Orm\Type,
    \RS\Helper\CustomView;

/**
* Класс содержит API функции дополтельные для работы в системе в рамках задач по модулю пользователя
*/
class ApiUtils
{
    /**
    * Возвращает секцию с дополнительными полями пользователя из конфига для внешнего API
    * 
    */
    public static function getAdditionalUserFieldsSection()
    {
        //Добавим доп поля для пользователя для регистрации
        $reg_fields_manager = \RS\Config\Loader::byModule('users')->getUserFieldsManager();
        $reg_fields_manager->setErrorPrefix('regfield_');
        $reg_fields_manager->setArrayWrapper('regfields');
        
        //Пройдёмся по полям
        $fields = [];
        foreach ($reg_fields_manager->getStructure() as $field){
            if ($field['type'] == 'bool'){  //Если тип галочка
                $field['val'] = $field['val'] ? true : false;    
            }
            $fields[] = $field;
        }
        
        return $fields;
    }

    /**
     * Возвращает дополнительные параметры отображения для пользователя
     * Необходимо возвращать массив
     * [
     *    [
     *      'title' => 'Баланс',
     *      'value' => '320 p.'
     *    ]
     * ]
     *
     */
    public static function getAdditionalUserInfoFieldsSection()
    {
        $user_info = [];

        //Добавим сведения по лицевому счету
        if (\RS\Module\Manager::staticModuleExists('shop') && \RS\Application\Auth::isAuthorize()){
            $config = \RS\Config\Loader::byModule('shop');

            $user = \RS\Application\Auth::getCurrentUser();
            if ($config['use_personal_account']){
                $user_info[] = [
                    'title' => t('Баланс'),
                    'value' => $user->getBalance(true, true)
                ];
            }
        }

        return $user_info;
    }

    /**
     * Возвращает валидатор для добавления и обновления пользователя
     *
     * @return \ExternalApi\Model\Validator\ValidateArray
     */
    public static function getUserAddAndUpdateValidator()
    {
        return new \ExternalApi\Model\Validator\ValidateArray([
            'is_company' => [
                '@title' => t('Является ли клиент компанией?'),
                '@require' => true,
                '@type' => 'integer'
            ],
            'company' => [
                '@title' => t('Название компании. Только если, стоит ключ is_company.'),
                '@type' => 'string',
                '@validate_callback' => function($is_company, $full_data) {
                    if (isset($full_data['is_company']) && $full_data['is_company']){
                        return "Название компании обязательное поле.";
                    }
                    return true;
                }
            ],
            'company_inn' => [
                '@title' => t('ИНН компании. Только если, ключ is_company.'),
                '@type' => 'string',
                '@validate_callback' => function($is_company, $full_data) {
                    if (isset($full_data['is_company']) && $full_data['is_company']){
                        return "ИНН компании обязательное поле.";
                    }
                    return true;
                }
            ],
            'surname' => [
                '@title' => t('Фамилия.'),
                '@type' => 'string',
                '@require' => true,
            ],
            'name' => [
                '@title' => t('Имя.'),
                '@type' => 'string',
                '@require' => true,
            ],
            'midname' => [
                '@title' => t('Отчество.'),
                '@type' => 'string',
            ],
            'phone' => [
                '@title' => t('Телефон.'),
                '@type' => 'string',
                '@require' => true,
            ],
            'e_mail' => [
                '@title' => t('E-mail.'),
                '@type' => 'string',
                '@require' => true,
            ],
            'changepass' => [
                '@title' => t('Нужно ли сменить пароль? 0 или 1.'),
                '@type' => 'integer'
            ],
            'pass' => [
                '@title' => t('Текущий пароль. Только если, changepass=1'),
                '@type' => 'string',
                '@validate_callback' => function($is_company, $full_data) {
                    if (isset($full_data['changepass']) && $full_data['changepass']){
                        return "Текущий пароль обязательное поле.";
                    }
                    return true;
                }
            ],
            'openpass' => [
                '@title' => t('Повтор открытого пароля. Только если, changepass=1'),
                '@type' => 'string',
                '@validate_callback' => function($is_company, $full_data) {
                    if (isset($full_data['changepass']) && $full_data['changepass']){
                        return "Повтор открытого пароля обязательное поле.";
                    }
                    return true;
                }
            ],
            'openpass_confirm' => [
                '@title' => t('Повтор открытого пароля. Только если, changepass=1'),
                '@type' => 'string',
                '@validate_callback' => function($is_company, $full_data) {
                    if (isset($full_data['changepass']) && $full_data['changepass']){
                        return "Повтор открытого пароля обязательное поле.";
                    }
                    return true;
                }
            ]
        ]);
    }

    /**
     * Возвращает массив данных ответа после проверки данных пользователя для создания и обновления пользователя
     *
     * @param array $data - массив данных пользователя
     * @param \Users\Model\Orm\User $current_user - текущий пользователь
     * @param string $client_id - идентификатор клиентского приложения
     * @param array $use_post_keys - массив полей POST для проверки
     * @param array $response - массив ответа
     *
     * @return array
     * @throws \RS\Exception
     */
    public static function getUserDataPostAddUpdateCheck($data, \Users\Model\Orm\User $current_user, $client_id, $use_post_keys, $response)
    {
        $errors = [];
        $current_user->usePostKeys($use_post_keys);

        $current_user->checkData($_POST['user']);

        //Изменяем пароль
        if ($data['changepass']) {
            $current_pass = $data['pass'];
            $crypt_current_pass = $current_user->cryptPass($current_pass);
            if ($crypt_current_pass === $current_user['pass']) {
                $current_user['pass'] = $crypt_current_pass;
            } else {
                $current_user->addError(t('Неверно указан текущий пароль'), 'pass');
            }

            $password = $data['openpass'];
            $password_confirm = $data['openpass_confirm'];

            if (strcmp($password, $password_confirm) != 0) {
                $current_user->addError(t('Пароли не совпадают'), 'openpass');
            }
        }

        if (!$current_user->hasError() && $current_user->save($current_user['id'])) {
            $_SESSION['user_profile_result'] = t('Изменения сохранены');
            $response['response']['success'] = true;

            //Выпишем новый токен под пользователя
            $token = \ExternalApi\Model\TokenApi::createToken($current_user['id'], $client_id);

            $auth_user           = \ExternalApi\Model\Utils::extractOrm($current_user);
            $auth_user['fio']    = $current_user->getFio();
            $auth_user['groups'] = $current_user->getUserGroups();

            $response['response']['auth']['token']  = $token['token'];
            $response['response']['auth']['expire'] = $token['expire'];
            $response['response']['user']           = $auth_user;
        }else{
            $errors = $current_user->getErrors();
        }
        $response['response']['errors'] = $errors;

        return $response;
    }
}