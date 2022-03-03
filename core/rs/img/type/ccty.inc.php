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
* Запрошенное изображение ищется в центре исходного только по горизонтали. 
* Итоговое изображение всегда будет запрошенного размера.
* Подходит для обрезки фотографий, благодаря тому, что выбирается всгда верхняя часть изображения
* 
* Crop Center Top Y
*/
class Ccty extends AbstractType
{
    public 
        $background = [255,255,255];
        
    
    public function resizeImage(File $srcImage, $dstImageFileName, $final_width, $final_height, $quality = 90)
    {
        //Исходные размеры
        $w_image = $srcImage->width;
        $h_image = $srcImage->height;
        if ($h_image == 0) return false;
        
        $k_final = $final_width/$final_height; //Пропорция финального изображения
        
        $possible_w = $h_image * $k_final; //Высчитываем ширину согласно пропорции запрошенного изображения 
        
        if ($possible_w <= $w_image) {
            //Центрировать будем по горизонтали
            $calc_w = $h_image * $k_final;
            $calc_h = $h_image;
            $offset_x = round($w_image/2 - $calc_w/2);
            $offset_y = 0;            
            
        } else {
            //Центрировать будем по вертикали
            $calc_w = $w_image;
            $calc_h = $w_image / $k_final;
            $offset_y = 0; //Вертикальное смещение всегда - 0
            $offset_x = 0;
        }
        
        $proxy_image = new Create($final_width, $final_height, $srcImage->type);
        imagecopyresampled ($proxy_image->handler, $srcImage->image_handler, 0, 0, $offset_x, $offset_y, $final_width, $final_height, $calc_w, $calc_h);
        $proxy_image->setQuality($quality)->save($dstImageFileName, false);

        return true;
    }
    
}
