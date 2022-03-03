<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Img;

use RS\Config\Loader;

/**
* Класс отвечает за создание и сохранение нового изображения
*/
class Create
{
    public
        $handler,
        $quality = 98,
        $width,
        $height,
        $type;
    
    /**
    * Конструктор. Создает новое изобажение
    * 
    * @param integer $width ширина изображения
    * @param integer $height высота изображения
    * @param integer $type тип изображения - см. константы IMAGETYPE_...
    * @param File $image - готовый объект изображения. Если задан, то все предидущие параметры будут извлечены из него.
    * @return NewFile
    */
    function __construct($width, $height, $type, File $image = null)
    {
        if ($image) {
            $this->width = $image->width;
            $this->height = $image->height;
            $this->type = $image->type;
            $this->handler = $image->image_handler;
            return;
        }
        
        $this->width = $width;
        $this->height = $height;
        $this->type = $type;
        $this->handler = imagecreatetruecolor ($this->width, $this->height);
        
        if ($this->type == IMAGETYPE_PNG || $this->type == IMAGETYPE_GIF || $this->type == IMAGETYPE_WEBP) {
            imagealphablending($this->handler, false);
            imagesavealpha($this->handler,true);
            $transparent = imagecolorallocatealpha($this->handler, 255, 255, 255, 127);
            imagefilledrectangle($this->handler, 0, 0, $this->width, $this->height, $transparent);
            if ($this->type == IMAGETYPE_GIF) {
                imagecolortransparent($this->handler, $transparent);
            }            
        }
    }
    
    /**
    * Устанавливает качество изображения
    * 
    * @param mixed $quality
    * @return Create
    */
    function setQuality($quality)
    {
        $this->quality = $quality;
        return $this;
    }
    
    /**
    * Сохраняет изображение в соответствии с заданным типом
    * 
    * @param string $filename - имя файла для сохранения
    * @param bool $destroy - если true, то ссылка на изображение будет разрушена после сохранения
    */
    function save($filename, $destroy = true)
    {
        $config = Loader::byModule('main');
        $type = $this->type;

        if (preg_match('/\.webp$/i', $filename)) {
            $type = IMAGETYPE_WEBP;
        }

        switch($type) {
            case IMAGETYPE_GIF: $result = imagegif($this->handler, $filename); break;
            case IMAGETYPE_PNG: $result = imagepng($this->handler, $filename); break;
            case IMAGETYPE_WEBP: $result = imagewebp($this->handler, $filename, $this->quality); break;
            default: {
                $result = imagejpeg($this->handler, $filename, $this->quality);
            } break;
        }

        if ($destroy) {
            $this->destroy();
        }
            
        return $result;
    }
    
    function destroy()
    {
        imagedestroy($this->handler);
    }
}