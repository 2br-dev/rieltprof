<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\File;

/**
* Класс предназначен для загрузки одного файла в заданный каталог.
* Может формировать уникальное имя загружаемому файлу. Проверять файл по различным параметрам.
*/
class Uploader extends \RS\Module\AbstractModel\BaseModel
{
    private
        $filename;
    
    protected
        $field_name,
        $field_form,
        $root_folder,
        $upload_folder,  //из \Setup
        $upload_filename,
        $allow_extensions, 
        $callback_checker,
        $allow_mime, 
        $max_size;
    
    /**
    * Конструктор загрузчика файлов
    * 
    * @param array $allow_extensions - допустимые расширения, без точки
    * @param string $upload_folder - папка для загрузки относительно DOCUMENT_ROOT. по умолчанию - это \Setup::$TMP_DIR
    * @param array $allow_mime - допустимые mime-типы файлов
    * @return Uploader
    */
    function __construct($allow_extensions = null, $upload_folder = null, $allow_mime = null)
    {
        $this
            ->setRootFolder(\Setup::$PATH)        
            ->setUploadFolder($upload_folder ?: \Setup::$TMP_REL_DIR)
            ->setAllowExtension($allow_extensions ?: [])
            ->setAllowMime($allow_mime);
    }
    
    /**
    * Устанавливает папку для загрузки файлов
    * 
    * @param string $folder - путь к папке, относительно корня, например /storage/tmp
    * @return Uploader
    */
    function setUploadFolder($folder)
    {
        $this->upload_folder = $folder;
        return $this;
    }
    
    /**
    * Устанавливает абсолютный путь к корнвой папке
    * 
    * @param string $root_folder - абсолютный путь к корневой папке
    * @return Uploader
    */
    function setRootFolder($root_folder)
    {
        $this->root_folder = $root_folder;
        return $this;
    }
    
    /**
    * Устанавливает имя файла, с которым необходимо загрузить файл.
    * 
    * @param mixed $upload_filename - Если задан null, то имя будет сгенерировано автоматически
    * @return Uploader
    */
    function setUploadFilename($upload_filename)
    {
        $this->upload_filename = $upload_filename;
        return $this;
    }
    
    /**
    * Устанавливает допустимые расширения файлов для загрузки
    * 
    * @param array $extensions
    * @return Uploader
    */
    function setAllowExtension($extensions)
    {
        $this->allow_extensions = (array)$extensions;
        return $this;
    }
    
    /**
    * Устанавливает допустимые mime типы файлов
    * 
    * @param mixed $allow_mime
    * @return Uploader
    */
    function setAllowMime($allow_mime)
    {
        $this->allow_mime = (array)$allow_mime;
        return $this;
    }
    
    /**
    * Устанавливает максимально допустимый размер файла
    * 
    * @param integer $max_size размер файла в байтах
    * @return Uploader
    */
    function setMaxSize($max_size)
    {
        $this->max_size = $max_size;
        return $this;
    }
    
    /**
    * Устанавливает поля, которые будут фигурировать в ошибке
    * 
    * @param string $field_name Название поля
    * @param string $field_form Идентификатор поля
    * @return Uploader
    */
    function setField($field_name, $field_form)
    {
        $this->field_name = $field_name;
        $this->field_form = $field_form;
    }
    
    /**
    * Устанавливает callback, которые вызывается перед загрузкой файла
    * Callback должен возвращать false - если загрузка может происходить, иначе текст ошибки.
    * 
    * @param callback $callback
    * @return Uploader
    */
    function setRightChecker($callback)
    {
        $this->callback_checker = $callback;
        return $this;
    }
    
    /**
    * Проверяет, возможно ли допустить загрузку файла
    * 
    * @param array $post_file_arr - массив входящего файла
    * @return false | string - False в случае, если ошибок нет, иначе текст ошибки
    */
    function checkRights($post_file_arr)
    {
        if (!$this->callback_checker) return false;
        return call_user_func($this->callback_checker, $this, $post_file_arr);
    }
    
    
    /**
    * Загружает файл в заданную папку
    * 
    * @param array $post_file_arr
    * @return boolean(false)
    */
    function uploadFile($post_file_arr)
    {
        $error = $this->checkRights($post_file_arr);
        if ($error === false && empty($post_file_arr)) {
            $error = t('Не выбран файл для загрузки');
        }        
        if ($error === false) {
            $error = Tools::checkUploadError($post_file_arr['error']);
        }
        if ($error === false) {
            list($fname, $fext) = Tools::parseFileName($post_file_arr['name'], true);
            
            if ($this->allow_extensions && !in_array(strtolower($fext), $this->allow_extensions)) {
                return $this->addError(t('Недопустимое расширение файла. Ожидается:'). implode(',', $this->allow_extensions), $this->field_name, $this->field_form);
            }
            
            if ($this->allow_mime && !in_array(strtolower($post_file_arr['type']), $this->allow_mime)) {
                return $this->addError(t('Недопустимый тип файла'), $this->field_name, $this->field_form);
            }
            
            if ($this->max_size && filesize($post_file_arr['tmp_name'])>$this->max_size) {
                return $this->addError(t('Превышен допустимый размер файла. Ожидается:'). Tools::fileSizeToStr($this->max_size), $this->field_name, $this->field_form);
            }
            
            $this->setFilename($this->upload_filename ?: md5(uniqid(mt_rand(), true)).'.'.$fext);
            Tools::makePath($this->getAbsolutePath(), true);
            if (!move_uploaded_file($post_file_arr['tmp_name'], $this->getAbsolutePath())) {
                return $this->addError(t('Неудалось переместить файл из временной папки'), $this->field_name, $this->field_form);
            }
        } else {
            return $this->addError($error, $this->field_name, $this->field_form);
        }
        return true;
    }
    
    /**
    * Возвращает полный путь к загруженному файлу
    * 
    * @return string
    */
    function getAbsolutePath()
    {
        return $this->root_folder.$this->upload_folder.'/'.$this->getFilename();
    }
    
    /**
    * Возвращает имя загруженного файла
    * 
    * @return string
    */
    function getFilename()
    {
        return $this->filename;
    }
    
    /**
    * Устанавливает имя загружаемого файла
    * 
    * @param string $filename
    * @return Uploader
    */
    function setFilename($filename)
    {
        $this->filename = $filename;
        return $this;
    }
}
