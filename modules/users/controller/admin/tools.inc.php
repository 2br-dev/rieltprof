<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Controller\Admin;

use RS\Controller\Admin\Front;
use Users\Model\Api;

class Tools extends Front
{
    /**
     * Нормализует телефонные номера существующих пользователей
     */
    function actionAjaxNormalizeUserPhones()
    {
        $counter = Api::normalizePhonesOldUsers();

        return $this->result->setSuccess(true)->addMessage(t('Обновлено %n телефонных номеров', [
            'n' => $counter
        ]));
    }
}