<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace ExternalApi\Config;

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
            $config = \RS\Config\Loader::byModule($this);
            //Устанавливает произвольный API ключ, при установке модуля
            $config['api_key'] = \RS\Helper\Tools::generatePassword(8, array_merge(range('a', 'z'), range('0', '9')));
            $config->update();
        }
        return $result;
    }
    
}
