<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Marketplace\Config;
use Main\Model\Widgets;

class Patches extends \RS\Module\AbstractPatches
{
    function init()
    {
        return [
            '20023'
        ];
    }

    /**
     * Патч добавляет виджет "Полезные модули из Маркетплейса"
     */
    function afterUpdate20023()
    {
        $widget_api = new Widgets();
        $widget_api->insertWidget('marketplace-widget-newmodules', 2, 0, Widgets::MODE_THREE_COLUMN);
    }
}