<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Site\Controller\Admin;

class PersonalData extends \RS\Controller\Admin\Front
{
    /**
     * Загружает текст документа по умолчанию
     */
    function actionLoadDefaultDocument()
    {
        $doc_id = $this->url->request('doc_id', TYPE_STRING);

        $folder = \Setup::$PATH.\Setup::$MODULE_FOLDER.'/site'.\Setup::$MODULE_TPL_FOLDER.'/policy/';
        $files = scandir($folder);
        $filename = $doc_id.'_default.tpl';

        if (in_array($filename, $files)) {
            $this->result->setSuccess(true)->setTemplate($folder.$filename);
        }

        return $this->result;
    }

}