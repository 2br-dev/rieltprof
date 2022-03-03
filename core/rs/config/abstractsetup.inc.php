<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Config;

use RS\Performance\Timing;

/**
* Класс содержит системные настройки, которые можно переназначить в файле setup.php в корневой папке
* Текущий файл будет перезаписан при обновлении ядра.
*/
abstract class AbstractSetup
{
    public static 
        //Системные параметры
        $INSTALLED,
        
        //Общие параметры
        $VERSION          = '5.2.67',  //Текущая версия ядра
        $CLASS_EXT        = 'inc.php',    //Расширение файлов с классами
        $CUSTOM_CLASS_EXT = 'my.inc.php', //Расширение файлов с перегруженными классами
        $PATH,   //Путь к корневому каталогу системы = DOCUMENT_ROOT + FOLDER
        $ROOT,   //Путь к корневому каталогу хоста = DOCUMENT_ROOT
        $FOLDER, //Папка системы (разница между PATH и ROOT) Например: /eshop
        $DOMAIN, //Текущий домен, например: example.com
        $ZONE,   //Текущая зона домена, например: com
        
        //Параметры базы данных
        $DB_HOST         = 'localhost',
        $DB_PORT         = null,
        $DB_SOCKET       = null,
        $DB_NAME         = ' ',
        $DB_USER         = ' ',
        $DB_PASS         = ' ',
        $DB_CHARSET      = 'utf8',
        $DB_AUTOINIT     = true,
        $DB_INSTALL_MODE = true,        
        $DB_TABLE_PREFIX = '',

        $STUB_SCALE = 'axy', // Режим соотношения сторон для заглушек

        $COOKIE_AUTH_DOMAIN = null, //Домен, для которого будут установлены авторизационные и гостевые куки. Нужно менять только в случае организации субдоменной авторизации
        
        $TIMEZONE = 'Europe/Moscow', //Временная зона по-умолчанию
        $DEFAULT_ROUTE_ENABLE = true, //Включает, выключает маршрут по-умолчанию. /controller/action/
        
        $FIX_REMOTE_ADDR = true, //Записывать в $_SERVER['REMOTE_ADDR'] - всегда актуальный IP клиента
        
        //Smarty - параметры
        $SM_RELATIVE_TEMPLATE_PATH = '/templates', //Папка с шаблонами
        $SM_TEMPLATE_PATH,
        $SM_COMPILE_PATH           = '/cache/smarty/compile', //Папка для компилированных шаблонов
        $SM_CACHE_PATH             = '/cache/smarty/cache',   //Папка для кэш данных шаблонов
        $SM_COMPILE_CHECK          = false, //Если true, то перекомпилировать шаблоны по мере надобности. (Для разработки необходимо указывать true, на продакшн сервере - false)
        
        //Параметры обновления и проверки лицензии
        $CHECK_DOMAIN_TIMEOUT = 4,
        
        $RS_SERVER_PROTOCOL = 'https', //Протокол взаимодействия с сервером ReadyScript
        $RS_SERVER_DOMAIN   = 'readyscript.ru', //Доменное имя сервера ReadyScript
        $RS_API_SERVER_DOMAIN = 'readyscript.ru', //Доменное имя API сервера ReadyScript                
        $CHECK_LICENSE_SERVER,  //URL для запросов проверки лицензии
        $BUY_LICENSE_URL,       //URL страницы для покупки лицензии
        $UPDATE_URL,            //URL для запросов на обновление системы
        $MODULE_LICENSE_URL, //URL для запросов по модульным лицензиям
        $UPDATE_CHANNEL       = 'release', //Канал обновления

        //Параметры магазина дополнений
        $MARKETPLACE_DOMAIN = 'marketplace.readyscript.ru', // Домен магазина дополнений
        $MODULE_LICENSE_LOG_ENABLE = false,

        //Переназначение имени БД и Таблицы в ORM Объектах
        $DB_MAPPING = [],   //Массив ['ИМЯ КЛАССА ORM Объекта' => 'имя базы данных']
        $TABLE_MAPPING = [], //Массив ['ИМЯ КЛАССА ORM Объекта' => 'имя таблицы данных']
        
        //Настройки локализации
        $INTERNAL_ENCODING = 'UTF-8',
        $LOCALE            = 'ru_RU.UTF-8',
        $NUMERIC_LOCALE    = 'en_US.UTF-8',

        //Настройки чисел
        $PRECISION = 14,
        
        //Пути к общим ресурсам
        $RESOURCE_PATH = '/resource',
        $JS_PATH       = '/resource/js',
        $CSS_PATH      = '/resource/css',
        $IMG_PATH      = '/resource/img',

        //Язык по умолчанию
        $DEFAULT_LANG  = 'ru',
        
        //Параметры безопасности
        $AUTH_TRY_COUNT   = 50, //Количество неуспешных авторизации с одного IP, до бана функции авторизации
        $AUTH_BAN_SECONDS = 14400, //Количество секунд, на которое блокировать попытки авторизации с IP злоумышленника
        
        $CREATE_DIR_RIGHTS = 0755, //Права на вновь создаваемые папки
        $DEFAULT_THEME     = 'flatlines(blue)', //Тема, которая будет установлена сразу после инсталяции системы
        
        //Параметры для шаблонов
        $SCRIPT_TYPE,
        $MODULE_WATCH_TPL = '/moduleview',   //путь к альтернативной папке шаблонов модулей
        $THEME_XML        = 'theme.xml',     //Файл с информацией о теме оформления
        $RES_CSS_FOLDER   = '/resource/css', //Путь к CSS относительно папки темы
        $RES_JS_FOLDER    = '/resource/js',  //Путь к JS относительно папки темы
        $RES_IMG_FOLDER   = '/resource/img', //Путь к изображениям относительно папки темы
        $DEFAULT_LAYOUT   = '%THEME%/layout.tpl', //Шаблон с которого начинается рендеринг любой страницы
        
        //Параметры кэша
        $CACHE_MAIN_FOLDER  = '/cache',          //Корневая папка для всех кэш файлов
        $CACHE_FOLDER       = '/cache/engine',   //Папка для общего кэша системы
        $CACHE_TABLE_FOLDER = '/cache/tableact', //Папка с информацией об актуальности таблиц
        $CACHE_LANG_FOLDER  = '/cache/lang',     //Папка для кэширования языковых фраз
        $CACHE_USE_WATCHING_TABLE = true, //Если true, то будет делаться отметка об изменении таблицы, при запросах INSERT, UPDATE, DELETE. (можно использовать в \RS\Cache для определения неактуальности кэша)
        $CACHE_LANG_JS_FILE       = true, //Если false, то при каждом запуске скрипта будет проверяться необходимость создания языкового файла для JS
        $CACHE_TIME               = 300,  //Время жизни кэша
        $CACHE_BLOCK_ENABLED      = true, //Если true, то будет включено кэширование HTML кэширование в Smarty
        $CACHE_ENABLED            = true, //Если true, то будет включено кэширование данных в PHP.

        //Задания по расписанию
        $CRON_ENABLE       = true,

        //Параметры модулей
        $MODULE_FOLDER     = '/modules',  //Папка для модулей
        $MODULE_TPL_FOLDER = '/view',     //Папка для шаблонов относительно папки модулей
        $CONFIG_FOLDER     = '/config',   //Относительно $module_folder
        $CONFIG_CLASS      = 'file',      //Класс конфигурации модулей
        $CONFIG_XML        = 'module.xml',//Файл настроек модуля
        $HANDLERS_CLASS    = 'handlers',  //Класс обработчиков собития модулей
        $MY_HANDLERS_CLASS = 'myhandlers',//Приоритетный класс обработчиков событий
        
        //Параметры сессии
        $SESSION_TIME = 10800,  //Срок жизни сессии в секундах
        
        //Параметры административной панели
        $ADMIN_SECTION = 'admin', //Секция URL для административной панели
        
        //Отладка
        $DETAILED_EXCEPTION       = false, //Подробно отображать информацию об исключениях. На production серверах должно быть - false
        $HIDE_STRICT_WARNING      = true, //Отключает некоторые типы warning'ов на PHP 7+. Необходимо для корректного обновления системы с включенным параметром $DETAILED_EXCEPTION = true
        $WRITE_EXCEPTIONS_TO_FILE = false, //Писать лог исключений в файл
        $EXCEPTIONS_FILE          = '/exceptions.auto.txt', //Имя файла лога исключений, относительно корня
        $LOG_EXECUTE_TIME         = false, //Записывать время выполнения скриптов. false - не записывать. Рекомендуется включать только на время отладки
        $LOG_EXECUTE_FILE         = '/logs/exectime.log',
        $LOG_SQLQUERY_TIME        = false, //Записывает все запросы к базе и время их выполнения. false - не записывать. Рекомендуется включать исключительно на время отладки
        $LOG_QUERY_STACK_TRACE_LEVEL = 0, //Если значение больше нуля, указывает, что необходимо логировать вместе с запросом и стек вызовов, чтобы понять откуда идет запрос. Актуально при $LOG_SQLQUERY_TIME = true
        $LOG_SETTINGS_DB_ADAPTER_MAX_FILE_SIZE = 1, //Максимальный размер файла лога для SQL запросов

        $COMPRESS_ADMIN_ENABLE    = true, //Возможность оптимизации CSS и JS в админ. панели. Общий выключатель. Если false, то $COMPRESS_ADMIN_CSS, $COMPRESS_ADMIN_JS не действуют
        
        //Параметры оптимизатора CSS, JS
        $COMPRESS_CSS      = 0, //0 - выключено, 1 - автоматически объединять CSS в один файл, 2 - объединять и оптимизировать
        $COMPRESS_ADMIN_CSS = 1, //Аналогично $COMPRESS_CSS
        $COMPRESS_CSS_PATH = '/cache/resource/min_css', //Путь, где будут храниться кэшированные файлы оптимизированных CSS.
        
        $COMPRESS_JS       = 0, //0 - выключено, 1 - автоматически объединять JS в один файл, 2 - объединять и оптимизировать
        $COMPRESS_ADMIN_JS = 1, //Аналогично $COMPRESS_JS
        $COMPRESS_JS_PATH  = '/cache/resource/min_js', //Путь к сжатым js файлам
        
        $JS_POSITION_FOOTER  = false, //Если true, то позиция скриптов по-умолчанию - низ HTML страницы
        $CSS_POSITION_FOOTER = false, //Если true, то позиция стилей по-умолчанию - низ HTML страницы
        
        $STORAGE_DIR        = '/storage',     //Папка для пользовательских данных
        $LOGS_DIR           = '/storage/logs', //Папка для лог файлов
        $TMP_REL_DIR        = '/storage/tmp', //Директория для временных файлов
        $BRAND_SPLASH_IMAGE = '/storage/branding/background.jpg', //Если данный файл присутствует, то он будет использован в качестве фона на странице авторизации в административную панель
        $TMP_DIR,
        $DOCTYPE     = 'HTML',
        
        $SECRET_KEY = 'A6k3a4leohg7b', //Секретный ключ для формирования путей к изображениям, закрытым файлам, и.т.д. Переопределяется в config.auto.php
        $SECRET_SALT = 'B6&3mkseoiwmd',//Соль безопасности. Переопределяется в config.auto.php

        $DISABLE_CAPTCHA = false, //Если true, то капча не будет проверяться и всегда будет считаться правильной. Опция нужна для автоматизированного тестирования front-end'а
        
        $NOPHOTO_IMAGE = '/resource/img/photostub/nophoto.jpg', //Путь к заглушке фотографий по умолчанию
        $NOPHOTO_THEME_PATH = '/photostub', //Путь к загрушке фотографий относительно картинок темы
        $NOPHOTO_THEME_FILE = 'nophoto.jpg',

        $METER_RECALCULATE_INTERVAL = 300, //5 мин, Интервал в секундах, с которым будут пересчитываться счетчики в админ. панели
        $DISABLE_WIDGETS = [], //Список коротких идентификаторов виджетов, которые следует отключить. Например: main-widget-bestsellers
        
        $IS_CLI_MODE = false, //Флаг запуска из командной строки
        
        $YOUR_IP_BLOCKED = 'Доступ с вашего IP запрещен', //Фраза, отображаемая при обращении с заблокированного IP

        $ENABLE_DEBUG_PROFILING = false, //Включает возможность просмотра отчета о времени выполнения различных блоков

        $ENABLE_OLD_STYLE_BLOCK_ID = false, //Если true, то используется абсолютный путь на диске к шаблону в расчете block_id,
        //иначе - относительный. В случае значения false (ркомендовано) - папку сайта можно переносить на разные хостинги и настройки
        //блоков будут успешно считываться из базы. Опция создана для совместимости со старыми версиями ReadyScript.

        //Параметры установки ReadyScript по умолчанию. Эти данные будут использованы в модуле install
        $INSTALL_DB_HOST = '127.0.0.1',   //Хост БД
        $INSTALL_DB_PORT = 3306,
        $INSTALL_DB_NAME = 'readyscript', //Имя БД
        $INSTALL_DB_USERNAME = 'root',    //Пользователь БД
        $INSTALL_DB_PASSWORD = '',        //Пароль БД
        $INSTALL_ADMIN_LOGIN = '',        //Логин к административной панели
        $INSTALL_ADMIN_PASSWORD = '',     //Пароль к административной панели
        $INSTALL_SET_DEMO_DATA = true;    //Устанавливать демо-данные
        
    protected static 
        //Подключаемые модули
        $include_list = [
            '/core/system/autoload.inc.php',
            '/core/system/constants.inc.php',
            '/core/system/exceptions.inc.php',
            '/core/smarty/Smarty.class.php',
            '/core/csstidy/class.csstidy.php',
    ];
    
    /**
    * Инициализирует основные настройки системы
    * 
    * @return void
    */
    public static function init()
    {
        $start_time = microtime(true);

        self::initVars();
        self::checkPhpModules();

        @ini_set("mbstring.internal_encoding", self::$INTERNAL_ENCODING);
        ini_set('precision', self::$PRECISION);
        ini_set('serialize_precision', self::$PRECISION);
        setlocale(LC_ALL, self::$LOCALE);
        setlocale(LC_NUMERIC, self::$NUMERIC_LOCALE); //Разделителем между целой и дробной частью должна быть "точка"
        setlocale(LC_NUMERIC, self::$NUMERIC_LOCALE); //Фикс странного бага в Windows 10.0.17134.48, установка локали срабатывает со второго раза

        date_default_timezone_set(self::$TIMEZONE);
        mb_internal_encoding(self::$INTERNAL_ENCODING);

        error_reporting(self::$DETAILED_EXCEPTION ? E_ALL & ~E_STRICT : 0);

        if (self::$HIDE_STRICT_WARNING) {
            self::hideStrictWarning();
        }

        self::fixRemoteIP();
        self::SendHeader();
        self::defineVars();

        foreach (self::$include_list as $inc_file)
            include(self::$PATH.$inc_file);

        \RS\Language\Core::init(); //Активируем языковые функции, объявляем функцию t

        if (!empty($_SERVER["REMOTE_ADDR"])) {
            self::$IS_CLI_MODE = true;
            //Проверяем, не заблокирован ли IP
            if (class_exists('\Main\Model\BlockedIpApi')
                && \Main\Model\BlockedIpApi::isIpBanned($_SERVER["REMOTE_ADDR"]))
            {
                header('HTTP/1.0 503 Service Unavailable');
                exit(t(self::$YOUR_IP_BLOCKED));
            }
            include(self::$PATH.'/core/system/sessions.inc.php'); //Если запуск не из коммандной строки, то активируем сессию
        }

        self::startPerformanceMeasure($start_time);

        include(self::$PATH.'/core/system/licenser.inc.php');
        \RS\Event\Manager::init(); //Инициализируем события
        \RS\Language\Core::initThemeLang(); //Подключаем языковые файлы темы

        \RS\Event\Manager::fire('initialize'); //Вызываем событие инициализации
    }

    /**
    * Инициализирует составные переменные
    * 
    * @return void
    */
    protected static function initVars()
    {
        if (self::$PATH === null && self::$FOLDER === null) {
            self::$PATH = str_replace('\\', '/', realpath(__DIR__.'/../../../'));
            self::$FOLDER = @str_replace(rtrim(str_replace('\\', '/',$_SERVER['DOCUMENT_ROOT']),'/'), '', self::$PATH); 
            if (self::$FOLDER == self::$PATH) {
                //В случае, если папку не удается определить, считаем что система установлена в корне домена. 
                //Если это не так, то необходимо определить в файле /setup.inc.php свойства $FOLDER и $PATH
                self::$FOLDER = ''; 
            }
        }
        
        self::$ROOT = str_replace(self::$FOLDER.'@@', '', self::$PATH.'@@');
        self::$ZONE                      = self::getZone();
        self::$DOMAIN                    = @$_SERVER['HTTP_HOST'];
        
        self::loadPackageConfig();  // Подключение настроек комплектации
        self::loadConfig();         // Подключение настроек от инсталятора и панели администратора.
        self::loadLocalConfig();    // Подключение настроек для разработки                
        
        self::$SM_RELATIVE_TEMPLATE_PATH = self::$FOLDER.self::$SM_RELATIVE_TEMPLATE_PATH;
        self::$SM_TEMPLATE_PATH          = self::$ROOT.self::$SM_RELATIVE_TEMPLATE_PATH.'/';
        self::$SM_COMPILE_PATH           = self::$PATH.self::$SM_COMPILE_PATH.'/';
        self::$SM_CACHE_PATH             = self::$PATH.self::$SM_CACHE_PATH.'/';
        
        self::$RESOURCE_PATH             = self::$FOLDER.self::$RESOURCE_PATH; //Нужен в модуле шаблонов
        self::$JS_PATH                   = self::$FOLDER.self::$JS_PATH;
        self::$CSS_PATH                  = self::$FOLDER.self::$CSS_PATH;
        self::$IMG_PATH                  = self::$FOLDER.self::$IMG_PATH;
        
        self::$CACHE_FOLDER              = self::$PATH.self::$CACHE_FOLDER;
        self::$CACHE_TABLE_FOLDER        = self::$PATH.self::$CACHE_TABLE_FOLDER;
        
        self::$STORAGE_DIR               = self::$FOLDER.self::$STORAGE_DIR;
        self::$TMP_REL_DIR               = self::$FOLDER.self::$TMP_REL_DIR;
        self::$TMP_DIR                   = self::$ROOT.self::$TMP_REL_DIR;
        
        self::$CHECK_LICENSE_SERVER      = self::$RS_SERVER_PROTOCOL.'://update.'.self::$RS_SERVER_DOMAIN.'/checklicense/';
        self::$UPDATE_URL                = self::$RS_SERVER_PROTOCOL.'://update.'.self::$RS_SERVER_DOMAIN.'/update/';
        self::$MODULE_LICENSE_URL        = self::$RS_SERVER_PROTOCOL.'://update.'.self::$RS_SERVER_DOMAIN.'/module-license/';
        self::$BUY_LICENSE_URL           = self::$RS_SERVER_PROTOCOL.'://'.self::$RS_SERVER_DOMAIN.'/product/{script_type}/';

        self::$BRAND_SPLASH_IMAGE        = self::$FOLDER.self::$BRAND_SPLASH_IMAGE;
    }
    
    /**
    * Возвращает текущий домен первого уровня
    * 
    * @return string
    */
    private static function getZone()
    {
        if (preg_match('/.*\.([^.]+?)$/', @$_SERVER['HTTP_HOST'], $match)) {
            return $match[1];
        }
    }
    
    
    /**
    * Загружает настройки из файла конфигурации
    * @return void
    */
    public static function loadConfig()
    {
        static::loadExternalFile(self::$PATH.'/config.auto.php');
    }
    
    /**
    * Загружает настройки характерные для комплектации CMS
    * 
    * @return void
    */
    public static function loadPackageConfig()
    {
        static::loadExternalFile(self::$PATH.'/package.inc.php');
    }
    
    /**
    * Загружает внешний конфигурационный файл php
    * 
    * @param string $file - php file для подключения
    * @return mixed
    */
    public static function loadExternalFile($file)
    {
        if (file_exists($file)) {
            if (function_exists('opcache_invalidate') && ini_get('opcache.restrict_api') === false) {
                opcache_invalidate($file, true);
            }
            $data = include($file);
            foreach($data as $key => $value) {
                if ($value !== null && property_exists(get_called_class(), $key)) {
                    self::$$key = $value;
                }
            }
        }
    }
    
    /**
    * Загружает локальные настройки конфигурации
    * @return void
    */
    public static function loadLocalConfig()
    {
        if (file_exists(self::$PATH.'/_local_settings.php')) {
            require(self::$PATH.'/_local_settings.php');
        }
    }
    
    /**
    * Возвращает все определенные свойства в виде массива
    * 
    * @return array
    */
    public static function varsAsArray()
    {
        return get_class_vars(get_called_class());
    }    
    
    /**
    * Регистрирует переменные в качестве констант, чтобы их можно было использовать в объявлении переменных других классов
    * 
    * @return void
    */
    protected static function defineVars()
    {
        $arr = self::varsAsArray();
        foreach ($arr as $key=>$val) {
            if (is_int($val) || is_string($val) || is_bool($val) || is_null($val)) {
                define($key, $val);
            }
        }
    }
    
    /**
    * Отравляет базовые заголовки
    * 
    * @return void
    */
    protected static function SendHeader()
    {
        header("Content-type: text/html; charset=utf-8");
    }
    
    /**
    * Проверяет наличие необходимых для запуска скрипта модулей
    * 
    * @return void
    */
    protected static function checkPhpModules()
    {
        if (self::$INSTALLED) return;

        $need_modules = [];
        if (!function_exists('mcrypt_module_open')
            && !function_exists('openssl_public_decrypt')) {
            $need_modules[] = 'mcrypt or openssl';
        }
        if (!function_exists('filter_var')) {
            $need_modules[] = 'filter';
        }        
        if (!class_exists('\XMLReader', false)) {
            $need_modules[] = 'xmlreader';
        }
        if (!class_exists('\SimpleXMLElement', false)) {
            $need_modules[] = 'simplexml';
        }        
        
        if ($need_modules) {
            die('Some PHP modules are not installed: '.implode(',', $need_modules));
        }
    }

    /**
     * Скрывает предупреждения Declaration of ... should be compatible with... на PHP 7 и выше
     *
     * Данные предупреждения могут возникать в короткие промежутки времени в период обновления,
     * когда Ядро уже обновлено, а остальные модули еще не обновлены. Чтобы обеспечить стабильное
     * обновление даже с включенным параметром \Setup::$DETAILED_EXCEPTION = true, данный тип ошибок будет
     * подавляться. Для разработчиков рекомендуем отключить подавление данных ошибок с помощью параметра
     * \Setup::$HIDE_STRICT_WARNING = false в файле _local_settings.php
     */
    protected static function hideStrictWarning()
    {
        if (PHP_MAJOR_VERSION >= 7) {
            set_error_handler(function ($errno, $errstr) {
                return strpos($errstr, 'Declaration of') === 0;
            }, E_WARNING);
        }
    }
    
    /**
    * Фиксим проблему, связанную с тем, что в ключе REMOTE_ADDR может находиться
    * не IP клиента в случае использования схемы Apache + nginx. В случае использования 
    * "чистого" Apache рекомендуется отключать опцию создав файл в корне _local_settings.php 
    * \Setup::$FIX_REMOTE_IP = false;
    * 
    * @return void
    */
    protected static function fixRemoteIP()
    {
        if (\Setup::$FIX_REMOTE_ADDR) {
            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
            if (isset($ip) 
                && (preg_match('/^((25[0-5]|2[0-4]\d|[01]?\d\d?)\.){3}(25[0-5]|2[0-4]\d|[01]?\d\d?)$/', $ip) //IPv4
                    || preg_match('/^((^|:)([0-9a-fA-F]{0,4})){1,8}$/', $ip)) //IPv6
                ) {
                $_SERVER['_REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'];
                $_SERVER['REMOTE_ADDR'] = $ip;
            }
        }
    }

    /**
     * Если включены опции запускает замер производительности
     *
     * @param float $start_time Время старта исполнения файла
     * @return void
     */
    protected static function startPerformanceMeasure($start_time)
    {
        $timing = Timing::getInstance();
        $timing->initializePageInfo();
        $timing->startMeasure( Timing::TYPE_INITIALIZE, '-', '', $start_time);
    }
}