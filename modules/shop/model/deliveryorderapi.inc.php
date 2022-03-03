<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model;

use RS\Module\AbstractModel\EntityList;
use Shop\Model\Orm\DeliveryOrder;

class DeliveryOrderApi extends EntityList
{
    public $uniq;

    public function __construct()
    {
        parent::__construct(new DeliveryOrder(), [
            'defaultOrder' => 'creation_date asc',
        ]);
    }
}
