<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace SiteUpdate\Model;

use RS\Cache\Manager as CacheManager;
use RS\Db\Exception as DbException;
use RS\Event\Manager as EventManager;
use RS\HashStore\Api as HashStoreApi;
use RS\Exception as RSException;
use RS\Module\AbstractModel\BaseModel;

/**
 * Класс содержит API функции по обновлению системы
 */
class Api extends BaseModel
{
    const
        REQUEST_TIMEOUT = 50,
        CHECK_FOR_UPDATES_INTERVAL = 14400, //4 часа
        SKIPPED_MODULE_STORE_KEY = 'SKIPPED_MODULES',
        UPDATE_IN_PROGRESS_STORE_KEY = 'UPDATE_IN_PROGRESS',
        SECTION_PRODUCTS_FOR_UPDATE = 'products';
    
    protected
        $time_marker = 0,
        $max_execution_time = 25, // сек
        $config,
        $write_data_to_file = true,
        $update_tmp_folder = '/siteupdate',
        $update_tmp_folder_zip,
        $update_upacked_folder,
        $backup_folder,        
        $data,
        $data_file = 'data.srz',
        $copy_files = [
            '/package.inc.php'
    ],
        $module_folders = [
            '@core' => [
                '/core/',
                '/resource/',
                '/templates/system/'
            ]
    ];
        
    function __construct()
    {
        $this->config = \RS\Config\Loader::byModule($this);
        $this->update_tmp_folder = \Setup::$TMP_DIR.$this->update_tmp_folder;
        $this->update_tmp_folder_zip = $this->update_tmp_folder.'/zip';
        $this->update_upacked_folder = $this->update_tmp_folder.'/tmp';
        $this->backup_folder = $this->update_tmp_folder.'/backup';
        $this->data = $this->getPrepearedData();
    }
    
    /**
    * Возвращает true, если возможно провести проверку обновлений, иначе - false
    * 
    * @return bool
    */
    function canCheckUpdate()
    {
        $main_license = null;
        __GET_LICENSE_LIST($main_license);

        if (!$main_license) {
            return $this->addError(t('Для проверки обновлений необходимо установить основную лицензию'));
        }
        
        return true;
    }
    
    
    /**
    * Подготавливает массив обязательных параметров для обращения к серверу обновлений
    * 
    * @return array
    */
    protected function getRequestVars()
    {
        $licenses = array_keys(__GET_LICENSE_LIST());
        return [
            'licenses' => $licenses,
            'channel' => \Setup::$UPDATE_CHANNEL,
            'product' => \Setup::$SCRIPT_TYPE,
            'copy_id' => COPY_ID,
            'install_id' => INSTALL_ID,
            'lang' => \RS\Language\Core::getCurrentLang(),
            'php_version' => phpversion()
        ];
    }
    
    /**
    * Возвращает версию модуля до обновления или false, если такой модуль не найден
    * 
    * @param string $module - Имя модуля, или @core - для возвращения версии ядра, #default - для возвращения версии шаблона
    * @return string | bool(false)
    */
    public function getBeforeUpdateVersion($module)
    {
        if (!isset($this->data['myVersions'])) return null;
        return isset($this->data['myVersions'][$module]) ? $this->data['myVersions'][$module] : false;
    }
                                               
    /**
    * Подготавливает список версий программы, для которых можно получить обновления
    * 
    * @return bool возвращает true в случае успеха, иначе - false
    */
    function prepareProductsForUpdate(&$count)
    {
        \RS\File\Tools::makePath($this->update_tmp_folder);
        $this->setData(null);
        $params = [
            'do' => 'getProductsForUpdate',
            'modules' => implode(',', get_loaded_extensions())
        ];
        $response = $this->requester($params);

        if ($response === false) {
            return $this->addError(t('Невозможно соединиться с сервером обновлений. Попробуйте повторить попытку позже'));
        } else {
            if ($response['success'] != true) {
                return $this->addError($response['error']);
            }
        }
        
        $this->setData('products', $response['products']);
        $count = count($response['products']);
        if ($count == 1) {
            return $this->prepareUpdateInfo($response['products'][0]);
        }
        return true;
    }
    
    /**
    * Возвращает массив со списком версий текущей системы
    */
    function getMyVersions()
    {
        $versions = [
            '@core' => \Setup::$VERSION
        ];
        
        //Добавляем версии модулей
        $mod_manager = new \RS\Module\Manager();
        $modules = $mod_manager->getList();
        
        foreach($modules as $name => $module) {
            $config = $module->getConfig();
            $versions[$name] = $config['version'];
        }
        
        //Добавляем версии шаблонов
        $theme_api = new \RS\Theme\Manager();
        $all_themes = $theme_api->getList();
        
        foreach($all_themes as $theme_id => $theme) {
            $versions['#'.$theme_id] = (string)$theme->getThemeXml()->general->version;
        }
        
        return $versions;
    }

    /**
     * Подготавливает информацию о том, какие модули могут быть обновлены
     *
     * @param $product
     * @return bool возвращает true в случае успеха, иначе - false
     * @throws DbException
     * @throws RSException
     */
    function prepareUpdateInfo( $product )
    {
        $params = [
            'do' => 'getServerVersions',
            'need_product' => $product,
            'client_versions' => $this->getMyVersions()
        ];
        $response = $this->requester($params);
        
        if ($response === false) {
            return $this->addError(t('Невозможно соединиться с сервером обновлений'));
        } else {
            if ($response['success'] != true) {
                return $this->addError($response['error']);
            }
        }

        if (!empty($response['delete_license_data']) && function_exists('__MODULE_LICENSE_DELETE_DATA')) {
            __MODULE_LICENSE_DELETE_DATA($response['delete_license_data']);
        }
        
        $this->setData([
            'updateProduct' => $product,
            'serverVersions' => $response['versions'],
        ]);

        return true;
    }
    
    /**
    * Возвращает true, если версия1 >= версии2
    * 
    * @param string $ver1
    * @param string $ver2
    * @return bool
    */
    function isActualVersion($ver1, $ver2)
    {
        return version_compare($ver1, $ver2) >= 0;
    }
    
    /**
    * Возвращает массив с модулями, которые можно обновить
    * @param array $skipped_modules - список модулей которые нужно исключить из проверки обновлений
    * @return array
    */
    function compareVersions($skipped_modules = [])
    {
        $result = [];
        $my_versions = $this->getMyVersions();
        foreach($this->data['serverVersions'] as $module => $data) {
            $module = strtolower($module);
            if (in_array($module, $skipped_modules)) continue;
            if (isset($my_versions[$module]) && $this->isActualVersion($my_versions[$module], $data['version']) ) continue;
            
            $item = [
                'new_version' => $data['version'],
                'my_version' => isset($my_versions[$module]) ? $my_versions[$module] : t('новый'),
                'module' => $module,
                'title' => $data['name'],
                'description' => $data['description'],
                'changelog_exists' => $data['changelog_exists']
            ];
            
            if ($module == '@core') {
                $item['checkbox_attr'] = ['checked' => 'checked', 'disabled' => 'disabled', 'class' => 'always-checked'];
            }
            
            $result[$module] = $item;
        }
        $this->setData('updateData', $result);
        $this->setData('myVersions', $my_versions);
        
        return $result;
    }
    
    
    /**
    * Возвращает все данные подготовленные для текущего обновления
    * 
    * @return array
    */
    function getPrepearedData()
    {
        if (!isset($this->data)) {
            if (file_exists($this->update_tmp_folder.'/'.$this->data_file)) {
                $this->data = unserialize( file_get_contents($this->update_tmp_folder.'/'.$this->data_file) );
            }
            if (!isset($this->data)) {
                $this->data = [];
            }
        }
        return $this->data;
    }
    
    /**
    * Сохраняет подготовленную информацию $value под клчем $key
    * 
    * @param mixed $key
    * @param mixed $value
    * @return SiteUpdate
    */
    function setData($key, $value = null)
    {
        if (!isset($this->data)) $this->data = $this->getPrepearedData();
        
        if ($key === null) {
            $this->data = [];
        } elseif (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }

        if ($this->write_data_to_file) {
            file_put_contents( $this->update_tmp_folder.'/'.$this->data_file, serialize($this->data) );
        }
        return $this;
    }
    
    /**
    * Устанавливает сохранять ли временные данные во внешний файл или нет.
    * 
    * @param bool $bool
    */
    function writeDataToFile($bool)
    {
        $this->write_data_to_file = $bool;
    }
    
    /**
    * Выполняет POST запрос к серверу обновления
    * 
    * @param mixed $url
    * @param mixed $params
    */
    function requester($params, $json = true)
    {
        $params = $params + $this->getRequestVars();
        
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'ignore_errors' => true,
                'timeout' => self::REQUEST_TIMEOUT,
                'header' => 'Content-Type: application/x-www-form-urlencoded' . PHP_EOL,
                'content' => http_build_query($params),
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ]);

        $result = @file_get_contents(\Setup::$UPDATE_URL, false, $context);
        
        if ($json && $result !== false) {
            $result = json_decode($result, true);
        }

        return $result;
    }
    
    
    /**
    * Проводит подготовку к обновленю системы, формирует список действий(карту) по обновлению системы
    * 
    * @param array $modules список устанавливаемых модулей
    * @return array возвращает информацию о следующем шаге или информацию об ошибках
    */
    function prepareInstallUpdate(array $modules)
    {
        //Формируем карту обновления
        $install_map = [
            [
                'callback' => 'backupSystem',
                'status' => t('Создание точки восстановления системы')
            ],
            [
                'callback' => 'getUpdateFilename',
                'status' => t('Получение сведений от сервера обновлений')
            ],
            [
                'callback' => 'downloadUpdatePackage',
                'status' => t('Загрузка обновлений с удаленного сервера')
            ],
            [
                'callback' => 'waitForCronFinished',
                'status' => t('Ожидание остановки планировщика')
            ],
            [
                'callback' => 'unpackUpdatePackage',
                'status' => t('Распаковка файла обновлений')
            ],
        ];
        
        $update_data = $this->getPrepearedData();
        
        if (isset($update_data['updateData']['@core'])) {
            array_unshift($modules, '@core'); //Добавляем к обновлению всегда ядро, если оно присутствует в списке обновлений
        }
        
        //Вычисляем отключенные модули и сохраняем их в хэш-хранилище
        if (!empty($update_data['updateData'])) {
            $skipped_modules = array_diff( array_keys($update_data['updateData']), $modules);
            HashStoreApi::set(self::SKIPPED_MODULE_STORE_KEY, $skipped_modules);
        }
        
        foreach($modules as $module) {
            if ($module == '@core') {
                $install_map[] = [
                    'callback' => 'updateCore',
                    'status' => t("Обновление ядра системы")
                ];
            } 
            elseif ($module[0] == '#') {
                $template = substr($module, 1);
                $install_map[] = [
                    'callback' => 'updateTemplate',
                    'callback_params' => [$template],
                    'status' => t("Обновление шаблона %0", [$template])
                ];
            }
            else {
                $install_map[] = [
                    'callback' => 'updateModule',
                    'callback_params' => [$module],
                    'moduleName' => $update_data['serverVersions'][$module]['name'],
                    'status' => t("Обновление модуля %0", [$update_data['serverVersions'][$module]['name']])
                ];
            }
        }
        
        $this->setData([
            'selectedModules' => $modules,
            'installMap' => $install_map,
            'position' => 0,
            'percent' => 0
        ]);

        //Оставляем в сесси только текущего пользователя
        //Все остальное очищаем с целью предотвращения преждевременной загрузки
        //объектов, которые могут обновиться.
        if (isset($_SESSION['user_id'])) {
            $_SESSION = [
                'user_id' => $_SESSION['user_id']
            ];
        } else {
            $_SESSION = [];
        }

        //Проверяем права на запись в папку перед установкой обновлений
        $write_test_filename = \Setup::$PATH.\Setup::$MODULE_FOLDER.'/write-test';
        $write_test = @file_put_contents($write_test_filename, 'test') && unlink($write_test_filename);
        if (!$write_test) {
            return $this->makeError('Установщик', t('Нет прав на запись в любую папку сайта. Невозможно начать обновление. Обратитесь к администратору вашего сервера.'));
        }

        return $this->getNextStepInfo();
    }
    
    /**
    * Выполняет один этап обновления по карте обновлений.
    * 
    * @return array возвращает информацию о следующем шаге или информацию об ошибках
    */
    function doUpdate()
    {
        $pos = $this->data['position'];
        
        if ($pos < count($this->data['installMap'])) {
            $method = $this->data['installMap'][$pos]['callback'];
            $params = isset($this->data['installMap'][$pos]['callback_params']) ? $this->data['installMap'][$pos]['callback_params'] : [];
            if ( ($update_result = call_user_func_array([$this, $method], $params)) !== true ) {
                
                if ($update_result == '@timelimit') { //Запрос на повторный вызов, для операций, которые выполняются больше max_execution_time
                    return $this->getNextStepInfo() + ['need_restart' => true];
                }
                
                if (is_array($update_result)) { //Метод обработки будет сам определять, что отдать браузеру
                    return $update_result;
                }
                
                if (isset($this->data['installMap'][$pos]['moduleName'])) {
                    $module = t("Модуль '%0'", [$this->data['installMap'][$pos]['moduleName']]);
                } else {
                    $module = null;
                }
                return $this->makeError($module, $update_result);
            }
        } else {
            return $this->finishUpdate();
        }
        
        return $this->incStep()->getNextStepInfo();
    }
    
    /**
    * Производит резервное копирвание файлов обновляемых модулей
    * 
    * @return string | bool(true) возвращает true, если обновление прошло успешно
    */
    private function backupSystem()
    {
        \RS\File\Tools::deleteFolder($this->backup_folder, false);
        \RS\File\Tools::makePath($this->backup_folder.'/modules');
        $this->makePrivateDir($this->update_tmp_folder);
        
        $this->startTimeCount();
        $last_stop_module = isset($this->data['backup_system_last_module']) ? $this->data['backup_system_last_module'] : false;
        
        foreach($this->data['updateData'] as $module => $data) {
            
            if ($last_stop_module) {
                if ($last_stop_module == $module) {
                    $last_stop_module = false;
                }
                continue; //Пропускаем модули, которые мы уже скопировали
            }
            
            if (isset($this->module_folders[$module])) {
                foreach($this->module_folders[$module] as $folder) {
                    if (file_exists(\Setup::$PATH.$folder)) {
                        if (is_dir(\Setup::$PATH.$folder)) {
                            \RS\File\Tools::moveWithReplace(\Setup::$PATH.$folder, $this->backup_folder, true, true);
                        } else {
                            \RS\File\Tools::makePath($this->backup_folder.$folder, true);
                            if (!@copy(\Setup::$PATH.$folder, $this->backup_folder.$folder)) throw new \RS\File\Exception(t('Не удалось переместить файл'));
                        }
                    }
                }
            } else {
                if ($module[0] == '#') {
                    $module_folder = '/templates/';
                    $module_name = substr($module, 1);
                } else {
                    $module_folder = '/modules/';
                    $module_name = $module;
                }
                //Копирование папки модуля
                if (file_exists(\Setup::$PATH.$module_folder.$module_name)) {
                    \RS\File\Tools::moveWithReplace(\Setup::$PATH.$module_folder.$module_name, $this->backup_folder.$module_folder, true, true);
                }
            }

            
            if ($time_limit = $this->isTimeExpire()) {
                //Фиксируем последний модуль, который был скопирован
                $this->setData('backup_system_last_module', $module);
                return $time_limit;
            }
        }
        return true;
    }
    
    /**
    * Устанавливает точку отсчета времени выполнения операции
    * 
    * @return void
    */
    function startTimeCount()
    {
        $this->time_marker = microtime(true);
    }
    
    /**
    * Проверяет 
    * 
    */
    function isTimeExpire()
    {
        if (microtime(true) - $this->time_marker > $this->max_execution_time) {
            return '@timelimit';
        }
        return false;
    }
    
    /**
    * Возвращает true, если возможно выполнить восстановление из резервной копии
    * 
    * @return bool
    */
    function canRestore()
    {
        return ($this->data['position'] >0 && file_exists($this->backup_folder));
    }
    
    /**
    * Восстанавливает систему с помощью созданной перед обновлением резервной копии
    * 
    * @return bool
    */
    function restoreSystem()
    {
        \RS\File\Tools::moveWithReplace($this->backup_folder, \Setup::$PATH);
    }
    
    /**
    * Запрашивает у сервера обновлений имя файла с обновлениями для скачивания
    * 
    * @return string | bool(true) возвращает true, если обновление прошло успешно
    */
    private function getUpdateFilename()
    {
        $response = $this->requester([
            'do' => 'getUpdateFile',
            'need_product' => $this->data['updateProduct'],
            'items' => $this->data['selectedModules']
        ]);
        
        if ($response === false) {
            return t('Невозможно соединиться с сервером обновлений. Попробуйте повторить попытку позже');
        } else {
            if ($response['success'] != true) {
                return $response['error'];
            }
        }
        
        $response += [
            'addons' => []
        ];
        
        $core_file_data = $response['size']>0 ? [[
            'name' => 'core',        
            'type' => 'core',
            'size' => $response['size'],
            'file_id' => $response['file_id']
        ]] : [];
        
        $this->setData([
            'download_files' => array_merge($core_file_data, $response['addons']),
            'download_state_index' => 0,
            'download_state_offset' => 0
        ]);
        
        return true;
    }

    /**
    * Загружает с сервера обновлений архив с обновлениями
    * 
    * @return string | bool(true) возвращает true, если обновление прошло успешно
    */    
    private function downloadUpdatePackage()
    {
        if ($this->data['download_state_index'] == 0
            && $this->data['download_state_offset'] == 0) {
            //Подготавливаем папку для загрузки картинки
            \RS\File\Tools::makePath($this->update_tmp_folder_zip);
            \RS\File\Tools::deleteFolder($this->update_tmp_folder_zip, false);
        }
        
        $download_file = $this->data['download_files'][ $this->data['download_state_index'] ];
        
        $params = [
            'do' => 'downloadUpdatePackage',
            'file_id' => $download_file['file_id'],
            'type' => $download_file['type'],
            'offset' => $this->data['download_state_offset'],
            'length' => $this->config->file_download_part_size_mb * 1024 * 1024
        ];

        $content = $this->requester($params, false);
        
        if ($content === false) 
            return t('Не удалось загрузить файл с сервера обновлений');
        
        if (strlen($content)>0 && $download_file['size']>0) {
            //Сохраняем полученные сведения на диск
            $destination = $this->update_tmp_folder_zip.'/'.strtolower($download_file['name']).'.zip';
            $this->data['download_files'][ $this->data['download_state_index'] ]['archive'] = $destination;
            $this->setData('download_files', $this->data['download_files']);
            
            file_put_contents($destination, $content, FILE_APPEND);
            
            $new_offset = $params['offset'] + $params['length'];
            
            if ($new_offset < $download_file['size']) {
                //Подготовливаем ответ для загрузки следующей части данных того же файла
                $this->setData('download_state_offset', $new_offset);
                
                $local_percent = round($new_offset / $download_file['size'] * 100);
                $repeat_step = $this->getNextStepInfo();
                $repeat_step['next'] .= " ({$download_file['name']} - {$local_percent}%)";
                return $repeat_step;
            }
        }
        
        //Переключаемся на новый файл, но остаемся на том же шаге
        if ($this->data['download_state_index'] < count($this->data['download_files'])-1) {
            $this->setData([
                'download_state_index' => $this->data['download_state_index']+1,
                'download_state_offset' => 0
            ]);
            
            $download_file = $this->data['download_files'][ $this->data['download_state_index'] ];
            $next_file = $this->getNextStepInfo();
            $next_file['next'] .= " ({$download_file['name']})";            
            return $next_file;
        }
        
        //Переходим на следующий шаг        
        $this->setData('unpack_index', 0); //Устанавливаем, текущий файл распаковки 
        return true;
    }
    
    /**
    * Распаковывает архив с обновлением во временную папку
    * 
    * @return string | bool(true) возвращает true, если обновление прошло успешно
    */        
    private function unpackUpdatePackage()
    {
        if ($this->data['unpack_index'] == 0) {
            \RS\File\Tools::deleteFolder($this->update_upacked_folder, false);
        }
        
        $file = $this->data['download_files'][ $this->data['unpack_index'] ];
        
        $zip = new \ZipArchive();
        if ($zip->open($file['archive']) === true ) {            
            //Шаблоны из marketplace распаковываем в папку templates, модули в modules
            switch($file['type']) {
                case 'template': $folder = '/templates'; break;
                case 'solution':
                case 'module': $folder = '/modules'; break;
                default: $folder = '';
            }
            
            $unpack_folder = $this->update_upacked_folder.$folder;
            \RS\File\Tools::makePath($unpack_folder);            
            
            if (!$zip->extractTo($unpack_folder)) {
                return t('Не удалось распаковать архив с файлами обновления');
            }
            $zip->close();
        } else {
            return t('Не удалось открыть архив с файлами обновления');
        }
        
        if ($this->data['unpack_index'] < count($this->data['download_files'])-1 ) {
            //Подготавливаем ответ для распаковки следующего архива
            $this->setData('unpack_index', $this->data['unpack_index']+1);
            return '@timelimit';
        }

        return true;
    }

    function waitForCronFinished()
    {
        //Для совместимости с ранними версиями ReadyScript проверяем наличие класса
        if (class_exists('\RS\Cron\Manager')) {
            $cron_manager = \RS\Cron\Manager::obj();
            if (method_exists($cron_manager, 'isCronLocked')) {
                $locked = $cron_manager->isCronLocked();
                if($locked)
                {
                    sleep(1);
                    $repeat_step = $this->getNextStepInfo();
                    return $repeat_step;
                }
            }
        }

        return true;
    }
    
    /**
    * Устанавливает обновление ядра
    * 
    * @return array | string | bool(true) возвращает true, если обновление прошло успешно
    */
    private function updateCore()
    {
        $exclude_folders = ['modules', 'templates'];

        $dir = $this->update_upacked_folder;
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if ($file != '.' && $file != '..' && !in_array($file, $exclude_folders)) {
                        
                        if (is_dir($dir.'/'.$file)) {
                            try {
                                \RS\File\Tools::moveWithReplace($dir.'/'.$file, \Setup::$PATH, true);
                            } catch (\RS\File\Exception $e) {
                                return t('Не удалось переместить файлы из временной папки');
                            }
                        } else {
                            if (!@copy($dir.'/'.$file, \Setup::$PATH.'/'.$file)) {
                                return t('Не удалось переместить файлы из временной папки');
                            }
                        }
                        
                    }
                }
                closedir($dh);
            }
        }
        
        //Копируем папку с системными шаблонами
        $system_templates = $dir.'/templates/system';
        if (is_dir($system_templates)) {
            \RS\File\Tools::moveWithReplace($system_templates, \Setup::$PATH.'/templates', true);
        }
        
        $installer = new \RS\Config\Install();
        if (!$installer->update()) {
            return $installer->getErrors();
        }
        
        return true;
    }
        
    /**
    * Устанавливает обновление ядра
    * 
    * @param string $module имя модуля
    * @return array | string | bool(true) возвращает true, если обновление прошло успешно
    */
    private function updateModule($module)
    {
        try {
            \RS\File\Tools::moveWithReplace($this->update_upacked_folder.'/modules/'.$module , \Setup::$PATH.'/modules', true);
        } catch (\RS\File\Exception $e) {
            return t('Не удалось переместить файлы из временной папки');
        }
        
        $module_item = new \RS\Module\Item($module);
        if (($install_result = $module_item->install()) !== true) {
            return $install_result;
        }
        
        return true;
    }
    
    /**
    * Обновляет шаблон
    * 
    * @param string $template
    * @return string | bool(true) возвращает true, если обновление прошло успешно
    */
    private function updateTemplate($template)
    {
        try {
            \RS\File\Tools::moveWithReplace($this->update_upacked_folder.'/templates/'.$template , \Setup::$PATH.'/templates', true);
        } catch (\RS\File\Exception $e) {
            return t('Не удалось переместить файлы из временной папки');
        }
        //Если происходит обновление до старшей комплектации и пользователь использует тему default, 
        //то переустанавливаем блоки
        $current_theme = \RS\Config\Loader::getSiteConfig()->getThemeName();
        if ($this->data['updateProduct'] != \Setup::$SCRIPT_TYPE && $current_theme == 'default') {
            $theme = new \RS\Theme\Item('default');
            $theme->setThisTheme();
        }
        
        return true;
    }
    
    private function finishUpdate()
    {
        foreach($this->copy_files as $file) {
            if (file_exists($this->update_upacked_folder.$file)) {
                copy($this->update_upacked_folder.$file, \Setup::$PATH.$file);
            }
        }
        
        try {
            \RS\File\Tools::deleteFolder($this->update_upacked_folder, false);
            \RS\File\Tools::deleteFolder($this->update_tmp_folder_zip);
        } catch (\RS\Exception $e) {}
        
        //Очищаем весь кэш после обновления системы
        //Не пользуемся константой Cleaner::CACHE_TYPE_FULL - для совместимости
        \RS\Cache\Cleaner::obj()->clean('full');

        $module_manager = new \RS\Module\Manager();
        //Соблюдаем совместимость со старыми версиями RS
        if (method_exists($module_manager, 'syncDb')) {
            //Исправляем структуру БД
            $module_manager->syncDb();
        }

        EventManager::fire('siteupdate.finish');
        
        return [
            'next' => t('Обновление завершено'),
            'complete' => true,
            'percent' => 100
        ];
    }
    
    protected function incStep()
    {
        //Установка прошла успешно
        $this->setData('position', $this->data['position']+1);
        
        if ($this->data['position'] < count($this->data['installMap']) ) {
            $percent = round(($this->data['position']/(count($this->data['installMap'])))*100);
        } else {
            //Завершена установка последнего модуля
            $percent = 100;
        }
        $this->setData('percent', $percent);
        return $this;
    }
    
    /**
    * Возвращает информацию о следующем шаге установки
    * 
    * @return array
    */
    protected function getNextStepInfo()
    {
        $result = [];
        $pos = $this->data['position'];
        
        if ( $pos < count($this->data['installMap']) ) {
            $result['next'] = $this->data['installMap'][$pos]['status'];
        } else {
            $result['next'] = t('Завершение установки обновлений');
        }
        $result['percent'] = $this->data['percent'];
        
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
                'class' => 'field',
                'fieldname' => $module_title,
                'errors' => (array)$text
            ];
        }
        
        return $return;
    }    
    
    function getChangelog($module)
    {
        $response = $this->requester([
            'do' => 'getChangelog',
            'module' => $module,
            'need_product' => $this->data['updateProduct'],
            'version' => isset($this->data['myVersions'][$module]) ? $this->data['myVersions'][$module] : null
        ]);
        
        if ($response === false) {
            return t('Невозможно соединиться с сервером обновлений. Попробуйте повторить попытку позже');
        } else {
            if ($response['success'] != true) {
                return $response['error'];
            }
        }
        
        return $response['content'];
    }
    
    /**
    * Возвращает количество дней в течение которых доступно обновление скрипта
    * 
    * @return integer
    */
    function getUpdateExpireDays()
    {
        return SCRIPT_UPDATE_EXPIRE_DAYS;
    }
    
    /**
    * Возвращает true, если подписка на обноления истекла
    * 
    * @return bool
    */
    function isUpdateExpire()
    {
        return SCRIPT_UPDATE_EXPIRE < time();
    }
    
    /**
    * Возвращает время истечения льготного периода обновлений или false, если такой период уже завершен или еще не наступил.
    * 
    * @return timestamp | false
    */
    function getSaleUpdateExpire()
    {
        if (defined('SCRIPT_UPDATE_EXPIRE') && SCRIPT_UPDATE_EXPIRE >0) {
            $expire_sale = SCRIPT_UPDATE_EXPIRE + 60*60*24*30; //+ 1 месяц со дня окончания 
            if (SCRIPT_UPDATE_EXPIRE < time() && $expire_sale >= time()) {
                return  $expire_sale;
            }
        }
        return false;
    }
    
    /**
    * Возвращает количество дней до истечения льготного периода обновлений
    * 
    * @return integer
    */
    function getSaleUpdateExpireDays()
    {
        if ($expire = $this->getSaleUpdateExpire()) {
            return ceil(($expire-time())/60/60/24);
        }
        return 0;
    }
    
    /**
    * Возвращает ссылку на льготное продление обновлений
    * 
    * @return string
    */
    function getSaleUpdateUrl()
    {
        return \RS\Router\Manager::obj()->getAdminUrl('discountUpdate', null, 'siteupdate-wizard');
    }
    
    /**
    * Возвращает массив с информацией об обновлении системы или false, 
    * что означает что сведения устарели и необходимо вызвать метод checkUpdates
    * 
    * @return array | bool(false)
    */
    function getCachedUpdateData()
    {
        $cache = new CacheManager();
        $cache->enabled = true;
        $cache_key = $cache->tags(CACHE_TAG_SITE_UPDATE)->generateKey('check_update_data');
        if ($cache->expire(self::CHECK_FOR_UPDATES_INTERVAL)->validate($cache_key)) {
            return CacheManager::obj()->read($cache_key);
        }
        return false;
    }
    
    /**
    * Проверяет наличие доступных обновлений на сервере
    * 
    * @return bool | string возвразает true  в случае усеха, в противном случае текст ошибки
    */
    function checkUpdates()
    {
        $result = [
            'error' => false,
        ];
        $prepare = $this->prepareUpdateInfo(\Setup::$SCRIPT_TYPE);
        if ($prepare === true) {
            $skipped_modules = HashStoreApi::get(self::SKIPPED_MODULE_STORE_KEY, []);
            $update_data = $this->compareVersions($skipped_modules);
            $result['has_updates'] = count($update_data)>0;
        } else {
            $result['error'] = $this->getErrorsStr();
        }
        $cache_key = CacheManager::obj()->tags(CACHE_TAG_SITE_UPDATE)->generateKey('check_update_data');
        CacheManager::obj()->write($cache_key, $result);
        
        return $result;
    }
    
    function makePrivateDir($path)
    {
        $htaccess_path = $path.'/.htaccess';
        if (!file_exists($htaccess_path)) {
            @file_put_contents($htaccess_path, 'deny from all');
        }
    }
}