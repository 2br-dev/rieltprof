<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Config\Migration;

use RS\Db\Adapter as DbAdapter;

/**
 * Конвертирует все таблицы в кодировку utf8mb4
 */
class ConvertTablesToAllowEmoji
{
    private $return_errors;

    /**
     * @param bool $return_errors - Если false, то ошибки будут игнорироваться
     */
    function __construct($return_errors = false)
    {
        $this->return_errors = $return_errors;
    }

    /**
     * Выполняет обновление кодировки всех таблиц в БД
     *
     * @param null $timeout
     * @param array $previous_state
     * @return array|bool|string
     * @throws \RS\Db\Exception
     */
    function patch($timeout = null, array $previous_state = [])
    {
        $error_tables = [];
        $result = DbAdapter::sqlExec('SHOW TABLE STATUS');

        $start_time = microtime(true);

        foreach ($result->fetchAll() as $n => $row) {
            if (isset($previous_state['next']) && $n <= $previous_state['next']) continue;

            $table = $row['Name'];
            $collation = $row['Collation'];
            if (!preg_match('/'.\Setup::$DB_TABLE_CHARSET.'/i', $collation)) {
                try {
                    $sql = "ALTER TABLE `$table` CONVERT TO CHARSET ".\Setup::$DB_TABLE_CHARSET;
                    DbAdapter::sqlExec($sql);
                } catch (\RS\Exception $e) {
                    $error_tables[] = $table;
                }
            }

            if ($timeout !== null && (microtime(true) - $start_time) > $timeout) {
                if ($error_tables && $this->return_errors) {
                    break;
                } else {
                    return [
                        'next' => $n
                    ];
                }
            }
        }

        if ($error_tables && $this->return_errors) {
            return t('Не удалось обновить кодировку у таблиц: '.implode(', ', $error_tables).'. Необходимо сократить длину индексов до 796 байт.');
        }

        return true;
    }
}