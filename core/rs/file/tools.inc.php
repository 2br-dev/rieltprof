<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\File;

/**
* Здесь собраны различные дополнительные функции для работы с файлами
*/
class Tools
{
    /**
    * Создает недостающие папки для указанного пути
    * 
    * @param string $dstFilename - путь к файлу или папке
    * @param boolean $parseDir - Устанавливайте true, если в $dstFilename присутствует еще имя файла, в этом случае оно будет игнорироваться.
    */
    public static function makePath($dstFilename, $parseDir = false)
    {
        if ($parseDir) $dstFilename = dirname($dstFilename);
        $old_umask = umask(0);
        $result = file_exists($dstFilename) || mkdir($dstFilename, \Setup::$CREATE_DIR_RIGHTS, true);
        umask($old_umask);
        return $result;
    }
    
    
    /**
    * Удаляет рекурсивно папку и все ее содержимое.
    * 
    * @param string $dir - путь к папке
    * @param bool $delself - Если true, то папка $dir тоже будет удалена, иначе, только содержимое папки $dir
    * @return bool true - если все прошло успешно, false - если что-то не удалено.
    */
    public static function deleteFolder($dir, $delself = true)
    {
        $result = true;
        $dir = rtrim($dir, '/');
        if (is_dir($dir)) {
            $files = array_diff(scandir($dir), ['.','..']);
            foreach ($files as $file) {
              $result = (is_dir("$dir/$file") ? self::deleteFolder("$dir/$file", true) : @unlink("$dir/$file")) && $result;
            }
            return $delself ? @rmdir($dir) && $result : $result;
        }
        return $result;
    }
    
    /**
    * Переносит рекурсивно или копирует папку из одного места в другое с заменой файлов
    * 
    * @param string $sfolder папка источник
    * @param string $dfolder папка назначения
    * @param bool $moveself переносить ли папку источник
    * @param bool $copy если задано true, то копировать файлы, иначе переносить
    * 
    * @return bool возвращает true, если все файлы перенесены успешно, если хоть один файл не перенесен, то false
    */
    public static function moveWithReplace($sfolder, $dfolder, $moveself = false, $copy = false)
    {
        $result = true;
        $sfolder = rtrim($sfolder, '/');
        $dfolder = rtrim($dfolder, '/');
        if (!file_exists($sfolder)) return $result;
        
        if ($moveself) {
            $dfolder.= '/'.basename($sfolder);            
        }
        $result = @(file_exists($dfolder) || mkdir($dfolder, \Setup::$CREATE_DIR_RIGHTS, true)) && $result;    
        
        if ($dh = opendir($sfolder)) {
            while (($file = readdir($dh)) !== false) {
                if ($file == '.' || $file == '..') continue;
                $sfile = $sfolder.'/'.$file;
                $dfile = $dfolder.'/'.$file;
                if (is_dir($sfile)) {
                    $result = (file_exists($dfile) || mkdir($dfile, \Setup::$CREATE_DIR_RIGHTS)) && $result;
                    $result = self::moveWithReplace($sfile, $dfile, false, $copy) && $result;
                    $result = @($copy || rmdir($sfile)) && $result;
                } else {
                    $result = @(!file_exists($dfile) || unlink($dfile)) && $result;
                    $result = @($copy ? copy($sfile, $dfile) : rename($sfile, $dfile)) && $result;
                }
            }
            closedir($dh);
            //Удаляем 
            if ($moveself && !$copy) {
                $result = @rmdir($sfolder) && $result;
            }
        }
        return $result;
    }
    
    /**
    * Возвращает массив с двумя элементами array(0 => 'Имя файла', 1 => 'Расширение')
    * Удобно применять так list($filename, $fileext) = \RS\File\Tools::parseFileName('test.txt');
    * 
    * @param string $filename - имя файла
    * @param bool $removeDot - если true, то точка в расширении будет удалена
    * @return array
    */
    public static function parseFileName($filename, $removeDot = false)
    {
        if (preg_match('/^(.+)(\..+)$/u', $filename, $match))
        {
            array_shift($match);
            if ($removeDot) {
                $match[1] = ltrim($match[1], '.');
            }
            return $match;
        }
        return [$filename,''];
    }

    /**
    * Возвращает false - если нет ошибок при загрузке файла, иначе текст ошибки
    * 
    * @param integer $err_status - результат $_FILES[имя_формы][error]
    * @return bool(false) | string
    */
    public static function checkUploadError($err_status)
    {
        switch($err_status) 
        {
            case UPLOAD_ERR_OK: $res = false; break;            
            case UPLOAD_ERR_INI_SIZE: $res = t('Загрузка файлов такого размера не поддерживается'); break;
            case UPLOAD_ERR_FORM_SIZE: $res = t('Загружен слишком большой файл'); break;
            case UPLOAD_ERR_PARTIAL: $res = t('Файл загружен частично'); break;
            case UPLOAD_ERR_NO_FILE: $res = t('Не выбран файл для загрузки'); break;
            case UPLOAD_ERR_NO_TMP_DIR: $res = t('На сервере не обнаружена временная папка'); break;
            case UPLOAD_ERR_CANT_WRITE: $res = t('Не возможно записать файл на сервер'); break;
            default: $res = t('Файл не загружен на сервер');
        }
         return $res;
    }       
    
    /**
    * Отправляет файл на скачивание
    * 
    * @param string $source - абсолютный путь к файлу
    * @param string $filename - имя файла в диалоге сохранения файла
    * @param string $mime - Mime тип содержимого файла
    * @return bool возвращает true, если файл найден, иначе false
    */
    public static function sendToDownload($source, $filename, $mime = 'application/octet-stream')
    {
        $app = \RS\Application\Application::getInstance();
        if (!file_exists($source)) {
            $app->headers->setStatusCode('404');
            return false;
        }
        $app->cleanOutput();
        $app->headers->addHeaders([
            'Content-Type' => $mime,
            'Content-Length' => filesize($source),
            'Content-Transfer-Encoding' => 'binary',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Connection' => 'close'
        ]);
        $app->headers->sendHeaders();
        
        readfile($source);
        return true;
    }
    
    /**
    * Возвращает строковое представление размера файла
    * 
    * @param integer $inbytes размер файла в байтах
    * @return string
    */
    public static function fileSizeToStr($inbytes)
    {
        //Переводим байты в Килобайты, мегабайты, и.т.д
        $filesize = $inbytes;
        $units = [t('байт'), t('Кб'), t('Мб'), t('Гб'), t('Тб'), t('Пб')];
        $i = 0;
        while($filesize > 1023) {
            $filesize /= 1024;
            $i++;
        }
        $filesize = number_format($filesize,1,'.',' ');
        //Делаем вместо 1.0 Кб или 200.0 Мб - 1 Кб, 200 Мб
        if (strrpos($filesize, '.0') !== false) $filesize = (int)$filesize;
        return $filesize.' '.$units[$i];        
    }
    
    /**
    * Возвращает максимально допустимый в настройках PHP размер загружаемого файла в байтах
    * 
    * @return integer максимально допустимый размер загружаемого файла в байтах
    */
    public static function getMaxPostFileSize()
    {
        $ini_post_max_size = trim(ini_get('post_max_size'));
        $ini_upload_max_filesize = trim(ini_get('upload_max_filesize'));
        $s = ['g'=> 1<<30, 'm' => 1<<20, 'k' => 1<<10];
        $v = strtolower(substr($ini_post_max_size, -1));
        if ($v == 'g' || $v == 'm' || $v == 'k') {
            $post_max_size = intval($ini_post_max_size) * ($s[strtolower(substr($ini_post_max_size, -1))] ?: 1);
            $upload_max_filesize = intval($ini_upload_max_filesize) * ($s[strtolower(substr($ini_upload_max_filesize, -1))] ?: 1);
            return min($post_max_size, $upload_max_filesize);
        } else{
            return min($ini_post_max_size, $ini_upload_max_filesize);
        }
    }
    
    /**
    * Группирует сведения по загруженным файлам вокруг файлов.
    * Вместо массива 
    * [
    *   'name' => [0 => ..., 1 => ...],
    *   'type' => [0 => ..., 1 => ...],
    *   'tmp_name' => [0 => ..., 1 => ...],
    *   'error' => [0 => ..., 1 => ...],
    *   'size' => [0 => ..., 1 => ...]
    * ]
    * массив принимает вид:
    * [
    *   [
    *       'name' => ...,
    *       'type' => ...,
    *       'tmp_name' => ...,
    *       'error' => ...,
    *       'size' => ...
    *   ],
    *   [
    *       'name' => ...,
    *       'type' => ...,
    *       'tmp_name' => ...,
    *       'error' => ...,
    *       'size' => ...
    *   ]
    * ]
    * 
    * @return array
    */
    public static function normalizeFilePost($post_files_arr)
    {
        $result = [];
        if (isset($post_files_arr['name'])) {
            foreach($post_files_arr['name'] as $n => $value) {
                $result[$n] = [
                    'name'      => $value,
                    'type'      => $post_files_arr['type'][$n],
                    'tmp_name'  => $post_files_arr['tmp_name'][$n],
                    'error'     => $post_files_arr['error'][$n],
                    'size'      => $post_files_arr['size'][$n],
                ];
            }
        }
        return $result;
    }
    
    /**
    * Закрывает директорию от публичного просмотра, добавляя в ней файл .htaccess со строкой deny from all
    * 
    * @param string $path - путь к директории
    * @return string возвращает путь к директории $path
    */
    public static function makePrivateDir($path)
    {
        $htaccess_path = $path.'/.htaccess';
        if (!file_exists($htaccess_path)) {
            file_put_contents($htaccess_path, 'deny from all');
        }

        return $path;
    }

    /**
     * Возвращает относительный путь на основе абсолютного пути.
     * Итоговый путь будет относительно $_SERVER['DOCUMENT_ROOT'], включая папку
     * В случае, если не удается сделать относительный путь, то возвращается исходный $absolute_path
     *
     * @param string $absolute_path абсолютный путь к папке или файлу
     * @return string
     */
    public static function buildRelativePath($absolute_path)
    {
        $absolute_path = str_replace('\\', '/', $absolute_path);
        return str_replace(\Setup::$ROOT, '', $absolute_path);
    }
}

