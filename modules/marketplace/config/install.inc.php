<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Marketplace\Config;
use Main\Model\Widgets;

class Install extends \RS\Module\AbstractInstall
{
    function install()
    {
        if ($result = parent::install()) {
            $widget_api = new Widgets();
            $widget_api->setUserId(1);
            $widget_api->insertWidget('marketplace-widget-newmodules', 2, 0, Widgets::MODE_THREE_COLUMN);
        }

        return $result;
    }
}