<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/ declare(strict_types=1);

namespace RS\Log;

use RS\Event\Manager as EventManager;

/**
 * Класс, работающий со списками объектов логирования
 */
class LogManager
{
    /**
     * Singleton, необходимо воспользоваться методом ::getInstance(),
     * для получения объекта данного класса
     */
    private function __construct()
    {}

    /**
     * Возвращает единственный объект текущего класса
     *
     * @return LogManager
     */
    public static function getInstance()
    {
        static $instance;
        if ($instance === null) {
            $instance = new self();
        }
        return $instance;
    }

    /**
     * Возвращает список зарегистрированных логов в системе
     *
     * @return AbstractLog[]
     */
    public function getLogList()
    {
        static $list;
        if ($list === null) {
            $list = [];
            /** @var AbstractLog[] $log_list */
            $log_list = EventManager::fire('getlogs', [])->getResult();
            foreach ($log_list as $log) {
                $list[$log->getIdentifier()] = $log;
            }
        }
        return $list;
    }
}
