<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Photogalleries\Config;

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
    /**
     * Импортирует баннеры
     *
     * @return array
     */
    function importCSVPhotogalleries()
    {
        return $this->importCsvFiles([
            ['\Photogalleries\Model\CsvSchema\Album', 'album'],
        ], 'utf-8', []);
    }

    /**
     * Добавляет демонстрационные данные
     *
     * @param array $params - произвольные параметры.
     * @return boolean|array
     * @throws \RS\Exception
     */
    function insertDemoData($params = [])
    {
        $imports = [];
        if (\RS\Module\Manager::staticModuleExists('photogalleries') && \RS\Module\Manager::staticModuleEnabled('photogalleries')){
            $imports[] = ['\Photogalleries\Model\CsvSchema\Album', 'album'];
        }

        return $this->importCsvFiles([
            $imports,
        ], 'utf-8', $params);
    }
}
