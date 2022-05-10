<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Controller\Front;

use RS\Application\Auth as AppAuth;
use RS\Controller\Front;
use RS\Helper\Tools as HelperTools;
use RS\Orm\Type;
use RS\Site\Manager as SiteManager;
use Users\Model\Orm\User;

class Register extends Front
{
    /** Поля, которые следует ожидать из POST */
    public $use_post_keys = ['is_company', 'company', 'company_inn', 'fio', 'name', 'login',
        'surname', 'midname', 'phone', 'e_mail', 'openpass', 'openpass_confirm', 'captcha', 'data'];

    function actionIndex()
    {
        $this->app->breadcrumbs->addBreadCrumb(t('Регистрация'));

        $this->app->title->addSection(t('Регистрация'));
        $this->app->meta->addDescriptions(t('На этой странице вы можете зарегистрироваться'));

        $referer = $this->url->request('referer', TYPE_STRING, SiteManager::getSite()->getRootUrl());
        $referer = HelperTools::cleanOpenRedirect(urldecode($referer));

        $user = $this->getUserForRegistration();

        if ($this->isMyPost()) {
            $user['changepass'] = 1;

            if ($user->save()) {
                //Если пользователь уже зарегистрирован
                AppAuth::setCurrentUser($user);
                if (AppAuth::onSuccessLogin($user)) {

                    return $this->result
                        ->addSection('closeDialog', true)
                        ->addSection('reloadPage', true)
                        ->setNoAjaxRedirect($referer);
                } else {
                    $user->addError(AppAuth::getError());
                }
            }
        }

        //Не передаем пароль в открытом виде в браузер
        $user['openpass'] = $user['openpass_confirm'] = '';

        $this->view->assign([
            'conf_userfields' => $user->getUserFieldsManager(),
            'user' => $user,
            'referer' => urlencode($referer)
        ]);

        return $this->result->setTemplate('register.tpl');
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
        }

        $user->enableOpenPassConfirm();
        $user->enableRegistrationCheckers();

        return $user;
    }
}
