<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Model\GeoIp;

/**
* Сервис по определению города и географических координат по IP - ipgeobase.ru
*/
class IpGeoBase extends AbstractService
{
    protected
        $url = 'http://ipgeobase.ru:7020/geo?ip=%IP';
        
    /**
    * Возвращает символьный идентификатор модуля геолокации
    * 
    * @return string
    */
    public function getId()
    {
        return 'ipgeobase';
    }
    
    /**
    * Возвращает название модуля геолокации
    * 
    * @return string
    */
    public function getTitle()
    {
        return t('IpGeoBase.ru');
    }
    
    /**
    * Возвращает двухсимвольный идентификатор страны
    * 
    * @param string $ip - IP адрес
    * @return string
    */
    public function getCountryId($ip)
    {
        if (($xml = $this->requestXml($ip)) && isset($xml->ip)) {
            return (string)$xml->ip->country;
        }
        return false;        
    }
        
    /**
    * Возвращает название города по IP
    * 
    * @param string $ip - IP адрес
    * @return string
    */
    public function getCityByIp($ip)
    {
        if (($xml = $this->requestXml($ip)) && isset($xml->ip)) {
            return (string)$xml->ip->city;
        }
        return false;
    }
    
    /**
    * Возвращает широту и долготу города по IP
    * 
    * @param string $ip - IP адрес
    * @return array ['lat' => широта, 'lng' => долгота]
    */
    public function getCoordByIp($ip)
    {
        if (($xml = $this->requestXml($ip)) && isset($xml->ip) ) {
            return [
                'lat' => (string)$xml->ip->lat,
                'lng' => (string)$xml->ip->lng,
            ];
        }
        return false;
    }
    
    /**
    * Выполняет запрос на сервер сервиса
    * 
    * @param string $ip
    * @return \SimpleXMLElement | false
    */
    protected function requestXml($ip)
    {
        static 
            $cache = [];
                
        if (!isset($cache[$ip])) {
            $url = str_replace('%IP', $ip, $this->url);
            $opts = ['http' => [
                    'timeout' => 2,
            ]
            ];
            $cache[$ip] = @file_get_contents($url, false, stream_context_create($opts));
        }

        if ($cache[$ip]) {
            return new \SimpleXMLElement($cache[$ip]);
        }
        return false;
    }
}
