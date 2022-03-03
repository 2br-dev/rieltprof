<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Model;

class LicenseApi extends \RS\Module\AbstractModel\EntityList
{
    function __construct()
    {
        parent::__construct(new \Main\Model\Orm\License, [
            'idField' => 'license'
        ]);
    }
    
    /**
    * Возвращает список лицензий для отображения в таблице
    * 
    * @return array
    */
    function getTableList()
    {
        $list = __GET_LICENSE_TABLE_LIST();
        $idnaConvert = \RS\Helper\IdnaConvert::singleton();
        foreach($list as &$license) {
            $license['domain'] = $idnaConvert->decode($license['domain']);
        }
        return $list;
    }
    
    /**
    * Возвращает ссылку на страницу официального сайта, где можно купить лицензии для текущей комплектации скрипта
    * 
    * @return string
    */
    function getBuyLicenseUrl()
    {
        $script_type = strtolower(str_replace('.', '-', \Setup::$SCRIPT_TYPE));
        return str_replace('{script_type}', $script_type, \Setup::$BUY_LICENSE_URL);
    }

    /**
     * Возвращает массив с данными основной лицензии или false, если ключ не установлен или неактивен
     *
     * @return array | false
     */
    function getMainLicense()
    {
        $main_license = false;
        __GET_LICENSE_LIST($main_license);

        return $main_license;
    }

    /**
     * Возвращает SHA-1 от лицензионного ключа или false, если ключ не установлен или неактивен
     *
     * @return string | false
     */
    function getMainLicenseHash()
    {
        if ($license = $this->getMainLicense()) {
            return sha1(str_replace('-', '', $license['license_key']));
        }
        return false;
    }

    /**
     * Возвращает SHA-1 от активационных данных основного лицензионного ключа или false, если ключ не установлен или неактивен
     *
     * @return string | false
     */
    function getMainLicenseDataHash()
    {
        if ($license = $this->getMainLicense()) {
            return sha1(
                $license['sites']
                .$license['type']
                .$license['update_months']
                .$license['expire']
                .$license['expire_month']
                .$license['update_expire']
                .$license['date_of_activation']
                .$license['product']
                .$license['domain']
                .$license['upgrade_to_product']
            );
        }
        return false;
    }
}
