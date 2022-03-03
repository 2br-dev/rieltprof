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
namespace Main\Controller\Front;

class PhotoHandler extends \RS\Img\Handler\AbstractHandler
{
    protected 
        $srcFolder = '/storage/system/original',
        $dstFolder = '/storage/system/resized';
}
