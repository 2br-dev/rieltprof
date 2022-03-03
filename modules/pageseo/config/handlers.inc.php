<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace PageSeo\Config;
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
                'title' => t('Заголовки, мета-теги'),
                'alias' => 'pageseo',
                'link' => '%ADMINPATH%/pageseo-ctrl/',
                'parent' => 'website',
                'typelink' => 'link',
                'sortn' => 30
        ];
            
       
        return $items;
    }
}