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
* Схема экспорта/импорта
*/
class DirProperty extends \RS\Csv\AbstractSchema
{
    function __construct()
    {
        parent::__construct(new Preset\Base([
                'ormObject' => new \Catalog\Model\Orm\Property\Link(),
                'fields' => [
                    'public'
                ],
                'titles' => [
                    'public' => t('Отображать в поиске')
                ],
                'replaceMode' => true,
                'nullFields' => [
                    'val_str', 'val_int'
                ],
                'multisite' => true,
                'selectRequest' => \RS\Orm\Request::make()->select('DISTINCT public, prop_id, group_id')->from(new \Catalog\Model\Orm\Property\Link())->where('group_id>0'),
                'searchFields' => ['site_id', 'group_id'],
        ]), [
                new Preset\LinkedTable([
                    'ormObject' => new \Catalog\Model\Orm\Property\Item(),
                    'save' => false,
                    'fields' => ['title'],
                    'titles' => ['title' => t('Характеристика')],
                    'idField' => 'id',
                    'multisite' => true,                
                    'linkForeignField' => 'prop_id',
                    'linkPresetId' => 0,
                    'linkDefaultValue' => 0
                ]),
                new Preset\LinkedTable([
                    'ormObject' => new \Catalog\Model\Orm\Dir(),
                    'save' => false,
                    'fields' => ['name'],
                    'titles' => ['name' => t('Категория')],
                    'idField' => 'id',
                    'multisite' => true,                
                    'linkForeignField' => 'group_id',
                    'linkPresetId' => 0,
                    'linkDefaultValue' => 0
                ]),
            ]
        );
    }
}