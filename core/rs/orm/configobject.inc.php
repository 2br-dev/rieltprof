<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Orm;

use Main\Model\ModuleLicenseApi;
use RS\AccessControl\AbstractModuleRights;
use RS\Cache\Manager as CacheManager;
use RS\Db\Exception as DbException;
use RS\Event\Exception as EventException;
use RS\Exception as RSException;
use RS\Module\ConfigInterface;
use RS\Module\Exception as ModuleException;
use RS\Orm\Storage\Serialized as StorageSerialized;
use RS\Module\Item as ModuleItem;
use RS\Site\Manager as SiteManager;

/**
 * Базовый core объект для конфигурационных файлов модулей
 */
abstract class ConfigObject extends AbstractObject implements ConfigInterface
{
    const CACHE_TAG = 'config_cache';

    /** @deprecated (08.18) */
    const ACCESS_BIT_READ = 0; //Присутствует для корректного обновления со старых версий. Не использовать
    /** @deprecated (08.18) */
    const ACCESS_BIT_WRITE = 1; //Присутствует для корректного обновления со старых версий. Не использовать

    protected static $init_default_method = '_configInitDefaults';

    public static $table = 'module_config';

    /**
     * Объявляет стандартные поля у объектов
     * @return PropertyIterator
     */
    function _init()
    {
        $properties = $this->getPropertyIterator()->append([
            t('Основные'),
                'name' => new Type\Varchar([
                    'useToSave' => false,
                    'description' => t('Название модуля'),
                    'readOnly' => true,
                ]),
                'description' => new Type\Varchar([
                    'useToSave' => false,
                    'maxLength' => 500,
                    'description' => t('Описание'),
                    'readOnly' => true,
                ]),
                'is_system' => new Type\Integer([
                    'useToSave' => false,
                    'maxLength' => 1,
                    'description' => t('Это системный модуль?'),
                    'visible' => false
                ]),
                'dependfrom' => new Type\Varchar([
                    'useToSave' => false,
                    'description' => t('Зависит от модулей(через запятую)'),
                    'visible' => false,
                    'readOnly' => true
                ]),
                //Пример версии: 0.1.0.0  -
                //первая цифра - изменения, влияющие на совместимость,
                //вторая - изменение в стуктуре БД,
                //третья - мелкие правки,
                //четвертая - ревизия из системы контроля версий
                'version' => new Type\Varchar([
                    'useToSave' => false,
                    'maxLength' => 10,
                    'description' => t('Версия модуля'),
                    'readOnly' => true
                ]),
                'version_date' => new Type\Varchar([
                    'useToSave' => false,
                    'maxLength' => '20',
                    'visible' => false
                ]),
                /**
                 * core_version
                 * Например: '0.1.0.0' (одна версия)
                 * или '0.1.0.0 - 0.2.0.0'  (Диапазон версий)
                 * или '>=0.1.0.156' или '<=0.1.0.200' (для всех версий младше или старше требуемой)
                 * Можно указать смешанно, через запятую так: '<=0.1.0.200, 0.2.0.0 - 0.3.0.0, 1.0.0.0, 1.1.0.0'
                 */
                'core_version' => new Type\Varchar([
                    'useToSave' => false,
                    'description' => t('Необходимая версия системы'),
                    'readOnly' => true
                ]),
                'author' => new Type\Varchar([
                    'useToSave' => false,
                    'description' => t('Автор модуля'),
                    'readOnly' => true
                ]),
                'installed' => new Type\Integer([
                    'maxLength' => 1,
                    'visible' => false
                ]),
                //Timestamp обновления модуля
                'lastupdate' => new Type\Integer([
                    'visible' => false
                ]),
                'enabled' => new Type\Integer([
                    'maxLength' => 1,
                    'description' => t('Включен'),
                    'checkboxview' => [1, 0],
                    'template' => '%main%/form/configobject/enabled.tpl',
                ]),
                'deactivated' => new Type\Integer([
                    'description' => t('Деактивирован'),
                    'maxLength' => 1,
                    'visible' => false,
                ]),
                //Утилиты по обслуживанию модуля
                'tools' => new Type\ArrayList([
                    'useToSave' => false,
                    'visible' => false
                ])
        ]);
        return $properties;
    }

    function beforeWrite($flag)
    {
        if (!$this->isMultisiteConfig()) {
            //Для модулей, чьи конфигурации одинаковые на всех мультисайтах
            $this['site_id'] = 0;
        }
    }

    function afterWrite($flag)
    {
        //Сбрасываем кэш модулей
        CacheManager::obj()->invalidateByTags(self::CACHE_TAG);
    }

    /**
     * Возвращает первичный ключ
     *
     * @return array
     */
    function getPrimaryKeyProperty()
    {
        return ['site_id', 'module'];
    }

    /**
     * Возвращает объект, отвечающий за хранилище
     *
     * @return Storage\AbstractStorage|StorageSerialized
     */
    function getStorageInstance()
    {
        return new StorageSerialized($this, [
            'primary' => [
                'module' => ModuleItem::nameByObject($this, false)
            ]
        ]);
    }

    /**
     * Метод инициализирует значения по умолчанию
     *
     * @return void
     * @throws ModuleException
     */
    function _configInitDefaults()
    {
        $this['enabled'] = true; //Включаем по-умолчанию модуль
        $this['installed'] = false;
        $this['site_id'] = SiteManager::getSiteId();
        $this->getFromArray($this->getDefaultValues());
        $this->_initDefaults(); //Вызов стандартного метода установки параметров 
    }

    /**
     * Загружает объект из базы данных
     *
     * @param integer $site_id ID сайта
     * @return bool
     */
    function load($site_id = null)
    {
        if (!$this->isMultisiteConfig()) {
            $site_id = 0;
        }

        if ($site_id !== null) {
            $this['site_id'] = $site_id;
        }

        if ($this['site_id'] !== null && $this['site_id'] !== false) {
            $result = parent::load();
            if ($result === false) {
                //Проверяем установлен ли модуль. 
                //Модуль считается установленным, если сохранена конфигурация хотя бы для одного сайта
                $this_module = ModuleItem::nameByObject($this, false);
                $this['installed'] = Request::make()
                        ->from($this)->where(['module' => $this_module])->count() > 0;
            }
        }
        return true;
    }

    /**
     * Загружает объект из кэша, если не удается, то из БД
     *
     * @param integer $site_id - ID сайта
     * @return bool
     */
    function loadFromCache($site_id = null)
    {
        if (!$this->isMultisiteConfig()) {
            $site_id = 0;
        }

        if ($site_id !== null) {
            $this['site_id'] = $site_id;
        }

        $cache = CacheManager::obj();
        $cache_id = $cache
            ->tags(self::CACHE_TAG)
            ->generateKey(self::CACHE_TAG . $this->_self_class . $site_id);

        $validate = $cache->validate($cache_id);
        if ( $validate && ($values = $cache->read($cache_id)) ) {
                $this->getFromArray($values);
        } else {
            if ($result = $this->load($site_id)) {
                $cache->write($cache_id, $this->getValues());
            }
            return $result;
        }

        return true;
    }

    /**
     * Возвращает true если у лицензии на данный модуль истёк период обновлений
     *
     * @return bool
     * @throws DbException
     * @throws RSException
     * @throws EventException
     */
    public function isLicenseUpdateExpired(): bool
    {
        if ($license_data = $this->getLicenseData() && isset($license_data['type'])) {
            return $license_data['type'] == 'exist' && !empty($license_data['update_expire']) && time() > (int)$license_data['update_expire'];
        }
        return false;
    }

    /**
     * Возвращает сохранённые данные по лицензии на модуль
     *
     * @return array|null
     * @throws DbException
     * @throws RSException
     * @throws EventException
     */
    public function getLicenseData()
    {
        if (!ModuleLicenseApi::isSystemModule($this->getModuleId())) {
            $module_name = ModuleItem::nameByObject($this);
            $data = ModuleLicenseApi::getDataByModule($module_name);

            return $data;
        }
        return null;
    }

    /**
     * Возвращает папку для хранения файлов данного модуля
     * @return string
     */
    function getModuleStorageDir()
    {
        $this_module = ModuleItem::nameByObject($this, false);
        $module_storage_dir = \Setup::$PATH . \Setup::$STORAGE_DIR . DS . $this_module;
        if (!is_dir($module_storage_dir)) {
            \RS\File\Tools::makePath($module_storage_dir);
        }
        return $module_storage_dir;
    }

    /**
     * Возвращает true, если конфигурация модуля может быть разной на каждом мультисайте
     *
     * @return bool
     */
    function isMultisiteConfig()
    {
        return true;
    }

    /**
     * Загружает значения свойств по-умолчанию из файла module.xml
     * При перегрузке данного метода, обязательно вызывайте родительский метод
     *
     * @return array
     * @throws ModuleException
     */
    public static function getDefaultValues()
    {
        $this_module = ModuleItem::nameByObject(get_called_class());
        $filename = \Setup::$PATH . \Setup::$MODULE_FOLDER . '/' . $this_module . \Setup::$CONFIG_FOLDER . '/' . \Setup::$CONFIG_XML;
        if (!$result = ModuleItem::parseModuleXml($filename)) {
            throw new ModuleException(t('Не удается найти или распарсить XML файл конфигурации модуля %module - %file', ['module' => $this_module, 'file' => $filename]));
        }

        return $result;
    }

    /**
     * Возвращает объект, который содержит информацию о правах модуля
     *
     * @return AbstractModuleRights
     */
    public function getModuleRightObject()
    {
        $class_name = ModuleItem::nameByObject($this) . '\Config\ModuleRights';
        if (!class_exists($class_name)) {
            $class_name = '\RS\AccessControl\DefaultModuleRights';
        }

        return call_user_func([$class_name, 'getInstance'], $this);
    }

    /**
     * Возвращает активен ли модуль
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this['enabled'] && (__MODULE_LICENSE_IS_SYSTEM_MODULE($this->getModuleId()) || !$this['deactivated']);
    }

    /**
     * Возвращает идентификатор модуля, к которому принадлежит данный конфиг
     *
     * @return string
     */
    public function getModuleId()
    {
        return ModuleItem::nameByObject($this);
    }
}
