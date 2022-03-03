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
 * Контроллер, позволяющий работать с компонентами
 */
class UploadCtrl extends Front
{
    /**
     * Поиск по названию картинки в Pixabay
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionPixabayImageSearch()
    {
        $term     = $this->request('term', TYPE_STRING, "");
        $category = $this->request('category', TYPE_STRING, "");
        $page     = $this->request('p', TYPE_INTEGER, 1); //Текущая страница

        $pixabay_api = new \Designer\Model\PixabaySearchApi();
        $result = $pixabay_api->makeSearch($term, $category, $page);

        if (!$result){
            return $this->result->setSuccess(false)->addEMessage(t('Не удалось выполнить запрос к прокси серверу'));
        }

        if (isset($result['messages'])){
            foreach($result['messages'] as $message){
                $pixabay_api->addError($message['text']);
            }
            return $this->result->setSuccess(false)->addEMessage($pixabay_api->getErrorsStr());
        }

        return $this->result->setSuccess(true)->addSection('list', $result['list']);
    }

    /**
     * Загрузка переданного файла
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionUploadFile()
    {
        if (empty($_FILES)){
            return $this->result->setSuccess(false)->addEMessage(t('Файл не передан'));
        }

        $file = reset($_FILES);
        $api = new \Designer\Model\UploadApi();
        //Загружаем файл
        $uploaded_file_url = $api->uploadFile($file);

        if ($api->hasError()){
            return $this->result->setSuccess(false)->addEMessage($api->getErrorsStr());
        }

        if ($uploaded_file_url){
            return $this->result->setSuccess(true)
                ->addSection('download_url', $uploaded_file_url)
                ->addSection('name', basename($uploaded_file_url));
        }
        return $this->result->setSuccess(false)->addEMessage(t('Файл не загружен'));
    }

    /**
     * Загрузка переданной картинки
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionUploadImage()
    {
        if (empty($_FILES)){
            return $this->result->setSuccess(false)->addEMessage(t('Файл не передан'));
        }

        $file = reset($_FILES);
        $api = new \Designer\Model\UploadApi();
        //Загружаем файл
        $uploaded_file_url = $api->uploadImage($file);

        if ($api->hasError()){
            return $this->result->setSuccess(false)->addEMessage($api->getErrorsStr());
        }

        if ($uploaded_file_url){
            return $this->result->setSuccess(true)
                                ->addSection('url', $uploaded_file_url);
        }
        return $this->result->setSuccess(false)->addEMessage(t('Файл не загружен'));
    }

    /**
     * Загрузка картинки по переданному URL для блока
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionUploadImageByUrl()
    {
        $url = $this->url->request('url', TYPE_STRING, "");

        if (empty($url)){
            return $this->result->setSuccess(false)->addEMessage(t('Файл для загрузки не указан'));
        }

        $api = new \Designer\Model\UploadApi();
        $uploaded_file_url = $api->uploadImageByURL($url);

        if ($api->hasError()){
            return $this->result->setSuccess(false)->addEMessage($api->getErrorsStr());
        }

        if ($uploaded_file_url){
            return $this->result->setSuccess(true)
                ->addSection('url', $uploaded_file_url);
        }
        return $this->result->setSuccess(false)->addEMessage(t('Файл не загружен'));
    }

    /**
     * Возвращает массив картинок загруженных на сервер
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionGetUploadedImages()
    {
        $page = $this->request('p', TYPE_INTEGER, 1); //Текущая страница

        $api  = new \Designer\Model\UploadApi();
        $data = $api->getListFromImagesFolder($page);

        if ($api->hasError()){
            return $this->result->setSuccess(false)->addEMessage($api->getErrorsStr());
        }
        return $this->result->setSuccess(true)->addSection('list', $data['list'])->addSection('total', $data['total']);
    }

    /**
     * Возвращает массив файлов загруженных ранее
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionGetUploadedFiles()
    {
        $page = $this->request('p', TYPE_INTEGER, 1); //Текущая страница

        $api  = new \Designer\Model\UploadApi();
        $data = $api->getListFromFilesFolder($page);

        if ($api->hasError()){
            return $this->result->setSuccess(false)->addEMessage($api->getErrorsStr());
        }
        return $this->result->setSuccess(true)->addSection('list', $data['list'])->addSection('total', $data['total']);
    }

    /**
     * Удаляет файл из папки с файлами
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionDeleteFile()
    {

        $file = $this->request('file', TYPE_STRING, null); //Текущая страница

        if ($file === null){
            return $this->result->setSuccess(false)->addEMessage(t('Не передано название файла'));
        }

        $api  = new \Designer\Model\UploadApi();
        $api->deleteFile($file);
        if ($api->hasError()){
            return $this->result->setSuccess(false)->addEMessage($api->getErrorsStr());
        }
        return $this->result->setSuccess(true);
    }

    /**
     * Удаляет файл из папки с файлами
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionDeleteImage()
    {
        $file = $this->request('file', TYPE_STRING, null); //Текущая страница

        if ($file === null){
            return $this->result->setSuccess(false)->addEMessage(t('Не передано название файла'));
        }

        $api  = new \Designer\Model\UploadApi();
        $api->deleteImage($file);
        if ($api->hasError()){
            return $this->result->setSuccess(false)->addEMessage($api->getErrorsStr());
        }
        return $this->result->setSuccess(true);
    }

    /**
     * Вовзращает набор SVG иконок
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionGetSVGIcons()
    {
        return $this->result->setSuccess(true)->addSection('list', \Designer\Model\UploadApi::getSVGImages());
    }
}