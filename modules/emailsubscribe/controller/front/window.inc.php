<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace EmailSubscribe\Controller\Front;

use EmailSubscribe\Model\Api;
use RS\Config\Loader as ConfigLoader;
use RS\Controller\Front;
use RS\Controller\Result\Standard;

/**
 * Контроллер отвечает за окно подписки
 */
class Window extends Front
{
    /**
     * Показывает окно подключения к рассылке
     *
     * @return Standard
     */
    public function actionIndex()
    {
        $errors = [];
        $api = new Api();

        if ($this->isMyPost()) {
            $email = trim($this->request('email', TYPE_STRING));
            $code = $this->request('code', TYPE_INTEGER);

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { //Проверим E-mail
                $errors[] = t("Укажите правильный E-mail");
            }
            if (empty($errors)) { //Если ошибок нет, то отправим E-mail
                if ($api->checkEasyCaptcha($code)) {
                    if (!$api->checkEmailPresent($email)) {
                        $config = ConfigLoader::byModule($this);
                        if ($config['send_confirm_email']) {
                            $api->sendSubscribeToEmail($email);
                            $this->view->assign([
                                'success' => t('На Ваш E-mail отправлено письмо с дальнейшей инструкцией для подтверждения подписки')
                            ]);
                        } else {
                            $this->view->assign([
                                'success' => t('Спасибо! Вы успешно подписаны на рассылку')
                            ]);
                        }
                    } else {
                        $errors[] = t("Ваш E-mail (%0) уже присутствует в списке подписчиков", [$email]);
                    }
                } else {
                    $errors[] = t("Не пройдена проверка от роботов, повторите попытку");
                }
            }
        }
        //Запишем в куку, то что мы показали окно подписки
        if (!$this->url->cookie('subscribe_is_shown', TYPE_BOOLEAN)) {
            $this->app->headers->addCookie('subscribe_is_shown', 1, time() + 60 * 60 * 24 * 30 * 60, "/");
        }

        $this->view->assign([
            'errors' => $errors,
            'easy_captcha_html' => $api->getEasyCaptchaInput()
        ]);
        return $this->result->setTemplate('window.tpl');
    }

    /**
     * Активирует E-mail для подписки
     *
     * @return Standard
     */
    public function actionActivateEmail()
    {
        $errors = [];
        $signature = $this->request('signature', TYPE_STRING);
        $api = new Api();

        if ($api->activateEmailBySignature($signature)) {
            $this->view->assign('success', t('Спасибо! Вы успешно подписаны на рассылку.'));
        } else {
            $errors[] = t('Вы уже активировали E-mail ранее или данного E-mail не существует');
        }

        $this->view->assign([
            'errors' => $errors
        ]);
        return $this->result->setTemplate('activate.tpl');
    }

    /**
     * Деактивирует E-mail для подписки по E-mail
     *
     * @return Standard
     */
    public function actionDeActivateEmail()
    {
        $errors = [];
        $email = $this->request('email', TYPE_STRING);
        $api = new Api();

        if ($api->deactivateEmailByEmail($email)) {
            $this->view->assign('success', t('Спасибо! Вы отписаны от рассылки.'));
        } else {
            $errors[] = t('Активный E-mail в базе не найден');
        }

        $this->app->headers->removeCookie('subscribe_is_shown');

        $this->view->assign([
            'errors' => $errors
        ]);
        return $this->result->setTemplate('deactivate.tpl');
    }
}
