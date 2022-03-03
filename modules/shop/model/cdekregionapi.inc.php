<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/ declare(strict_types=1);

namespace Shop\Model;

use RS\Module\AbstractModel\EntityList;
use Shop\Model\Orm\Delivery\CdekRegion;

/**
 * API функции для работы со способами доставки для текущего сайта
 */
class CdekRegionApi extends EntityList
{
    protected static $types;

    function __construct()
    {
        parent::__construct(new CdekRegion(), [
            'nameField' => 'city',
            'defaultOrder' => 'code',
        ]);
    }
}
