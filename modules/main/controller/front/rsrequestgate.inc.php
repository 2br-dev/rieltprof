<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Main\Controller\Front;

use Main\Model\Orm\License;
use Main\Model\RemoteSupportApi;
use Main\Model\WallPostApi;
use RS\Controller\ExceptionPageNotFound;
use RS\Controller\Front;
use RS\Db\Exception as DbException;
use RS\Exception as RSException;
use RS\Orm\Exception as OrmException;
use RS\Orm\Request as OrmRequest;
use RS\Site\Manager as SiteManager;

/**
 * Контроллер обеспечивает ответы на входящие запросы от ReadyScript
 * Используется для работы сервисной инфраструктуры ReadyScript
 */
class RsRequestGate extends Front
{
    /**
     * @throws ExceptionPageNotFound
     */
    function init()
    {
        $action = strtolower($this->getAction());

        //Сверяем подпись домена, к которому производится запрос
        $domain_sign = base64_decode($this->url->request('domain_sign', TYPE_STRING));
        $domain = preg_replace('/^www\./', '', strtolower($this->url->server('HTTP_HOST')));

        $pubkeyfile = \Setup::$PATH . \Setup::$MODULE_FOLDER . '/main/model/keys/request_public.key';
        $pubkeyid = openssl_pkey_get_public("file://" . $pubkeyfile);

        $check_string = $domain . $action;

        if (in_array($action, ['supportlogin', 'supportlogout'])) {
            $check_string .= gmdate('Y-m-d'); //Актуальность подписи для некоторых запросов 1 день
        }

        $ok = openssl_verify($check_string, $domain_sign, $pubkeyid);

        if ($ok == -1) {
            $this->e404(t('Ошибка проверки подписи'));
        }

        if ($ok != 1) {
            $this->e404(t('Неверная подпись'));
        }

        $this->wrapOutput(false);
    }

    /**
     * Возвращает мультисайты, которые присутствуют в системе
     *
     * @return string
     * @throws DbException
     * @throws OrmException
     */
    function actionGetMultisites()
    {
        $sites = SiteManager::getSiteList();
        $result = [];
        foreach ($sites as $site) {
            $result[] = [
                'title' => $site['title'],
                'full_title' => $site['full_title'],
                'folder' => $site['folder'],
                'language' => $site['language'],
                'main_domain' => $site->getMainDomain(),
                'uniq_hash' => $site->getSiteHash(),
                'absolute_root_url' => $site->getMainDomain() . $site->getRootUrl(),
                'use_ssl' => $site['redirect_to_https']
            ];
        }

        return json_encode([
            'response' => $result
        ]);
    }

    /**
     * Возвращает SHA1 от основной лицензии
     */
    function actionGetMainLicenseHash()
    {
        if (defined('CLOUD_UNIQ')) {
            $response = [
                'type' => 'cloud',
                'main_license_hash' => CLOUD_UNIQ
            ];
        } else {
            $main_license = false;
            __GET_LICENSE_LIST($main_license);

            $response = [
                'type' => 'license',
                'main_license_hash' => $main_license ? sha1(str_replace('-', '', $main_license['license_key'])) : false
            ];
        }

        return json_encode([
            'response' => $response
        ]);
    }

    /**
     * Обновляет сведения по лицензии
     *
     * @return string
     * @throws RSException
     * @throws DbException
     * @throws OrmException
     */
    function actionLicenseRefresh()
    {
        if (!defined('CLOUD_UNIQ')) {
            $license_hash = $this->url->request('license_hash', TYPE_STRING);

            /** @var License $license */
            $license = OrmRequest::make()
                ->from(new License())
                ->where("SHA1(license) = '#hash'", [
                    'hash' => $license_hash
                ])
                ->object();

            if ($license['license']) {
                if ($license->refresh()) {
                    return json_encode([
                        'response' => [
                            'success' => true
                        ]
                    ]);
                } else {
                    $error = [
                        'message' => $license->getErrorsStr()
                    ];
                }
            } else {
                $error = [
                    'message' => t('Лицензия не найдена')
                ];
            }
        } else {
            $error = [
                'message' => t('Функция недоступна в облаке')
            ];
        }

        return json_encode([
            'response' => [
                'success' => false,
                'error' => $error
            ]
        ]);
    }

    /**
     * Скрывает уведомления о том, что можно получить бонус за пост в социальные сети
     *
     * @return string
     * @throws RSException
     */
    function actionHideWallPostNotice()
    {
        $social_type = $this->url->request('social_type', TYPE_STRING);
        WallPostApi::hideWallPostNotice($social_type);

        return json_encode([
            'response' => [
                'success' => true
            ]
        ]);
    }

    /**
     * Удаляет шифрованные данные модульной лицензии
     *
     * @return string
     * @throws DbException
     * @throws RSException
     */
    function actionDeleteModuleLicenseData()
    {
        $module = $this->url->request('module', TYPE_STRING);
        __MODULE_LICENSE_DELETE_DATA($module);

        return json_encode([
            'response' => [
                'success' => true,
            ]
        ]);
    }

    /**
     * Позволяет авторизовываться специальному техническому пользователю ReadyScript для оказания поддержки, если
     * включена соответствующая опция в настройках системного модуля
     *
     * @return string
     */
    function actionSupportLogin()
    {
        $support_api = new RemoteSupportApi();

        if ($support_api->login()) {
            $this->app->redirect($this->router->getAdminUrl(false, [], false));
            return '';
        } else {
            return $support_api->getErrorsStr();
        }
    }

    /**
     * Позволяет выходить и удалять технического пользователя ReadyScript
     *
     * @return string
     */
    function actionSupportLogout()
    {
        $support_api = new RemoteSupportApi();

        if ($support_api->logout()) {
            return t('Выполнено успешно');
        } else {
            return $support_api->getErrorsStr();
        }
    }
}
