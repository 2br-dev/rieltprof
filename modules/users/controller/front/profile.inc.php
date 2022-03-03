<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Controller\Front;

use RS\Application\Auth as AppAuth;
use RS\Config\Loader as ConfigLoader;
use RS\Controller\AuthorizedFront;
use RS\Helper\Tools as HelperTools;
use RS\Orm\Type;
use RS\Theme\Item as ThemeItem;
use Users\Model\Orm\User;
use Users\Model\Verification\Action\TwoStepProfile;

class Profile extends AuthorizedFront
{
    /** Поля, которые следует ожидать из POST */
    public $use_post_keys = [
        'is_company', 'company', 'company_inn', 'name', 'surname', 'midname', 'sex', 'passport', 'phone', 'e_mail',
        'openpass', 'current_pass', 'openpass_confirm', 'captcha', 'data', 'changepass', 'birthday'
    ];

    /**
     * Страница - профиль пользователя
     *
     * @return \RS\Controller\Result\Standard
     */
    public function actionIndex()
    {
        $this->app->title->addSection(t('Профиль'));
        $this->app->breadcrumbs->addBreadCrumb(t('Профиль пользователя'));

        $user = $this->getUserForProfile();

        if ($this->isMyPost() && $this->url->checkCsrf()) {

            if ($user->save($user['id'])) {
                $_SESSION['user_profile_result'] = t('Изменения сохранены');
                AppAuth::setCurrentUser($user); //Обновляем в пользователя в текущей сессии
                $this->refreshPage();
            }

        }

        //Не отправляем пароль в браузер
        $user['current_pass'] = '';
        $user['openpass'] = '';
        $user['openpass_confirm'] = '';

        $this->view->assign([
            'conf_userfields' => $user->getUserFieldsManager(),
            'user' => $user
        ]);

        if (isset($_SESSION['user_profile_result'])) {
            $this->view->assign('result', $_SESSION['user_profile_result']);
            unset($_SESSION['user_profile_result']);
        }

        return $this->result->setTemplate('profile.tpl');
    }

    /**
     * Возвращает объект пользователя, подготовленный для редактирования профиля
     *
     * @return User
     */
    protected function getUserForProfile()
    {
        $user = clone AppAuth::getCurrentUser();
        $user->usePostKeys($this->use_post_keys);

        //Добавим объекту пользователя 2 виртуальных поля
        $user->getPropertyIterator()->append([
            'current_pass' => new Type\Varchar([
                'name' => 'current_pass',
                'maxLength' => '100',
                'description' => t('Текущий пароль'),
                'runtime' => true,
                'Attr' => [['size' => '20', 'type' => 'password', 'autocomplete' => 'off']],
                'checker' => [function($user, $value) {
                    if ($user['changepass'] && $user->cryptPass($value) !== $user['pass']) {
                        return t('Неверно указан текущий пароль');
                    }
                    return true;
                }]
            ]),

            'openpass_confirm' => new Type\Varchar([
                'name' => 'openpass_confirm',
                'maxLength' => '100',
                'description' => t('Повтор пароля'),
                'runtime' => true,
                'Attr' => [['size' => '20', 'type' => 'password', 'autocomplete' => 'off']],
                'checker' => [function($user, $value) {
                    if ($user['changepass'] && strcmp($user['openpass'], $user['openpass_confirm']) != 0) {
                        return t('Пароли не совпадают');
                    }
                    return true;
                }]
            ]),
        ]);

        //Отмечаем номер телефона, который указан у пользователя - как подтвержденный
        $user['__phone']->setVerifiedPhone($user['phone']);
        $user['__phone']->setVerificationAction(new TwoStepProfile());

        return $user;
    }
}
