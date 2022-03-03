<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Model\GeoIp;
use \RS\Config\Loader as ConfigLoader;

/**
* Сервис по определению города и географических координат по IP - dadata.ru
*/
class Dadata extends AbstractService
{
    protected
        $url = 'https://dadata.ru/api/v2/detectAddressByIp?ip=%IP';
        
    /**
    * Возвращает символьный идентификатор модуля геолокации
    * 
    * @return string
    */
    public function getId()
    {
        return 'dadata';
    }
    
    /**
    * Возвращает название модуля геолокации
    * 
    * @return string
    */
    public function getTitle()
    {
        return t('DaData.ru');
    }
    
    /**
    * Возвращает двухсимвольный идентификатор страны
    * 
    * @param string $ip - IP адрес
    * @return string
    */
    public function getCountryId($ip)
    {
        //Сервис не поддерживает определение идентификатора страны
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
        if (($xml = $this->requestXml($ip)) && isset($xml->location->data->city)) {
            return (string)$xml->location->data->city;
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
        if (($xml = $this->requestXml($ip)) && !empty($xml->location->data->geo_lat) && !empty($xml->location->data->geo_lon) ) {
            return [
                'lat' => (string)$xml->location->data->geo_lat,
                'lng' => (string)$xml->location->data->geo_lon,
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
    function requestXml($ip)
    {
        static 
            $cache = [];
                
        if (!isset($cache[$ip])) {
            $url = str_replace('%IP', $ip, $this->url);
            $token = ConfigLoader::byModule($this)->dadata_token;
            if ($token) {
                $opts = ['http' => [
                        'timeout' => 2,
                         "ssl"=> [
                            "verify_peer"=>false,
                            "verify_peer_name"=>false,
                         ],
                        'header'=>"Accept: application/xml\r\n" .
                                  "Authorization: Token $token\r\n"
                ]
                ];
                $cache[$ip] = @file_get_contents($url, false, stream_context_create($opts));
            } else {
                $cache[$ip] = false;
            }
        }
        
        if ($cache[$ip]) {
            return new \SimpleXMLElement($cache[$ip]);
        }
        return false;
    }    
}
