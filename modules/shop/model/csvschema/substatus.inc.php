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
 * Схема экспорта/импорта прияин отмены заказа в CSV
 */
class SubStatus extends \RS\Csv\AbstractSchema
{
    function __construct()
    {
        parent::__construct(
            new Preset\Base([
                'ormObject' => new \Shop\Model\Orm\SubStatus(),
                'excludeFields' => [
                    'id', 'site_id'
                ],
                'savedRequest' => \Shop\Model\ZoneApi::getSavedRequest('Shop\Controller\Admin\SubStatusCtrl_list'), //Объект запроса из сессии с параметрами текущего просмотра списка
                'multisite' => true,
                'searchFields' => ['alias']
            ]));
    }
}