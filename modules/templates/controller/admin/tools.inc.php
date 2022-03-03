<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Templates\Controller\Admin;

/**
* Содержит действия по обслуживанию
*/
class Tools extends \RS\Controller\Admin\Front
{
    function actionAjaxFixSortn()
    {
        $api = new \Templates\Model\ContainerApi();
        $api->fixBloksSortn();
        
        return $this->result->setSuccess(true)->addMessage(t('Сортировка блоков успешно обновлена'));
    }
}