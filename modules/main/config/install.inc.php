<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Config;

class Install extends \RS\Module\AbstractInstall
{
    function install()
    {
        $result = parent::install();
        if ($result) {
            //Устанавливаем стартовые виджеты
            $widget_api = new \Main\Model\Widgets();
            $widget_api->setUserId(1);
            $widget_api->insertWidget('comments-widget-newlist', 2);
            $widget_api->insertWidget('users-widget-authlog', 3);
            $widget_api->insertWidget('main-widget-bestsellers', 2, 0);
        }
        return $result;
    }
    
    /**
    * Добавляет демонстрационные данные
    * 
    * @return bool
    */
    function insertDemoData($params = [])
    {
        //Заполняем демо значения предприятия
        $site_config = \RS\Config\Loader::getSiteConfig(\RS\Site\Manager::getSiteId());
        $site_config['logo'] = $site_config['__logo']->addFromUrl($this->mod_folder.'/config/demo/logo.png');
        $site_config['slogan'] = t('Ваш интернет-магазин');
        $site_config['firm_name'] = t('ООО Ваше предприятие');
        $site_config['firm_inn'] = t('2308001234');
        $site_config['firm_kpp'] = t('231001001');
        $site_config['firm_bank'] = t('ОАО Интернет банк');
        $site_config['firm_bik'] = t('1234567890');
        $site_config['firm_rs'] = t('12345678909876543210');
        $site_config['firm_ks'] = t('65432101234567890987');
        $site_config['firm_director'] = t('Иванов П.С.');
        $site_config['firm_accountant'] = t('Семенова Н.В.');
        $site_config['facebook_group'] = 'http://fb.com';
        $site_config['vkontakte_group'] = 'http://vk.com';
        $site_config['twitter_group'] = 'http://twitter.com';
        $site_config->update();
        return true;
    }
    
    /**
    * Возвращает true, если модуль может вставить демонстрационные данные
    * 
    * @return bool
    */
    function canInsertDemoData()
    {
        return true;
    }    
}
