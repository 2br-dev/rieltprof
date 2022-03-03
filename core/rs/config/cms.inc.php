<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Config;

use RS\Config\Loader as ConfigLoader;
use RS\Cron\Manager as CronManager;
use RS\Log\LogManager;
use RS\Language\Core as LanguageCore;
use RS\Orm\AbstractObject;
use RS\Orm\Storage\Arrayfile;
use RS\Orm\Type;

/**
* Общая конфигурация CMS
*/
class Cms extends AbstractObject
{
    const
        ACCESS_ALLOW = 'allow',
        ACCESS_DISALLOW = 'disallow';
    
    protected
        $changelog_filename = 'changelog';
    
    function _init()
    {
        $this->getPropertyIterator()->append([
            t('Основные'),
                    //Параметры, которые будут перезаписывать значения в \Setup
                    'INSTALLED' => new Type\Integer([
                        'visible' => false
                    ]),
                    'FOLDER' => new Type\Varchar([
                        'visible' => false
                    ]),
                    'DB_INSTALL_MODE' => new Type\Integer([
                        'visible' => false
                    ]),
                    'DB_HOST' => new Type\Varchar([
                        'visible' => false
                    ]),
                    'DB_PORT' => new Type\MixedType([
                        'runtime' => false,
                        'visible' => false
                    ]),
                    'DB_SOCKET' => new Type\MixedType([
                        'runtime' => false,
                        'visible' => false
                    ]),
                    'DB_NAME' => new Type\Varchar([
                        'visible' => false
                    ]),
                    'DB_USER' => new Type\Varchar([
                        'visible' => false
                    ]),
                    'DB_PASS' => new Type\Varchar([
                        'visible' => false
                    ]),
                    'DB_TABLE_PREFIX' => new Type\Varchar([
                        'visible' => false
                    ]),
                    'SECRET_KEY' => new Type\Varchar([
                        'visible' => false
                    ]),
                    'SECRET_SALT' => new Type\Varchar([
                        'visible' => false
                    ]),
                    'ADMIN_SECTION' => new Type\Varchar([
                        'description' => t('Адрес панели администрирования'),
                        'maxLength' => 100,
                        'hint' => t('Например, если значение этого поля равно "admin", то адрес админ. панели будет http://<адрес_сайта>/admin/'),
                        'visible' => !defined('CLOUD_UNIQ')
                    ]),
                    'DEFAULT_THEME' => new Type\Varchar([
                        'visible' => false
                    ]),
                    'SM_COMPILE_CHECK' => new Type\Integer([
                        'description' => t('Всегда проверять шаблоны на предмет модификации'),
                        'hint' => t('Включение данной опции замедлит скорость работы сайта'),
                        'checkboxView' => [1,0]
                    ]),
                    'DETAILED_EXCEPTION' => new Type\Integer([
                        'description' => t('Подробно отображать информацию об исключениях'),
                        'hint' => t('Отображение ошибок значительно снижает уровень безопасности. Используйте эту опцию, только во время разработки и настройки сайта'),
                        'checkboxView' => [1,0]
                    ]),
                    'CACHE_ENABLED' => new Type\Integer([
                        'description' => t('Включить кэширование данных'),
                        'hint' => t('Кэширование данных значительно ускоряет работу сайта и снижает нагрузку на сервер'),
                        'checkboxView' => [1,0]
                    ]),
                    'CACHE_BLOCK_ENABLED' => new Type\Integer([
                        'description' => t('Включить кэширование Smarty'),
                        'hint' => t('Кэширование готового HTML поддерживают некоторые блоки. Информация в таких блоках будет обновляться только после принудительного сброса кэша или истечении срока жизни КЭШа'),
                        'checkboxView' => [1,0]
                    ]),
                    'CACHE_TIME' => new Type\Integer([
                        'description' => t('Время жизни КЭШ`а, в сек.'),
                        'hint' => t('Стандартное значение - 300 сек')
                    ]),
                    'COMPRESS_CSS' => new Type\Integer([
                        'description' => t('Подключаемые CSS файлы'),
                        'hint' => t('<b>Объединение CSS файлов</b> увеличивает скорость загрузки страницы в браузере<br><b>Объединение и оптимизация</b> значительно замедляет первичную генерацию объединенного CSS файла, но ускоряет его последующую загрузку'),
                        'listFromArray' => [[
                            0 => t('Оставлять как есть'),
                            1 => t('Объединять'),
                            2 => t('Объединять и оптимизировать'),
                        ]]
                    ]),
                    'COMPRESS_JS' => new Type\Integer([
                        'description' => t('Подключаемые JavaScript файлы'),
                        'hint' => t('<b>Объединение JavaScript файлов</b> увеличивает скорость загрузки страниц в браузере<br><b>Объединение и оптимизация</b> значительно замедляет первичную генерацию объединенного JS файла, но ускоряет его последующую загрузку'),
                        'listFromArray' => [[
                            0 => t('Оставлять как есть'),
                            1 => t('Объединять'),
                            2 => t('Объединять и оптимизировать'),
                        ]]

                    ]),
                    'JS_POSITION_FOOTER' => new Type\Integer([
                        'description' => t('Размещать по-умолчанию инструкции подключения скриптов внизу страницы'),
                        'hint' => t('Опцию следует использовать, если тема оформления создана с поддержкой подключения скриптов внизу страницы'),
                        'checkboxView' => [1,0]
                    ]),
                    'CSS_POSITION_FOOTER' => new Type\Integer([
                        'description' => t('Размещать по-умолчанию инструкции подключения стилей внизу страницы'),
                        'hint' => t('Опцию следует использовать, если тема оформления создана с поддержкой подключения стилей внизу страницы'),
                        'checkboxView' => [1,0]
                    ]),
                    'show_debug_header' => new Type\Integer([
                        'description' => t('Отображать администратору спецблок на сайте в режиме отладки'),
                        'checkboxView' => [1,0]
                    ]),
                    'CRON_ENABLE' => new Type\Integer([
                        'description' => t('Разрешить выполнение заданий cron<br>(последний запуск был: '.CronManager::obj()->getLastExecTimeStr().')'),
                        'default' => 1,
                        'hint' => t('Для работы планировщика необходимо настроить на хостинге ежеминутный запуск файла /core/cron/cron.php'),
                        'checkboxView' => [1,0]
                    ]),
                    'TIMEZONE' => new Type\Varchar([
                        'description' => t('Часовой пояс интернет-магазина'),
                        'list' => [function() {
                            $list = \DateTimeZone::listIdentifiers();
                            $result = [];
                            foreach($list as $item) {
                                $time = new \DateTime('now', new \DateTimeZone($item));
                                $result[$item] = $item." (UTC".$time->format('P').")";
                            }
                            return $result;
                        }]
                    ]),
                    'COMPRESS_ADMIN_ENABLE' => new Type\Integer([
                        'description' => t('Включить оптимизацию CSS, JS в админ. панели'),
                        'hint' => t('Опция ускоряет работу административной панели. Отключайте её, при необходимости, только на время разработки собственных модулей'),
                        'checkboxView' => [1,0]
                    ]),
                    'METER_RECALCULATE_INTERVAL' => new Type\Integer([
                        'description' => t('Интервал, с которым будет происходить обновление счетчиков в административной панели'),
                        'listFromArray' => [[
                            '60' => t('1 мин.'),
                            '120' => t('2 мин.'),
                            '180' => t('3 мин.'),
                            '240' => t('4 мин.'),
                            '300' => t('5 мин.')
                        ]]
                    ]),
                    'ENABLE_DEBUG_PROFILING' => new Type\Integer([
                        'description' => t('Включить профилирование кода'),
                        'hint' => t('В клиентской части сайта будет отображена кнопка "Отчет по производительности" в шапке. Включение данной опции замедлит работу системы. Включайте только во время разработки.'),
                        'checkboxView' => [1,0],
                        'visible' => !defined('CLOUD_UNIQ')
                    ]),
                    'LOG_QUERY_STACK_TRACE_LEVEL' => new Type\Integer([
                        'description' => t('Глубина сохраняемого стека вызовов для SQL запросов'),
                        'hint' => t('Стек вызовов нужен, чтобы понять в каком участке кода был вызван SQL-запрос. Данная опция актуальна только при включенном профилировании кода или логировании запросов к БД. Чем больше стек, тем более точно можно определить цепочку вызовов. 0 - отключено. 7 - оптимальное значение. Включайте только на время отладки!'),
                        'visible' => !defined('CLOUD_UNIQ')
                    ]),
                    'LOG_SQLQUERY_TIME' => new Type\Integer([
                        'description' => t('Включить логирование SQL запросов'),
                        'checkboxView' => [1,0],
                        'visible' => false
                    ]),
            t('Кэш'),
                '__cache__' => new Type\UserTemplate('%main%/form/sysoptions/cache.tpl'),
            
            t('Уведомления'),
                    'notice_from' => new Type\Varchar([
                        'maxLength' => 255,
                        'description' => t("Будет указано в письме в поле  'От'"),
                        'hint' => t('Например: "Магазин ReadyScript&lt;robot@ваш-магазин.ru&gt;" или просто "robot@ваш-магазин.ru". Перекрывается настройками сайта'),
                        'checker' => ['chkPattern', t('Неправильно указан email<br>Например: "Магазин ReadyScript&lt;robot@ваш-магазин.ru&gt;" или просто "robot@ваш-магазин.ru"'), '/^.+\&lt\;{1}[a-z0-9_.-]+\@[a-z0-9_.-]+.[a-z.]{2,6}\&gt\;{1}$|^[a-z0-9_.-]+\@[a-z0-9_.-]+.[a-z.]{2,6}$/']

                    ]),
                    'notice_reply' => new Type\Varchar([
                        'maxLength' => 255,
                        'description' => t("Куда присылать ответные письма? (поле Reply)"),
                        'hint' => t('Например: "Магазин ReadyScript&lt;support@ваш-магазин.ru&gt;" или просто "support@ваш-магазин.ru". Перекрывается настройками сайта')
                    ]),
                    'smtp_is_use' => new Type\Integer([
                        'description' => t('Использовать SMTP для отправки писем'),
                        'checkboxView' => [1,0],
                        'template' => '%main%/form/sysoptions/smtp_is_use.tpl',
                        'hint' => t('Настройки SMTP перекрываются настройками сайта'),
                    ]),
                    'smtp_host' => new Type\Varchar([
                        'description' => t('SMTP сервер')
                    ]),
                    'smtp_port' => new Type\Varchar([
                        'description' => t('SMTP порт'),
                        'maxLength' => 10
                    ]),
                    'smtp_secure' => new Type\Varchar([
                        'description' => t('Тип шифрования'),
                        'listFromArray' => [[
                            '' => t('Нет шифрования'),
                            'ssl' => 'SSL',
                            'tls' => 'TLS'
                        ]]
                    ]),
                    'smtp_auth' => new Type\Integer([
                        'description' => t('Требуется авторизация на SMTP сервере'),
                        'checkboxView' => [1,0]
                    ]),
                    'smtp_username' => new Type\Varchar([
                        'description' => t('Имя пользователя SMTP'),
                        'maxLength' => 100
                    ]),
                    'smtp_password' => new Type\Varchar([
                        'description' => t('Пароль SMTP'),
                        'maxLength' => 100
                    ]),
                    'hostname' => new Type\Varchar([
                        'description' => t('Hostname для отправки писем'),
                        'maxLength' => 100,
                        'hint' => t('Используется в заголовках MESSAGE-ID в письмах. Оставьте данное поле пустым, если вы желаете, чтобы hostname определялся автоматически.'),
                    ]),
            t('CAPTCHA'),
                'captcha_class' => new Type\Varchar([
                    'description' => t('Какую капчу использовать?'),
                    'list' => [['\RS\Captcha\Manager', 'getCaptchaList']],
                    'hint' => t('В случае выбора GoogleCaptcha, необходимо произвести настройку модуля KAPTCHA')
                ]),
            t('Система прав доступа'),
                'access_priority' => new Type\Varchar([
                    'description' => t('Приоритет прав'),
                    'hint' => t('Данное право будет также являться правом по умолчанию, если ни одна группа не задает явно разрешающее или запрещающее право'),
                    'listFromArray' => [[
                        self::ACCESS_ALLOW => t('Разрешение'),
                        self::ACCESS_DISALLOW => t('Запрещение'),
                    ]],
                ]),
            t('Переадресация'),
                'robot_user_agents' => new Type\Text([
                    'description' => t('Список агентов пользователей роботов'),
                ]),
            t('Система логирования'),
                '_log_settings_' => new Type\UserTemplate('%main%/form/sysoptions/log_settings.tpl', null, [
                    'log_manager' => LogManager::getInstance(),
                ]),
                'log_settings' => new Type\ArrayList([
                    'description' => t('Настройки логирования'),
                    'runtime' => false,
                    'visible' => false,
                ]),
        ]);
    }
    
    function _initDefaults()
    {
        $this['DB_INSTALL_MODE'] = 1;
        $this['show_debug_header'] = 1;
        $this['notice_from'] = 'no-reply@'.\Setup::$DOMAIN;
        $this['notice_reply'] = 'no-reply@'.\Setup::$DOMAIN;
        $this['TIMEZONE'] = \Setup::$TIMEZONE;
        $this['COMPRESS_ADMIN_ENABLE'] = 1;
        $this['METER_RECALCULATE_INTERVAL'] = 300;
        $this['captcha_class'] = 'RS-default';
        $this['access_priority'] = self::ACCESS_ALLOW;
        $this['robot_user_agents'] = "http://yandex.com/bots\nGoogle";
        $this['LOG_QUERY_STACK_TRACE_LEVEL'] = 7;
    }
    
    function loadFromCache($prmaryKeyValue = null)
    {
        return $this->load($prmaryKeyValue);
    }
    
    protected function getStorageInstance()
    {
        return new Arrayfile($this, ['store_file' => \Setup::$PATH.'/config.auto.php']);
    }
    
    /**
    * Возвращает данные для поля From писем
    * 
    * @param bool $return_email - Если true, то вернет email, если false - то вернет надпись, если null - то вернет массив
    * @return mixed
    */
    function getNoticeFrom($return_email = null)
    {
        $parsed = $this->getNoticeParsed('notice_from');
        
        if ($return_email === null) return $parsed;
        return $return_email ? $parsed['email'] : $parsed['string'];
    }
    
    /**
    * Возвращает данные для поля Reply писем
    * 
    * @param $return_email - Если true, то вернет email, если false - то вернет надпись, если null - то вернет массив
    * @return mixed
    */    
    function getNoticeReply($return_email = null)
    {
        $parsed = $this->getNoticeParsed('notice_reply');
        
        if ($return_email === null) return $parsed;
        return $return_email ? $parsed['email'] : $parsed['string'];        
    }
    
    /**
    * Возвращает историю изменения ядра
    * 
    * @param string| null $lang Идентификатор языка
    * @return string
    */
    function getChangelog($lang = null)
    {
        if ($lang === null) {
            $lang = LanguageCore::getCurrentLang();
        }
        
        $base_name = \Setup::$PATH.'/core/rs/config/'.$this->changelog_filename;
        $default_changelog = $base_name.'.txt';
        $lang_changelog = $base_name.'_'.$lang.'.txt';
        
        if (file_exists($lang_changelog)) {
            $changelog = $lang_changelog;
        } elseif (file_exists($default_changelog)) {
            $changelog = $default_changelog;
        } else {
            return false;
        }
        
        return file_get_contents($changelog);
    }

    
    /**
    * Возвращает разобранные данные для полей From или Reply уведомлений
    * Возьмет свойство из настроек сайта, если таковой существует
    * 
    * @param string $property - имя свойства, которое нужно разобрать
    * @return array
    */
    protected function getNoticeParsed($property)
    {
        $site = ConfigLoader::getSiteConfig();
        $result = ['line' => ($site && !empty($site[$property])) ? $site[$property] : $this[$property]];
        if (preg_match('/^(.*?)<(.*)>$/', html_entity_decode($result['line']), $match)) {
            $result['string'] = $match[1];
            $result['email'] = $match[2];
        } else {
            $result['string'] = '';
            $result['email'] = $result['line'];
        }
        return $result;
    }

    /**
     * Проверка на возможность сохранения данных. при этом объект заполняется из POST. насколько это возможно.
     *
     * @param array $user_post - дополнительные данные, которые будут добавлены к post_var
     * @param array $post_var - если передан, то будет использован вместо $_POST
     * @param array $files_var - если передан, то будет использован вместо $_FILES
     * @param array $usekeys - массив с ключами, которые нужно исползовать для заполнения объекта
     * @param array $exclude - массив с ключами, которые нужно исключить при заполнении объекта
     * @param string $flag - флаг опреации с объектом (self::INSERT_FLAG|self::UPDATE_FLAG)
     * @return boolean
     */
    public function checkData($user_post = [], $post_var = null, $files_var = null, $usekeys = null, $exclude = null, $flag = self::UPDATE_FLAG)
    {
        //Проверяем права на запись для модуля перед заполнением из POST
        if ($this->getLocalParameter('checkRights')){
            switch ($flag) {
                case self::INSERT_FLAG:
                    $checking_right = $this->getRightCreate();
                    break;
                case self::UPDATE_FLAG:
                    $checking_right = $this->getRightUpdate();
                    break;
                default:
                    $checking_right = $this->getRightUpdate();
            }
            if ($this->noWriteRights($checking_right)) {
                return false;
            }
        }//Конец проверки прав на запись для модуля

        return parent::checkData($user_post, $post_var, $files_var, $usekeys, $exclude, $flag);
    }

    /**
     * Вызывается перед сохранением объекта в storage
     * Если возвращено false, то сохранение не произойдет
     *
     * @param string $save_flag - тип операции (insert|update|replace)
     * @return void|false
     */
    public function beforeWrite($save_flag)
    {
        $this['LOG_SQLQUERY_TIME'] = $this['log_settings']['db_adapter']['enabled'] ?? 0;
        $this['LOG_SETTINGS_DB_ADAPTER_MAX_FILE_SIZE'] = $this['log_settings']['db_adapter']['max_file_size'] ?? 1;
    }
}
