<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace ModControl\Config;
use \RS\Orm\Type as OrmType;

/**
* Класс предназначен для объявления событий, которые будет прослушивать данный модуль и обработчиков этих событий.
*/
class Handlers extends \RS\Event\HandlerAbstract
{
    function init()
    {
        $this
            ->bind('getmenus');
    }
    
    /**
    * Возвращает пункты меню этого модуля в виде массива
    * 
    */
    public static function getMenus($items)
    {     
        $items[] = [
                'title' => t('Настройка модулей'),
                'alias' => 'moduleconfig',
                'typelink' => 'link', 
                'link' => '%ADMINPATH%/modcontrol-control/',
                'parent' => 'website',
                'sortn' => 70
        ];
            
       
        return $items;
    }
}