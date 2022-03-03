<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Alerts\Controller\Admin;

use \Alerts\Model\SMS\Manager as SmsManager;

class Tools extends \RS\Controller\Admin\Front
{
    /**
     * Выводит содержимое лог-файла SMS
     *
     * @return string
     */
    function actionShowSmsLog()
    {
        $this->wrapOutput(false);
        $log_file = SmsManager::getLogFileName();
        if (file_exists($log_file)) {
            echo '<pre>';
            readfile($log_file);
            echo '</pre>';
        } else {
            return t('Лог файл не найден');
        }
    }

    /**
     * Удаляет лог-файл SMS
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionDeleteSmsLog()
    {
        $log_file = SmsManager::getLogFileName();
        if (file_exists($log_file)) {
            unlink($log_file);
            return $this->result->setSuccess(true)->addMessage(t('Лог-файл успешно удален'));
        } else {
            return $this->result->setSuccess(true)->addEMessage(t('Лог-файл отсутствует'));
        }
    }
}
