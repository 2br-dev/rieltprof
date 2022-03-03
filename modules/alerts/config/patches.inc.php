<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Alerts\Config;

class Patches extends \RS\Module\AbstractPatches
{
    function init()
    {
        return [
            '20033'
        ];
    }
    
    function afterUpdate20033()
    {
        \RS\Orm\Request::make()
            ->update(new \Alerts\Model\Orm\NoticeConfig())
            ->set('enable_desktop = 1')
            ->exec();
    }
}
