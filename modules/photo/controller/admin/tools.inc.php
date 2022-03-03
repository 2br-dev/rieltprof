<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Photo\Controller\Admin;

/**
* Содержит действия по обслуживанию фотографий
*/
class Tools extends \RS\Controller\Admin\Front
{
    /**
     * @return \RS\Controller\Result\Standard
     * @throws \RS\Db\Exception
     * @throws \RS\Exception
     */
    function actionAjaxDelUnlinkPhotos()
    {
        $api = new \Photo\Model\PhotoApi();
        $count = $api->deleteUnlinkedPhotos();
        return $this->result
            ->setSuccess(true)
            ->addMessage(t('Удалено %0 не связанных фото', [$count]));
    }

    /**
     * @return \RS\Controller\Result\Standard
     */
    function actionAjaxDelPreviewPhotos()
    {
        $api = new \Photo\Model\PhotoApi();
        $api->deletePreviewPhotos();
        return $this->result
            ->setSuccess(true)
            ->addMessage(t('Миниатюры фотографий удалены'));        
    }

    /**
     * Удаление дублей фотографий товара
     * @return \RS\Controller\Result\Standard
     * @throws \RS\Db\Exception
     * @throws \RS\Exception
     */
    public function actionAjaxDelDoublesPhotos()
    {
        $api = new \Photo\Model\PhotoApi();
        $api->delDuplicatePhotosOfSomeType('catalog');
        $count = $api->deleteUnlinkedPhotos();

        return $this->result
            ->setSuccess(true)
            ->addMessage(t('Дубли фотографий удалены. Удалено %0 фото', [$count]));
    }
}