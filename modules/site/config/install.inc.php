<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Site\Config;

class Install extends \RS\Module\AbstractInstall
{
    function install()
    {
        $result = parent::install();
        if ($result) {
            //Добавляем первый сайт
            $domain = \RS\Http\Request::commonInstance()->getDomainStr();
            $site = new \Site\Model\Orm\Site();
            $site['id'] = 1;
            $site['title'] = t('Сайт ').$domain;
            $site['full_title'] = t('Сайт ').$domain;
            $site['domains'] = \Setup::$DOMAIN;
            $site['language'] = \RS\Language\Core::getCurrentLang();
            $site['default'] = 1;
            $site['update_robots_txt'] = 1;
            $site->insert();
            
            \RS\Cache\Manager::obj()->invalidate(['\RS\Site\Manager', '*']);
        }
        return $result;
    }   
    
}
