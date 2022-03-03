<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\CsvSchema;
use \RS\Csv\Preset,
    \Shop\Model\Orm;

/**
* Схема экспорта/импорта справочника цен в CSV
*/
class Zone extends \RS\Csv\AbstractSchema
{
    function __construct()
    {
        parent::__construct(
            new Preset\Base([
                'ormObject' => new \Shop\Model\Orm\Zone(),
                'excludeFields' => [
                    'id', 'site_id'
                ],
                'savedRequest' => \Shop\Model\ZoneApi::getSavedRequest('Shop\Controller\Admin\ZoneCtrl_list'), //Объект запроса из сессии с параметрами текущего просмотра списка
                'multisite' => true,
                'searchFields' => ['title']
            ]),
            [
                new Preset\ManyTreeParent([
                    'ormObject' => new Orm\Region(),
                    'idField' => 'id',
                    'manylinkOrm' => new Orm\Xregion(),
                    'title' => t('Регионы'),
                    
                    'manylinkIdField' => 'region_id',
                    'manylinkForeignIdField' => 'zone_id',
                    'linkPresetId' => 0,
                    'linkIdField' => 'id',
                    
                    'rootValue' => 0,
                    'treeField' => 'title',
                    'treeParentField' => 'parent_id',
                    'multisite' => true,
                    
                    'arrayField' => 'xregion',
                ])
            ]);
    }
}