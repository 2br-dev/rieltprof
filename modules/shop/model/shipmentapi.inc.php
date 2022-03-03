<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model;

use RS\Module\AbstractModel\EntityList;
use Shop\Model\Orm\Shipment;

/**
 * API функции для работы с отгрузками
 */
class ShipmentApi extends EntityList
{
    function __construct()
    {
        parent::__construct(new Shipment());
    }
}
