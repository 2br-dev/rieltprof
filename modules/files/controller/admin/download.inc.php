<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Files\Controller\Admin;

class Download extends \RS\Controller\Admin\Front
{
    function actionIndex()
    {
        $uniq = $this->url->get('uniq', TYPE_STRING);
        
        $file = \Files\Model\Orm\File::loadByWhere([
            'uniq' => $uniq
        ]);
        if (!$file['id']) {
            $this->e404(t('Файл с таким идентификатором не найден'));
        }
        
        $mime = $file['mime'] ?: 'application/octet-stream';
        \RS\File\Tools::sendToDownload($file->getServerPath(), $file['name'], $mime);
        exit;
    }
}