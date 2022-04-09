<?php
namespace TinyPNG\Controller\Admin;

/**
* Содержит действия по обслуживанию
*/
class Tools extends \RS\Controller\Admin\Front
{
    /**
    * Добавляет фото из текущей для сайта темы оформления для оптимизации
    * 
    */
    function actionAjaxAddThemeImages()
    {
        $api = new \TinyPNG\Model\Api();
        if (!$api->isPHPRightVersion()){
            return $this->result->setSuccess(false)
                            ->addEMessage(t('Минимальная версия PHP для работы модуля - 5.5.'));
        }
        $api->addThemeImagesToCompress();
        
        return $this->result->setSuccess(true)
                            ->addMessage(t('Фото из темы оформления поставлены в очередь. Обновите страницу.'))
                            ->setNoAjaxRedirect($this->url->selfUri());
    }
    
    
    /**
    * Заново запускает весь процесс оптимизации изображений с удалением сведений о фото
    * 
    */
    function actionAjaxRestart()
    {
        $api = new \TinyPNG\Model\Api();
        if (!$api->isPHPRightVersion()){
            return $this->result->setSuccess(false)
                            ->addEMessage(t('Минимальная версия PHP для работы модуля - 5.5.'));
        }
        
        //Удалим фото
        $photo_api = new \Photo\Model\PhotoApi();
        $photo_api->deletePreviewPhotos();
        //Удалим фото рендов для оптимизации
        \RS\File\Tools::deleteFolder(\Setup::$PATH.\Setup::$STORAGE_DIR.'/storage/banners/resized');
        \RS\File\Tools::deleteFolder(\Setup::$PATH.\Setup::$STORAGE_DIR.'/system/resized');
        
        //Очистим таблицу
        $api->clearHistory();              
        
        return $this->result->setSuccess(true)
                        ->addMessage(t('Процесс перезапущен, очередь очищена. Обновите страницу.'))
                        ->setNoAjaxRedirect($this->url->selfUri());
    }
}