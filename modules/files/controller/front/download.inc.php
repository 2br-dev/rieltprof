<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Files\Controller\Front;

/**
* Контроллер, отвечающий за отдачу файлов для закачки браузером
*/
class Download extends \RS\Controller\Front
{
    function actionIndex()
    {
        $uniq_name = $this->url->get('uniq_name', TYPE_STRING);
        
        $file = \Files\Model\Orm\File::loadByWhere([
            'uniq_name' => $uniq_name
        ]);
        if (!$file['id']) {
            $this->e404(t('Файл с таким идентификатором не найден'));
        }
        
        //Администратор может скачивать файлы без проверки прав
        $linktype = $file->getLinkType();
        $group = $linktype->getNeedGroupForDownload($file);
        if ($group && ($this->user['id']<=0 || !$this->user->inGroup($group))) {
            return $this->authPage(t('Для загрузки файла недостаточно прав'), $this->url->selfUri());
        }
        
        if ($error = $file->getLinkType()->checkDownloadRightErrors($file)) {
            $this->app->showException(403, $error);
        }
        
        $mime = $file['mime'] ?: 'application/octet-stream';
        \RS\File\Tools::sendToDownload($file->getServerPath(), $file['name'], $mime);
        exit;
    }
}
