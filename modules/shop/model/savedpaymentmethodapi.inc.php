<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/ declare(strict_types=1);

namespace Shop\Model;

use RS\Module\AbstractModel\EntityList;

class SavedPaymentMethodApi extends EntityList
{
    function __construct()
    {
        parent::__construct(new Orm\SavedPaymentMethod, [
            'defaultOrder' => 'save_date ASC',
        ]);
    }
}
