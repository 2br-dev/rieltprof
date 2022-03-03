<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Banners\Model;

/**
 * Класс содержит API функции дополтельные для работы в системе в рамках задач по модулю баннеров
 */
class ApiUtils
{
    /**
    * Подготавливает секцию с картинками
    * 
    * @param mixed $image_orm - объект картинки
    */
    static function prepareImagesSection($image_orm)
    {
        $data = [
            'original_url' => $image_orm->getOriginalUrl(true),
            'big_url' => $image_orm->getBannerUrl(1000, 1000, 'xy', true),
            'middle_url' => $image_orm->getBannerUrl(600, 600, 'xy', true),
            'small_url' => $image_orm->getBannerUrl(300, 300, 'xy', true),
            'micro_url' => $image_orm->getBannerUrl(100, 100, 'xy', true),
            'nano_url' => $image_orm->getBannerUrl(50, 50, 'xy', true),
        ];
        return $data;
    }
}