<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Controller\Admin;

use RS\Controller\Admin\Front;

/**
 * Контроллер, позволяющий работать с компонентом картинки
 */
class AtomImageCtrl extends Front
{
    /**
     * Создает картинку по умолчанию для атома картинки и возвращает массив сведений по картинкам
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionCreateDefaultAtomImage()
    {
        $block_id = $this->request('block_id', TYPE_STRING, null); //Блок
        $atom_id  = $this->request('id', TYPE_STRING, null);

        $api  = new \Designer\Model\AtomApis\ImageApi();
        $data = $api->createDefaultAtomImage($block_id, $atom_id);
        return $this->result->setSuccess(true)->addSection('data', $data);
    }

    /**
     * Создает картинку из переданного адреса для атома картинки возвращает массив сведений по картинкам
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionSaveAtomUploadedImageByUrl()
    {
        $block_id  = $this->request('block_id', TYPE_STRING, null); //Блок
        $atom_id   = $this->request('id', TYPE_STRING, null);
        $image_url = $this->request('image_url', TYPE_STRING, null);

        $api  = new \Designer\Model\AtomApis\ImageApi();
        $data = $api->saveAtomUploadedImageByUrl($block_id, $atom_id, $image_url);
        return $this->result->setSuccess(true)->addSection('data', $data);
    }

    /**
     * Изменяет ширину и высоту картинки по переданому коэфициенту из оригинала в рабочую картинку
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionChangeImageForAtomImageByRatio()
    {
        $block_id = $this->request('block_id', TYPE_STRING, null); //Блок
        $atom_id  = $this->request('id', TYPE_STRING, null);
        $ratio    = $this->request('ratio', TYPE_STRING, null);

        $api  = new \Designer\Model\AtomApis\ImageApi();
        $data = $api->changeImageForAtomImageByRatio($block_id, $atom_id, $ratio);

        if ($api->hasError()){
            return $this->result->setSuccess(false)->addEMessage($api->getErrorsStr());
        }
        return $this->result->setSuccess(true)->addSection('data', $data);
    }

    /**
     * Изменяет ширину и высоту картинки по переданой высоте из оригинала в рабочую картинку
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionChangeImageForAtomImageByHeightAndWidth()
    {
        $block_id = $this->request('block_id', TYPE_STRING, null); //Блок
        $atom_id  = $this->request('id', TYPE_STRING, null);
        $width    = $this->request('width', TYPE_STRING, null);
        $height   = $this->request('height', TYPE_STRING, null);

        $height = (int)$height;

        if (empty($width) || $width == '100%'){
            $width = 0;
        }

        $width = (int)$width;

        $api  = new \Designer\Model\AtomApis\ImageApi();
        $data = $api->changeImageForAtomImageByHeightAndWidth($block_id, $atom_id, $width, $height);

        if ($api->hasError()){
            return $this->result->setSuccess(false)->addEMessage($api->getErrorsStr());
        }
        return $this->result->setSuccess(true)->addSection('data', $data);
    }

    /**
     * Изменяет ширину и высоту картинки по переданой высоте из оригинала в рабочую картинку
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionSaveAtomCroppedImage()
    {
        $block_id  = $this->request('block_id', TYPE_STRING, null); //Блок
        $atom_id   = $this->request('id', TYPE_STRING, null);
        $imageData = $this->request('imageData', TYPE_STRING, null);

        if (empty($imageData)){
            return $this->result->setSuccess(false)->addEMessage(t('Данные не переданы'));
        }
        $api  = new \Designer\Model\AtomApis\ImageApi();
        $data = $api->saveAtomCroppedImage($block_id, $atom_id, $imageData);

        if ($api->hasError()){
            return $this->result->setSuccess(false)->addEMessage($api->getErrorsStr());
        }
        return $this->result->setSuccess(true)->addSection('data', $data);
    }
}