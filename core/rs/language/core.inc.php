<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/  

namespace RS\Language {

    use RS\Application\Application;
    use \RS\Cache\Manager as CacheManager;
    use RS\http\Request;
    use RS\Site\Manager;

    /**
* API языковых функций.
*/
class Core
{
    const
        COOKIE_ADMIN_LANG = 'admin_lang',
        COOKIE_CUSTOMER_LANG = 'customer_lang',
        DEFAULT_BASE_LANG = 'ru';
        
    public static
        $cache_current_lang,
        $cache_js_folder = CACHE_LANG_FOLDER,
        $lang_folder = '/lang',
        $mod_lang_folder = '/view/lang',
        $theme_lang_folder = '/resource/lang',
        $lang_php_file = 'messages.lng.php',
        $lang_js_file = 'messages.js.php',
        $messages = [], //Сообщения текущей локали для PHP
        $messages_js = [], //Сообщения текущей локали для JS
        $messages_js_lastmodify;
    
    private static
        $params,
        $translated;
    
    
    /**
    * Инициализируем языковые файлы
    */
    public static function init($cache_enabled = true)
    {
        if ($cache_enabled) {
                list(self::$messages, 
                     self::$messages_js,
                     self::$messages_js_lastmodify
                     ) = CacheManager::obj()
                                ->expire(0)
                                ->tags(CACHE_TAG_MODULE)
                                ->request([__CLASS__, 'init'], false, self::getCurrentLang());
        } else {
            $lang = self::getCurrentLang();
            $system_lang_folder = self::getSystemLangFolder().'/'.$lang;
            self::connectFolder($system_lang_folder);
            
            $modules_api = new \RS\Module\Manager();
            foreach ($modules_api->getList() as $module) {
                $mod_lang_folder = $module->getFolder().self::$mod_lang_folder.'/'.$lang;
                self::connectFolder($mod_lang_folder);
            }
            
            return [
                self::$messages,
                self::$messages_js,
                self::$messages_js_lastmodify
            ];
        }        
    }
    
    /**
    * Подключает языковые файлы темы оформления
    * @return void
    */
    public static function initThemeLang()
    {
        if (!\RS\Router\Manager::obj()->isAdminZone()) { 
            $lang = self::getCurrentLang();
            $theme_data = \RS\Theme\Manager::getCurrentTheme();
            $theme_lang_folder = \Setup::$SM_TEMPLATE_PATH.$theme_data['theme'].self::$theme_lang_folder.'/'.$lang;
            self::connectFolder($theme_lang_folder);
        }
    }
    
    /**
    * Возвращает папку с системными локализациями
    * @return string
    */
    public static function getSystemLangFolder()
    {
        return \Setup::$PATH.\Setup::$RESOURCE_PATH.self::$lang_folder;
    }
    
    /**
    * Возвращает список системных локалий
    * @return array();
    */
    public static function getSystemLanguages($cache_enabled = false)
    {
        if ($cache_enabled) {
                return CacheManager::obj()
                                ->expire(0)
                                ->request([__CLASS__, 'getSystemLanguages'], false);
        } else {
            $folder_list = glob(self::getSystemLangFolder().'/*', GLOB_ONLYDIR);
            $locale_list = [
                strtolower(self::getBaseLang()) => ucfirst(self::getBaseLang())
            ];
            
            if ($folder_list) {
                foreach($folder_list as $locale) { //Формируем двухбуквенные абревиатуры языков
                    $locale = substr(basename($locale),0,2);
                    $locale_list[strtolower($locale)] = ucfirst($locale);
                }
            }
            return $locale_list;
        }                
    }
    
    /**
    * Подключает нужный языковый файл в директории
    */
    public static function connectFolder($path)
    {
        self::$messages += self::loadLangFile($path.'/'.self::$lang_php_file);
        $js_file = $path.'/'.self::$lang_js_file;
        if (file_exists($js_file)) {
            self::$messages_js += self::loadLangFile($js_file);
            $last_modify = filemtime($js_file);
            if (self::$messages_js_lastmodify === null || self::$messages_js_lastmodify < $last_modify) {
                self::$messages_js_lastmodify = $last_modify;
            }
        }
    }
    
    /**
    * Возвращает массив с фразами на текущем языке
    */
    protected static function loadLangFile($file)
    {
        if (file_exists($file)) {
            $messages_local = include($file);
            if (!is_array($messages_local)) {
                throw new Exception("Language file '$file' should return array");
            }
            return $messages_local;
        }
        return [];
    }
    
    /**
    * Устанавливает системный язык
    * 
    * @param string $lang
    * @return boolean
    */
    public static function setSystemLang($lang)
    {
        $system_langs = self::getSystemLanguages();
        $is_admin_zone = \RS\Router\Manager::obj()->isAdminZone();
        if (!$is_admin_zone || in_array(strtolower($lang), array_keys($system_langs))) {
            self::$cache_current_lang = $lang;
            $cookie_name = $is_admin_zone ? self::COOKIE_ADMIN_LANG : self::COOKIE_CUSTOMER_LANG;
            Application::getInstance()->headers->addCookie($cookie_name, $lang, time()+60*60*24*365*2, '/');
            return true;
        }
        return false;
    }

    /**
     * Устанавливает язык для текущей сессии выполнения PHP скрипта
     *
     * @param string $lang
     * @return bool
     */
    public static function setCurrentLang($lang)
    {
        $system_langs = self::getSystemLanguages();
        if ( in_array(strtolower($lang), array_keys($system_langs) ) ) {
            self::$cache_current_lang = $lang;
            self::$messages = [];
            self::$messages_js = [];
            self::$messages_js_lastmodify = null;
            self::init(false);
            self::initThemeLang();
            return true;
        }
        return false;
    }
        
    /**
    * Возвращает текущую локаль
    */
    public static function getCurrentLang()
    {
        if (!isset(self::$cache_current_lang)) {
            $request = Request::commonInstance();
            if (\RS\Router\Manager::obj()->isAdminZone()) {
                //Если это административная панель
                $sysLangs = self::getSystemLanguages();
                if ($request->cookie(self::COOKIE_ADMIN_LANG, TYPE_STRING)) {
                    //Ищем установленный язык в cookie
                    $current_lang = $request->cookie(self::COOKIE_ADMIN_LANG, TYPE_STRING);
                } else {
                    //Ищем предпочтительный язык у браузера
                    $accept_langs = explode(',', $request->server('Accept-Language'));
                    foreach($accept_langs as $lang) {
                        $try_lang = strtolower(substr($lang,0, 2));
                        if (isset($sysLangs[$try_lang])) {
                            $current_lang = $try_lang;
                            break;
                        }
                    }
                    if (!isset(self::$cache_current_lang)) {
                        //Устанавливаем язык по-умолчанию
                        $current_lang = \Setup::$DEFAULT_LANG;
                    }
                }

            } else {
                //Если это клиентская часть
                if ($request->cookie(self::COOKIE_CUSTOMER_LANG, TYPE_STRING)) {
                    //Читаем параметр из cookie
                    $current_lang = $request->cookie(self::COOKIE_CUSTOMER_LANG, TYPE_STRING);
                } else {
                    $site = \Setup::$INSTALLED ? Manager::getSite() : null;
                    $current_lang = ($site) ? $site->language : $request->cookie(self::COOKIE_CUSTOMER_LANG, TYPE_STRING, \Setup::$DEFAULT_LANG);
                }
            }
            self::$cache_current_lang = $current_lang;
        }
        return self::$cache_current_lang;
    }
    
    /**
    * Возвращает базовую локаль
    */
    public static function getBaseLang()
    {
        return self::DEFAULT_BASE_LANG;
    }
    
    public static function issetJsMessages()
    {
        return !empty(self::$messages_js);
    }
    
    /**
    * Возвращает перевод фразы на текущем языке
    * @param string $phrase - фраза на базовом языке
    * @param array $params - параметры для замены
    * @param string $alias - ID фразы (альтернативное имя для поиска, используется для больших текстов)
    */
    public static function translate($phrase, $params = [], $alias = null)
    {
        $to_translate = ($alias === null) ? $phrase : '!'.$alias;
        self::$translated = isset(self::$messages[$to_translate]);
        if (self::$translated) {
            $phrase = self::$messages[$to_translate];
        }

        //Применяем плагины
        self::$params = (array)$params;
        $phrase = preg_replace_callback('/(\[(.*?):%(.*?):(.*?)\])/', [__CLASS__, 'execPlugin'], $phrase);
        
        //Заменяем обычные переменные
        if (self::$params) {
            foreach(self::$params as $k=>$value) {
                $phrase = str_replace("%$k", $value, $phrase);
            }
        }
        $phrase = strtok($phrase, '^');
        return $phrase;
    }
    
    /**
    * Выполняет плагин к участку фразы
    */
    protected static function execPlugin($matches)
    {
        $plugin = __NAMESPACE__.'\Plugin\\'.$matches[2];
        $param_name = $matches[3];
        $param_value = isset(self::$params[$param_name]) ? self::$params[$param_name] : null;
        $value = $matches[4];
        $lang = self::$translated ? self::getCurrentLang() : self::getBaseLang();
        
        if (class_exists($plugin)) {
            $plugin_instance = new $plugin();
            if ($plugin_instance instanceof Plugin\PluginInterface) {
                return $plugin_instance->process($param_value, $value, self::$params, $lang);
            } else {
                throw new Exception('Language plugin should implements interface "PluginInterface"');
            }
        }
        return '';
    }
    
    /**
    * Возвращает имя файла словаря для текущего языка
    */
    public static function getScriptFilename()
    {
        $js_file = self::$cache_js_folder.'/'.self::getCurrentLang().'.js';
        $js_fullpath = \Setup::$PATH.$js_file;
        $dir = \Setup::$PATH.self::$cache_js_folder;
        if (!is_dir($dir)) @mkdir($dir, \Setup::$CREATE_DIR_RIGHTS, true);
        
        if (!file_exists($js_fullpath) || filemtime($js_fullpath) < self::$messages_js_lastmodify) {
            $data = 'lang.messages = '.json_encode(self::$messages_js);
            file_put_contents($js_fullpath, $data);
            touch($js_fullpath, self::$messages_js_lastmodify);
        }
        return $js_file.'?m='.self::$messages_js_lastmodify;
    }
}}

namespace {

    use RS\Language\Core;

    /**
    * Возвращает фразу в текущей локали. Сокращенный синтаксис
    * @param string $phrase - фраза на базовом языке
    * @param array $params - параметры для замены
    * @param string $context - контекст фразы
    * @return string
    */
    function t($phrase, $params = [], $alias = null) {
        return Core::translate($phrase, $params, $alias);
    }
}


