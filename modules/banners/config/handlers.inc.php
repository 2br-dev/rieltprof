<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Banners\Config;
use \RS\Router;

class Handlers extends \RS\Event\HandlerAbstract
{
    function init()
    {
        $this
            ->bind('getmenus')
            ->bind('getroute');
    }
    
    public static function getRoute(array $routes) 
    {        
        $routes[] = new \RS\Router\Route('banners-front-photohandler', 
            '/storage/banners/resized/{type}/{picid}\.{ext}$', null,
            t('Изображение для баннеров'), true);
        
        return $routes;
    }
    
    /**
    * Возвращает пункты меню этого модуля в виде массива
    * 
    */
    public static function getMenus($items)
    {
        $items[] = [
                'title' => t('Баннеры'),
                'alias' => 'banners',
                'link' => '%ADMINPATH%/banners-ctrl/',
                'typelink' => 'link',
                'parent' => 'modules'
        ];
        return $items;
    }
}