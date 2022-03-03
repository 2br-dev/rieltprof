<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Photogalleries\Config;

/**
* Класс предназначен для объявления событий, которые будет прослушивать данный модуль и обработчиков этих событий.
*/
class Handlers extends \RS\Event\HandlerAbstract
{
    function init()
    {
        $this
            ->bind('getroute')
            ->bind('getmenus');
    }

    
    
    /**
    * Возвращает новые маршруты в системе
    * 
    * @param array $routes - массив маршрутов
    *
    * @return array
    */
    public static function getRoute($routes)
    {                             
        //Альбом    
        $routes[] = new \RS\Router\Route('photogalleries-front-album',
            '/gallery/album/{id}/', null, t('Альбом фотогаллереи'));
        

        return $routes;
    }

    /**
     * Возвращает пункты меню этого модуля в виде массива
     *
     * @param array $items - массив с пунктами меню модулей
     *
     * @return array
     */
    public static function getMenus($items)
    {
        $items[] = [
                'title' => t('Фотогалерея'),
                'alias' => 'photogalleries',
                'link' => '%ADMINPATH%/photogalleries-ctrl/',
                'sortn' => 20,
                'typelink' => 'link',
                'parent' => 'modules'
        ];
        return $items;
    }
    
   
}