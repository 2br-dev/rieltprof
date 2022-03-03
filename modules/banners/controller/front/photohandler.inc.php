<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
/**
* Класс, отвечающий за отдачу фотографий, по адресу /storage/system/resized/%ФОРМАТ%/ИмяКартинки.jpg
*/
namespace Banners\Controller\Front;

class PhotoHandler extends \RS\Img\Handler\AbstractHandler
{
    protected 
        $srcFolder = '/storage/banners/original',
        $dstFolder = '/storage/banners/resized';

    function exec()
    {
        $this->parseParameters();
        try {
            $img = new \RS\Img\Core(\Setup::$ROOT, \Setup::$FOLDER.$this->srcFolder, \Setup::$FOLDER.$this->dstFolder);
            $img->disableWatermark();
            $img->toOutput($this->pic_id, $this->width, $this->height, $this->scale, $this->hash);
        } catch (\RS\Img\Exception $e) {
            throw new \RS\Controller\ExceptionPageNotFound($e->getMessage(), get_class($this));
        }
        
        return true;        
    }
}

