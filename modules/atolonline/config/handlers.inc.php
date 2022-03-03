<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace AtolOnline\Config;
use \RS\Orm\Type as OrmType;

/**
* Класс предназначен для объявления событий, которые будет прослушивать данный модуль и обработчиков этих событий.
*/
class Handlers extends \RS\Event\HandlerAbstract
{
    function init()
    {
        $this
            ->bind('cashregister.gettypes');
    }
    
    /**
    * Возвращает процессоры(типы) доставки, присутствующие в текущем модуле
    * 
    * @param array $list - массив из передаваемых классов доставок
    */
    public static function cashRegisterGetTypes($list)
    {
        $list[] = new \AtolOnline\Model\CashRegisterType\AtolOnline();
        return $list;
    }
    
}