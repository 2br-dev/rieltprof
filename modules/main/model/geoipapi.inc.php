<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Model;

use RS\Config\Loader as ConfigLoader;
use \RS\Event\Manager as EventManager;

class GeoIpApi
{
    const
        SESSION_GEOIP_CITY = 'geoip_city',
        SESSION_GEOIP_COORD = 'geoip_coord';

    /** @var \Main\Model\GeoIp\AbstractService */
    protected $geo_service;

    function __construct($geo_service_id = null)
    {
        if (!$geo_service_id) {
            $geo_service_id = ConfigLoader::byModule($this)->geo_ip_service;
        }
        $this->setService($geo_service_id);
    }

    /**
     * Устанавливает сервис, через который будет определяться город
     *
     * @param string $geo_service_id
     * @return bool
     */
    function setService($geo_service_id)
    {
        $service_list = $this->getGeoIpServices();
        if (isset($service_list[$geo_service_id])) {
            $this->geo_service = $service_list[$geo_service_id];
            return true;
        }
        return false;
    }

    /**
     * Возвращает текущий сервис геолокации
     *
     * @return \Main\Model\GeoIp\AbstractService | null
     */
    function getService()
    {
        return $this->geo_service;
    }

    /**
     * Возвращает список сервисов для определения города по IP
     *
     * @return \Main\Model\GeoIp\AbstractService[]
     * @throws GeoIp\Exception
     */
    public static function getGeoIpServices()
    {
        $result = [];
        $event_result = EventManager::fire('geoip.getservices', []);
        foreach ($event_result->getResult() as $item) {
            if ($item instanceof GeoIp\AbstractService) {
                $result[$item->getId()] = $item;
            } else {
                throw new GeoIp\Exception(t('Сервис геолокации должен быть потомком класса \Main\Model\GeoIp\AbstractService'));
            }
        }
        return $result;
    }

    /**
     * Возвращает список сервисов для определения города по IP
     *
     * @return string[]
     */
    public static function getGeoIpServicesName()
    {
        $result = [];
        $services = self::getGeoIpServices();
        foreach ($services as $key => $service) {
            $result[$key] = $service->getTitle();
        }
        return $result;
    }

    /**
     * Возвращает город по IP адресу
     *
     * @param string $ip - IP адрес
     * @param bool $use_session_cache - кэшировать результат в сессии
     * @return string | false
     */
    public function getCityByIp($ip, $use_session_cache = true)
    {
        if (!$this->geo_service) return false;

        if (!isset($_SESSION[self::SESSION_GEOIP_CITY]) || !is_array($_SESSION[self::SESSION_GEOIP_CITY])) {
            $_SESSION[self::SESSION_GEOIP_CITY] = [];
        }

        if (!$use_session_cache || !isset($_SESSION[self::SESSION_GEOIP_CITY][$ip])) {
            $_SESSION[self::SESSION_GEOIP_CITY][$ip] = $this->geo_service->getCityByIp($ip);
        }

        return $_SESSION[self::SESSION_GEOIP_CITY][$ip];
    }

    /**
     * Возвращает координаты по IP адресу ['lat' => широта, 'lng' => долгота]
     *
     * @param string $ip - IP адрес
     * @param bool $use_session_cache - кэшировать результат в сессии
     * @return false|array
     */
    public function getCoordByIp($ip, $use_session_cache = true)
    {
        if (!$this->geo_service) return false;

        if (!$use_session_cache || !isset($_SESSION[self::SESSION_GEOIP_COORD][$ip])) {
            $_SESSION[self::SESSION_GEOIP_COORD][$ip] = $this->geo_service->getCoordByIp($ip);
        }

        return $_SESSION[self::SESSION_GEOIP_COORD][$ip];
    }
}
