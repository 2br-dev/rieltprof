<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\RemoteApp;
use \RS\Event\Manager as EventManager;

/**
* Класс содержит функции для работы с внешними приложениями
*/
class Manager
{
    /**
    * Возвращает список всех имеющихся типов приложений, 
    * зарегистрированных в системе. Для регистрации в системе используется событие getapps
    * 
    * @param bool $cache - если true, то будет использовано кэширование
    * @return AbstractAppType[]
    */
    public static function getAppTypes($cache = true)
    {
        static $result;
            
        if (!$cache || $result === null) {
            $result = [];
            $event_result = EventManager::fire('getapps', []);
            foreach($event_result->getResult() as $item) {
                $result[$item->getId()] = $item;
            }
        }
        return $result;
    }
    
    /**
    * Возвращает список названий всех имеющихся приложений,
    * зарегистрированных в системе.
    * 
    * @param bool $cache - если true, то будет использовано кэширование
    * @return string[]
    */
    public static function getAppTypesTitles($cache = true)
    {
        $apps = self::getAppTypes($cache);
        $result = [];
        foreach($apps as $id => $app) {
            $result[$id] = $app->getTitle();
        }
        return $result;
    }
    
    /**
    * Возвращает экземпляр класса типа приложения
    * 
    * @param string $app_id - идентификатор приложения
    * @param bool $cache - если true, то будет использовано кэширование
    * @return AbstractAppType
    */
    public static function getAppByType($id, $cache = true)
    {
        $types = self::getAppTypes($cache);
        return isset($types[$id]) ? clone $types[$id] : false;
    }
}
