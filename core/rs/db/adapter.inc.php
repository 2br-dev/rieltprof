<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Db;

use RS\File\Tools;
use RS\Cache\Manager as CacheManager;
use RS\Performance\Timing;

/**
* Класс работы с базой данных
*/
class Adapter
{
    /** Указатель на соединение с базой */
    protected static $connection;
    /** Инстанс логирования */
    protected static $log;
    /** @var Timing */
    protected static $timing;
    
    public static function init()
    {
        self::$timing = Timing::getInstance();
        self::$log = LogDbAdapter::getInstance();
        
        if (self::connect()) {
            if (\Setup::$LOG_SQLQUERY_TIME) {
                $str = "-------- Page: ".@$_SERVER['HTTP_HOST'].@$_SERVER['REQUEST_URI'];
                self::$log->write($str, LogDbAdapter::LEVEL_INFO);
            }

            //Устанавливаем часовой пояс для Mysql 
            $time = new \DateTime('now', new \DateTimeZone(\Setup::$TIMEZONE));
            self::sqlExec("SET time_zone = '#time_zone'", [
                'time_zone' => $time->format('P')
            ]);
            
            //Устанавливаем не строгий режим MYSQL
            self::sqlExec("SET sql_mode = ''");
        }
    }
    
    /**
    * Открывает соединение с базой данных
    */
    public static function connect()
    {
        self::$connection = @mysqli_connect(\Setup::$DB_HOST,
            \Setup::$DB_USER,
            \Setup::$DB_PASS,
            \Setup::$DB_NAME,
            (int)\Setup::$DB_PORT,
            \Setup::$DB_SOCKET);

        if(self::$connection){
            self::sqlExec("SET names utf8");
        }

        return self::$connection;
    }
    
    /**
    * Закрывает соединение с базой данных
    */
    public static function disconnect()
    {
        return mysqli_close(self::$connection);
    }

    /**
     * Выполняет sql запрос
     *
     * @param mixed $sql - SQL запрос.
     * @param array | null $values - массив со знчениями для замены.
     * @return Result
     * @throws Exception
     */
    public static function sqlExec($sql, $values = null)
    {
        if(!self::$connection){
            if (\Setup::$INSTALLED) {
                throw new Exception(t('Не установлено соединение с базой данных'));
            }
            return new Result(false);
        }

        if ($values !== null) {
            foreach($values as $key => $val) {
                $sql = str_replace("#$key",  self::escape($val) , $sql);
            }
        }
        
        $start_time = microtime(true);
        $resource = mysqli_query(self::$connection, $sql);

        if (\Setup::$LOG_SQLQUERY_TIME || self::$timing->isEnable()) {
            $duration = (microtime(true)-$start_time);
            $stack = self::getSqlCallerStack();

            if (\Setup::$LOG_SQLQUERY_TIME) { //Лог в старом формате
                $str = $duration." - {$sql}".($stack ? "\n" . implode(";\n", $stack) : '');
                self::$log->write($str, LogDbAdapter::LEVEL_INFO);
            }

            if (self::$timing->isEnable()) { //Профилирование в новом формате
                self::$timing->addSqlQueryToMeasure($sql, $duration, $stack);
            }
        }
        
        // Блок необходим для определения неактуальности кэша
        if (\Setup::$CACHE_ENABLED && \Setup::$CACHE_USE_WATCHING_TABLE) { //Если кэш включен, то пишем информацию о модификации таблиц
            self::invalidateTables($sql);
        }

        $error_no = mysqli_errno(self::$connection);
        if ($error_no != 0){
            //Не бросаем исключения об ошибках - неверено указана БД, не существует таблицы, не выбрана база данных, когда включен режим DB_INSTALL_MODE
            if ( !\Setup::$DB_INSTALL_MODE || !($error_no == 1102 || $error_no == 1146 || $error_no == 1046 || $error_no == 1142) ) {
                throw new Exception($sql.mysqli_error(self::$connection), $error_no);
            }
        }
        return new Result($resource);
    }

    /**
     * Возвращает стек из $limit последних элементов, вызвавших запрос
     * @return []
     */
    private static function getSqlCallerStack()
    {
        if (\Setup::$LOG_QUERY_STACK_TRACE_LEVEL > 0) {
            $last_call_stack = [];
            $stack_trace = debug_backtrace();

            for ($i = 1; $i < count($stack_trace); $i++) {
                $item = $stack_trace[$i];
                if (isset($item['file'])) {
                    $file = Tools::buildRelativePath($item['file']);
                    $last_call_stack[] = $file . ':' . $item['line'];
                }

                if ($i == \Setup::$LOG_QUERY_STACK_TRACE_LEVEL + 1) break;
            }

            return $last_call_stack;
        } else {
            return [];
        }
    }
    
    /**
    * Парсит sql запрос и делает отметку о изменении таблиц, присутствующих в запросе
    * @param string $sql
    */
    private static function invalidateTables($sql)
    {
        //Находим имя таблицы и базы данных у SQL запроса
        $sql = preg_replace(['/\s\s+/','/\n/'], ' ', $sql); //Удаляем лишние пробелы и переносы строк
        
        if (preg_match('/^(INSERT|REPLACE|UPDATE|DELETE)/ui', $sql)) {
            if (preg_match('/^(INSERT|REPLACE).*?INTO([a-zA-Z0-9\-_`,\.\s]*?)(\(|VALUE|SET|SELECT)/ui', $sql, $match)) {
                $tables_str = $match[2];
            }
            if (preg_match('/^UPDATE (LOW_PRIORITY|IGNORE|\s)*([a-zA-Z0-9\-_`,\.]*).*?SET/ui', $sql, $match)) {
                $tables_str = $match[2];
            }
            if (preg_match('/^DELETE(LOW_PRIORITY|QUICK|IGNORE|\s)*(.*?)FROM(.*?)((WHERE|ORDER\sBY|LIMIT).*)?$/ui', $sql, $match)) {
                $tables_str = $match[3];
            }
        
            $tables_arr = preg_split('/[,]+/', $tables_str, -1, PREG_SPLIT_NO_EMPTY);
            foreach($tables_arr as $table) {
                //Очищаем имя базы данных и имя таблицы
                if (preg_match('/`(.*?)`.`(.*?)`/ui', $table, $match)
                    || preg_match('/(?:(.*?)\.)?(.*?)( (as)?.*?)?$/ui', $table, $match)) {
                    $db = trim($match[1],"` ");
                    $table = trim($match[2],"` ");
                    CacheManager::obj()->tableIsChanged($table, $db);
                }
            }
        }
    }
    
    /**
    * Экранирует sql запрос.
    * 
    * @param string $str SQL запрос
    * @return string экранированный запрос
    */
    public static function escape($str)
    {
        if(!self::$connection) {
            return $str;
        }

        return mysqli_real_escape_string(self::$connection, $str);
    }
    
    /**
    * Возвращает значение инкремента для последнего INSERT запроса
    * 
    * @return integer | bool(false)
    */
    public static function lastInsertId()
    {
        return mysqli_insert_id(self::$connection);
    }    

    /**
    * Возвращает число затронутых в результате запросов строк
    * 
    * @return integer
    */    
    public static function affectedRows()
    {
        return mysqli_affected_rows(self::$connection);
    }    
    
    /**
    * Возвращает версию Mysql сервера
    * 
    * @return string | bool(false)
    */
    public static function getVersion()
    {
        return mysqli_get_server_info(self::$connection);
    }
    
}

if (\Setup::$DB_AUTOINIT) {
    Adapter::init();
}