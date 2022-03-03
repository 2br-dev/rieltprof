<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\CsvSchema;
use \RS\Csv\Preset;

/**
* Схема экспорта/импорта справочника складов в CSV
*/
class Warehouse extends \RS\Csv\AbstractSchema
{
    function __construct()
    {
        parent::__construct(new Preset\Base([
                'ormObject' => new \Catalog\Model\Orm\WareHouse(),
                'excludeFields' => [
                    'id', 'site_id', 'image'
                ],
                'nullFields' => [
                    'xml_id'
                ],
                'savedRequest' => \Catalog\Model\WareHouseApi::getSavedRequest('Catalog\Controller\Admin\WareHouseCtrl_list'), //Объект запроса из сессии с параметрами текущего просмотра списка
                'multisite' => true,
                'searchFields' => ['title'],
        ]),
            [
                new Preset\SinglePhoto([
                    'linkPresetId' => 0,
                    'linkForeignField' => 'image',
                    'title' => t('Изображение')
                ]),
            ]
        );
    }
}
