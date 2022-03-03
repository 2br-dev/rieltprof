<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace PushSender\Model;

/**
* Класс отвечает за запись log файл отправки push уведомлений
*/
class WriteLog extends \RS\Helper\Log
{
    /**
    * Возвращает путь к log файлу
    * 
    * @return string
    */
    public static function getFilename()
    {
        return \Setup::$PATH.\Setup::$STORAGE_DIR.'/logs/push.log';
    }
        
    /**
    * Возвращает instance логера
    * @return self
    */
    public static function make()
    {
        $instance = self::file(self::getFilename());        
        $instance->enableDate();
        return $instance;
    }    
}
