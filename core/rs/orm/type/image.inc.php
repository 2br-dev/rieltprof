<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Orm\Type;

/**
* Тип - изображение.
*/
class Image extends File
{
    public
        $preview_width = 150,
        $preview_height = 150,
        $preview_resize_type = 'xy',
        $form_template = '%SYSTEM%/coreobject/type/form/image.tpl',
        $maxLength = 255,
        $max_file_size = 10000000,
        $allow_file_types = ['image/pjpeg', 'image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    
    protected
        $original_folder = '/storage/system/original',
        $preview_folder = '/storage/system/resized';        
        
    
    function __construct(array $options = null)
    {
        $this->preview_folder = \Setup::$FOLDER.$this->preview_folder;        
        parent::__construct($options);
    }
    
    public function normalizePost()
    {
        if ($this->value && isset($this->value['tmp_name'])) {
            $this->tmp_arr = $this->value;            
            $this->value = $this->generateValue($this->tmp_arr['name']);
        } else {
            $this->value = null;
        }
    }
    
    /**
    * Возвращает объект системы изображений с предустановленными путями
    * 
    * @return \RS\Img\Core
    */
    function getImageCore()
    {
        //Пользуемся общей системой отображения картинок этой CMS.
        return new \RS\Img\Core($this->base, $this->path, $this->preview_folder);
    }
    
    /**
    * Возвращает путь к изображению
    * 
    * @param integer $width - ширина в пикселях
    * @param integer $height - высота в пикселях
    * @param string $img_type - тип масштабирования 
    * @param bool $absolute - если true, то будет возвращен абсолютный адрес
    * @return string
    */
    function getUrl($width, $height, $img_type = 'xy', $absolute = false)
    {
        if ($this->get()) {
            $url = $this->getImageCore()->getImageUrl($this->getRealPath(), $width, $height, $img_type);
            return $absolute ? \RS\Site\Manager::getSite()->getAbsoluteUrl($url) : $url;
        } else {
            return $this->getStub()->getUrl($width, $height, $img_type, $absolute);
        }
    }
    
    /**
    * Возвращает объект заглушки изображения
    * 
    * @return \Photo\Model\Stub
    */
    function getStub()
    {
        return new \Photo\Model\Stub();
    }
    
    /**
    * Устанавливает размер миниатюры изображений, который отображается в административной панели
    * 
    * @param integer $width - ширина в пикселях
    * @param integer $height - высота в пикселях
    * @param string $img_type - тип масштабирования 
    * @return void
    */
    function setPreviewSize($width, $height, $img_type = 'xy')
    {
        $this->preview_width = $width;
        $this->preview_height = $height;
        $this->preview_resize_type = $img_type;
    }
    
    /**
    * Возвращает путь к оригиналам изображений
    * 
    * @return string
    */
    function getOriginalFolder()
    {
        return $this->original_folder;
    }
    
    /**
    * Возвращает путь к папке с миниатюрами изображений
    * 
    * @return string
    */
    function getPreviewFolder()
    {
        return $this->preview_folder;
    }
    
    /**
    * Устанавливает папку для миниатюр изображений
    * 
    * @param string $path - 
    * 
    * @return Image
    */
    function setPreviewFolder($path)
    {
        $this->preview_folder = $path;
        return $this;
    }
    
    /**
    * Удаляет оригинал изображения со всеми миниатюрами
    */
    function removeImageFiles()
    {
        $original_filename = $this->getRealPath();
        $this->getImageCore()->removeFile( $original_filename );
    }
}

