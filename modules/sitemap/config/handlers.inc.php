<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Sitemap\Config;

/**
* Класс предназначен для объявления событий, которые будет прослушивать данный модуль и обработчиков этих событий.
*/
class Handlers extends \RS\Event\HandlerAbstract
{
    function init()
    {
        $this->bind('getroute');
    }
    
    public static function getRoute($routes) 
    {
        $routes[] = new \RS\Router\Route('sitemap-front-sitemap', [
            '/sitemap_{type:(google)}-{site_id}-{chunk:(\d+)}.xml',
            '/sitemap_{type:(google)}-{site_id}-{chunk:(\d+)}.xml.{pack:(gz)}',

            '/sitemap_{type:(google)}-{site_id}.xml',
            '/sitemap_{type:(google)}-{site_id}.xml.{pack:(gz)}',

            '/sitemap-{site_id}-{chunk:(\d+)}.xml',
            '/sitemap-{site_id}-{chunk:(\d+)}.xml.{pack:(gz)}',

            '/sitemap-{site_id}.xml',
            '/sitemap-{site_id}.xml.{pack:(gz)}',

        ], null, t('Sitemap XML'), true);

        return $routes;
    }
}