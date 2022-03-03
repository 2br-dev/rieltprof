<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\CsvSchema;
use \RS\Csv\Preset,
    \Shop\Model\CsvPreset,
    \Shop\Model\Orm;

/**
* Схема экспорта заказнных товаров в CSV
*/
class OrderItems extends \RS\Csv\AbstractSchema
{
    function __construct()
    {
        parent::__construct(new CsvPreset\OrderItemsBase([
                'ormObject' => new Orm\OrderItem(),
                'excludeFields' => [
                    'uniq', 'type', 'entity_id', 'sortn', 'extra',
                ],
                'multisite' => true,
                'searchFields' => ['order_id']
        ]), []
        );
    }
}
