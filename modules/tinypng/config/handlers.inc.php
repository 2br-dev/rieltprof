<?php
namespace TinyPNG\Config;
use \RS\Router;

class Handlers extends \RS\Event\HandlerAbstract
{
    function init()
    {
        $this
            ->bind('cron')
            ->bind('img.core.afterbuild');
    }
    
    /**
    * Добавляет задание в планировщик задач (cron)
    * 
    * @param array $params - массив параметров текущего времени.
    */
    public static function cron($params)
    {
         $api = new \TinyPNG\Model\Api();
         $api->compressNextImagesPortion();
    }
    
    /**
    * Расширяет объект фотографии
    * 
    * @param \Photo\Model\Orm\Image $image - объект картинки
    */
    public static function imgCoreAfterBuild($data)
    {
        $file  = $data['file']; 
        $image = new \TinyPNG\Model\Orm\Image();
        $image['file'] = \Setup::$ROOT.\Setup::$FOLDER.$file;
        $image->insert();
    }
}