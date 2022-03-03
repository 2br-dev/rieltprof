<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Export\Controller\Admin;

use Export\Model\Orm\ExportProfile;
use RS\Router\Manager;

/**
 * Обрабатывает входящий запрос от ВК, для получения accessToken из code
 */
class OauthVK extends \RS\Controller\Admin\Front
{

    /**
     * Производим запись токена в профиль экспорта данных
     *
     * @throws \RS\Event\Exception
     */
    function actionSetApi()
    {
        $code = $this->url->get('code', TYPE_STRING);
        $profile_id = $this->url->get('profile_id', TYPE_INTEGER);

        $profile = new ExportProfile($profile_id);
        $profile_type = $profile->getTypeObject();
        $data = $profile['data'];


        $router = Manager::obj();
        $url = $router->getAdminUrl('SetApi', ['profile_id' => $profile_id], 'export-oauthvk', true);

        if ($profile['id']) {

            $params = [
                'code' => $code,
                'client_id' => $profile_type->getAppClientId(),
                'client_secret' => $profile_type->getAppSecretKey(),
                'redirect_uri' => $url,
            ];

            $response = file_get_contents('https://oauth.vk.com/access_token?' . http_build_query($params));
            if ($response) {
                $token = @json_decode($response, true);
                if ($token && isset($token['access_token'])) {
                    $access_token = $token['access_token'];

                    $data['access_token'] = $access_token;
                    $profile['data'] = $data;
                    $profile->update();

                    $this->app->redirect($this->router->getAdminUrl(false, [], 'export-ctrl'));
                }
            }
        }
    }
}