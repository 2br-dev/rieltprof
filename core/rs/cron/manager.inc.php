<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Cron;

use RS\HashStore\Api as HashStoreApi;

class Manager
{
    const LOCK_FILE = '/storage/locks/cron';
    const LAST_TIME_KEY = 'cron.last_execution_timestamp';
    const LOCK_EXPIRE_INTERVAL = 3600;

    private static $instance;

    /**
    * Получить экземпляр объекта необходимо с помощью вызова Manager::obj()
    */
    protected function __construct()
    {}
    
    /**
     * Возвращает объект текущего класса
     * @return self
     */
    public static function obj()
    {
        if ( !isset(self::$instance) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }


    /**
     * Настроен ли файл крона
     * Если крон не выполнялся более $fail_period, то он считается не настроенным
     *
     * @return bool
     */
    public function isCronWork()
    {
        $fail_period = 1800;
        $last_execution_timestamp = HashStoreApi::get(self::LAST_TIME_KEY);
        return $last_execution_timestamp > time() - $fail_period;
    }


    /**
     * Выполняется ли задание в данный момент
     *
     * @return bool
     */
    public function isCronLocked()
    {
        $lock_file = \Setup::$PATH . self::LOCK_FILE;
        return file_exists($lock_file);
    }

    /**
     * Возвращает время последнего запуска cron
     * @return bool|string
     */
    public function getLastExecTimeStr()
    {
        $time = HashStoreApi::get(self::LAST_TIME_KEY);
        if ($time) {
            return date('d.m.Y H:i:s', $time);
        } 
        return t('никогда');
    }
}

