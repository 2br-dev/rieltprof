<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model;

use Catalog\Model\Orm\WareHouseGroup;
use RS\Module\AbstractModel\EntityList;

class WareHouseGroupApi extends EntityList
{
    public $uniq;

    public function __construct()
    {
        parent::__construct(new WareHouseGroup(), [
            'multisite' => true,
            'name_field' => 'title',
            'defaultOrder' => 'sortn',
            'sortField' => 'sortn'
        ]);
    }
}
