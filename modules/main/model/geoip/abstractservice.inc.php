<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Model\GeoIp;

/**
* Абстрактный класс сервиса по определению города и географических координат по IP 
*/
abstract class AbstractService
{
    /**
    * Возвращает символьный идентификатор модуля геолокации
    * 
    * @return string - английские символы и цифры
    */
    abstract public function getId();
    
    /**
    * Возвращает название модуля геолокации
    * 
    * @return string
    */
    abstract public function getTitle();
    
    /**
    * Возвращает название города по IP
    * 
    * @param string $ip - IP адрес
    * @return string
    */
    abstract public function getCityByIp($ip);
    
    /**
    * Возвращает широту и долготу города по IP
    * 
    * @param string $ip - IP адрес
    * @return array ['lat' => широта, 'lng' => долгота]
    */
    abstract public function getCoordByIp($ip);
}