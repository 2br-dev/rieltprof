<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Comments\Config;
use Comments\Model\Api as CommentsApi;

class Handlers extends \RS\Event\HandlerAbstract
{
    function init()
    {
        $this
            ->bind('getmenus')
            ->bind('meter.recalculate');
    }
    
    /**
    * Возвращает пункты меню этого модуля в виде массива
    * 
    */
    public static function getMenus($items)
    {
        $items[] = [
                'title' => t('Комментарии'),
                'alias' => 'comments',
                'link' => '%ADMINPATH%/comments-ctrl/',
                'typelink' => 'link',
                'parent' => 'modules'
        ];
        return $items;
    }

    /**
     * Добавляем информацию о количестве непросмотренных заказов
     * во время вызова события пересчета счетчиков
     */
    public static function meterRecalculate($meters)
    {
        $comment_api  = new CommentsApi();
        $comment_meter_api = $comment_api->getMeterApi();
        $meters[$comment_meter_api->getMeterId()] = $comment_meter_api->getUnviewedCounter();

        return $meters;
    }
}
