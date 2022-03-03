<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\Orm;
use \RS\Orm\Type;

/**
 * --/--
 * @property integer $zone_id ID Зоны
 * @property integer $region_id ID Региона
 * --\--
 */
class Xregion extends \RS\Orm\AbstractObject
{
    protected static
        $table = "order_x_region";
    
    function _init()
    {
        $this->getPropertyIterator()->append([
            'zone_id' => new Type\Integer([
                'description' => t('ID Зоны')
            ]),
            'region_id' => new Type\Integer([
                'description' => t('ID Региона')
            ]),
        ]);
        
        $this->addIndex(['zone_id', 'region_id'], self::INDEX_UNIQUE);
    }
}

