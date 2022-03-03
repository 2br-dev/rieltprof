<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Model;

use RS\Module\AbstractModel\BaseModel;

/**
 * Класс отвечает за работу с загрузкой файлов блоков дизайнера
 */
class UploadApi extends BaseModel
{

    const FILES_DIR  = "/designer/files/";
    const IMAGES_DIR = "/designer/images/";
    const ORIGINAL_IMAGES_DIR = "/designer/originals/";
    const FILE_TYPE_FILE  = 'file';
    const FILE_TYPE_IMAGE = 'image';
    const LIMIT = 32; //Количество загружаемых за раз картинок

    protected $files_dir;   //Директория с загруженными файлами
    protected $images_dir;  //Директория с загруженными картинками
    protected $original_image_storage_folder; //Папка с оригиналами для атома картинки

    /**
     * ComponentApi constructor.
     */
    function __construct()
    {
        $this->files_dir  = \Setup::$ROOT.\Setup::$FOLDER.\Setup::$STORAGE_DIR.self::FILES_DIR;
        $this->images_dir = \Setup::$ROOT.\Setup::$FOLDER.\Setup::$STORAGE_DIR.self::IMAGES_DIR;
        $this->original_image_storage_folder = \Setup::$ROOT.\Setup::$FOLDER.\Setup::$STORAGE_DIR.self::ORIGINAL_IMAGES_DIR;
    }

    /**
     * Возвращает конечное имя файла сохранения
     *
     * @param string $file - название файла, который будет сохранен
     * @param string $type - тип файла (file|image)
     *
     * @return string
     */
    private function getFileDestinationToSaveFile($file, $type)
    {
        $file_folder = ($type == self::FILE_TYPE_FILE) ? $this->files_dir : $this->images_dir;//Папка для хранения файлов
        $new_file    = $file_folder.$file;

        if (file_exists($new_file)){ //Если файл присутствует, то подставим ему другое имя
            $name = pathinfo($new_file, PATHINFO_FILENAME);
            $data = explode("_", $name);
            $num  = 1;
            if (count($data) > 1){
                $end = intval(array_reverse($data)[0]);
                $num = $end + 1;
            }
            //Подготовим новое имя
            $ext  = pathinfo($new_file, PATHINFO_EXTENSION);

            $new_file = $data[0]."_".$num.".".$ext;
            return $this->getFileDestinationToSaveFile($new_file, $type);
        }
        return $new_file;
    }


    /**
     * Возвращает абсолютный путь к файлу на сервере
     *
     * @param string $file - полный путь к файлу на сервере
     * @return string
     * @throws \RS\Db\Exception
     * @throws \RS\Orm\Exception
     */
    function getAbsoluteFileName($file)
    {
        return \RS\Site\Manager::getSite()->getAbsoluteUrl($this->getRelativeFileName($file));
    }

    /**
     * Возвращает относительный путь к файлу вырезая путь не сервере
     *
     * @param string $file - полный путь к файлу на сервере
     * @return string
     */
    function getRelativeFileName($file)
    {
        return str_replace(\Setup::$ROOT.\Setup::$FOLDER, "", $file);
    }

    /**
     * Загружает файл и возвращает путь на сервере к файлу
     *
     * @param array $file - массив загружаемого файла из $_FILES
     * @param string $type - тип файла (file|image)
     *
     * @return false|string
     */
    function uploadFile($file, $type = 'file')
    {
        if ($error = \RS\File\Tools::checkUploadError($file['error'])) {
            $this->addError("{$file['name']}: ".$error);
        }
        $uploaded_file = $this->getFileDestinationToSaveFile($file['name'], $type);

        \RS\File\Tools::makePath($uploaded_file, true); //Создадим директорию, если нет

        if (@copy($file['tmp_name'], $uploaded_file)){
            return $this->getAbsoluteFileName($uploaded_file);
        }
        $this->addError(t('Не удалось загрузить файл'));
        return false;
    }

    /**
     * Загружает картинку для компонента
     *
     * @param array $file - массив загружаемого файла из $_FILES
     *
     * @return bool|\Photo\Model\Orm\Image
     */
    function uploadImage($file)
    {
        //Проверим картинка ли
        if (!in_array($file['type'], ['image/jpeg', 'image/jpg', 'image/pjpeg', 'image/png', 'image/gif'])){
            $this->addError(t('Принимаются только файлы изображений'));
            return false;
        }
        return $this->uploadFile($file, self::FILE_TYPE_IMAGE);
    }


    /**
     * Возвращает массив на подобии $_FILES, для дальнейшей обработки на загрузки, нормализуя вид
     *
     * @param string $url - адрес картинки
     * @return array
     */
    public static function normalizeFileByUrl($url)
    {
        return [
            'name' => basename($url),
            'type' => self::get_image_mime_type($url),
            'tmp_name' => $url,
            'error' => 0,
        ];
    }

    /**
     * Возвращает mime тип картинки
     *
     * @param $image_path
     * @return bool|mixed
     */
    public static function get_image_mime_type($image_path)
    {
        $mimes  = [
            IMAGETYPE_GIF     => "image/gif",
            IMAGETYPE_JPEG    => "image/jpg",
            IMAGETYPE_PNG     => "image/png",
            IMAGETYPE_SWF     => "image/swf",
            IMAGETYPE_PSD     => "image/psd",
            IMAGETYPE_BMP     => "image/bmp",
            IMAGETYPE_TIFF_II => "image/tiff",
            IMAGETYPE_TIFF_MM => "image/tiff",
            IMAGETYPE_JPC     => "image/jpc",
            IMAGETYPE_JP2     => "image/jp2",
            IMAGETYPE_JPX     => "image/jpx",
            IMAGETYPE_JB2     => "image/jb2",
            IMAGETYPE_SWC     => "image/swc",
            IMAGETYPE_IFF     => "image/iff",
            IMAGETYPE_WBMP    => "image/wbmp",
            IMAGETYPE_XBM     => "image/xbm",
            IMAGETYPE_ICO     => "image/ico"];

        if (($image_type = exif_imagetype($image_path)) && (array_key_exists($image_type ,$mimes))) {
            return $mimes[$image_type];
        }
        return false;
    }


    /**
     * Загружает картинку для компонента через переданный URL
     *
     * @param string $url - путь к картинке
     *
     * @return bool|\Photo\Model\Orm\Image
     */
    function uploadImageByURL($url)
    {
        return $this->uploadImage(self::normalizeFileByUrl($url));
    }

    /**
     * Возвращает массив SVG картинок
     *
     */
    public static function getSVGImages()
    {
        static $images;
        if (!$images){
            foreach (glob(\Setup::$ROOT.\Setup::$MODULE_FOLDER."/designer".\Setup::$MODULE_TPL_FOLDER."/img/iconsset/*", GLOB_ONLYDIR) as $folder){
                $category = basename($folder);
                foreach (glob($folder."/*") as $file){
                    $title = str_replace([".svg", "_"], ["", " "], basename($file));
                    $src   = "/modules/designer/view/img/iconsset/".str_replace(\Setup::$ROOT.\Setup::$MODULE_FOLDER."/designer".\Setup::$MODULE_TPL_FOLDER."/img/iconsset/", "", $file);
                    $images[] = [
                        'category' => $category,
                        'title' => $title,
                        'src' => $src,
                    ];
                }
            }
        }

        return self::prepareSVGImages($images);
    }

    /**
     * Подготавливает SVG картинки для выбора
     *
     * @param array $images - массив картинок по категория
     * @return array
     */
    public static function prepareSVGImages($images)
    {
        $site = \RS\Site\Manager::getSite();
        foreach ($images as &$image){
            $image['src'] = $site->getAbsoluteUrl($image['src']);
        }
        return $images;
    }


    /**
     * Возвращает массив картинок загруженных в папке хранилища
     *
     * @param integer $page - текущая страница
     * @return array
     */
    function getListFromImagesFolder($page = 1)
    {
        $offset = ($page-1) * self::LIMIT; //Откуда начинать читать
        $m=0;
        $list = [];
        $files = glob($this->images_dir."*.*");
        foreach($files as $image){
            $m++;
            if ($offset > 0 && $m < $offset) continue; //Если не в промежутке
            if ($m > ($offset + self::LIMIT)) break; //Если промежуток привысили

            list($width, $height, $type, $attr) = getimagesize($image);

            $data = [
                'url'    => $this->getRelativeFileName($image),
                'title' => basename($image),
                'width'  => $width,
                'height' => $height,
            ];

            $list[] = $data;
        }

        return [
            'list' => $list,
            'total' => count($files)
        ];
    }

    /**
     * Возвращает массив картинок загруженных в папке хранилища
     *
     * @param integer $page - текущая страница
     * @return array
     */
    function getListFromFilesFolder($page = 1)
    {
        $offset = ($page-1) * self::LIMIT; //Откуда начинать читать
        $m=0;
        $list = [];
        $files = glob($this->files_dir."*.*");
        foreach($files as $file){
            $m++;
            if ($offset > 0 && $m < $offset) continue; //Если не в промежутке
            if ($m > ($offset + self::LIMIT)) break; //Если промежуток привысили

            $data = [
                'url' => $this->getRelativeFileName($file),
                'title' => basename($file)
            ];

            if (in_array(mb_strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg','gif','webp','bmp', 'png'])){ //Если это картинка, то вернем к ней размера
                list($width, $height, $type, $attr) = getimagesize($file);

                $data['width']  = $width;
                $data['height'] = $height;
            }

            $list[] = $data;
        }

        return [
            'list' => $list,
            'total' => count($files)
        ];
    }

    /**
     * Удаляет картинку
     *
     * @param static $file - имя файла
     * @return bool
     */
    function deleteImage($file)
    {
        $path = $this->images_dir.$file;
        if (!file_exists($path)){
            $this->addError(t('Картинка %0 не найден', $file));
            return false;
        }
        @unlink($path);
        return true;
    }

    /**
     * Удаляет файл
     *
     * @param static $file - имя файла
     * @return bool
     */
    function deleteFile($file)
    {
        $path = $this->files_dir.$file;
        if (!file_exists($path)){
            $this->addError(t('Файл %0 не найден', $file));
            return false;
        }
        @unlink($path);
        return true;
    }
}