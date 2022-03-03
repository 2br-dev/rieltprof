<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Debug;

use Main\Config\ModuleRights;
use RS\AccessControl\Rights;

/**
* Класс отвечает за состояние режима отладки 
*/
class Mode
{
    CONST
        DEBUG_MODE = 'DEBUGMODE';
        
    /**
    * Определяет включен ли сейчас режим отладки сайта
    * @return bool возвращает true, если режим отладки включен, иначе false
    */
    public static function isEnabled()
    {
        return !empty($_SESSION[self::DEBUG_MODE]) && \RS\Application\Auth::isAuthorize();
    }
    
    /**
    * Включает или выключает режим отладки сайта.
    * 
    * @param bool $enable
    * @return void
    */
    public static function enable($enable = true)
    {
        if ($enable && !Rights::hasRight('main', ModuleRights::RIGHT_DEBUG_MODE, true)) {
            return;
        }

        $_SESSION[self::DEBUG_MODE] = $enable;
    }
    
    /**
    * Выключает режим отладки сайта
    * @return void
    */
    public static function disable()
    {
        self::enable(false);
    }
}

