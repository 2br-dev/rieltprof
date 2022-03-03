<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace EmailSubscribe\Controller\Block;

use EmailSubscribe\Model\Api;
use RS\Config\Loader as ConfigLoader;
use RS\Controller\StandartBlock;

/**
 * Блок контроллер - Форма подписки на рассылку
 */
class SubscribeButton extends StandartBlock
{
    protected static $controller_title = 'Подписка на рассылку';
    protected static $controller_description = 'Отображает блок подписки на рассылку';

    protected $default_params = [
        'indexTemplate' => 'blocks/button/button.tpl', //Должен быть задан у наследника
    ];

    public function actionIndex()
    {
        $errors = [];
        $api = new Api();
        if ($this->isMyPost()) { //Если E-mail передан
            $email = $this->request('email', TYPE_STRING, false);
            $code = $this->request('code', TYPE_INTEGER);

            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                if ($api->checkEasyCaptcha($code)) {
                    if (!$api->checkEmailPresent($email)) {
                        $config = ConfigLoader::byModule($this);
                        $api->sendSubscribeToEmail($email);
                        if ($config['send_confirm_email']) {
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
            } else {
                $errors[] = t('Укажите правильный E-mail');
            }
        }
        $this->view->assign([
            'errors' => $errors,
            'easy_captcha_html' => $api->getEasyCaptchaInput()
        ]);
        return $this->result->setTemplate($this->getParam('indexTemplate'));
    }
}
