<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Search\Config;
use \RS\Router;

class Handlers extends \RS\Event\HandlerAbstract
{
    function init()
    {
        $this->bind('search.getengines');
    }
    
    /**
    * Регистрирует поисковый сервис в системе
    * 
    * @param \Search\Model\AbstractEngine[] $list
    * @return \Search\Model\AbstractEngine[]
    */
    public static function searchGetEngines($list)
    {
        $list[] = new \Search\Model\Engine\Mysql();
        return $list;
    }
}