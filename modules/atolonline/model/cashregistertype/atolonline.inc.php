<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace AtolOnline\Model\CashRegisterType;

/**
 * Проксирующий класс интеграции с АТОЛ, позволяющая работать с одной из версий протокола АТОЛ
 */
class AtolOnline extends \Shop\Model\CashRegisterType\AbstractProxy
{
    /**
     * Доступные версии API
     */
    const
        API_VERSION_3 = 3,
        API_VERSION_4 = 4;

    /**
     * Возвращает имя класса, в котором реализована логика работы с необходимой версий API АТОЛ
     *
     * @return string
     */
    protected static function getApiVersionClass()
    {
        $atol_config = \RS\Config\Loader::byModule(__CLASS__);
        return '\AtolOnline\Model\CashRegisterType\Version\AtolOnlineV'.$atol_config->api_version;
    }
}