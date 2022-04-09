<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model;

use RS\Module\AbstractModel\EntityList;
use Shop\Model\Orm\Cargo\CargoPreset;

/**
 * API для работы со списком грузомест
 */
class CargoPresetApi extends EntityList
{
    function __construct()
    {
        parent::__construct(new CargoPreset(), [
            'multisite' => true,
            'sortField' => 'sortn'
        ]);
    }
}