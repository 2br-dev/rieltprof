<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Router;

/**
* Объект данного класса будет удалять раннее определенный Route.
* Применяется в случае если необходимо удалить route из другого модуля
*/
class DeleteRoute
{
    private
        $route_id;
    
    /**
    * Конструктор
    * 
    * @param string $route_id - id раннее определенного route
    * 
    * @return DeleteRoute
    */
    function __construct($route_id)
    {
        $this->route_id = $route_id;
    }
    
    /**
    * Возвращает id маршрута, который необходимо удалить
    * @return string
    */
    function getRouteId()
    {
        return $this->route_id;
    }
}
