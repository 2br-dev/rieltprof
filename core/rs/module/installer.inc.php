<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Module;

use RS\AccessControl\Rights;
use RS\AccessControl\DefaultModuleRights;

/**
* Класс предназначен для установки нового модуля.
*/
class Installer extends \RS\Module\AbstractModel\BaseModel
{
    const
        STATUS_OK = 1,//Модуль распакован, импортирован.
        DELBEFORE_TYPE_FULL = 'full',
        DELBEFORE_TYPE_PART = 'part',
        DELBEFORE_TYPE_NONE = 'none';
        
    protected
        $module_folder = MODULE_FOLDER, //Берется из \Setup
        $config_folder = CONFIG_FOLDER, //Относительно $module_folder
        $config_class = CONFIG_CLASS,
    
        $tmp_path = '/storage/tmp/new_module',
        $allow_mime = ['application/zip'],
        $path,
        $prefix,
        $error = false,
        
        $options = [
            'insertDemoData' => false,
    ],
        //Заполняется при валидации
        $valid,
        $module_info,
        $module_name,
        $module_already_exists;
    
    protected static $instance;
        
    protected function __construct()
    {
        $this->path = \Setup::$PATH.$this->tmp_path;
    }
    
    /**
    * Возвращает экземпляр класса Installer
    * @return Installer 
    */
    public static function getInstance()
    {
        if (!isset(self::$instance)) self::$instance = new self();
        return self::$instance;
    }
    
    /**
    * Возвращает имя устанавливаемого модуля
    * 
    * @return string
    */
    public function getModName()
    {
        return $this->module_name;
    }
    
    /**
    * Распраковка zip архива прямо из POST данных
    * 
    * @return bool возвращает true в случае успешной распаковки, иначе - false
    */
    function extractFromPost($postVar)
    {
        $field_name = t('Файл модуля');
        if (!$postVar) {
            return $this->addError(t('Не выбран файл модуля'), $field_name, 'module');
        }

        if (($error = Rights::CheckRightError('modcontrol', DefaultModuleRights::RIGHT_CREATE)) !== false ) {
            return $this->addError($error);
        }
        
        if (($error = \RS\File\Tools::checkUploadError($postVar['error'])) !== false ) {
            return $this->addError($error, $field_name, 'module');
        }

        return $this->extractFromZip( $postVar['tmp_name'] );
    }
    
    /**
    * Распаковка zip архива во временное хранилище
    * 
    * @param string путь к zip файлу
    * @return bool Возвращает true, в случае успешной распаковки архива, иначе - false
    */
    function extractFromZip($filename)
    {
        $zip = new \ZipArchive();
        if ($zip->open($filename) !== true) {
            return $this->addError(t('Некорректный zip архив'), t('Файл модуля'), 'module');
        }

        \RS\File\Tools::makePath($this->path); //Создаем папку, если нужно
        \RS\File\Tools::deleteFolder($this->path, false); //Очищаем её
        
        $result = $zip->extractTo($this->path);
        if (!$result) {
            return $this->addError(t('Не удалось распаковать архив.'));
        }
        return true;
    }
    
    /**
    * Установка опций для Инсталяции модуля
    * 
    * @return void
    */
    function setOption($key, $value)
    {
        if (isset($this->options[$key])) {
            $this->options[$key] = $value; 
        } else {
            throw new Exception(t('Указана несуществующая опция установки модуля'));
        }
    }
    
    /**
    * Устанавливает модуль из временного хранилища
    * 
    * @return bool
    */
    function installFromTmp()
    {
        if (!$this->validateTmp()) return false;
        $this->cleanErrors();
        $this->moveFiles(); //Копирование файлов из временной папки.
        
        $module = new \RS\Module\Item($this->module_name);
        if (($install_result = $module->install($this->options)) !== true) {
            foreach($install_result as $error) {
                $this->addError($error);
            }
        }
       
        return !$this->hasError();
    }
    
    
    /**
    * Переносит файлы из временной папки в основную
    * 
    * @return void
    */
    protected function moveFiles()
    {
        $module_from = $this->path.'/'.$this->module_name;
        $module_to = \Setup::$PATH.$this->module_folder;
        \RS\File\Tools::moveWithReplace($module_from, $module_to, true);
    }
    
    /**
    * Возвращает список изменений в версиях у модуля
    * 
    * @return string | false
    */
    function getChangeLog()
    {
        $lang = strtolower(\RS\Language\Core::getCurrentLang());
        $changelog = $this->path.'/'.$this->module_name.$this->config_folder.'/changelog';
        
        if (file_exists($changelog."_{$lang}.txt")) {
            return file_get_contents($changelog."_{$lang}.txt");
        } else {
            if (file_exists($changelog.".txt")) {
                return file_get_contents($changelog.".txt");
            }
        }
        return false;
    }
    
    /**
    * Получение информации о модуле, распакованном во временное хранилище
    * 
    * @return array | bool
    */
    function getTmpInfo()
    {
        if (!isset($this->valid)) $this->validateTmp();
        if ($this->valid) {
            $current_module_config = $this->module_already_exists ? \RS\Config\Loader::byModule($this->module_name) : null;
            
            return [
                'info' => $this->module_info,
                'name' => $this->module_name,
                'changelog' => $this->getChangeLog(),
                'can_insert_demo_data' => $this->can_insert_demo_data,
                'already_exists' => $this->module_already_exists,
                'current_version' => $current_module_config['version']
            ];
        }
        return false;
    }
    
    
    /**
    * Возвращает true, если во временная папка для установки пуста.
    * 
    * @return bool
    */
    function isEmptyTmp()
    {
        $list = glob($this->path.'/*');
        return empty($list);
    }
    
    /**
    * Проверка корректности модуля, который находится во временном хранилище
    * 
    * @return bool
    */
    function validateTmp()
    {
        $this->valid = false;
        
        $files = [];
        if ($dh = opendir($this->path)) 
            while (($file = readdir($dh)) !== false) 
            {
                if ($file == '.svn' || $file == '.' || $file == '..') continue;
                if (is_dir($this->path.'/'.$file)) $files[] = $file;
            }
        
        if (!count($files)) {
            return $this->valid = $this->addError(t('В архиве не найдена папка с модулем'));
        }
        
        if (count($files) > 1) {
            //В архиве может быть только одна папка, названная именем модуля.
            return $this->valid = $this->addError(t('В архиве может быть только одна папка, названная именем модуля'));
        }        
        
        $this->module_name = strtolower($files[0]);

        $this->module_already_exists = file_exists(\Setup::$PATH.\Setup::$MODULE_FOLDER.'/'.$this->module_name);
        
        //Проверяем наличие необходимых сведений о модуле
        $module_xml = $this->path.'/'.$this->module_name.$this->config_folder.'/module.xml';        

        if (file_exists($module_xml)) {
            $this->module_info = Item::parseModuleXml($module_xml);
            if (!isset($this->module_info['name'])) $this->addError(t('В конфигурационном файле не задано имя модуля'));
            if (!isset($this->module_info['version'])) $this->addError(t('В конфигурационном файле не задана версия модуля'));
            if (!isset($this->module_info['description'])) $this->addError(t('В конфигурационном файле не задано описание модуля'));
            if (!isset($this->module_info['author'])) $this->addError(t('В конфигурационном файле не указан автор модуля'));
        } else {
            $this->addError(t('Не найден файл конфигурации модуля module.xml'));
        }
        
        if ($this->hasError()) {
            return $this->valid = false;
        }
        
        $this->can_insert_demo_data = !empty($this->module_info['can_insert_demo_data']);
        
        //Проверяем версию ядра.
        if (isset($this->module_info['core_version']) && !$this->checkCoreVersion($this->module_info['core_version'])) {
            $this->addError( t('Версия ядра не соответствует требованиям модуля (%0)', [$this->module_info['core_version']]) );
        }
        
        $this->valid = !$this->hasError();
        return $this->valid;
    }
    
    /**
    * Возвращает true, если версия ядра соответствует требуемой модулем 
    * 
    * @param string $need  - поддерживаемые модулем версии ядра
    * @return bool
    */
    protected function checkCoreVersion($need)
    {
        $core_version = \Setup::$VERSION;
        $need_parts = explode(',', $need);
        foreach ($need_parts as $need_ver)
        {
            $need_ver = str_replace(' ','', $need_ver);
            if (preg_match('/^>=(.*)/', $need_ver, $match)) {
                if (\RS\Helper\Tools::compareVersion($match[1], $core_version, '>=')) return true;
            }
            elseif (preg_match('/^<=(.*)/', $need_ver, $match)) {
                if (\RS\Helper\Tools::compareVersion($match[1], $core_version, '<=')) return true;
            }
            elseif (preg_match('/^(.*?)-(.*)/', $need_ver, $match)) {
                if (\RS\Helper\Tools::compareVersion($match[1], $core_version, '>=') &&
                    \RS\Helper\Tools::compareVersion($match[2], $core_version, '<=')) return true;
            }
            else {
                if (\RS\Helper\Tools::compareVersion($need_ver, $core_version, '==')) return true;
            }
        }
        return false;        
    }    
    
    /**
    * Очищает временную папку, в которой находятся файлы для установки нового модуля
    * @return bool
    */
    function cleanTmpFolder()
    {
        if (file_exists($this->path)) {
            \RS\File\Tools::deleteFolder($this->path, false);
        }
        return true;
    }
    
    /**
    * Деинсталирует модули
    * 
    * @param array $aliases
    * @return bool возвращает true, в случае если все модули были успешно удалены
    */
    function uninstallModules($aliases)
    {
        $this->cleanErrors();
        $aliases = (array)$aliases;
        $notdelete = [];
        $notuninstall = [];

        foreach ($aliases as $modname) {
            $module = new \RS\Module\Item($modname);
            if (($uninstall_result = $module->uninstall()) !== true ) {
                $err = implode(', ', $uninstall_result);
                $this->addError( t("Ошибка при удалении модуля '%module':%error", ['module' => $modname, 'error' => $err]) );
            }
        }
        
        return !$this->hasError();
    }


}

