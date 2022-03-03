<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace PageSeo\Model\CsvSchema;
use \RS\Csv\Preset;

/**
* Схема экспорта/импорта справочника цен в CSV
*/
class Pageseo extends \RS\Csv\AbstractSchema
{
    function __construct()
    {
        parent::__construct(new Preset\Base([
            'ormObject' => new \PageSeo\Model\Orm\PageSeo(),
            'excludeFields' => [
                'id', 'site_id'
            ],
            'multisite' => true,
            'searchFields' => ['route_id']
        ]));
    }
}