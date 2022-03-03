<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Model\AtomApis;

use RS\Module\AbstractModel\BaseModel;

/**
 * Класс API для компонента видео
 */
class VideoApi extends BaseModel
{
    /**
     * Проверяет существует ли удалённый файл
     *
     * @param string $url - адрес для проверки
     *
     * @return bool
     */
    public static function checkRemoteUrlExists($url)
    {
        return @fopen($url, 'r') ? true : false;
    }

    /**
     * Возвращает данные по youtube видео
     *
     * @param string $video_id - id видео на youtube
     *
     * @return array
     */
    public static function getYoutubeVideoData($video_id)
    {
       $response = file_get_contents('https://www.youtube.com/oembed?url=https://www.youtube.com/watch?v='.$video_id.'&format=json');
        return @json_decode($response, true);
    }

    /**
     * Возвращает соотношение сторон для расчёта отступа
     *
     * @param string $video_id - id видео на youtube
     *
     * @return array
     */
    public static function getYoutubeVideoAspectRatio($video_id)
    {
        $data = self::getYoutubeVideoData($video_id);

        if (isset($data['width'])){
            $def = $data['width']/$data['height'];
            if ($def < 1.7){
                return "4:3";
            }
        }
        return "16:9";
    }
}