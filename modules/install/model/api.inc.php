<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Install\Model;

/**
* API функции, необходимые для инсталяции системы
*/
class Api extends \RS\Module\AbstractModel\BaseModel
{
    const 
        NEED_PHP_VERSION = '7.1.0',
        NEED_MYSQL_VERSION = '5.0';
    
    private
        $local_storage,
        $local_storage_file,
        $check_write_rights = [
            '/storage' => [
                    'description' => 'Папка для хранения пользовательских файлов'
            ],
            '/cache' => [
                    'description' => 'Папка для хранения кэш-данных'
            ],
            '/templates' => [
                'description' => 'Папка для шаблонов. Доступ необходим для корректной работы функции установки новых тем оформления'
            ],
            '/modules' => [
                'description' => 'Папка для модулей. Доступ необходим для корректной работы функции установки и обновления модулей'
            ],
            '/' => [
                'description' => 'В процессе установки в корне будет создан файл конфигурации'
            ],
    ],
        $exts;
    
    function __construct()
    {
        $this->local_storage_file = \Setup::$PATH.'/installation.auto.php';
    }
    
    /**
    * Возвращает текст лицензии на языке $lang (если таковой не существует, то на базовом языке системы)
    * 
    * @param string $lang - язык (ru или en....)
    * @return string
    */
    public function getLicenseText($lang)
    {
        $path_mask = \Setup::$PATH.\Setup::$MODULE_FOLDER.'/install'.\Setup::$MODULE_TPL_FOLDER.'/eula/%LANG%/license.htm';
        
        $need = str_replace('%LANG%', $lang, $path_mask);
        $default = str_replace('%LANG%', \Setup::$DEFAULT_LANG, $path_mask);
        $license_filename = file_exists($need) ? $need : $default;
        return file_get_contents($license_filename);
    }
    
    /**
    * Проверяет, доступн ли база данных по указанным пользователем параметрам
    *  
    * @return bool(true) | string Возвращает true, в случае успеха, иначе текст ошибки
    */
    public function checkDbConnect()
    {
        \Setup::$DB_AUTOINIT = false;
        if ( \RS\Db\Adapter::connect() === false ) {
            return t('Не удается соединиться с базой данных. Проверьте параметры соединения.');
        }
        
        //Пытаемся создать базу данных, если есть права на это
        try {
            \RS\Db\Adapter::sqlExec("CREATE DATABASE IF NOT EXISTS `".\Setup::$DB_NAME."` DEFAULT CHARACTER SET = ".\Setup::$DB_CHARSET );
        } catch (\RS\Db\Exception $e) {}
        
        //Пытаемся выбрать базу данных
        try {
            \RS\Db\Adapter::sqlExec("USE `".\Setup::$DB_NAME."`");
        } catch (\RS\Db\Exception $e) {
            return t('Невозможно подключиться к базе данных');
        }
    
        return true;
    }    
    
    /**
    * Устанавливает начальные параметры
    * 
    * @param mixed $config
    */
    public function resetInstall($config)
    {
        //Сохраняем общие настройки
        $cms_cofig = \RS\Config\Loader::getSystemConfig();
        if (empty($cms_cofig['db_port'])) {
            $cms_cofig['db_port'] = null;
        }

        foreach($config as $key => $value) {
            $cms_cofig[strtoupper($key)] = $value;
        }
        $cms_cofig['DB_TABLE_PREFIX'] = $config['db_prefix'] != '' ? rtrim($config['db_prefix'], '_').'_' : '';
        $cms_cofig['SECRET_KEY'] = \RS\Helper\Tools::generatePassword(13);
        $cms_cofig['SECRET_SALT'] = \RS\Helper\Tools::generatePassword(13);
        if (isset($config['default_theme'])) {
            $cms_cofig['DEFAULT_THEME'] = $config['default_theme'];
        }
        
        if (!empty($config['folder'])) {
            $config['folder'] = '/'.trim($config['folder'], '\\/');
            $cms_cofig['FOLDER'] = rtrim($config['folder'], '/');
        }
        
        $cms_cofig->replace();
        \Setup::loadConfig();
        $stack = $this->getInstallStack();
        if (is_string($stack)) {
            return $this->makeError(null, $stack);
        }
        
        $db = $this->checkDbConnect();
        if ($db !== true) {
            return $this->makeError(null, $db);
        }
        
        //Устанавливаем ядро
        $rs_install = new \RS\Config\Install();
        if (!$rs_install->install()) {
            return $this->makeError(null, $rs_install->getErrors());
        }
        
        $steps = $this->getInstallSteps($stack, $config);
        
        $this->setKey('progress', [
            'config' => $config,
            'stack' => $stack,
            'steps' => $steps,
            'percent' => 0,
            'pos' => 0
        ]);
        return $this->getNextStepInfo();
    }
    
    /**
    * Возврщает массив шагов инсталяции
    * 
    * @param array $modules - список модулей
    * @param array $config - настройки
    */
    function getInstallSteps($modules, $config)
    {
        $steps = [];
        $module_items = [];
        foreach($modules as $module) {
            $item = new \RS\Module\Item( $module );
            if ($mod_config = $item->getConfig()) {
                $module_items[$module] = $item;
                $caption = t("Установка модуля '%0'", [$mod_config['name']]);
                $steps[] = ['action' => 'doInstall', 'module' => $module, 'caption' => $caption];
            }
        }
        
        $steps[] = ['action' => 'doAllowDbWrite', 'caption' => t('Подготовка к установке демо данных')];
        
        if ($config['set_demo_data']) {
            foreach($module_items as $module => $mod_item) {
                $install = $mod_item->getInstallInstance();
                if ($install && $install->canInsertDemoData()) {
                    $caption = t("Установка данных модуля '%0'", [$mod_item->getConfig()->name]);
                    $steps[] = ['action' => 'doInsertDemoData', 'module' => $module, 'caption' => $caption];
                }
            }
        }
        
        $steps[] = ['action' => 'doAfterInstall', 'caption' => t('Конфигурирование модулей')];
        
        return $steps;
    }
    
    /**
    * Выполняется после установки всех модулей и демо данных
    * 
    * @return bool | string
    */
    public function doAfterInstall()
    {
        $status = $this->getKey('progress');
        foreach($status['stack'] as $module) {
            $mod_item = new \RS\Module\Item( $module );
            if ($install = $mod_item->getInstallInstance()) {
                $result = $install->deferredAfterInstall($status['config']);
                if ($result !== true) {
                    return $this->makeError(t("Модуль '%0'", [$module]), $result);
                }
            }
        }
        return true;
    }
    
    /**
    * Меняет флаг DB_INSTALL_MODE в false, включая отображение ошибок "Таблица в БД не создана"
    * 
    * @return bool
    */
    public function doAllowDbWrite()
    {
        $cms_cofig = \RS\Config\Loader::getSystemConfig();
        $cms_cofig['DB_INSTALL_MODE'] = false;
        $cms_cofig->replace();
        return true;
    }
    
    /**
    * Выполняет один шаг установки
    * 
    * @return array - возвращает инсформацию о следующем этапе установки или массив с ошибками
    */
    public function progress()
    {        
        $status = $this->getKey('progress');
        if (!$status) {
            return $this->makeError(null, t('Внутренняя ошибка, попробуйте запустить установку заново'));
        }    
        $install_result = [];
        if ($status['pos'] < count($status['steps'])) {
            $do = $status['steps'][$status['pos']];
            
            $install_result = call_user_func_array([$this, $do['action']], array_diff_key($do, ['action' => true, 'caption' => true]));
         
            if (is_array($install_result)) { //Если мы получили массив с данными 
                $status['steps'][$status['pos']]['params'] = $install_result;            
            } elseif ($install_result !== true) {
                return $install_result; //Если произошла ошибка
            }
        } else {
            return $this->finishInstall();
        }
        
        //Установка прошла успешно
        if (!is_array($install_result)){ //Если нет необходимости повторится, не увеличиваем шаг
            $status['pos']++;      
        }  
        if ($status['pos'] < count($status['steps']) ) {
            $status['percent'] = round(($status['pos']/(count($status['steps'])))*100);
        } else {
            //Завершена установка последнего модуля
            $status['percent'] = 100;
        }
        $this->setKey('progress', $status);
        $result = $this->getNextStepInfo();
        
        if (is_array($install_result)){
            
           $result += $install_result;
        }
        
        return $result;
    }
    
    /**
    * Устанавливает демонстрационные данные для модулей
    * 
    * @param string $module     - имя модуля
    * @param string $schema     - имя текущей схемы для продолжения обработки
    * @param string $file       - файл импорта с которого продолжать
    * @param integer $start_pos - позиция с которой продолжать импорт
    */
    public function doInsertDemoData($module, $params = [])
    {
        $current_module = new \RS\Module\Item( $module );     
        $install_instance = $current_module->getInstallInstance();
        
        if ( ($result = $install_instance->insertDemoData($params)) === false) {
            return $this->makeError(t('Ошибка во время установки демо данных: %0', $current_module->getConfig()->name), $install_instance->getErrors());
        }
            
        return $result;
    }

    
    /**
    * Выполняет установку одного модуля
    * 
    * @param string $module имя модуля
    * @return array - возвращает инсформацию о следующем этапе установки или массив с ошибками
    */
    public function doInstall($module)
    {        
        $current_module = new \RS\Module\Item( $module );
        if ( ($install_result = $current_module->install()) !== true ) {
            $mod = $current_module->getConfig()->name;
            return $this->makeError(t("Модуль '%0'", [$mod]), $install_result);
        }
        return true;
    }
    
    /**
    * Завершает установку
    * 
    */
    protected function finishInstall()
    {
        //Вызываем событие окончания процесса инсталяции.
        $event_result = \RS\Event\Manager::fire('install.complete');
        if ($event_result->getEvent()->isStopped()) {
            return $this->makeError(null, $event_result->getResult());
        }
        __SET_INSTALL_ID();
        
        //Запрещаем открывать PHP файлы там, где это неположено
        $htaccess_php_deny = 
                t('# Запрещаем открывать php файлы напрямую')."\n".
                '<Files ~ "(\.php.?)$">'."\n".
                    'Order allow,deny'."\n".
                    'Deny from all'."\n".
                '</Files>';
        
        $php_deny_folders = ['/cache', '/storage', '/templates'];
        
        foreach($php_deny_folders as $folder) {
            $folder_full_path = \Setup::$PATH.$folder;
            \RS\File\Tools::makePath($folder_full_path);
            file_put_contents($folder_full_path.'/.htaccess', $htaccess_php_deny);
        }
        
        //Записываем точку невозврата установки - шаг 4
        $this->setKey('min_allow_step', 4);
        $this->setMaxAllowStep(4);
        
        $result = [
            'next' => t('Установка завершена'),
            'complete' => true,
            'percent' => 100
        ];
        $result['complete'] = true;
        return $result;
    }
    
    /**
    * Вызывается после полного завершения установки.
    * Удаляет временные файлы
    */
    public function installComplete()
    {
        $progress = $this->getKey('progress');
        $notice = new Notice\InstallSuccess;
        $notice->init(
            $progress['config']['supervisor_email'], 
            $progress['config']['supervisor_pass'], 
            $progress['config']['admin_section'], 
            \RS\Helper\IdnaConvert::getInstance()->decode(\Setup::$DOMAIN)
        );
            
        \Alerts\Model\Manager::send($notice);
        $this->removeLocalStorage();
        
        $cms_cofig = \RS\Config\Loader::getSystemConfig();
        $cms_cofig['INSTALLED'] = true;
        $cms_cofig['CACHE_ENABLED'] = true;
        $cms_cofig->replace();
        
        \RS\Application\Auth::login($progress['config']['supervisor_email'], $progress['config']['supervisor_pass']);
        \RS\Cache\Cleaner::obj()->clean();
    }
    
    /**
    * Возвращает информацию о следующем шаге установки
    * 
    * @return array
    */
    protected function getNextStepInfo()
    {
        $result = [];
        $status = $this->getKey('progress');
        
        if ( $status['pos'] < count($status['steps']) ) {
            //Загружаем информацио о следующем модуле
            $reason = $status['steps'][$status['pos']]['caption'];
        } else {
            $reason = t('Завершение установки');
        }
        $result['next'] = $reason;        
        $result['percent'] = $status['percent'];
        
        return $result;
    }
    
    /**
    * Подготавливает массив с информацией об ошибках для вывода в браузер
    * 
    * @param string $module_title
    * @param array | string $message
    * @return array
    */
    protected function makeError($module_title, $message)
    {
        if ($module_title === null) {
            $module_title = t('Установщик');
        }
        $return = ['errors' => []];
        $message = (array)$message;
        foreach($message as $text) {
            $return['errors'][] = [
                'moduleTitle' => $module_title,
                'message' => $message
            ];
        }
        
        return $return;
    }
    
    /**
    * Возвращает массив модулей, которые необходимо установить или строку с ошибкой
    * 
    * @return array | string
    */
    public function getInstallStack()
    {
        $mod_manager = new \RS\Module\Manager();
        $modules = $mod_manager->getAllConfig();
        
        $first_modules = ['menu', 'site', 'main', 'users', 'modcontrol'];
        $modules = array_diff_key($modules, array_flip($first_modules));
        
        //Сортируем с учетом зависимостей
        $list = array_combine($first_modules, $first_modules);
        foreach($modules as $name => $one_mod) { //Если у модуля нет зависимостей, то помещаем его в список
            if ( empty($one_mod['dependfrom'])) {
                $list[$name] = $name;
                unset($modules[$name]);
            }
        }
        
        while(count($modules)) {
            $before = count($modules);
            foreach($modules as $name => $one_mod) {
                //Если зависимость удовлетворяет условиям, то добавляем модуль
                $depend = explode(',', $one_mod['dependfrom']);
                $verdict = true;
                foreach($depend as $one_depend) {
                    $verdict = isset($list[trim($one_depend)]) && $verdict;
                }
                if ($verdict) {
                    $list[$name] = $name;
                    unset($modules[$name]);
                }
            }
            if ($before == count($modules)) {
                return t("Следующие модули зависят от несуществующих модулей '%0'", [implode(',', array_keys($modules))]);
            }
        }
        
        return array_values($list);
    }
    
    public function removeLocalStorage()
    {
        @unlink($this->local_storage_file);
    }
    
    /**
    * Записывает пару ключ => значение в локальное хранилище, которое удаляетс после установки
    * 
    * @param string $key - ключ
    * @param mixed $value - значение
    */
    public function setKey($key, $value)
    {
        if (is_array($key)) {
            $this->local_storage = $key;
        } else {
            $this->getKey();
            $this->local_storage[$key] = $value;            
        }
        file_put_contents($this->local_storage_file, serialize($this->local_storage));    
    }
    
    /**
     * 
     * @param type $step
     */
    function setMaxAllowStep($step)
    {
        $current_step = $this->getKey('max_allow_step');
        if ($current_step < $step) {
            $this->setKey('max_allow_step', $step);    
        }
    }
    
    
    /**
    * Возвращает значение по ключу из локального хранилища
    * 
    * @param mixed $key - ключ
    * @param mixed $default - значение по-умолчанию
    * @return mixed
    */
    public function getKey($key = null, $default = null)
    {
        if ($this->local_storage === null) {
            if (file_exists($this->local_storage_file)) {
                $this->local_storage = unserialize(file_get_contents($this->local_storage_file));
            } else {
                $this->local_storage = [];
            }
        }
        if ($key !== null) {
            return isset($this->local_storage[$key]) ? $this->local_storage[$key] : $default;
        }
    }

    
    /**
    * Проверяет соответствует ли сервер требуемым параметрам системы
    * 
    * @return array Возвращает массив с результатом проверки всех параметров
    */
    public function checkServerParams()
    {
        $check_result = [
            'php_version' => $this->checkPhpVersion(),
            'mysql_support' => $this->checkMysqlSupport(),
            'safe_mode' => $this->checkSafeMode(),
            'upload_files' => $this->checkUploadFiles(),
            'gd' => $this->checkGDSupport(),
            'zip' => $this->checkZipSupport(),
            'mbstring' => $this->checkMbSupport(),
            'curl' => $this->checkCurl(),
            'crypt' => $this->checkCrypt(),
            'write_rights' => $this->checkWriteRights(),
            'can_continue' => true
        ];
        
        foreach($check_result as $key => $item) {
            if (is_array($item) && isset($item['decision'])) {
                $check_result['can_continue'] = $check_result['can_continue'] & $item['decision'];
            }
            
            if ($key == 'write_rights') {
                foreach($item as $path_data) {
                    $check_result['can_continue'] = $check_result['can_continue'] & $path_data['decision'];
                }
            }
        }
        $this->setMaxAllowStep($check_result['can_continue'] ? 3 : 2);
        
        return $check_result;
    }
    
    /**
    * Проверяет наличие графических функций(модуля GD) в PHP
    * 
    * @return array
    */
    public function checkGDSupport()
    {
        $exts = $this->getLoadedExtensions();
        return [
                'decision' => in_array('gd', $exts),
        ];
    }
    
    /**
    * Проверяет наличие функций для распаковки zip архивов в PHP
    * 
    * @return array
    */
    public function checkZipSupport()
    {
        $exts = $this->getLoadedExtensions();
        return [
                'decision' => in_array('zip', $exts),
        ];
    }
    
    /**
    * Проверяет наличие Multibyte функций для работы с кодировкой UTF-8
    * 
    * @return array
    */
    public function checkmbSupport()
    {
        $exts = $this->getLoadedExtensions();
        $func_overload = ini_get('mbstring.func_overload');
        return [
                'decision' => in_array('mbstring', $exts) && ($func_overload==0 || $func_overload == 1),
        ];
    }
    
    /**
    * Проверяет наличие модуля mcrypt с шифрованием twofish или openSSL
    * 
    */
    public function checkCrypt()
    {
        $exts = $this->getLoadedExtensions();
        return [
            'decision' =>
                (in_array('mcrypt', $exts) && @mcrypt_module_open('twofish', '', 'ecb', '') !== false)
                || (function_exists('openssl_public_decrypt'))
        ];
    }
    
    
    /**
    * Проверяет соответствие установленной версии PHP требуемой
    * 
    * @return array
    */
    public function checkPhpVersion()
    {
        $ver = phpversion();
        return [
                'server' => $ver,
                'decision' => $this->compareVersion(self::NEED_PHP_VERSION, $ver),
                'need' => self::NEED_PHP_VERSION
        ];
    }
    
    /**
    * Проверяет наличие функций mysql в системе
    * 
    * @return array
    */
    public function checkMysqlSupport()
    {
        $exts = $this->getLoadedExtensions();
        return [
                'decision' => in_array('mysqli', $exts),
                'need' => self::NEED_MYSQL_VERSION
        ];
    }
    
    /**
    * Проверяет, выключен ли безопасный режим
    * 
    * @return array
    */
    public function checkSafeMode()
    {
        $safe_mode = ini_get('safe_mode');
        return [
                'decision' => $safe_mode == 0,
        ];
    }
    
    /**
    * Проверяет, возможна ли загрузка файлов в системе
    * 
    * @return array
    */
    public function checkUploadFiles()
    {
        $file_uploads = ini_get('file_uploads');
        return [
                'decision' => $file_uploads == 1,
        ];
    }
    
    /**
    * Проверяет, включен ли модуль CUrl
    * 
    * @return array
    */
    public function checkCurl()
    {
        $exts = $this->getLoadedExtensions();
        return [
                'decision' => in_array('curl', $exts),
        ];
    }
    
    /**
    * Проверяет права на запись в директориях
    * 
    * @return array
    */
    public function checkWriteRights()
    {
        $result = $this->check_write_rights;
        foreach($result as $dir => &$data) {
            $full_path = \Setup::$PATH.$dir;
            $check_filename = $full_path.'/'.md5(uniqid(mt_rand()));
            $data['description'] = t($data['description']); //Переводим данные фразы
            $data['decision'] = @mkdir($check_filename, \Setup::$CREATE_DIR_RIGHTS, true) && rmdir($check_filename);
        }
        return $result;
    }
    
    /**
    * Проверяет, соответствует ли версия $version требуемой $need
    * 
    * @param string $need - требуемая версия, например 7.1 или 7.01.2525
    * @param string $version - имеющаяся версия, например 5.2.10
    * @return bool Возвращает true, если версия $version больше или равно $need
    */
    protected function compareVersion($need, $version)
    {
        $need_parts = explode('.', $need);
        foreach($need_parts as &$part) {
            $part = sprintf('%05d', $part);
        }
        
        $parts = explode('.', $version);
        $ver_parts = [];
        for($i=0; $i<count($need_parts); $i++) {
            $one = isset($parts[$i]) ? $parts[$i] : 0;
            $ver_parts[] = sprintf('%05d', $one);
        }
        
        $need_str = implode('', $need_parts);
        $ver_str = implode('', $ver_parts);
        
        return strcmp($ver_str, $need_str) >= 0;
    }
    
    /**
    * Возвращает массив с установленными в модулями PHP
    * 
    * @return array
    */
    protected function getLoadedExtensions()
    {
        if (!$this->exts) {
            $this->exts = get_loaded_extensions();
        }
        
        return $this->exts;
    }
    
}

