<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Marketplace\Config;
use \RS\Orm\Type;

/**
* Конфигурационный файл модуля
*/
class File extends \RS\Orm\ConfigObject
{
    function _init()
    {
        $properties = parent::_init();
        $properties['enabled']->setAttr(['disabled' => 'disabled']);

        $properties['allow_remote_install'] = new Type\Integer([
            'description' => t('Разрешить установку дополнений из Marketplace'),
            'hint' => t('Разрешить удаленную установку дополнений с сервера Marketplace'),
            'default' => 1,
            'checkboxView' => [1,0]
        ]);
        
        return $properties;
    }
    
    function beforeWrite($flag)
    {
        //Модуль всегда должен быть включен
        if (!$this['enabled']) $this['enabled'] = 1;
    } 
}

