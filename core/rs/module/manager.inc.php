<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Module;

use RS\Cache\Manager as CacheManager;
use RS\Config\Loader as ConfigLoader;
use RS\Exception as RSException;
use RS\Orm\ConfigObject;
use RS\Router\Manager as RouterManager;

/**
* Класс содержит функции по работе с модулями. (которые находятся в папке /modules/)
* Получение списка модулей, возвращение их конфигурационных файлов, нахождение всех контроллеров модулей, и.т.д
*/
class Manager
{
    protected $module_folder = MODULE_FOLDER; //Берется из \Setup
    protected $config_folder = CONFIG_FOLDER; //Относительно $module_folder
    protected $config_class = CONFIG_CLASS;

    protected static $mod_list = null; //Кэширует список модулей в рамках одной сессии выполнения скрипта.

    /**
     * Возвращает массив модулей
     * Читает каталог modules и ищет в нем конфигурационные файлы модулей.
     * Модуль виден, только если у него есть конфигурационный файл!!!
     *
     * @param bool $cache_enabled - использовать кэш
     * @return Item[]
     */
    public function getList($cache_enabled = true)
    {
        if (!isset(self::$mod_list) || !$cache_enabled) {
            if ($cache_enabled) {
                self::$mod_list = CacheManager::obj()
                    ->expire(0)
                    ->tags(CACHE_TAG_MODULE)
                    ->request([$this, 'getList'], false);
            } else {
                $module_folder_list = scandir(\Setup::$PATH . $this->module_folder);

                $ret = [];
                foreach ($module_folder_list as $n => $item) {
                    if ($item == '.' || $item == '..') continue;
                    if ($this->moduleExists($item)) $ret[$item] = new Item($item);
                }
                self::$mod_list = $ret;
            }
        }
        return self::$mod_list;
    }

    /**
    * Возвращает список включенных модулей.
    * 
    * @param integer $site_id - ID сайта, если null, то будет использован текущий сайт. (модули активны в рамках сайта)
    * @param bool $cache_enabled - Если true, то будет использоваться кэширование
    * 
    * @return \RS\Module\Item[]
    */
    public function getActiveList($site_id = null, $cache_enabled = true)
    {
        if (!\Setup::$INSTALLED) {
            //Во время установки - все модули активные
            return $this->getList($cache_enabled);
        }

        if ($cache_enabled) {
            $is_admin_zone = RouterManager::obj()->isAdminZone();
            return CacheManager::obj()
                ->expire(0)
                ->tags(CACHE_TAG_MODULE)
                ->request([$this, 'getActiveList'], $site_id, false, $is_admin_zone);
        } else {
            $active_module_list = [];
            $full_list = $this->getList();
            //В административной панели все модули всегда включены!
            foreach ($full_list as $module) {
                $config = $module->getConfig($site_id);
                if ($config['installed'] && $config->isActive()) {
                    $active_module_list[] = $module;
                }
            }
            return $active_module_list;
        }
    }

    /**
     * Возвращает массив конфигураций модулей
     *
     * @return ConfigObject[]
     */
    public function getAllConfig()
    {
        $tmp = [];
        $list = $this->getList();
        foreach ($list as $modclass => $module) {
            $tmp[$modclass] = $module->getConfig();
        }
        return $tmp;
    }

    /**
     * Возвращает true, если модуль существует
     *
     * @param string $mod_name - имя модуля
     * @return bool
     */
    public function moduleExists($mod_name)
    {
        $config_prefix = str_replace('/', '\\', trim($this->config_folder, '/'));
        $class_name = $mod_name . '\\' . $config_prefix . '\\' . $this->config_class;
        return class_exists($class_name);
    }

    /**
     * Функция для статического вызова. Возвращает true, если модуль существует
     *
     * @param string $mod_name - имя модуля
     * @return bool
     */
    public static function staticModuleExists($mod_name)
    {
        $obj = new self();
        return $obj->moduleExists($mod_name);
    }

    /**
     * Функция для статического вызова. Возвращает true, если модуль включён
     *
     * @param string $mod_name - имя модуля
     * @return bool
     */
    public static function staticModuleEnabled($mod_name)
    {
        /** @var ConfigObject */
        if ($class_config = ConfigLoader::byModule($mod_name)) {
            return $class_config['enabled'];
        }
        return false;
    }

    /**
     * Возвращет путь к папке с файлом конфигурации модуля
     *
     * @param string $mod_name
     * @return string
     */
    public function configPath($mod_name)
    {
        return \Setup::$PATH . $this->module_folder . '/' . $mod_name . $this->config_folder;
    }

    /**
     * Возвращает все блочные контроллеры всех модулей в древовидном виде
     */
    public function getBlockControllers()
    {
        $list = [];
        $active_modules = $this->getActiveList();
        
        foreach ($active_modules as $mod) {
            $controllers = $mod->getBlockControllers();
            if (!empty($controllers)) {
                $module_config = $mod->getConfig();
                $list[$mod->getName()] = [
                    'moduleItem' => $mod,
                    'moduleTitle' => $module_config['name'],
                    'controllers' => $controllers
                ];
            }
        }
        return $list;
    }

    /**
     * Синхронизирует базу данных для всех модулей
     * возвращает количество обновленных таблиц
     *
     * @return int
     */
    public function syncDb()
    {
        $modules = $this->getList(false);
        $count = 0;
        foreach($modules as $item) {
            $objects = $item->getOrmObjects();
            foreach($objects as $orm_object) {
                $orm_object->dbUpdate();
                $count++;
            }
        }
        return $count;
    }
}
