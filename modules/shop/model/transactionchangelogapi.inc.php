<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model;

use RS\Module\AbstractModel\EntityList;
use Shop\Model\Orm\TransactionChangeLog;

class TransactionChangeLogApi extends EntityList
{
    public function __construct()
    {
        parent::__construct(new TransactionChangeLog(), [
            'multisite' => true,
            'sortField' => 'date desc',
        ]);
    }
}
