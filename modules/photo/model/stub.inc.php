<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Photo\Model;

/**
* Заглушка для фотографий.
* как и объект image имеет возможность возвращать заглушку в нужном размере
*/
class Stub extends \Photo\Model\Orm\Image
{
    protected
        $render_path = '/storage/photo/stub/',
        $image_path = NOPHOTO_IMAGE,
        $theme_image_path = NOPHOTO_THEME_PATH,
        $theme_image_file = NOPHOTO_THEME_FILE; 
    
    function setDefaultImage($pathToImage)
    {
        $this->image_path = $pathToImage;
    }
    
    function setImageFilename($filename)
    {
        $this->theme_image_file = $filename;
    }
    
    /**
    * Возвращает путь к заглушке, создает ее, если не существует.
    * 
    * @param integer $width - Ширина изображения
    * @param integer $height - Высота изображения
    * @param mixed $type - не используется.
    * @param bool $absolute - если true, то будет возвращен абсолютный URL
    * @param bool $force_create - если true, то изображение будет гарантировано создано на диске в
    * момент вызова этой функции, если такого изображения еще нет.
    */
    function getUrl($width, $height, $type = null, $absolute = false, $force_create = false)
    {
        $theme = \RS\Theme\Manager::getCurrentTheme('theme');
        $cimg = new \RS\Img\Core(\Setup::$ROOT, '', \Setup::$FOLDER.'/storage/photo/stub/resized');
        $url = $cimg->getImageUrl($this->theme_image_file, $width, $height, $theme, $absolute);
        
        return $url;
    }
    
    /**
    * Возвращает URL оригинала заглушки
    * 
    * @param boolean $absolute - флаг отвечает за, то какую ссылку отображать абсолютную или относительную
    * 
    * @return string
    */
    function getOriginalUrl($absolute = false)
    {
        $theme = \RS\Theme\Manager::getCurrentTheme('theme');
        $theme_full_path = \Setup::$SM_RELATIVE_TEMPLATE_PATH.'/'.$theme.\Setup::$IMG_PATH.$this->theme_image_path;
        
        if (file_exists(\Setup::$PATH.$theme_full_path.'/'.$this->theme_image_file)) {
            $url = \Setup::$PATH.$theme_full_path.'/'.$this->theme_image_file;
        } else {
            $url = $this->image_path;
        }
        return $absolute ? \RS\Site\Manager::getSite()->getAbsoluteUrl($url) : $url;
    }
    
    /**
    * Возвращает объект хранилища
    * 
    * @return \RS\Orm\Storage\AbstractStorage
    */
    protected function getStorageInstance()
    {
        return new \RS\Orm\Storage\Stub($this);
    }
}
