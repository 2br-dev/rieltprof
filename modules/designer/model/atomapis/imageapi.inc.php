<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Model\AtomApis;

use Designer\Model\BlocksApi;
use Designer\Model\UploadApi;

/**
 * Класс API для компонента картинки
 */
class ImageApi extends UploadApi
{
    public static $inst = null;

    /**
     * Возвращает текущий объект Апи
     *
     * @return ImageApi|null
     */
    static public function getInstance()
    {
        if (self::$inst == null) {
            self::$inst = new self();
        }
        return self::$inst;
    }

    /**
     * Возвращает имя файла катинки по id блока и атома.
     *
     * @param string $block_id - id блока
     * @param string $atom_id - id атома
     * @param string $extension - расширение файла
     *
     * @return string
     */
    private function getImageFileNameByBlockAndAtom($block_id, $atom_id, $extension = null)
    {
        $blockApi = new BlocksApi();
        $info     = $blockApi->getAtomInfo($block_id, $atom_id);

        if (!$extension && !empty($info['attrs']['original']['value'])){
            $extension = $this->getImageExtension($info['attrs']['original']['value']);
        }
        if (empty($extension)){
            $extension = 'png';
        }
        return $block_id."_".$atom_id.".".$extension;
    }

    /**
     * Копирует данные по картинке в нужную папку в зависимости от типа
     *
     * @param string $block_id - id блока
     * @param string $atom_id - id атома
     * @param string $image - путь к картинке
     * @param string $type - тип картинки src или original
     *
     * @return string
     */
    function copyImagesFromPreset($block_id, $atom_id, $image, $type)
    {
        $ext = pathinfo($image, PATHINFO_EXTENSION);
        $default_file = $this->getImageFileNameByBlockAndAtom($block_id, $atom_id, $ext);

        if ($type == 'src'){ //Обычное фото
            $file_name = $this->images_dir.$default_file;
        }else{ //Оригинал
            $file_name = $this->original_image_storage_folder.$default_file;
        }

        copy(\Setup::$ROOT.$image, $file_name);
        return $this->getRelativeFileName($file_name);
    }


    /**
     * Создавает изображения по умолчанию для атома картинки по id блока и атома. Возвращает ссылку на оригинал и ссылку на копию для модификации
     *
     * @param string $block_id - id блока
     * @param string $atom_id - id атома
     *
     * @return array
     */
    function createDefaultAtomImage($block_id, $atom_id)
    {
        $default_atom_image_file  = \Setup::$ROOT.\Setup::$MODULE_FOLDER.'/designer'.\Setup::$MODULE_TPL_FOLDER.'/img/defaultdata/default_image.png';

        \RS\File\Tools::makePath($this->images_dir);
        \RS\File\Tools::makePath($this->original_image_storage_folder);

        $default_file = $this->getImageFileNameByBlockAndAtom($block_id, $atom_id);

        copy($default_atom_image_file, $this->original_image_storage_folder.$default_file);
        copy($default_atom_image_file, $this->images_dir.$default_file);

        return [
            'original' => $this->getRelativeFileName($this->original_image_storage_folder.$default_file),
            'copy' => $this->getRelativeFileName($this->images_dir.$default_file)
        ];
    }

    /**
     * Возвращает настоящее расширение файла используя MIME тип из файла. Например png, jpg,
     *
     * @param string $image_path - путь к картинку
     *
     * @return string
     */
    private function getImageExtension($image_path)
    {
        if ((mb_stripos($image_path, 'http://') === false) && (mb_stripos($image_path, 'https://') === false)){
            $image_path = \Setup::$ROOT.$image_path;
        }
        $path = explode(".", $image_path);
        return mb_strtolower(array_pop($path));
    }

    /**
     * Возвращает настоящее расширение файла используя MIME тип из файла. Например png, jpg,
     *
     * @param string $image_path - путь к картинку
     *
     * @return string
     */
    private function getMimeImageType($image_path)
    {
        $image_path = str_replace(\Setup::$ROOT, "", $image_path);
        if ((mb_stripos($image_path, 'http://') === false) && (mb_stripos($image_path, 'https://') === false)){
            $image_path = \Setup::$ROOT.$image_path;
        }
        $mime_type = @mime_content_type($image_path);
        return explode("/", $mime_type)[1];
    }

    /**
     * Создавает изображения по умолчанию для атома картинки по id блока и атома. Возвращает ссылку на оригинал и ссылку на копию для модификации
     *
     * @param string $block_id - id блока
     * @param string $atom_id - id атома
     * @param string $image_url - url картинки
     *
     * @return array
     */
    function saveAtomUploadedImageByUrl($block_id, $atom_id, $image_url)
    {
        \RS\File\Tools::makePath($this->images_dir);
        \RS\File\Tools::makePath($this->original_image_storage_folder);

        $ext = $this->getImageExtension($image_url);
        if ($ext == 'jpeg'){
            $ext = 'jpg';
        }

        $default_file = $block_id."_".$atom_id.".".$ext;

        if (!(mb_stripos($image_url, 'http://') !== false) && !(mb_stripos($image_url, 'https://') !== false)){
            $image_url = \Setup::$ROOT.$image_url;
        }

        copy($image_url, $this->original_image_storage_folder.$default_file);
        copy($image_url, $this->images_dir.$default_file);

        return [
            'original' => $this->getRelativeFileName($this->original_image_storage_folder.$default_file),
            'copy' => $this->getRelativeFileName($this->images_dir.$default_file)
        ];
    }



    /**
     * Обрезает оригинал фото от верхнего центра до нужных размеров
     *
     * @param string $source_image_path - путь к оригиналу
     * @param string $dest_image_path - путь к картинке копии
     * @param integer $new_width - новая ширина
     * @param integer $new_height - новая высота
     */
    function cropImageFromTopCenter($source_image_path, $dest_image_path, $new_width, $new_height)
    {
        $ext = $this->getMimeImageType($source_image_path);

        $create_function = 'imagecreatefrom'.$ext;

        if (!function_exists($create_function)){
            $this->addError(t('Нет функции для обрезки фото %0', [$ext]));
            return;
        }

        list ($original_width, $original_height) = getimagesize($source_image_path);
        $im  = $create_function($source_image_path);
        $x = ceil($original_width / 2) - ceil($new_width / 2);
        if ($x < 0){
            $x = 0;
        }

        if ($new_width > $original_width){ //Если больше чем оригинал, то возмём тот же размер
            $new_width = $original_width;
        }

        $im2 = imagecrop($im, ['x' => $x, 'y' => 0, 'width' => $new_width, 'height' => $new_height]);

        $this->saveResizedPhoto($im, $im2, $ext, $dest_image_path);
    }


    /**
     * Изменяет размер картинки в зависимости от переданного коэфициента изменения
     *
     * @param string $block_id - id блока
     * @param string $atom_id - id атома
     * @param float $ratio - коэфициент изменения
     *
     * @return array
     */
    function changeImageForAtomImageByRatio($block_id, $atom_id, $ratio)
    {
        $default_file = $this->getImageFileNameByBlockAndAtom($block_id, $atom_id);
        $ext = $this->getImageExtension($default_file);

        $image = $this->images_dir.$default_file;
        if ($ext != 'svg'){ //SVG нельзя обрезать
            $original = $this->original_image_storage_folder.$default_file;

            list ($original_width, $original_height) = getimagesize($original);

            //Изменим ширину как нам надо
            $new_width  = ceil($original_height * $ratio);
            $new_height = $original_height;

            $this->cropImageFromTopCenter($original, $image, $new_width, $new_height);
        }

        return [
            'copy' => $this->getRelativeFileName($image)
        ];
    }

    /**
     * Обрезает оригинал фото от центра центра до нужных размеров
     *
     * @param string $source_image_path - путь к оригиналу
     * @param string $dest_image_path - путь к картинке копии
     * @param integer $new_width - новая ширина
     * @param integer $new_height - новая высота
     */
    function cropImageFromCenterCenter($source_image_path, $dest_image_path, $new_width, $new_height)
    {
        $ext = $this->getMimeImageType($source_image_path);
        $create_function = 'imagecreatefrom'.$ext;

        if (!function_exists($create_function)){
            $this->addError(t('Нет функции для создания фото для расширения %0', [$ext]));
            return;
        }

        list ($original_width, $original_height) = getimagesize($source_image_path);
        $im  = $create_function($source_image_path);

        $y = ceil($original_height / 2) - ceil($new_height / 2);
        if ($y < 0){
            $y = 0;
        }

        $x = 0;
        if (!$new_width){
            $new_width = $original_width;
        }else{
            $x = ceil($original_width / 2) - ceil($new_width / 2);
        }

        if ($new_height > $original_height){ //Если больше чем оригинал, то возмём тот же размер
            $new_height = $original_height;
        }

        $im2 = imagecrop($im, ['x' => $x, 'y' => $y, 'width' => $new_width, 'height' => $new_height]);

        $this->saveResizedPhoto($im, $im2, $ext, $dest_image_path);
    }

    /**
     * Уменьшает оригинал фото по назначенной ширине
     *
     * @param string $source_image_path - путь к оригиналу
     * @param string $dest_image_path - путь к картинке копии
     * @param integer $new_width - новая ширина
     */
    function resizeImageByWidth($source_image_path, $dest_image_path, $new_width)
    {
        $ext = $this->getMimeImageType($source_image_path);

        $create_function = 'imagecreatefrom'.$ext;

        if (!function_exists($create_function)){
            $this->addError(t('Нет функции для обрезки фото '.$ext));
            return;
        }

        $im = $create_function($source_image_path);

        list ($width, $height) = getimagesize($source_image_path);
        if ($ext == 'png' || $ext == 'gif' || $ext == 'webp') {
            $im = imagecreatetruecolor($width, $height);
            imagealphablending($im, false);
            imagesavealpha($im,true);
            $transparent = imagecolorallocatealpha($im, 255, 255, 255, 127);
            imagefilledrectangle($im, 0, 0, $width, $height, $transparent);
            if ($ext == 'gif') {
                imagecolortransparent($im, $transparent);
            }
            $im2 = $create_function($source_image_path);
            imagealphablending($im2, false);
            imagesavealpha($im2, true);
            imagecopyresampled( $im, $im2,
                0, 0,
                0, 0,
                $width, $height,
                $width, $height);
        }

        $im2 = imagescale($im, $new_width);

        $this->saveResizedPhoto($im, $im2, $ext, $dest_image_path);
    }

    /**
     * Сохраняет измененное фото на диск
     *
     * @param resource $im - ссылка на ресурс фото оригинала
     * @param resource $im2 - ссылка на ресурс фото изменённого
     * @param string $ext - тип изображения
     * @param string $dest_image_path - путь до конечной картинки
     */
    function saveResizedPhoto($im, $im2, $ext, $dest_image_path)
    {
        if ($im2 !== false) {
            imagealphablending($im2, false);
            imagesavealpha($im2, true);
            $desc_function = 'image'.$ext;
            $desc_function($im2, $dest_image_path);
            imagedestroy($im2);
        }else{
            $this->addError(t('Не удалось создать изображение'));
        }
        imagedestroy($im);
    }

    /**
     * Изменяет размер картинки в зависимости от переданного коэфициента изменения
     *
     * @param string $block_id - id блока
     * @param string $atom_id - id атома
     * @param float $new_width - новая ширина картинки
     * @param float $new_height - новая высота картинки
     *
     * @return array
     */
    function changeImageForAtomImageByHeightAndWidth($block_id, $atom_id, $new_width, $new_height)
    {
        $default_file = $this->getImageFileNameByBlockAndAtom($block_id, $atom_id);
        $ext = $this->getImageExtension($default_file);

        $image = $this->images_dir.$default_file;

        if ($ext != 'svg'){ //SVG не обрезать
            $original = $this->original_image_storage_folder.$default_file;

            list ($original_width, $original_height) = getimagesize($original);

            if (!$new_width){
                $new_width = $original_width;
            }

            $this->resizeImageByWidth($original, $image, $new_width);
            $this->cropImageFromCenterCenter($image, $image, $new_width, $new_height);
        }
        return [
            'copy' => $this->getRelativeFileName($image)
        ];
    }

    /**
     * Сохраняет картинку образанную из переданных данных
     *
     * @param string $block_id - id блока
     * @param string $atom_id - id атома
     * @param string $image_data - данные в base64
     */
    function saveAtomCroppedImage($block_id, $atom_id, $image_data)
    {
        $default_file = $this->getImageFileNameByBlockAndAtom($block_id, $atom_id);

        $original = $this->original_image_storage_folder.$default_file;
        $image = $this->images_dir.$default_file;

        $image_parts  = explode(";base64,", $image_data);
        $image_base64 = base64_decode($image_parts[1]);
        file_put_contents($image, $image_base64);
        file_put_contents($original, $image_base64);
    }
}