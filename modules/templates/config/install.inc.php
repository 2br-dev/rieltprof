<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Templates\Config;

class Install extends \RS\Module\AbstractInstall
{
    function install()
    {
        $result = parent::install();
        if ($result) {
            //Устанавливаем шаблон новому сайту
            $theme = new \RS\Theme\Item(\Setup::$DEFAULT_THEME);
            $theme->setThisTheme(null, \RS\Site\Manager::getSiteId() );
        }
        return $result;
    }
    
}
