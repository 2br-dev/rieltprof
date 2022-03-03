<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Img\Type;

use RS\Img\Create;
use RS\Img\File;

/**
* Этот класс задает тип масштабирования картинки. 
* Картинка масштабируется с учетом её исходной пропорции.
* Итоговое изображение не будет превышать запрошенное изображение по обоим осям, 
* однако по одной оси изображение может быть меньше запрошенного размера, согласно пропорции.
*/
class Xy extends AbstractType
{
    
    public function resizeImage(File $srcImage, $dstImageFileName, $final_width, $final_height, $quality = 90)
    {
        //Исходные размеры
        $w_image = $srcImage->width;
        $h_image = $srcImage->height;
        if ($h_image == 0) return false;

        $k_image = $w_image / $h_image;

        if($w_image>$w_image)
        { //Если фото горизонтальное
            $w_real = $w_image < $final_width ? $w_image : $final_width;
            $h_real = round($w_real / $k_image);
            if($h_real > $final_height )
            {
                $h_real = $final_height;
                $w_real = round($h_real * $k_image);
            }
        } else {
            //Если фото вертикальное
            $h_real = $h_image < $final_height ? $h_image : $final_height;
            $w_real = round($h_real * $k_image);
            if($w_real > $final_width )
            {
                $w_real = $final_width;
                $h_real = round($w_real / $k_image);
            }
        }
        
        $newImage = new Create($w_real, $h_real, $srcImage->type);
        imagecopyresampled ($newImage->handler, $srcImage->image_handler, 0, 0, 0, 0, $w_real, $h_real, $w_image, $h_image);
        $newImage->setQuality($quality)->save($dstImageFileName);
        return true;
    }
    
}

