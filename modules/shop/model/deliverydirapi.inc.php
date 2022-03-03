<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model;

use RS\Module\AbstractModel\EntityList;
use Shop\Model\Orm\DeliveryDir;

class DeliveryDirApi extends EntityList
{
    public $uniq;

    public function __construct()
    {
        parent::__construct(new DeliveryDir(), [
            'multisite' => true,
            'defaultOrder' => 'sortn',
            'nameField' => 'title',
            'sortField' => 'sortn'
        ]);
    }
}
