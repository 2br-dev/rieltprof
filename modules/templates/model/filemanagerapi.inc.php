<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Templates\Model;

use RS\AccessControl\Rights;
use RS\AccessControl\DefaultModuleRights;

class FileManagerApi extends \RS\Module\AbstractModel\BaseModel
{
    const        
        PATH_SESSION_VAR = 'TMANAGER_PATH',
        ERROR_FILE_ALREADY_EXISTS = 1;
        
    protected
        $field_list,
        $theme_list,
    
        $allow_ext = ['css', 'tpl', 'js', 'jpg', 'gif', 'png', 'fla', 'flv', 'svg', 'htm', 'html', 'ttf', 'odt', 'woff'],
        $allow_edit_ext = ['css', 'tpl', 'js'];
        
    function __construct()
    {
        $this->field_list = [
            'filename' => t('Имя файла')
        ];
    }
    
    /**
    * Возвращает разрешения, с которыми можно загружать и в которые можно переименовывать файлы.
    * @return array
    */
    function getAllowExt()
    {
        return $this->allow_ext;
    }
    

    /**
    * Возвращает true, если у текущего пользователя нет прав на запись.
    * Текст ошибки можно получить через getErrors
    * 
    * @param string $right - Проверяемое право
    * @return bool
    */
    function noWriteRights($right)
    {
        if ($acl_err = Rights::CheckRightError($this, $right)) {
            $this->addError($acl_err);
            return true;
        }
        return false;
    }

    /**
    * Возвращает корневые элементы, в которых возможно изменение шаблонов или ресурсов
    * @return array
    */
    public function getRootSections($only_themes = false)
    {
        $theme_list = $this->getThemeList();
        
        $result = [
            'themes' => [],
            'modules' => []
        ];
        foreach($this->theme_list as $theme) {
            $info = $theme->getInfo();
            $result['themes'][$theme->getName()] = [
                'title' => $info['name']." ({$theme->getName()})"
            ];
        }
        
        if (!$only_themes) {
            $module_manager = new \RS\Module\Manager();
            $module_list = $module_manager->getList();        
            foreach($module_list as $short_name => $module) {
                $result['modules'][$short_name] = [
                    'title' => $module->getConfig()->name
                ];
            }
        }
        return $result;
    }
    
    protected function getThemeList()
    {
        if (!isset($this->theme_list)) {
            $theme_manager = new \RS\Theme\Manager();            
            $this->theme_list = $theme_manager->getList();
        }
        return $this->theme_list;
    }
    
    public function getAllowEditExtensions()
    {
        return array_combine($this->allow_edit_ext, $this->allow_edit_ext);
    }
    
    public function getDefaultPath()
    {
        //Пытаемся загрузить первую тему
        $theme_list = $this->getThemeList();
        if (count($theme_list)) {
            $name = reset($theme_list)->getName();
            $path = "theme:{$name}/";
        } else {
            $path = "module:main/";
        }
        return $this->extractPath($path);
    }
    
    /**
    * Возвращает массив с подробной информацией о пути $path
    * В массиве содержится: тип корневого элемента, имя корневого элемента, относительный путь от корневого элемента,
    * базовый путь для корневого элмента
    * 
    * @param string $path
    * @return array
    */
    public function extractPath($path)
    {
        //Значние по умолчанию
        $path = str_replace(['../', '<', '>'], '', $path);
        $result = false;
        if (preg_match('/^(theme|module):([\w\-]+)(\/.*)?/', $path, $match)) {
            
            $result = [];
            $result['type'] = $match[1];
            $result['type_value'] = $match[2];
            
            if (isset($match[3]) && mb_substr($match[3],-1) != '/') { //Если задано имя файла
                $result['filename'] = basename($match[3]);
                $result['relative_path'] = rtrim(dirname($match[3]),'\/').'/';
                $result['is_file'] = true;
            } else {
                $result['relative_path'] = isset($match[3]) ? $match[3] : '/';
                $result['is_file'] = false;
            }
            
            if ($result['type'] == 'theme') {
                if (\RS\Theme\Manager::issetTheme($result['type_value'])) {
                    $theme = new \RS\Theme\Item($result['type_value']);
                    $result['relative_basepath'] = rtrim($theme->getRelativePath(),'/');
                } else {
                    return false;
                }
            } else {
                if (\RS\Module\Manager::staticModuleExists($result['type_value'])) {
                    $folders = \RS\Module\Item::getResourceFolders($result['type_value']);
                    $result['relative_basepath'] = rtrim($folders['mod_tpl'],'/');
                } else {
                    return false;
                }
            }
            $result['basepath'] = \Setup::$ROOT.$result['relative_basepath'];            
            
            $sections = preg_split('/\//', $result['relative_path'], null, PREG_SPLIT_NO_EMPTY);
            $this_path = $result['type'].':'.$result['type_value'].'/';
            $this_rel_path = '/';
            foreach($sections as $section) {
                $result['parent'] = $this_path;
                $result['parent_rel'] = $this_rel_path;
                $this_rel_path .= $section.'/';
                $this_path.= $section.'/';
                $result['sections'][$this_path] = $section;
            }
            $result['public_dir'] = $result['type'].':'.$result['type_value'].$result['relative_path'];
            $result['relative_rootpath'] = rtrim($result['relative_basepath'].$result['relative_path'], '/').(isset($result['filename']) ? '/'.$result['filename'] : '');
            
            $result['rootpath'] = \Setup::$ROOT.$result['relative_rootpath'];
        }
        
        return $result;
    }
    
    /**
    * Возвращает список файлов по специално заданному пути.
    * 
    * @param string $path - путь к каталогу в формате: (theme|module):ИМЯ ТЕМЫ | МОДУЛЯ / [относительлный путь/]
    * @param array $allow_extension - массив с расширениями файлов, которые должны выводиться. Если null - то используется $this->allow_ext
    * @return array
    */
    function getFileList($path, array $allow_extension = null, $only_themes = false)
    {
        if (!$allow_extension) {
            $allow_extension = $this->allow_ext;
        }
        
        $result = [];
        $epath = $this->extractPath($path);
        if ($only_themes && $epath && $epath['type'] == 'module') $epath = false;
        if (!$epath) $epath = $this->getDefaultPath();
       
        $result['epath'] = $epath;
        $result['allow_extension'] = $allow_extension;
        
        $dir = $epath['basepath'].$epath['relative_path'];
        $folder_list = glob($dir.'*', GLOB_ONLYDIR);
        if (is_array($folder_list))
            foreach ($folder_list as $folder) {
                $name = basename($folder);
                $result['items'][] = [
                    'name' => $name, 
                    'type' => 'dir', 
                    'link' => $epath['public_dir'].$name.'/', 
                    'descr' => ''];
            }
        
        $file_list = [];
        foreach ($allow_extension as $type) {
            $tmp = glob($dir.'*.'.$type);
            if ($tmp) $file_list = array_merge($file_list, $tmp);
        }
        
        foreach ($file_list as $file) {
            $info = $this->getTplInfo($file);
            if ( $info !== false) {
                $name = basename($file);
                list($fname, $fext) = \RS\File\Tools::parseFileName($name);
                
                $result['items'][] = [
                    'path' => $epath['public_dir'],
                    'filename' => $name,
                    'name' => $fname,
                    'ext' => ltrim($fext,'.'),
                    'link' => $epath['public_dir'].$name,
                    'type' => 'file', 
                    'descr' => $info
                ];
            }
        }
        
        return $result;        
    }
    
    /**
    * Возвращает первый Smarty комментарий у шаблона или false - если шаблон не найден
    * Считаем, что в первом комментарии содержится краткая (до 255 символов) информация о шаблоне
    * 
    * @param string $filename
    * @return string | false
    */
    function getTplInfo( $filename )
    {
        if (!file_exists($filename)) return false;
        $fp = fopen($filename, 'r');
        $line = fread($fp, 502);
        $line = mb_substr($line,0,255);
        fclose($fp);
        if (preg_match('/\{\*\s*(.*?)\s*\*\}/ums', $line,$match)) {
            return $match[1];
        }
        return '';
    }    
    
    function saveFile($fullpath, $content, $overwrite = false)
    {
        //Проверяем права на запись для модуля
        if ($this->noWriteRights(DefaultModuleRights::RIGHT_CREATE)) return false;

        $epath = $this->extractPath($fullpath);
        
        if ($epath && $epath['is_file']) {
            if ($this->checkFileName($epath['filename'], true)) {
                
                $full_filename = $epath['rootpath'];
                if (!$overwrite && file_exists($full_filename)) {
                    $this->addError(t('Файл с таким именем уже существует'), 'filename');
                    return false;
                } else {
                    if (!is_dir($epath['basepath'].$epath['relative_path'])) {
                        if (!mkdir($epath['basepath'].$epath['relative_path'], null, true)) {
                            $this->addError(t('Не удалось создать заданную папку для файла'));
                        }
                    }
                    
                    if (@file_put_contents($full_filename, $content) !== false) {
                        return true;
                    } else {
                        $this->addError(t('Не удалось сохранить файл. Проверьте права на запись в данной директории.').$full_filename);
                        return false;
                    }
                }
            }
        } else {
            $this->addError(t('Неверно задано имя файла'), 'filename');
        }
        return false;
    }
    
    public function downloadFile($path)
    {
        $epath = $this->extractPath($path);
        if ($epath && $epath['is_file']) {
            \RS\File\Tools::sendToDownload($epath['rootpath'], $epath['filename']);
        }
    }
    
    /**
    * Возвращает содержимое файла
    * 
    * @param string $path - специальный путь к файлу
    * @return string
    */
    public function getFileContent($path)
    {
        $epath = $this->extractPath($path);
        if ($epath) {
            return file_get_contents($epath['rootpath']);
        }
        return '';
    }
    
    /**
    * Возвращает true, если имя файла или папки соответствует требованиям системы, иначе false
    * Добавляет в список ошибок - ошибку несоответствия имени файла
    * 
    * @param string $filename
    * @param boolean $filterExtension - проверять расширение
    * @return boolean
    */
    function checkFileName($filename, $filterExtension = false)
    {
        
        if (preg_match('/^[a-zA-Z0-9\-\_.]+$/', $filename)
            && (!$filterExtension || preg_match('/^[a-zA-Z0-9\-\_.]+?\.('.implode($this->allow_ext, '|').')$/', $filename))) 
        {
            return true;
        }
        $this->addError(t('Недопустимое имя'), 'filename');
        return false;
    }
    
    /**
    * Переименовывает файл или папку
    * 
    * @param string $path - специальный путь к файлу или папке
    * @param string $new_name - новое имя файла, папки
    * @return boolean
    */
    function rename($path, $new_name)
    {
        //Проверяем права на запись для модуля
        if ($this->noWriteRights(DefaultModuleRights::RIGHT_UPDATE)) return false;

        $epath = $this->extractPath($path);
        
        if ($epath['is_file']) { 
            //Переименовываем файл
            $full_new_name = $epath['basepath'].$epath['relative_path'].$new_name;
            list($name, $ext) = \RS\File\Tools::parseFileName($new_name, true);
            if (!in_array($ext, $this->getAllowExt())) {
                return $this->addError(t('Задано недопустимое расширение'));
            }
        } else { 
            //Переименовываем папку
            $full_new_name = $epath['basepath'].$epath['parent_rel'].$new_name;
        }
        
        if ($epath && file_exists($epath['rootpath'])) {
            $result = @rename($epath['rootpath'], $full_new_name);
            if (!$result) {
                $this->addError(t('Не удалось переименовать файл или папку'));
            }
            return $result;
        } else {
            $this->addError(t('Исходного файла или папки не существует'));
            return false;
        }
    }
    
    /**
    * Удаляет файл или папку
    * 
    * @param string $path - специальный путь к файлу или папке
    * @return boolean
    */
    function delete($path)
    {
        //Проверяем права на запись для модуля
        if ($this->noWriteRights(DefaultModuleRights::RIGHT_DELETE)) return false;

        $epath = $this->extractPath($path);
        
        if ($epath) {
            if ($epath['is_file']) { 
                //Удаляем файл
                return @unlink($epath['rootpath']);
            } else { 
                //Удаляем папку
                return \RS\File\Tools::deleteFolder($epath['rootpath']);
            }
        }
        return false;
    }
    
    /**
    * Создает папку
    * 
    * @param string $path - специальный путь к файлу или папке
    * @param string $new_folder - новое имя папки
    */
    function makeDir($path, $new_folder)
    {
        //Проверяем права на запись для модуля
        if ($this->noWriteRights(DefaultModuleRights::RIGHT_CREATE)) return false;
                
        $epath = $this->extractPath($path);
        if ($epath) {
            $new_folder_path = $epath['basepath'].$epath['relative_path'].$new_folder;
            if (file_exists($new_folder_path)) {
                $this->addError(t('Файл или папка с таким именем уже существует'));
                return false;
            } else {
                return @mkdir($new_folder_path);
            }
        }
        return false;
    }
    
    
    public function getPathFromSession()
    {
        return isset($_SESSION[self::PATH_SESSION_VAR]) ? $_SESSION[self::PATH_SESSION_VAR] : '';
    }

    public function savePathInSession($path)
    {
        $_SESSION[self::PATH_SESSION_VAR] = $path;
    }    
    
    /**
    * Парсит путь к начальному шаблону и возвращает отдельно путь и имя файла
    * 
    * @param string $start_tpl
    * @return array | bool(false) - false в случае если $start_tpl некорректный
    */
    public function parseStartTpl($start_tpl)
    {
        $path = dirname($start_tpl).'/';
        $path = preg_replace('/^%(.*?)%/', 'module:$1', $path);
        $filename = basename($start_tpl);
        if (preg_match('/^(module|theme)\:/', $path) && $filename) {
            return [
                'path' => $path,
                'filename' => $filename
            ];
        }
        return false;
    }
    
    /**
    * Помещает загруженный файл в директорию $path.
    * Для выполнения необходимы права на запись у модуля.
    * Проверяет соответствие расширения файла допущеным
    * 
    * @param string $path Папка для загрузки файла
    * @param array $file_arr Массив со сведениями о файле, полеченном из POST
    */
    public function uploadFile($path, $file_arr)
    {
        if ($this->noWriteRights(DefaultModuleRights::RIGHT_CREATE)) return false;
        
        $epath = $this->extractPath($path);
        if ($epath === false) return $this->addError(t('Неверный путь для записи файла'));
        
        list($name, $ext) = \RS\File\Tools::parseFileName($file_arr['name'], true);
        if (!in_array($ext, $this->getAllowExt())) {
            return $this->addError(t('Недопустимое расширение файла'));
        }
        
        $uploader = new \RS\File\Uploader(null, $epath['relative_basepath'].$epath['relative_path']);
        $uploader->setUploadFilename($file_arr['name']);
        
        if (!$uploader->uploadFile($file_arr)) {
            return $this->addError($uploader->getErrorsStr());
        }
        
        return true;
    }
}

