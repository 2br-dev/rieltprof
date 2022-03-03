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
 * Запрошенное изображение ищется вверху исходного только по горизонтали.
 * Итоговое изображение может быть другого размера, если пропорции исходного изображения не совпадут
 *
 * Crop Top Y
*/
class Cty extends AbstractType
{
    public 
        $background = [255,255,255];
        
    
    public function resizeImage(File $srcImage, $dstImageFileName, $final_width, $final_height, $quality = 90)
    {
        //Исходные размеры
        $w_image = $srcImage->width;
        $h_image = $srcImage->height;
        if ($w_image == 0) return false;

        $src_ratio = $h_image / $w_image;
        $dst_ratio = $final_height / $final_width;

        $src_height = $w_image * $dst_ratio;
        if ($src_height > $h_image) {
            $src_height = $h_image;
            $new_src_ratio = $src_height / $w_image;
            $final_height = $final_width * $new_src_ratio;
        }

        $newImage = new Create($final_width, $final_height, $srcImage->type);
        imagecopyresampled ($newImage->handler, $srcImage->image_handler, 0, 0, 0, 0, $final_width, $final_height, $w_image, $src_height);
        $newImage->setQuality($quality)->save($dstImageFileName);
        return true;
    }
    
}
