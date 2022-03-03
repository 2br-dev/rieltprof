<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Controller\Admin;

use Crm\Model\Telephony\Manager;
use RS\Controller\Admin\Front;

/**
 * Контроллер, обрабатывающие запросы Телфина в административной панели
 */
class TelphinCtrl extends Front
{
    /**
     * Проверяет авторизацию на сервисе Телфин
     *
     * @return \RS\Controller\Result\Standard
     */
    public function actionCheckAuth()
    {
        $provider = $this->url->get('provider', TYPE_STRING);
        $telphin_app_id = $this->url->post('telphin_app_id', TYPE_STRING);
        $telphin_secret_key = $this->url->post('telphin_secret_key', TYPE_STRING);

        $provider_object = Manager::getProviderById($provider);
        $access_token = $provider_object->getAccessToken([
            'client_id' => $telphin_app_id,
            'client_secret' => $telphin_secret_key
        ],true);

        if ($access_token === false) {
            return $this->result->addEMessage($provider_object->getLastError());
        } else {
            return $this->result->addMessage(t('Авторизация прошла успешно, получен токен: %token', [
                'token' => $access_token
            ]));
        }
    }

    /**
     * Устанавливает event URL для всех пользователей, которые указаны в административной панели
     *
     * @return \RS\Controller\Result\Standard
     */
    public function actionSetEventUrl()
    {
        $provider = $this->url->get('provider', TYPE_STRING);
        $telphin_app_id = $this->url->post('telphin_app_id', TYPE_STRING);
        $telphin_app_secret = $this->url->post('telphin_app_secret', TYPE_STRING);

        $tel_secret_key = $this->url->post('tel_secret_key', TYPE_STRING);
        $telphin_user_map = $this->url->post('telphin_user_map', TYPE_ARRAY);

        $provider_object = Manager::getProviderById($provider);
        $provider_object->setUrlSecret($tel_secret_key);

        $result = $provider_object->setEventUrl(
            $telphin_app_id,
            $telphin_app_secret,
            $telphin_user_map
        );

        if (!$result) {
            return $this->result->setSuccess(false)->addEMessage($provider_object->getLastError());
        } else {
            return $this->result->setSuccess(true)->addMessage(t('Успешно обновлены адреса добавочных: %ids', [
                'ids' => implode(', ', $result)
            ]));
        }
    }

}