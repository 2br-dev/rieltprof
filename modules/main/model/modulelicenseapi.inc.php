<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/ declare(strict_types=1);

namespace Main\Model;

use RS\Event\Exception as EventException;
use RS\Exception as RSException;
use RS\Module\AbstractModel\BaseModel;
use RS\Db\Exception as DbException;
use RS\Router\Manager as RouterManager;

class ModuleLicenseApi extends BaseModel
{
    const LICENSE_STATUS_OK = 'success';
    const LICENSE_STATUS_WARNING = 'warning';
    const LICENSE_STATUS_DANGER = 'danger';
    const LICENSE_INFO_LEVEL_INFORMATION = 'information';
    const LICENSE_INFO_LEVEL_WARNING = 'warning';
    const LICENSE_INFO_LEVEL_DANGER = 'danger';
    const WARNING_WAIT_SECONDS = 432000;
    const ADDON_TYPE_THEME = 'template';

    /**
     * Возвращает данные по лицензии на модуль в текстовом виде
     *
     * @param string $module - название модуля
     * @param string $information_level
     * @return string
     * @throws DbException
     * @throws EventException
     * @throws RSException
     */
    public static function getLicenseDataText($module, &$information_level = ''): string
    {
        $is_theme = preg_match('/^#/', $module);
        $prolongation_url = self::getProlongationUrl($module);

        $text = '';
        if (ModuleLicenseApi::isSystemModule($module)) {
            $text .= ($is_theme) ?  t('Тема оформления является системной.') : t('Модуль является системным.');
            $information_level = self::LICENSE_INFO_LEVEL_INFORMATION;
        } else {
            $license_data = ModuleLicenseApi::getDataByModule($module);

            if (!empty($license_data)) {
                if (isset($license_data['error'])) {
                    $text .= $license_data['error'];
                    $information_level = self::LICENSE_INFO_LEVEL_DANGER;

                } elseif ($license_data['type'] == 'free') {
                    $text .= ($is_theme) ? t('Басплатная тема оформления') : t('Бесплатный модуль');
                    $information_level = self::LICENSE_INFO_LEVEL_INFORMATION;

                } elseif ($license_data['type'] == 'exist') {
                    if ($license_data['expire'] == 0) {
                        if ($license_data['update_expire'] == 0) {
                            $text .= ($is_theme) ? t('Бессрочные обновления темы оформления') : t('Бессрочные обновления на модуль');
                            $information_level = self::LICENSE_INFO_LEVEL_INFORMATION;

                        } elseif ($license_data['update_expire'] > time()) {
                            $text .= t('Обновления на %addon_type истекают %date. '."\n".'Продление - %price ₽ на %duration [plural:%duration:месяц|месяца|месяцев]', [
                                'date' => date('d.m.Y', (int)$license_data['update_expire']),
                                'price' => $license_data['update_cost'],
                                'duration' => $license_data['update_duration'],
                                'addon_type' => ($is_theme) ? t('тему оформления') : t('модуль'),
                            ]);

                            if ($license_data['update_expire'] - self::WARNING_WAIT_SECONDS < time()) {
                                $information_level = self::LICENSE_INFO_LEVEL_WARNING;
                            } else {
                                $information_level = self::LICENSE_INFO_LEVEL_INFORMATION;
                            }

                        } else {
                            $text .= t('Обновления на %addon_type истекли. '."\n".'<a href="%href" target="_blank" class="u-link">Продлить</a> за %price ₽ на %duration [plural:%duration:месяц|месяца|месяцев]', [
                                'href' => $prolongation_url,
                                'price' => $license_data['update_cost'],
                                'duration' => $license_data['update_duration'],
                                'addon_type' => ($is_theme) ? t('тему оформления') : t('модуль'),
                            ]);
                            $information_level = self::LICENSE_INFO_LEVEL_DANGER;
                        }

                    } elseif ($license_data['expire'] > time()) {
                        $text .= t('Лицензия на %addon_type истекает %date. '."\n".'Продление - %price ₽ на %duration [plural:%duration:месяц|месяца|месяцев]', [
                            'date' => date('d.m.Y', (int)$license_data['expire']),
                            'price' => $license_data['cost'],
                            'duration' => $license_data['duration'],
                            'addon_type' => ($is_theme) ? t('тему оформления') : t('модуль'),
                        ]);

                        if ($license_data['expire'] - self::WARNING_WAIT_SECONDS < time()) {
                            $information_level = self::LICENSE_INFO_LEVEL_WARNING;
                        } else {
                            $information_level = self::LICENSE_INFO_LEVEL_INFORMATION;
                        }

                    } else {
                        $text .= t('Лицензия на %addon_type истекла. '."\n".'<a href="%href" target="_blank" class="u-link">Продлить</a> за %price ₽ на %duration [plural:%duration:месяц|месяца|месяцев]', [
                            'href' => $prolongation_url,
                            'price' => $license_data['cost'],
                            'duration' => $license_data['duration'],
                            'addon_type' => ($is_theme) ? t('тему оформления') : t('модуль'),
                        ]);

                        $information_level = self::LICENSE_INFO_LEVEL_DANGER;
                    }

                } elseif ($license_data['type'] == 'market') {
                    $text .= ($is_theme) ? t('Лицензия на тему оформления не приобретена.') : t('Лицензия на модуль не приобретена.');
                    $information_level = self::LICENSE_INFO_LEVEL_DANGER;

                } else { //type = none
                    $text .= ($is_theme) ? t('Тема оформления не требует лицензии.') : t('Модуль не требует лицензии.');
                    $information_level = self::LICENSE_INFO_LEVEL_INFORMATION;
                }
            } else {
                $text .= ($is_theme) ? t('Не удалось получить данные по лицензии для темы оформления') : t('Не удалось получить данные по лицензии для модуля');
                $information_level = self::LICENSE_INFO_LEVEL_DANGER;
            }
        }

        return $text;
    }

    /**
     * Возвращает является ли модуль системным
     *
     * @param string $module_name - имя модуля
     * @return bool
     */
    public static function isSystemModule(string $module_name): bool
    {
        return __MODULE_LICENSE_IS_SYSTEM_MODULE($module_name);
    }

    /**
     * Перезагружает данные по модульным лицензиям
     *
     * @return array | string Возвращает строку в случае ошибки, иначе - массив
     * @throws DbException
     * @throws EventException
     * @throws RSException
     */
    public static function reloadAllLicenseData()
    {
        return __MODULE_LICENSE_LOAD(__MODULE_LICENSE_GET_ALL_LIST());
    }

    /**
     * Возвращает данные по лицензиям для всех модулей
     *
     * @return array
     * @throws DbException
     * @throws EventException
     * @throws RSException
     */
    public static function getLicenseDataByAllModules(): array
    {
        return __MODULE_LICENSE_GET_ALL();
    }

    /**
     * Возвращает список всех не системных модулей
     *
     * @return array
     */
    public static function getAllModules(): array
    {
        return __MODULE_LICENSE_GET_MODULES();
    }

    /**
     * Возвращает данные по модульной лицензии
     *
     * @param string $module_name - имя модуля
     * @return array
     * @throws DbException
     * @throws EventException
     * @throws RSException
     */
    public static function getDataByModule(string $module_name): ?array
    {
        $licenses = self::getLicenseDataByAllModules();
        return $licenses[$module_name] ?? null;
    }

    /**
     * Возвращает массив модулей, для которых нет лицензий
     *
     * @return array
     * @throws DbException
     * @throws EventException
     * @throws RSException
     */
    static function getNoLicensesModules()
    {
        $licenses = self::getLicenseDataByAllModules();
        $no_license_modules = [];
        foreach($licenses as $module_name => $license_data) {
            if (!$license_data['is_working']) {
                $no_license_modules[] = $module_name;
            }
        }

        return $no_license_modules;
    }

    /**
     * Возвращает абсолютную ссылку на страницу продления лицензии на обновления для модуля
     *
     * @param string $module - имя модуля
     * @return string
     */
    public static function getProlongationUrl($module)
    {
        $router = RouterManager::obj();
        return $router->getAdminUrl('moduleLicenseUpdate', ['module' => $module], 'main-modulelicensescontrol');
    }

    /**
     * Возвращает абсолютный путь в личный кабинет с покупками лицензий
     *
     * @return string
     */
    public static function getMyAddonLicenseCabinetUrl()
    {
        return \Setup::$RS_SERVER_PROTOCOL.'://'.\Setup::$MARKETPLACE_DOMAIN.'/myapps/';
    }

    /**
     * Возвращает true, если подписка на обновлния действительна
     *
     * @return bool
     */
    public static function isLicenseRenewalActive()
    {
        if (!defined('CLOUD_UNIQ') && SCRIPT_TRIAL_STATUS != 'LOCAL' && (SCRIPT_UPDATE_EXPIRE < time())){
            return false;
        }
        return true;
    }
}
