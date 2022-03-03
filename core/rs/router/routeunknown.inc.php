<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Router;

/**
* Маршрут - заглушка. Возвращается, когда не найден искомый маршрут.
*/
class RouteUnknown extends RouteAbstract
{        
    /**
    * Констуктор "неизвестного маршрута"
    * 
    * @param mixed $id - id маршрута, который не найден
    * @return RouteUnknown
    */
    function __construct($id)
    {
        $this->id = $id;
        $this->description = t('Маршрут не найден. Возможно модуль был удален');
    }
    
    public function isUnknown()
    {
        return true;
    }
}

