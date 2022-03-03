<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\CsvSchema;

use Crm\Model\StatusApi;
use \RS\Csv\Preset;

/**
 * Схема экспорта/импорта статусов в CSV
 */
class Status extends \RS\Csv\AbstractSchema
{
    function __construct()
    {
        parent::__construct(new Preset\Base([
            'ormObject' => new \Crm\Model\Orm\Status(),
            'excludeFields' => [
                'id'
            ],
            'savedRequest' => StatusApi::getSavedRequest('Crm\Controller\Admin\StatusCtrl_list'), //Объект запроса из сессии с параметрами текущего просмотра списка
            'searchFields' => ['object_type_alias', 'alias']
        ]));
    }
}