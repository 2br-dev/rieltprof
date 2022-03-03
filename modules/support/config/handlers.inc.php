<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Support\Config;
use \RS\Orm\Type as OrmType;
use Support\Model\TopicApi;

class Handlers extends \RS\Event\HandlerAbstract
{
    function init()
    {
        $this
            ->bind('getmenus')
            ->bind('meter.recalculate')
            ->bind('getroute');
    }
    
    /**
    * Возвращает маршруты данного модуля
    */
    public static function getRoute(array $routes) 
    {        
        $routes[] = new \RS\Router\Route('support-front-support', ['/my/support/{Act}/{id}/', '/my/support/'], null, t('Поддержка'));
        return $routes;
    }

    /**
     * Возвращает счетчик непросмотренных объектов
     */
    public static function meterRecalculate($meters)
    {
        $topic_api = new TopicApi();
        $topic_meter_api = $topic_api->getMeterApi();
        $meters[$topic_meter_api->getMeterId()] = $topic_meter_api->getUnviewedCounter();

        return $meters;
    }
    
    /**
    * Возвращает пункты меню этого модуля в виде массива
    * 
    */
    public static function getMenus($items)
    {
        $items[] = [
                'title' => t('Поддержка'),
                'alias' => 'support',
                'link' => '%ADMINPATH%/support-topicsctrl/',
                'typelink' => 'link',                      
                'parent' => 'modules',
                'sortn' => 0
        ];
        return $items;
    }
    
}