<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Img\Type;

use RS\Img\File;

/**
* Абстрактный класс для Ресайзеров изображений
*/
abstract class AbstractType {
    
    abstract public function resizeImage(File $srcImage, $dstImageFileName, $width, $height, $quality = 90);

}

