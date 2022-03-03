<?php
/**
 * Этот файл должен быть поставлен в крон для запуска ежеминутно
 */

use RS\Cron\LogCron;
use RS\Cron\Manager as CronManager;
use RS\File\Tools as FileTools;
use RS\HashStore\Api as HashStoreApi;

$readyscript_root = realpath(__DIR__ . '/../../');

//Подключаем файл с локальными настройками, если таковой существует.
if (file_exists('_local_settings.php')) {
    include_once('_local_settings.php');
}

require $readyscript_root . '/setup.inc.php';

//Актуально для облачной версии ReadyScript
if (defined('ACCOUNT_EXPIRED')) {
    die(t('Аккаунт заблокирован'));
}

class CronEventRiser
{
    const PHP_MIN_VERSION = '7.1';
    const LOCK_FILE = '/storage/locks/cron';

    private $current_time;
    private $last_execution_timestamp;

    public function __construct()
    {
        $this->current_time = time();
        $this->last_execution_timestamp = HashStoreApi::get(CronManager::LAST_TIME_KEY, $this->current_time - 60);

        if ($this->current_time - $this->last_execution_timestamp > 1440 * 60) {
            //Разрыв от предыдущего запуска не может превышать 1 суток
            $this->last_execution_timestamp = $this->current_time - 1440 * 60;
        }
    }

    /**
     * Запускает планировщик
     *
     * @return void
     * @throws Throwable
     */
    public function run()
    {
        if (phpversion() < self::PHP_MIN_VERSION) {
            $log_text = t('Текущая версия PHP - %0, ниже минимально допустимой - %1', [phpversion(), self::PHP_MIN_VERSION]);
            LogCron::getInstance()->write($log_text, LogCron::LEVEL_OTHER);
            echo $log_text;
            return;
        }

        if (!\Setup::$CRON_ENABLE) {
            echo t('Cron отключен в настройках системы (\Setup::$CRON_ENABLE)');
            return;
        }

        if ($this->isLocked()) {
            echo 'Cron locked';
            return;
        }

        // Устанавливаем блокировку
        $this->lock();

        // Сохраняем время начала выполнения
        RS\HashStore\Api::set(CronManager::LAST_TIME_KEY, (string)$this->current_time);

        // устанавливаем обработчин небросаемых ошибок
        set_error_handler(function (int $errno, string $errstr, string $errfile, int $errline) {
            $log_text = self::handbookErrnoStr($errno) . ": $errstr in $errfile on line $errline\n";
            $log_text .= "backtrace:\n";
            foreach (debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) as $n => $item) {
                $log_text .= "#$n ";
                if (isset($item['file'])) {
                    $log_text .= "{$item['file']} ({$item['line']}): ";
                }
                if (isset($item['class'])) {
                    $log_text .= "{$item['class']}{$item['type']}";
                }
                $log_text .= "{$item['function']}\n";
            }
            LogCron::getInstance()->write($log_text, LogCron::LEVEL_ERROR_HANDLER);
            return false;
        });

        try {
            $time = null;
            RS\Event\Manager::fire('cron', [
                'last_time' => $this->last_execution_timestamp,
                'current_time' => $this->current_time,
                'minutes' => $this->getArrayOfMinutesFromPeriod($this->last_execution_timestamp, $this->current_time)
            ],
                function($params, $event, $callback) use(&$time) {
                    if (is_array($callback)) {
                        echo "\n".t("Запуск обработчика %0", [$callback[0]])."\n";
                        $time = microtime(true);
                    }
                },
                function($params, $event, $callback) use(&$time) {
                if (is_array($callback)) {
                    $delta_sec = microtime(true) - $time;
                    echo "\n".t("Завершение обработчика %0. Время выполнения: %1 сек. ", [
                        $callback[0], number_format($delta_sec, 3)])."\n";
                }
            });

        } catch (\Throwable $e) {
            LogCron::getInstance()->write($e->__toString(), LogCron::LEVEL_THROWABLE);
            $this->unlock();

            throw $e;
        }
        // Убираем блокировку
        $this->unlock();

        echo 'complete';
    }

    /**
     * Создает файл блокировки, препятствующий одновременному запуску двух планировщиков
     *
     * @return void
     */
    private function lock()
    {
        $lock_file = \Setup::$PATH . self::LOCK_FILE;
        FileTools::makePath($lock_file, true);
        file_put_contents($lock_file, date('Y-m-d H:i:s'));
    }

    /**
     * Улаляет файл блокировки, записывает ошибки в лог-файл
     *
     * @return void
     */
    private function unlock()
    {
        $lock_file = \Setup::$PATH . self::LOCK_FILE;
        unlink($lock_file);
    }

    /**
     * Возвращает true, если планировщик уже работает в данное время
     *
     * @return bool
     */
    private function isLocked()
    {
        $lock_file = \Setup::$PATH . self::LOCK_FILE;

        if (file_exists($lock_file)) {
            // Если файл блокировки слишком давно не перезаписовался, то удаляем его
            if (time() > filemtime($lock_file) + CronManager::LOCK_EXPIRE_INTERVAL) {
                @unlink($lock_file);
            }
        }

        $skip_lock = (isset($_SERVER['argv'][1]) && $_SERVER['argv'][1] == '--force');

        return !$skip_lock && file_exists($lock_file);
    }


    /**
     * Возвращает массив с перечислением минут прошедших от предыдущего запуска
     *
     * @param integer $start_time
     * @param integer $end_time
     * @return array
     */
    private function getArrayOfMinutesFromPeriod($start_time, $end_time)
    {
        $start_time_rounded = strtotime(date('Y-m-d H:i', $start_time));
        $end_time_rounded = strtotime(date('Y-m-d H:i', $end_time));

        $arr = [];

        $time = $start_time_rounded;

        while ($time < $end_time_rounded) {
            $time += 60;

            $arr[] = $this->getNumberOfMinute($time);
        }

        return $arr;
    }

    /**
     * Возвращает номер минуты относительно 0 часов для заданного времени
     *
     * @param integer $time
     * @return integer
     */
    private function getNumberOfMinute($time)
    {
        return date('H', $time) * 60 + date('i', $time);
    }

    /**
     * Возвращает тип ошибки текстом
     *
     * @param int $key - код уровня ошибки
     * @return string|int
     */
    private static function handbookErrnoStr(int $key)
    {
        $values = [
            1 => 'Error',
            2 => 'Warning',
            4 => 'Parse',
            8 => 'Notice',
            16 => 'Core error',
            32 => 'Core warning',
            128 => 'Compile error',
            256 => 'Compile warning',
            512 => 'User error',
            1024 => 'User warning',
        ];

        return $values[$key] ?? $key;
    }
}

$cronEventRiser = new CronEventRiser();
$cronEventRiser->run();
