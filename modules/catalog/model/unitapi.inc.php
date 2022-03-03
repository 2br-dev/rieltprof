<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Catalog\Model;

use Catalog\Model\Orm\Unit;
use RS\Module\AbstractModel\EntityList;

class UnitApi extends EntityList
{
    function __construct()
    {
        parent::__construct(new Unit(),
            [
                'multisite' => true,
                'defaultOrder' => 'sortn',
                'sortField' => 'sortn'
            ]);
    }

    public static function selectList()
    {
        $_this = new self();
        return [0 => '-'] + $_this->getAssocList('id', 'stitle');
    }
}
