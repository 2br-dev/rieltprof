<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Notes\Config;

/**
 * Класс отвечает за установку и обновление модуля
 */
class Install extends \RS\Module\AbstractInstall
{
    function install()
    {
        $result = parent::install();
        if ($result) {
            //Добавляем виджеты на рабочий стол
            $widget_api = new \Main\Model\Widgets();
            $widget_api->setUserId(1);
            $widget_api->insertWidget('notes-widget-notes', 1);
        }
        return $result;
    }
}
