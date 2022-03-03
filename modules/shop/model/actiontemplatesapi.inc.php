<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model;

use RS\Module\AbstractModel\EntityList;

/**
 * PHP API для работы со списком действий курьера
 */
class ActionTemplatesApi extends EntityList
{
    function __construct()
    {
        parent::__construct(new Orm\ActionTemplate(), [
            'multisite' => true
        ]);
    }
}