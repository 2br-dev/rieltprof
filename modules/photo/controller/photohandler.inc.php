<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
/**
* Класс, отвечающий за отдачу фотографий, по адресу /storage/photo/resized/%ФОРМАТ%/ИмяКартинки.jpg
*/
namespace Photo\Controller;

class PhotoHandler extends \RS\Img\Handler\AbstractHandler
{
    protected 
        $srcFolder = '/storage/photo/original',
        $dstFolder = '/storage/photo/resized';
}
