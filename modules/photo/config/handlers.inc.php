<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Photo\Config;

class Handlers extends \RS\Event\HandlerAbstract
{
    function init()
    {
        $this->bind('getroute');
    }
    
    public static function getRoute($routes) 
    {
        $routes[] = new \RS\Router\Route('photo.stub', '/storage/photo/stub/resized/{type}/{picid}\.{ext}$', [
            'controller' => 'photo-stubhandler'
        ], t('Изображение-заглушка'), true);
        
        $routes[] = new \RS\Router\Route('photo.image', '/storage/photo/resized/{type}/{picid}\.{ext}$', [
            'controller' => 'photo-photohandler'
        ], t('Изображение'), true);
        
        return $routes;
    }
}


