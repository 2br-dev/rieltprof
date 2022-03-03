<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Config;

use Catalog\Model\Dirapi;
use Catalog\Model\Orm\Dir;
use Main\Model\Widgets as WidgetsApi;
use RS\Config\Loader as ConfigLoader;
use RS\Module\AbstractInstall;
use RS\Orm\AbstractObject;
use RS\Site\Manager as SiteManager;
use Templates\Model\PageApi;
use Users\Model\Orm\User;
use Users\Model\Orm\UserGroup;

/**
* Класс отвечает за установку и обновление модуля
*/
class Install extends AbstractInstall
{
    function install()
    {
        $result = parent::install();
        if ($result) {
            //Вставляем в таблицы данные по-умолчанию, в рамках нового сайта, вызывая принудительно обработчик события
            Handlers::onSiteCreate([
                'orm' => SiteManager::getSite(),
                'flag' => AbstractObject::INSERT_FLAG
            ]);
            
            //Добавляем виджеты на рабочий стол
            $widget_api = new WidgetsApi();
            $widget_api->setUserId(1);
            $widget_api->insertWidget('catalog-widget-watchnow', 1, 0);
            $widget_api->insertWidget('catalog-widget-oneclick', 3);
        }
        
        return $result;
    }
    
    /**
    * Функция обновления модуля, вызывается только при обновлении
    */
    function update()
    {
        $result = parent::update();
        if ($result) {
            $user = new User();
            $user->dbUpdate();

            $user = new UserGroup();
            $user->dbUpdate();
        }
        return $result;
    }     
    
    /**
    * Добавляет демонстрационные данные
    * 
    * @param array $params - произвольные параметры. 
    * @return boolean|array
    */
    function insertDemoData($params = [])
    {
        return $this->importCsvFiles([
            ['\Catalog\Model\CsvSchema\Brand', 'brand'],
            ['\Catalog\Model\CsvSchema\Typecost', 'typecost'],
            ['\Catalog\Model\CsvSchema\Property', 'property'],
            ['\Catalog\Model\CsvSchema\Unit', 'unit'],
            ['\Catalog\Model\CsvSchema\Dir', 'dir'],
            
            ['\Catalog\Model\CsvSchema\Product', 'product'],
            ['\Catalog\Model\CsvSchema\Offer', 'offer'],
            ['\Catalog\Model\CsvSchema\DirProperty', 'dirproperty'],
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
            $site_config = ConfigLoader::getSiteConfig();
            if ($site_config->getThemeName() == 'default') {
                $dir_api = new Dirapi();
                $top_dir_id = $dir_api->getIdByAlias('top');

                //Настраиваем блок Лидеры продаж
                PageApi::setupModule('main.index', 'catalog\controller\block\topproducts', [
                    'dirs' => $top_dir_id
                ]);
                
                //Настраиваем блок товары в виде баннера
                PageApi::setupModule('main.index', 'catalog\controller\block\bannerview', [
                    'categories' => [
                        $dir_api->getIdByAlias('popular'),
                        $dir_api->getIdByAlias('newest')
                    ]
                ]);
                
                //Настраиваем спецкатегорию, которую выводим в брендах
                $actual_dir = Dir::loadByWhere([
                    'alias' => 'actual'
                ]);
                if ($actual_dir['id']) {
                    $catalog_config = ConfigLoader::byModule($this);
                    $catalog_config['brand_products_specdir'] = $actual_dir['id'];
                    $catalog_config->update();
                }
            }
        }
        return true;
    }
}
