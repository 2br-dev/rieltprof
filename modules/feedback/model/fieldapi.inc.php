<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Feedback\Model;

use Feedback\Model\Orm\FormFieldItem;
use RS\Module\AbstractModel\EntityList;

class FieldApi extends EntityList
{
    function __construct()
    {
        parent::__construct(new FormFieldItem(), [
            'multisite' => true,
            'sortField' => 'sortn',
            'idField' => 'id',
            'aliasField' => 'alias',
            'defaultOrder' => 'sortn'
        ]);
    }
}
