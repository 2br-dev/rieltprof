<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Banners\Config;

class Install extends \RS\Module\AbstractInstall
{
    
    /**
    * Добавляет демонстрационные данные
    * 
    * @param array $params - произвольные параметры. 
    * @return boolean|array
    */
    function insertDemoData($params = [])
    {
        return $this->importCsvFiles([
            ['\Banners\Model\CsvSchema\Zone', 'zones'],
            ['\Banners\Model\CsvSchema\Banner', 'banners'],
        ],'utf-8', $params);
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
    
    /**
    * Выполняется, после того, как были установлены все модули. 
    * Здесь можно устанавливать настройки, которые связаны с другими модулями.
    * 
    * @param array $options параметры установки
    * @return bool
    */        
    function deferredAfterInstall($options)
    {
        if ($options['set_demo_data']) {
            $site_config = \RS\Config\Loader::getSiteConfig();
            if ($site_config->getThemeName() == 'default') {
                $zone = \Banners\Model\Orm\Zone::loadByWhere([
                    'alias' => 'leftcolumn'
                ]);

                //Настраиваем блок новости в дефолтном шаблоне
                \Templates\Model\PageApi::setupModule(null, 'banners\controller\block\bannerzone', [
                    'zone' => $zone['id'],
                    'rotate' => 1
                ]);
            }
        }
        return true;        
    }
}
