<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Kaptcha\Config;

/**
* Патчи к модулю
*/
class Patches extends \RS\Module\AbstractPatches
{
    /**
    * Возвращает список имен существующих патчей
    */
    function init()
    {
        return [
            '310',
        ];
    }
    
    /**
    * Устанавливает класс капчи в зависимости от того, был ли включён модуль
    */
    function afterUpdate310()
    {
        $system_config = \RS\Config\Loader::getSystemConfig();
        $kaptcha_config = \RS\Config\Loader::byModule('kaptcha');
        $system_config['captcha_class'] = ($kaptcha_config['enabled']) ? 'RS-default' : 'stub';
        $system_config->update;
    }
}
