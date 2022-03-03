<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Language\Plugin;

interface PluginInterface 
{
    public function process($param_value, $value, $params, $lang);
    
}

