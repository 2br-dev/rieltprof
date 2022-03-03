<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Model;

use RS\Application\Auth;
use RS\Config\Loader;
use RS\Module\AbstractModel\BaseModel;
use Users\Model\Orm\User;
use Users\Model\Orm\UserGroup;

/**
 * Класс отвечает за работу функций удаленной
 * авторизации технической поддержки ReadyScript
 */
class RemoteSupportApi extends BaseModel
{
    /**
     * Создает при необходимости пользователя - техническая
     * поддержка, авторизовывает его
     *
     * @return bool Возвращает true,в случае успеха, иначе - false. Текст ошибки можно получить через getErrorsStr
     */
    public function login()
    {
        $is_enabled = Loader::byModule($this)->enable_remote_support;

        if (!$is_enabled) {
            return $this->addError(t('Функция отключена'));
        }

        $user = $this->getSupportUser();
        if ($user) {
            Auth::logout();
            Auth::setCurrentUser($user);
            return true;
        }

        return false;
    }

    /**
     * Удаляет пользователя поддержки
     *
     * @return bool Возвращает true,в случае успеха, иначе - false. Текст ошибки можно получить через getErrorsStr
     */
    public function logout()
    {
        $is_enabled = Loader::byModule($this)->enable_remote_support;

        if (!$is_enabled) {
            return $this->addError(t('Функция отключена'));
        }

        $user = $this->getSupportUser(false);
        if ($user) {
            Auth::logout();
            if ($user->delete()) {
                return true;
            } else {
                return $this->addError($user->getErrorsStr());
            }
        }

        return false;
    }

    /**
     * Возвращает объект пользователя технической поддержки
     *
     * @return User | bool(false)
     */
    private function getSupportUser($create = true)
    {
        $data = [
            'name' => t('Техническая поддержка ReadyScript'),
            'company' => 'ReadyScript lab.'
        ];

        $user = User::loadByWhere($data);

        $user->getFromArray($data);
        $user['no_send_notice'] = true;
        $user['no_validate_userfields'] = true;

        //Изменяем пользователю логин, пароль, email - всегда
        $user['email'] = substr(md5(uniqid(time(), true)), 0, 20).'@example.com';
        $user['login'] = $user['email'];
        $user['openpass'] = sha1(uniqid(time(), true).\Setup::$SECRET_KEY);
        $user['changepass'] = 1;
        $user['groups'] = [UserGroup::GROUP_SUPERVISOR];

        if ($user['id']) {
            $result = $user->update();
        } elseif ($create) {
            $result = $user->insert();
        } else {
            return $this->addError(t('Пользователя технической поддержки не существует'));
        }

        if (!$result) {
            return $this->addError($user->getErrorsStr());
        }

        return $user;
    }
}