<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Article\Config;

class Install extends \RS\Module\AbstractInstall
{
    
    /**
    * Добавляет демонстрационные данные
    * @param array $params - произвольные параметры. 
    * @return boolean|array
    */
    function insertDemoData($params = [])
    {
        return $this->importCsvFiles([
            ['\Article\Model\CsvSchema\Article', 'articles'],
        ], 'utf-8', $params);
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
                $cat_api = new \Article\Model\Catapi();
                $news_category = $cat_api->setFilter('title', t('Новости'))->getFirst();

                //Настраиваем блок новости в дефолтном шаблоне
                if ($news_category) {
                    \Templates\Model\PageApi::setupModule(null, 'article\controller\block\lastnews', [
                        'category' => $news_category['id'],
                        'pageSize' => 5
                    ]);
                }
            }
        }
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
