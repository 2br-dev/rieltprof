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
* Этот класс задает тип масштабирования картинки. также как и у XY, только добавляет белые полосы до заданного размера.
* Absolute XY
*/
class Axy extends AbstractType
{
    public 
        $background = [255,255,255];

    public function resizeImage(File $srcImage, $dstImageFileName, $final_width, $final_height, $quality = 90)
    {
        //Исходные размеры
        $w_image = $srcImage->width;
        $h_image = $srcImage->height;        
        
        $insert_top = 0;
        $insert_left = 0;
        
        //Исходные размеры
        $k_image = $w_image / $h_image;

        if($w_image>$h_image)
        { //Если фотка горизонтальная
            $w_real = $w_image < $final_width ? $w_image : $final_width;
            $h_real = round($w_real / $k_image);
            if($h_real > $final_height )
            {
                $h_real = $final_height;
                $w_real = round($h_real * $k_image);
            }
        } else {
        //Если вертикальная    
            $h_real = $h_image < $final_height ? $h_image : $final_height;
            $w_real = round($h_real * $k_image);
            if($w_real > $final_width )
            {
                $w_real = $final_width;
                $h_real = round($w_real / $k_image);
            }
        }
        
        
        if ($w_real<$final_width) $insert_left = round( ($final_width/2) - ($w_real/2) );
        if ($h_real<$final_height) $insert_top = round( ($final_height/2) - ($h_real/2) );
        
        if ($insert_left<0) $insert_left = 0;
        if ($insert_top<0) $insert_top = 0;
        
        $newImage = new Create($final_width, $final_height, $srcImage->type);
        
        $fillcolor = imagecolorallocate($newImage->handler, 255, 255, 255);
		imagefill($newImage->handler, 0, 0, $fillcolor);
        imagecopyresampled ($newImage->handler, $srcImage->image_handler, $insert_left, $insert_top, 0, 0, $w_real, $h_real, $w_image, $h_image);
        
        $newImage->setQuality($quality)->save($dstImageFileName);
        return true;
	}
}

