<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Marketplace\Config;

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
        $routes[] = new \RS\Router\Route('marketplace-front-checkforfatal', [
            '/checkforfatal/'
        ], null, t('Проверка на отсутсвие фатальных ошибок'), true);

        return $routes;
    }
}