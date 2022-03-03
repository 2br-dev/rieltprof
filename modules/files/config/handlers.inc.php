<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Files\Config;
use \RS\Router;

/**
* Класс обработчиков событий
*/
class Handlers extends \RS\Event\HandlerAbstract
{
    function init()
    {
        $this->bind('getroute');
        
        //Подключаем обработку событий товаров
        $handlers_product = new HandlersProduct();
        $handlers_product->init();
        
        //Подключаем обработку событий заказов
        $handlers_order = new HandlersOrder();
        $handlers_order->init();
    }
    
    /**
    * Добавляет маршрут в систему
    * 
    * @param Router\Route[] $routes
    * @return Router\Route[]
    */
    public static function getRoute($routes)
    {
        $routes[] = new Router\Route('files-front-download', '/download-file/{uniq_name}', null, t('Блок файлов: загрузка файла'));
        return $routes;
    }
    
}