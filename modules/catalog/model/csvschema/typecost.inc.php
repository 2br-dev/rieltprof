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
* Схема экспорта/импорта справочника цен в CSV
*/
class Typecost extends \RS\Csv\AbstractSchema
{
    function __construct()
    {
        parent::__construct(new Preset\Base([
            'ormObject' => new \Catalog\Model\Orm\Typecost(),
            'nullFields' => ['xml_id'],
            'excludeFields' => [
                'id', 'site_id'
            ],
            'savedRequest' => \Catalog\Model\CostApi::getSavedRequest('Catalog\Controller\Admin\CostCtrl_list'), //Объект запроса из сессии с параметрами текущего просмотра списка
            'multisite' => true,
            'searchFields' => ['title']
        ]));
    }
}