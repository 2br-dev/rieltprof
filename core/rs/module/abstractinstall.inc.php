<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Module;

use RS\Config\Loader as ConfigLoader;
use RS\File\Tools as FileTools;

/**
* Базовый класс установщика модуля. Выполняет роль установщика "по-умолчанию" для модуля.
* Вызывается с параметром $module_name в конструкторе, если у модуля не определн собственный класс МОДУЛЬ/Config/Install
*/
class AbstractInstall implements InstallInterface
{
    protected $module;
    protected $mod_folder;
    protected $demo_data_folder = '/config/demo';
    /** @var AbstractPatches */
    protected $patches;
    protected $errors = [];
    
    /**
    * Конструктор. 
    * 
    * @param string $module_name имя модуля, если не задано, то будет получено из имени класса наследника.
    */
    function __construct($module_name = null)
    {
        $this->module     = $module_name ?: Item::nameByObject($this);
        $this->mod_folder = \Setup::$PATH.\Setup::$MODULE_FOLDER.'/'.$this->module;
        $patches_class = '\\'.$this->module.'\Config\Patches';
        if (class_exists($patches_class)) {
            $this->patches = new $patches_class();
        }
    }

    /**
     * Выполняет установку модуля
     *
     * @return bool
     * @throws \ReflectionException
     */
    function install()
    {
        //Ставим отметку, что модуль установлен
        $self_module = ConfigLoader::byModule($this->module);;
        $config = ConfigLoader::byModule($this->module);;
        $config['installed'] = true;
        $config->update();

        // Обновение модуля
        return $this->update();
    }

    /**
     * Обновляет модуль, приводит в соответствие базу данных.
     *
     * @return bool
     * @throws \ReflectionException
     */
    function update()
    {
        // Если модуль содержит папку solutiontemplate
        $template_folder = \Setup::$PATH . \Setup::$MODULE_FOLDER . '/' . $this->module . '/solutiontemplate';
        if (is_dir($template_folder)) {
            // Установка темы оформления
            if ($this->installTemplate($template_folder)) {
                FileTools::deleteFolder($template_folder);
            } else {
                return false;
            }
        }

        if ($this->patches) {
            $this->patches->runBeforeUpdatePatches();
        }

        //Находим все ORM Объекты, обновляем базу
        $orm_objects = $this->findOrmObjects();
        foreach ($orm_objects as $object) {
            if (!$object->dbUpdate()) {
                $this->addError(t("Не удалось обновить базу данных объекта '%0'", [get_class($object)]));
                return false;
            }
        }

        if ($this->patches) {
            $this->patches->runAfterUpdatePatches();
        }

        $config = ConfigLoader::byModule($this->module);
        if ($config) {
            $config['lastupdate'] = time();
            $config->update();
        }

        return true;
    }

    private function installTemplate($src_path)
    {
        $theme_xml = $src_path.'/theme.xml';
        if(!file_exists($theme_xml))
        {
            return $this->addError(t("Не найден файл параметров темы '%0'", [$theme_xml]));
        }

        // Перенос файлов темы
        $theme_name = $this->module.'_theme';
        $dest_folder = \Setup::$SM_TEMPLATE_PATH.'/'.$theme_name;
        FileTools::moveWithReplace($src_path, $dest_folder);

        return true;
    }

    /**
     * Добавляет в меню административной панели
     *
     * @return void
     * @deprecated меню формируется с помощью событий. Данный метод более не используется
     */
    function installAdminMenu()
    {}

    /**
     * Возвращает список ORM объектов, находящихся в указанной папке
     *
     * @param mixed $base - путь к корневой папке orm объектов
     * @param mixed $subfolder - путь к объектам, отностельно корневой папки
     * @param mixed $prefix - текст, приписываемый вначале к имени класса
     * @return array
     * @throws \ReflectionException
     */
    protected function findOrmObjects($base = null, $subfolder = '', $prefix = null)
    {
        if ($base === null) {
            $base = $this->mod_folder.'/model/orm/';
            $prefix = '\\'.$this->module.'\model\orm\\';
        }
        
        $result = [];
        $dir = $base.$subfolder;
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if ($file == '.' || $file == '..') continue;
                    if (is_dir($dir.$file)) {
                        $result = array_merge($result, $this->findOrmObjects($base, $subfolder.$file.'/', $prefix));
                    } else {
                        //Ищем файлы классов
                        if (strpos($file, '.'.\Setup::$CLASS_EXT) !== false && strpos($file, '.'.\Setup::$CUSTOM_CLASS_EXT) === false) {
                            $classname =  $prefix. str_replace('/', '\\', $subfolder.str_replace('.'.\Setup::$CLASS_EXT, '', $file));
                            if (is_subclass_of($classname, '\RS\Orm\AbstractObject')) {
                                $reflection = new \ReflectionClass($classname);
                                if (!$reflection->isAbstract()) {
                                    $result[] = new $classname();
                                }
                            }
                        }
                    }
                }
                closedir($dh);
            }
        }
        return $result;
    }
    
    /**
    * Добавляет ошибку в список
    * 
    * @param string $message
    * @return bool(false)
    */
    function addError($message)
    {
        $this->errors[] = $message;
        return false;
    }
    
    /**
    * Возвращает список ошибок 
    * 
    * @return array
    */
    function getErrors()
    {
        return $this->errors;
    }
    
    /**
    * Добавляет демонстрационные данные
    * @param array $params - произвольные параметры. 
    * @return bool(true) | array Если возвращается array, то 
    */
    function insertDemoData($params = [])
    {}
    
    /**
    * Добавляет демонстрационные данные
    * 
    * @param array $demo_schemas_array - массив с данными по схеме и файлу импорта, 
    * которые необходимо вызвать для установки модуля:
    * первый элемент массива это класс схемы для импорта, а второй имя файла импорта
    * массив[0][0] - Схема импорта
    * массив[0][1] - Имя файла импорта
    * 
    * @param string $charset - кодировка в которой будут файлы импорта демо данных
    * @param null|string $start_class_name - класс схемы с которого начинать свой импорт. Если false, то идти по порядку.
    * @param null|string $start_file_name - файл импорта с которого начинать свой импорт. Если false, то идти по порядку.
    * @param integer $start_pos - позиция с которой начинать чтение файла
    * 
    * @return bool|array
    */
    protected function importCsvFiles($demo_schemas_array= [], $charset='utf-8', $params = [])
    {
        $params += [
            'schema' => null,
            'file' => null,
            'start_pos' => null
        ];
                
        $class_found = false;
        $check_timeout = ConfigLoader::byModule('main')->csv_check_timeout;
        $start_pos = $check_timeout ? $params['start_pos'] : false;
        
        $result = true;
        foreach ($demo_schemas_array as $demo_schema) {
            $schema_class = $demo_schema[0]; //Объект схемы
            $file_name    = $demo_schema[1]; //Имя файла
            if (is_string($schema_class)) {
                $schema_class = new $schema_class();
            }
            $schema_class_name = mb_strtolower(get_class($schema_class)); //Имя класса схемы
            
            if ($params['schema'] && !$class_found) { //Если задано с какого класса схемы начинать
                if (!$class_found && ($schema_class_name != $params['schema'])) {
                    continue; 
                }else{
                    $class_found = true; 
                }
            }
            $result = $this->importCsv($schema_class, $file_name, null, $charset, $start_pos);
            if (is_numeric($result) && $result > 0) { //Если вернулась позиция в файле, то вернём массив с информацией
                $result = [
                   'schema'    => $schema_class_name,  
                   'file'      => $file_name,  
                   'start_pos' => $result,
                ];
                break;
            } elseif ($result) {
                $start_pos = 0;
            } else {
                break;                
            }
        }
        
        return $result;
    }
    
    /**
    * Возвращает true, если модуль может вставить демонстрационные данные
    * 
    * @return bool
    */
    function canInsertDemoData()
    {
        return false;
    }
    
    /**
    * Испортирует CSV с учетом текущего языка.
    * Например, если установлен английский язык то к имени файла будет подставлен постфикс _en. Если такого файла не существует, 
    * то импортируется файл $csv_file без постфикса.
    * 
    * @param \RS\Csv\AbstractSchema $schema - схема импорта
    * @param string $csv_file               - путь к файлу без расширения
    * @param integer $site_id               - ID сайта на который необходимо загрузить данные. Если null, то текущий сайт.
    * @param string $charset                - кодировка в которой будет обрабатыватся файл
    * @param bool(false) | integer $start_pos - позиция с которой начинать чтение файла. Если false, то проверка на таймаут будет отключена
    */
    function importCsv(\RS\Csv\AbstractSchema $schema, $csv_file, $site_id = null, $charset = 'utf-8', $start_pos = false)
    {        
        $schema->setCharset($charset);
        $lang = \RS\Language\Core::getCurrentLang();
        $search_files = [
            "{$csv_file}_{$lang}.csv",
            "{$csv_file}.csv"
        ];
        
        foreach($search_files as $file) {
            $filename = $this->mod_folder.$this->demo_data_folder.'/'.$file;
            
            if (file_exists($filename)) { //Если файл с данными найден выполняем импорт
                $result = $schema->import($filename, $start_pos !== false, $start_pos, $site_id);
                if (!$result) {
                    $this->addError($schema->getErrorsStr());
                }
                return $result;
            }
        }
        
        $this->addError( t('Не найден файл с демо данными %0', [$csv_file]) );
        return false;
    }

    /**
    * Выполняется, после того, как были установлены все модули. 
    * Здесь можно устанавливать настройки, которые связаны с другими модулями.
    * 
    * @param array $options параметры установки
    * @return bool
    */
    function deferredAfterInstall($options)
    {
        return true;
    }
}
