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
class Currency extends \RS\Csv\AbstractSchema
{
    function __construct()
    {
        parent::__construct(new Preset\Base([
            'ormObject' => new \Catalog\Model\Orm\Currency(),
            'excludeFields' => [
                'id', 'site_id'
            ],
            'savedRequest' => \Catalog\Model\CurrencyApi::getSavedRequest('Catalog\Controller\Admin\CurrencyCtrl_list'), //Объект запроса из сессии с параметрами текущего просмотра списка
            'multisite' => true,
            'searchFields' => ['title']
        ]));
    }
}