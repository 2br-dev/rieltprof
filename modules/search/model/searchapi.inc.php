<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Search\Model;
use \RS\Event\Manager as EventManager;

/**
* Класс организации поиска
*/
class SearchApi
{
    protected static
        $current_engine;
    
    /**
    * Возвращает объект текущего поискового сервиса
    *
    * @throws \RS\Exception
    * @return \Search\Model\Engine\AbstractEngine
    */
    public static function currentEngine()
    {
        if (self::$current_engine === null) {
            $config = \RS\Config\Loader::byModule(__CLASS__);
            $service_class = $config->search_service;
            if (class_exists($service_class)) {
                self::setCurrentEngine(new $service_class);
            } else {
                throw new \RS\Exception(t('Не найден класс поискового сервиса %0', [$service_class]));
            }
        }
        
        return self::$current_engine;
    }
    
    /**
    * Принудительно устанавливает поисковый сервис
    * 
    * @param Engine\AbstractEngine $engine
    * @return void
    */
    public static function setCurrentEngine(\Search\Model\Engine\AbstractEngine $engine)
    {
        self::$current_engine = $engine;
    }
    
    /**
    * Возвращает полный список зарегистрированных в системе поисковых сервисов
    *
    * @throws \RS\Exception
    * @return \Search\Model\Engine\AbstractEngine[]
    */
    public static function getEngines()
    {
        $event_result = EventManager::fire('search.getengines', []);
        $result = [];
        foreach ($event_result->getResult() as $service) {
            if ($service instanceof \Search\Model\Engine\AbstractEngine) {
                $result[get_class($service)] = $service;
            } else {
                throw new \RS\Exception(t('Поисковый сервис должен имплементировать интерфейс \Search\Model\Engine\AbstractEngine'));
            }
        }
        return $result;
    }
    
    /**
    * Возвращает массив с названиями поисковых сервисов
    * 
    * @return array
    */
    public static function getEnginesNames()
    {
        $result = [];
        foreach(self::getEngines() as $class => $engine) {
            $result[$class] = $engine->getTitle();
        }
        return $result;
    }
}