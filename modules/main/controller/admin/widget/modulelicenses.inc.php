<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Main\Controller\Admin\Widget;

use Main\Model\ModuleLicenseApi;
use RS\Controller\Admin\Widget;
use RS\Router\Manager as RouterManager;
use RS\Theme\Manager as ThemeManager;

class ModuleLicenses extends Widget
{
    protected $info_title = 'Лицензии на модули'; //Определить у наследников название виджета.
    protected $info_description = 'Отображает информацию по всем модульным лицензиям'; //Определить у наследников описание виджета

    public function actionIndex()
    {
        $shop_type = (defined('CLOUD_UNIQ')) ? 'cloud' : 'box';
        $licenses = ModuleLicenseApi::getLicenseDataByAllModules();
        $cost_in_month = 0;
        foreach ($licenses as $module => &$license) {
            if (!isset($license['error']) && $license['type'] == 'exist' && $license['auto_prolongation']) {

                if ($license['expire']) { //cloud
                    if ($license['duration']) {
                        $cost_in_month += $license['cost'] / $license['duration'];
                    }
                } elseif ($license['update_expire']) { //box
                    if ($license['update_duration']) {
                        $cost_in_month += $license['update_cost'] / $license['update_duration'];
                    }
                }
            }
        }

        $this->view->assign([
            'my_apps_cabinet_url' => ModuleLicenseApi::getMyAddonLicenseCabinetUrl(),
            'shop_type' => $shop_type,
            'total_in_month' => $cost_in_month,
            'total_in_year' => $cost_in_month * 12,
            'modules' => ModuleLicenseApi::getAllModules(),
            'licenses' => $licenses,
            'themes' => ThemeManager::selectList(),
            'module_license_api' => new ModuleLicenseApi(),
        ]);

        return $this->result->setTemplate('widget/module_licenses.tpl');
    }

    /**
     * Возвращает массив с кнопками, которые будут отображаться в шапке виджета.
     * Все элементы массива будут добавлены как атрибуты к тегу <a>
     *
     * @return array
     */
    public function getTools()
    {
        $router = RouterManager::obj();
        return [
            [
                'title' => t('Обновить данные по лицензиям'),
                'class' => 'zmdi zmdi-refresh crud-get',
                'href' => $router->getAdminUrl('ajaxReloadLicenseData', ['context' => 'widget'], 'main-modulelicensescontrol'),
                '~data-update-container' => '.widget-module-license',
            ],
            [
                'title' => t('Настройки модулей'),
                'class' => 'zmdi zmdi-open-in-new',
                'href' => $router->getAdminUrl(false, [], 'modcontrol-control')
            ]
        ];
    }
}
