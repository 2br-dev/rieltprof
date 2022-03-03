<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Img;

use RS\Img\Exception as ImgException;

/**
 * Класс объектов - "файл изображения". Содержит сведения об одном изображении
 */
class File
{
    public $filename;
    public $width;
    public $height;
    public $type;
    public $bits;
    public $channels;
    public $mime;
    public $image_handler;

    /**
     * Конструктор.
     *
     * @param string $filename - путь к файлу с изображением
     * @return void
     * @throws Exception
     */
    function __construct($filename)
    {
        $this->filename = $filename;
        $this->load();
    }

    /**
     * Загружает информацию об изображении
     *
     * @return void
     * @throws ImgException
     */
    function load()
    {
        if (!file_exists($this->filename)) {
            throw new ImgException(t("Не найдена картинка %0", [$this->filename]), Exception::IMG_FILE_NOT_FOUND);
        }
        $image_info = getimagesize($this->filename);

        $this->width = $image_info[0];
        $this->height = $image_info[1];
        $this->type = $image_info[2];
        $this->bits = @$image_info['bits'];
        $this->mime = $image_info['mime'];
        $this->channels = @$image_info['channels'];

        $memory_limit = ini_get('memory_limit');
        if (strpos($memory_limit, 'M')) {
            $memory_limit = (int)$memory_limit * 1024 * 1024;
        } elseif (strpos($memory_limit, 'K')) {
            $memory_limit = (int)$memory_limit * 1024;
        } elseif (strpos($memory_limit, 'G')) {
            $memory_limit = (int)$memory_limit * 1024 * 1024 * 1024;
        }
        if ($memory_limit != '-1' && ($this->width * $this->height * 10 > $memory_limit - memory_get_usage())) {
            throw new ImgException(t('Слишком большой размер изображения'));
        }

        $this->getImageHandler();
    }

    /**
     * Возвращает указать на ресурс изображения
     * @return void
     * @throws ImgException
     */
    function getImageHandler()
    {
        switch ($this->mime) {
            case "image/jpeg":
                $this->image_handler = imagecreatefromjpeg($this->filename);
                break;
            case "image/gif":
                $this->image_handler = imagecreatefromgif($this->filename);
                break;
            case "image/png":
                $this->image_handler = imagecreatefrompng($this->filename);
                break;
            case "image/webp":
                if (function_exists('imagecreatefromwebp')) {
                    $this->image_handler = imagecreatefromwebp($this->filename);
                } else {
                    throw new ImgException(t('PHP собран без поддержки формата WebP'));
                }
                break;
        }
    }

    /**
     * Уничтожает указатель на ресурс изображения
     * @return void
     */
    function __destruct()
    {
        imagedestroy($this->image_handler);
    }
}
