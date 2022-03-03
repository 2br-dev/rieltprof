<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace MobileSiteApp\Controller\Admin;

use Main\Model\LicenseApi;
use MobileSiteApp\Model\AppApi;

/**
 * Контроллер для отображения информации о мобильном приложении
 * Для отображения сведений требуется наличие установленной и активной лицензии на продукт
 */
class AppCtrl extends \RS\Controller\Admin\Front
{
    /**
     * @var AppApi
     */
    public $app_api;

    /**
     * @var LicenseApi
     */
    public $license_api;

    function init()
    {
        $this->license_api = new LicenseApi();
        $this->app_api = new AppApi();

        $this->view->assign([
            'app_api' => $this->app_api
        ]);
    }

    function actionIndex()
    {
        return $this->result->setTemplate('loading.tpl');
    }

    /**
     * Загружает данные о подписке с сервера ReadyScript, кладет их в кэш.
     * В случае успеха далее отображается страница просмотра информации о подписке,
     * в случае неудачи отображается промо-страница с рекламной информацией
     */
    function actionLoadMsaData()
    {
        $refresh = $this->url->get('refresh', TYPE_BOOLEAN);
        $site = \RS\Site\Manager::getAdminCurrentSite();
        $domain = $site->getMainDomain().$site->getRootUrl();

        $main_license_hash = defined('CLOUD_UNIQ') ? CLOUD_UNIQ :  $this->license_api->getMainLicenseHash();
        $main_license_data_hash = $this->license_api->getMainLicenseDataHash();

        $info = $this->app_api->getAppSubscribeInfo($domain, $main_license_hash, $main_license_data_hash, !$refresh);
        if ($info) {
            //Успех, подписка на приложение для этого сайта создана
            $this->result
                    ->setSuccess(true)
                    ->setTemplate( $this->viewInfo($domain, $info) );

        } else {
            //Неудача, не удалось получить информацию или подписка не создана
            $this->result
                    ->setSuccess(false)
                    ->setTemplate($this->viewPromo($domain));

            if ($this->app_api->hasError()) {
                $this->result->addEMessage( $this->app_api->getErrorsStr() );
            }
        }

        return $this->result;
    }

    /**
     * Возвращает контент страницы с информацией о подписке
     */
    function viewInfo($domain, $info)
    {
        $this->view->assign([
            'info' => $info['app'],
            'domain' => $domain,
            'order_count' => $this->app_api->getAppOrderCount()
        ]);

        return 'view_app.tpl';
    }


    /**
     * Возвращает контент для промо страницы
     */
    function viewPromo()
    {
        return 'promo.tpl';
    }
}