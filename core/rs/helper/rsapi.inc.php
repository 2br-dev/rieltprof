<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Helper;

/**
 * Класс содержит функции, необходимые при работе с API ReadyScript
 */
class RSApi
{
    /**
     * Возвращает параметры для идентификации магазина на сервере ReadyScript,
     * которые следует передавать в запросах к API.
     *
     * @return array
     */
    public static function getAuthParams()
    {
        $result = [
            'shop_key' => sha1(\Setup::$SECRET_KEY.'-shop-key')
        ];

        if (defined('CLOUD_UNIQ')) {
            //Для облачной сборки
            $result['auth_type'] = 'cloud';
            $result['main_license_hash'] = CLOUD_UNIQ;
        } else {
            //Для коробочной сборки
            $main_license = null;
            __GET_LICENSE_LIST($main_license);

            if ($main_license) {
                $result['auth_type'] = 'license';
                $result['main_license_hash'] = sha1(str_replace('-', '', $main_license['license_key']));
            }
        }
        return $result;
    }

    /**
     * Возвращает cloud - если скрипт развернут в облаке ReadyScript,
     * если используется коробочная версия, то возвращает box
     *
     * @return string
     */
    public static function getScriptType()
    {
        return defined('CLOUD_UNIQ') ? 'cloud' : 'box';
    }
}