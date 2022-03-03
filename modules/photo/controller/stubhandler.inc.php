<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
/**
* Класс, отвечающий за отдачу фотографий, по адресу /storage/photo/stub/resized/%ФОРМАТ%/ИмяКартинки_подпись.jpg
* Данный URL задан в \Photo\Config\Handlers
*/
namespace Photo\Controller;

class StubHandler extends \RS\Img\Handler\AbstractHandler
{
    protected 
        $pic_id_orig,
        
        $srcFolder = '',
        $dstFolder = '/storage/photo/stub/resized';
        
    function parseParameters()
    {
        parent::parseParameters();

        //Определяем папку исходников
        $config = \RS\Config\Loader::getSiteConfig();
        $theme_full_path = 
            \Setup::$SM_RELATIVE_TEMPLATE_PATH.'/'.$this->scale.\Setup::$IMG_PATH.\Setup::$NOPHOTO_THEME_PATH;

        if (file_exists(\Setup::$PATH.$theme_full_path.'/'.$this->pic_id)) {
            $this->srcFolder = $theme_full_path;
        } else {
            $this->srcFolder = dirname(\Setup::$NOPHOTO_IMAGE);
        }
    }        
    
    
    function exec()
    {
        $this->parseParameters();
        $img = new \RS\Img\Core(\Setup::$ROOT, \Setup::$FOLDER.$this->srcFolder, \Setup::$FOLDER.$this->dstFolder);

         if (!$img->checkOpenKey($this->pic_id, $this->width, $this->height, $this->scale, $this->hash)) {
            throw new \RS\Img\Exception(t('Неверная подпись ссылки'));
        }
        
        $img->setQuality(97);
        $url = $img->buildImage($this->pic_id, $this->width, $this->height, \Setup::$STUB_SCALE, $this->scale);

        header('Content-type:image/jpeg');
        $img_content = readfile(\Setup::$ROOT.$url);
        return;
    }    

}