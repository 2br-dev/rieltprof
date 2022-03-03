<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Config;

use Crm\Model\CsvSchema\Status;

class Install extends \RS\Module\AbstractInstall
{
    /**
     * Выполняет установку модуля
     *
     * @return bool
     */
    function install()
    {
        if ($result = parent::install()) {
            $this->importCsv(new Status(), 'status');

            //Добавляем виджеты на рабочий стол
            $widget_api = new \Main\Model\Widgets();
            $widget_api->setUserId(1);
            $widget_api->insertWidget('crm-widget-task', 2);
        }
        return $result;
    }

}