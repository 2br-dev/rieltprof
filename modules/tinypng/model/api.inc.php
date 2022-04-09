<?php
namespace TinyPNG\Model;

class Api{
    
    /**
    * Возращает есть ли в стеке фото с таких же именем или нет.
    * 
    * @param string $file - отностельный путь к фото
    */
    private function checkImage($file)
    {
        $row = \RS\Orm\Request::make()
                        ->from(new \TinyPNG\Model\Orm\Image())
                        ->where(array(
                            'file' => $file
                        ))->exec()
                        ->fetchRow();
        return $row ? false : true;
    }
    
    /**
    * Возвращает массив изображений из папки для оптимизации
    * 
    * @param string $path - директория для просмотра
    */
    private function getImagesFromDir($path, $theme = null)
    {
        $dirs = glob($path."/*", GLOB_ONLYDIR);
        if (!empty($dirs)){
            foreach ($dirs as $dir){
                $this->getImagesFromDir($dir, $theme);
            }
        }
        
        //Теперь ищем сами фото
        $valid_ext = array("jpg", "jpeg", "png", "gif"); // нужные форматы

        foreach (new \DirectoryIterator($path) as $fileInfo) { 
            if (in_array($fileInfo->getExtension(), $valid_ext) ) {
                $file  = $fileInfo->getPathname();
                //занесем в бд
                if ($this->checkImage($file)){ //Если в базе ещё нет этого фото
                    $image = new \TinyPNG\Model\Orm\Image();
                    $image['file'] = $file;
                    $image['theme'] = $theme;
                    $image->insert();    
                }
            }
        }
    }
    
    
    /**
    * Добавляет на сжатие изображения в текущей теме оформления
    * 
    */
    function addThemeImagesToCompress()
    {
        $current_theme = \RS\Theme\Manager::getCurrentTheme();
        $this->getImagesFromDir(\Setup::$SM_TEMPLATE_PATH.$current_theme['theme'], $current_theme['theme']);
    }
    
    /**
    * Возвращает количество не обработанных фото
    * 
    * @return integer
    */
    public static function getCountInStack()
    {
        return \RS\Orm\Request::make()
                    ->from(new \TinyPNG\Model\Orm\Image())
                    ->count();
    }
    
    
    
    /**
    * Удаляет историю об уже обратотанных фото
    * 
    */
    function clearHistory()
    {
        $image = new \TinyPNG\Model\Orm\Image();
        \RS\Db\Adapter::sqlExec('TRUNCATE TABLE '.$image->_getTable());
    }
    
    /***
    * Сжимает фото уменьшая его размер
    * 
    * @param \TinyPNG\Model\Orm\Image $image - картинка на сжатие
    */
    function compressImage(\TinyPNG\Model\Orm\Image $image)
    {
        $file = $image['file'];
        
        //Сожмём через сервис
        $smashItApi = new \TinyPNG\Model\DavGothic\SmushIt();
        $result = $smashItApi->compress($file);
        
        //Если сжатие удалось, то всё хорошо
        if (isset($result['dest']) && !empty($result['dest'])){
            try{
                @copy($result['dest'], $file);
                $image->delete();
            }catch(\Exception $e){
                throw new \RS\Exception($e->getMessage(), $e->getCode(), $e->getPrevious(), t('Ошибка при автосжатии фото'));
            }
        }
    }
    
    /**
    * Сжимает очередную порцию фото не сжатых
    * 
    */
    function compressNextImagesPortion()
    {
        if ($this->isPHPRightVersion()){            
            $config = \RS\Config\Loader::byModule($this);
            $images = \RS\Orm\Request::make()
                        ->from(new \TinyPNG\Model\Orm\Image())
                        ->limit($config['portion_count'])
                        ->objects();
                        
            if (!empty($images)){
                foreach ($images as $image){
                    $this->compressImage($image);
                }
            }    
        }
    }
    
    /**
    * Проверяет нужную версию PHP, минимально нужно 5.5
    * 
    */
    public static function isPHPRightVersion()
    {
        return (version_compare(PHP_VERSION, '5.5') >= 0) ? true : false;
    } 
}
