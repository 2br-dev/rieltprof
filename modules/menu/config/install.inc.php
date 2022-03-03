<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Menu\Config;

class Install extends \RS\Module\AbstractInstall
{
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
                $menu_api = new \Menu\Model\Api();
                $bottom_root_id = $menu_api->getIdByAlias('bottom');

                //Настраиваем блок Нижнее меню
                \Templates\Model\PageApi::setupModule('default', 'menu\controller\block\menu', [
                    'indexTemplate' => 'blocks/menu/foot_menu.tpl',
                    'root' => $bottom_root_id
                ], 'footmenu');
            }
        }
        return true;
    }      
    
    /**
    * Добавляет демонстрационные данные
    * 
    * @param array $params - произвольные параметры. 
    * @return bool(true) | array
    */
    function insertDemoData($params = [])
    {
        return $this->importCsvFiles([
            ['\Menu\Model\CsvSchema\Menu', 'menu']
        ], 'utf-8', $params);
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
