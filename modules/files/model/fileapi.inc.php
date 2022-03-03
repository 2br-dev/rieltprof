<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Files\Model;
use \Files\Model\FilesType;

/**
* Класс для организации выборок ORM объекта.
* В этом классе рекомендуется также реализовывать любые дополнительные методы, связанные с заявленной в конструкторе моделью
*/
class FileApi extends \RS\Module\AbstractModel\EntityList
{
    const       
        UPLOAD_TYPE_COPY = 0,
        UPLOAD_TYPE_MOVE = 1,
        UPLOAD_TYPE_MOVEUPLOADED = 2,
        
        /**
        * Длина генерируемого имени файла на сервере
        */
        SERVERNAME_TOTAL_LENGTH = 15;
    
    protected static
        /**
        * Папка с оригиналами файлов, относительно /storage
        */
        $upload_folder = '/files/original';
        
        
    function __construct()
    {
        parent::__construct(new Orm\File, [
            'sortField' => 'sortn',
            'defaultOrder' => 'sortn',            
            'multisite' => true,
        ]);
    }
    
    /**
    * Загружает список файлов из POST
    * 
    * @param array $files - массив с файлами, приходящий из POST
    * @param FilesType\AbstractType $type_class - тип файлов
    * @param mixed $link_id
    */
    function uploadFromPost($files, FilesType\AbstractType $type_class, $link_id)
    {
        $items = [];
        $normalized_files = \RS\File\Tools::normalizeFilePost($files);
        foreach ($normalized_files as $file) {            
            $this->cleanErrors();
            
            if ($error = \RS\File\Tools::checkUploadError($file['error'])) {
                $this->addError("{$file['name']}: ".$error);
                $uploaded_file = false;
            } else {
                $uploaded_file = $this->uploadFromUrl($file['tmp_name'], 
                                                      $type_class, 
                                                      $link_id, 
                                                      $file['name'], 
                                                      self::UPLOAD_TYPE_MOVEUPLOADED,
                                                      ['mime' => $file['type']]);
            }
            
            if ($uploaded_file) {
                $item = [
                    'success' => true,
                    'file' => $uploaded_file
                ];
            } else {
                $item = [
                    'success' => false,
                    'error' => $this->getErrorsStr()
                ];
            }
            $items[] = $item;
        }
        
        return $items;
    }
    
    /**
    * Загружает в систему один файл и связывает его с объектом
    * 
    * @param string $file_path - путь к файлу
    * @param FilesType\AbstractType $type_class - класс типа связи
    * @param integer $link_id - ID связываемого объекта
    * @param string $display_name - Имя файла, которое будет предложено браузером при сохранении
    * @param string $upload_type - тип переноса файла при загрузке. 0 - копирование, 1 - перемещение, 2 - перемещение только загруженного файла
    * @param array $info - Масив с доп. полями. Поддерживается [mime => 'MIME тип файла']
    * @return Orm\File | boolean(false)
    */
    function uploadFromUrl($file_path, FilesType\AbstractType $type_class, $link_id, $display_name = null, $upload_type = self::UPLOAD_TYPE_COPY, $info = [])
    {
        $display_name = $display_name ?: basename($file_path);
        list($name, $ext) = \RS\File\Tools::parseFileName($display_name);
        
        if (!$this->canUploadFile($file_path, $type_class, $display_name)) {
            return false;
        }
        
        $server_name = self::generateFilename().$ext;
        $server_folder = self::getStoragePath();
        $destination_path = $server_folder.'/'.$server_name;
        \RS\File\Tools::makePath($destination_path, true);
        \RS\File\Tools::makePrivateDir($server_folder);
    
        switch ($upload_type) {
            case self::UPLOAD_TYPE_COPY: {
                $move_result = copy($file_path, $destination_path); break;
            }
            case self::UPLOAD_TYPE_MOVE: {
                $move_result = rename($file_path, $destination_path); break;
            }
            case self::UPLOAD_TYPE_MOVEUPLOADED: {
                $move_result = move_uploaded_file($file_path, $destination_path); break;
            }
        }
        
        if (!$move_result) {
            return $this->addError($display_name.': '.t('Не удалось переместить файл из временной папки'));
        }
        
        $file = new Orm\File();
        $file->getFromArray($info);
        $file['servername'] = $server_name;
        $file['name'] = $display_name;
        $file['size'] = filesize($destination_path);
        $file['access'] = $type_class->default_access_type;
        $file['link_type_class'] = strtolower($type_class->getShortName());
        $file['link_id'] = $link_id;
        if ($file->insert()) {
            return $file;
        } else {
            return $this->addError($file->getErrorsStr());
        }
    }
    
    /**
    * Возвращает true, если возможна загрузка файла на сервер, иначе false.
    * Сообщение об ошибке можно получить с помощью метода getErrors()
    * 
    * @param string $filepath Путь к оригиналу файла
    * @param FilesType\AbstractType $type_class 
    * @param mixed $display_name
    * @return boolean(false)
    */
    function canUploadFile($file_path, FilesType\AbstractType $type_class, $display_name = null )
    {
        //Проверяем права
        if ($error = $type_class->checkUploadRightErrors($file_path)) {
            return $this->addError($error);
        }
                    
        //Проверяем расширения
        $allowed_extensions = $type_class->getAllowedExtensions();
        list($filename, $ext) = \RS\File\Tools::parseFileName($display_name, true);

        if ($allowed_extensions && !in_array($ext, $allowed_extensions)) {
            return $this->addError(t(' Недопустимое расширение файла. Допускается:%0', [implode(', ', $allowed_extensions)]));
        }        
        
        return true;
    }

    
    
    /**
    * Возвращает объект типа связи
    * 
    * @param string $type_class_short_name короткое имя класса
    * Например files-catalogproduct, будет сконвертирован в имя класса \Files\Model\FilesType\CatalogProduct
    * т.е. первый минус конвертируется в \Model\FilesType\, остальные минусы конвертируются в \
    * 
    * @return \Files\Model\FilesType\AbstractType
    */
    public static function getTypeClassInstance($type_class_short_name)
    {
        if (preg_match('/^([^\-]+?)\-(.*)$/', $type_class_short_name, $match)) {
            $type_class_name = str_replace('-','\\', "-{$match[1]}-model-filestype-{$match[2]}");
        } else {
            throw new FilesType\Exception(t('Неверно указан идентификатор типа связи'));
        }
        
        if (class_exists($type_class_name)) {
            $link_type = new $type_class_name;
            if (!$link_type instanceof FilesType\AbstractType) {
                throw new FilesType\Exception(t('Класс связи должен быть потомком \Files\Model\FilesType\AbstractType'));
            }
            return $link_type;
        } else {
            throw new FilesType\Exception(t('Не найден класс типа связи %0', [$type_class_name]));
        }
    }
    
    /**
    * Возвращает путь к папке, в которой хранятся загруженные к объектам файлы
    * 
    * @return string
    */
    public static function getStoragePath()
    {
        return \Setup::$PATH.\Setup::$STORAGE_DIR.self::$upload_folder;
    }
    
    /**
    * Возвращает сгенерированный путь для сохранения файла
    * 
    * @return string
    */
    public static function generateFilename()
    {
        $folder_symbols = 'abcdefghi';
        $folder = $folder_symbols[ mt_rand(0, strlen($folder_symbols)-1) ];
        
        $symbols = '01234abcdefghi567jklmnopqrstuvwxyz890';
        $uniq = '';
        for ($i=0; $i<self::SERVERNAME_TOTAL_LENGTH; $i++) {
            $uniq .= $symbols[ mt_rand(0, strlen($symbols)-1) ];
        }
        return $folder.'/'.$uniq;
    }
    
    /**
    * Возвращает объект одного файла с учетом текущего сайта
    * 
    * @param integer $id - ID файла
    * @param integer | null $site_id - ID сайта
    * @return Orm\File | false
    */
    function getFile($id, $site_id = null)
    {
        if ($site_id === null) {
            $site_id = \RS\Site\Manager::getSiteId();
        }
        
        return \RS\Orm\Request::make()
            ->from($this->obj_instance)
            ->where([
                'id' => $id,
                'site_id' => $site_id
            ])->object();
    }
    
    /**
    * Удаляет файлы с учетом текущего сайта
    * 
    * @param array $ids
    * @param integer | null $site_id
    * @return boolean
    */
    function deleteFiles($ids, $site_id = null)
    {
        if ($this->noWriteRights()) return false;
        
        $result = true;
        foreach($ids as $id) {
            if ($file = $this->getFile($id, $site_id)) {
                $result = $file->delete() && $result;
            }
        }
        return $result;
    }
    
    /**
    * Изменяет уровень видимости файла
    * 
    * @param integer $id - ID файла
    * @param string $access
    */
    function changeAccess($id, $access, $site_id = null)
    {
        if ($this->noWriteRights()) return false;
        
        if ($file = $this->getFile($id, $site_id)) {
            $allowed_access = $file->getLinkType()->getAccessTypes();
            if (isset($allowed_access[$access])) {
                $file['access'] = $access;
                $file->update();
                return true;
            } else {
                return $this->addError(t('Неверный идентификатор уровня доступа'));
            }
        } else {
            return $this->addError(t('Файл не найден'));
        }
    }
    
    /**
    * Перемещает элемент from на место элемента to. Если flag = 'up', то до элемента to, иначе после
    * 
    * @param int $from - id элемента, который переносится
    * @param int $to - id ближайшего элемента, возле которого должен располагаться элемент
    * @param string $flag - up или down - флаг выше или ниже элемента $to должен располагаться элемент $from
    * @param FilesType\AbstractType $type_class - тип связи
    * @param mixed $link_id - ID элемента связанного
    */
    function moveFileElement($from, $to, $flag, FilesType\AbstractType $type_class, $link_id)
    {
        $extra_expr = \RS\Orm\Request::make()
            ->where([
                'link_type_class' => $type_class->getShortName(),
                'link_id' => $link_id
            ]);
            
        return $this->moveElement($from, $to, $flag, $extra_expr);
    }

    /**
     * Выполняет перепривязку файлов к новому ID объекта
     *
     * @param integer $old_id ID старого объекта
     * @param integer $new_id ID нового объекта
     * @param string $link_type_class строковый идентификатор типа связи
     *
     * @return integer возвращает количество обновленных записей
     */
    public static function changeLinkId($old_id, $new_id, $link_type_class)
    {
        //Переносим файлы к сохраненному объекту
        return \RS\Orm\Request::make()
            ->update(new Orm\File())
            ->set(['link_id' => $new_id])
            ->where([
                'link_type_class' => $link_type_class,
                'link_id' => $old_id
            ])->exec()->affectedRows();
    }
    
}