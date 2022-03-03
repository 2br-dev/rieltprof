<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Model\Verification\Action;

use RS\Application\Auth as AppAuth;
use RS\Config\Loader;
use RS\Controller\Result\Standard as StandardResult;
use RS\Helper\Tools;
use RS\Orm\Request;
use Users\Model\Orm\User;
use Users\Model\Verification\VerificationException;

/**
 * Класс описывает действие, выполняемое после верификации
 * кода при авторизации по номеру телефона, когда требуется регистрация пользователя
 */
class TwoStepRegisterByPhone extends TwoStepAuthorize
{

    /**
     * Метод вызывается при успешном прохождении верификации
     *
     * @return StandardResult
     * @throws VerificationException
     */
    public function resolve()
    {
        $session = $this->getVerificationSession();
        $user_config = Loader::byModule($this);

        $user = new User();
        $user['phone'] = $session['phone'];
        $user['login'] = $this->generateLogin();
        $user['name'] = $this->generateName();
        $user['openpass'] = Tools::generatePassword($user_config['generate_password_length'], $user_config['generate_password_symbols']);
        $user['changepass'] = true;
        $user['e_mail'] = null;
        $user['is_company'] = 0;


        if (!$user->insert()) {
            throw new VerificationException($user->getErrorsStr());
        }

        $authorized_user = AppAuth::login($user['phone'], $user['pass'], $this->data['remember'], true);
        if (!$authorized_user) {
            throw new VerificationException(AppAuth::getError());
        }

        return $this->successResult();
    }


    /**
     * Возвращает гарантированно уникальный логин для пользователя
     *
     * @return string
     */
    protected function generateLogin()
    {
        $q = Request::make()
            ->from(new User());

        do {
            $login = 'user-'.strtolower(Tools::generatePassword(8));
            $q->where = '';
            $count = $q->where(['login' => $login])->count();
        } while($count > 0);

        return $login;
    }

    /**
     * Возвращает гарантированно уникальное имя для пользователя
     *
     * @return string
     */
    protected function generateName()
    {
        $q = Request::make()
            ->from(new User());

        do {
            $name = t('Пользователь-').Tools::generatePassword(6, range(0,9));
            $q->where = '';
            $count = $q->where(['name' => $name])->count();
        } while($count > 0);

        return $name;
    }


    /**
     * Возвращает название операции в родительном падеже
     * Например (код для): авторизации, ргистрации...
     *
     * @return string
     */
    public function getRpTitle()
    {
        return t('регистрации');
    }
}