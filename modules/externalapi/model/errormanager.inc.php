<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace ExternalApi\Model;
use \RS\Event\Manager as EventManager;

/**
* Управляет списком ошибок, которые могут вернуть методы API
*/
class ErrorManager
{
    /**
    * Возвращает все классы исключений, которые могут выбрасываться во время выполнения методов API
    * 
    * @return array
    */
    public static function getExceptionClasses()
    {
        static $result;
            
        if ($result === null) {
            $result = [];
            $event_result = EventManager::fire('externalapi.getexceptions', []);
            foreach($event_result->getResult() as $item) {
                $result[get_class($item)] = $item;
            }
        }
        return $result;        
    }
}
