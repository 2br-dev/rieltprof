<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Cache;

use Designer\Model\DesignAtoms\Attrs\SelectFieldValueAsTree;

/**
* Класс принудительной очистки кэша
*/
class Cleaner
{
    const
        //Условные обозначения разных типов кэша
        CACHE_TYPE_COMMON = 'common', //Общий кэш - контроллируемый классом Cache
        CACHE_TYPE_MIN = 'min',  //Кэш - минимизированные файлы CSS и JS
        CACHE_TYPE_TPLCOMPILE = 'tplcompile', //Кэш - компилированные файлы шаблонов Smarty
        CACHE_TYPE_TABLEACT = 'tableact', //Кэш - информация об актуальности таблиц
        CACHE_TYPE_AUTOTPL = 'autotpl', //Кэш - автосгенерированные шаблоны
        CACHE_TYPE_FULL = 'full';
    
    public
        $dirs; //Массив, содержит сведения какой в какой папке лежит тот или иной тип кэша
    
    public static $obj;
    
    public static function obj()
    {
        if (!isset(self::$obj)) self::$obj = new self();
        return self::$obj;
    }    
    
    function __construct()
    {
        $this->dirs = [
            self::CACHE_TYPE_COMMON => [\Setup::$CACHE_FOLDER, \Setup::$CACHE_TABLE_FOLDER],
            self::CACHE_TYPE_MIN => [\Setup::$PATH.\Setup::$COMPRESS_CSS_PATH, \Setup::$PATH.\Setup::$COMPRESS_JS_PATH],
            self::CACHE_TYPE_TPLCOMPILE => [\Setup::$SM_COMPILE_PATH, \Setup::$SM_CACHE_PATH],
            self::CACHE_TYPE_TABLEACT => [\Setup::$CACHE_TABLE_FOLDER]
        ];
    } 
    
    /**
    * Очищает кэш
    * 
    * @param mixed $type см. константы CacheCleaner::CACHE_TYPE_..... если null, то очищается весь кэш
    * @return boolean
    */
    function clean($type = null)
    {
        if ($type === null || $type == self::CACHE_TYPE_FULL) {
            foreach($this->dirs as $folders) {
                foreach($folders as $folder) {
                    \RS\File\Tools::deleteFolder($folder, false);
                }
            }
            if ($type == self::CACHE_TYPE_FULL) {
                $this->cleanAutoTpl();
                $this->cleanOpcache();
            }
            if (\RS\Module\Manager::staticModuleExists('designer') && \RS\Module\Manager::staticModuleEnabled('designer')){
                $block_api = new \Designer\Model\BlocksApi();
                $block_api->clearResourceCacheFolder();
            }
            return true;
        } 
        elseif (method_exists($this, 'clean'.$type)) {
            $func_name = 'clean'.$type;
            return $this->$func_name();
        }
        elseif (isset($this->dirs[$type])) {
            foreach($this->dirs[$type] as $folder) {
                \RS\File\Tools::deleteFolder($folder, false);
            }
            return true;
        }
        return false;
    }
    
    /**
    * Очищает автоматически сгенерированные шаблоны для административной панели
    * @return void
    */
    function cleanAutoTpl()
    {
        $dir = \Setup::$PATH.\Setup::$MODULE_FOLDER;
        $modules = array_diff(scandir($dir), ['.','..']);
        foreach($modules as $module) {
            if (is_dir($dir.'/'.$module)) {
                $mask = $dir.'/'.$module.'/view/form';
                if (is_dir($mask) && ($files = glob($mask.'/*.auto.tpl')) ) {
                    foreach($files as $file) {
                        unlink($file);
                    }
                }
            }
        }
        return true;
    }
    
    /**
    * Очищает кэш акселераторов, если таковой имеется
    * @return void
    */
    function cleanOpcache()
    {
        if (ini_get('opcache.restrict_api') === false) {
            if (function_exists('opcache_reset')) opcache_reset();
            if (function_exists('apc_clear_cache')) apc_clear_cache();
        }
    }
}
