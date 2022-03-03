<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace PushSender\Controller\Admin;

/**
* Вспомогательные инструкменты
*/
class Tools extends \RS\Controller\Admin\Front
{
    /**
     * Просмотр лога Push токенов
     *
     * @return string
     */
    function actionReadLog()
    {
        $this->wrapOutput(false);
        
        $filename = \PushSender\Model\WriteLog::getFilename();
        if (file_exists($filename)) {
            
            $this->app->headers->addHeader('Content-type', 'text/plain; charset=utf-8');
            readfile($filename);
            
        } else {
            return t('Нет log файла');
        }
    }

    /**
     * Очищение лога Push токенов
     *
     * @return string
     */
    function actionClearLog()
    {
        $filename = \PushSender\Model\WriteLog::getFilename();
        if (file_exists($filename)) {
            file_put_contents($filename, "");
        }
        return $this->result->setSuccess(true)->addMessage(t('Файл очищен'));
    }
}
