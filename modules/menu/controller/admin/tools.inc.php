<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Menu\Controller\Admin;

/**
* Содержит действия по обслуживанию
*/
class Tools extends \RS\Controller\Admin\Front
{
    function actionAjaxRemakeMenu()
    {
        $api = new \Menu\Model\Api();
        $api->getAdminMenu();
        
        return $this->result->setSuccess(true)->addMessage(t('Меню обновлено'));
    }
}