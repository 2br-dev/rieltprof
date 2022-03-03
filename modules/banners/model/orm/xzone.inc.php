<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Banners\Model\Orm;
use \RS\Orm\Type;

/**
 * --/--
 * @property integer $zone_id ID зоны
 * @property integer $banner_id ID баннера
 * --\--
 */
class Xzone extends \RS\Orm\AbstractObject
{
    protected static
        $table = 'banner_x_zone';
    
    function _init()
    {
        $this->getPropertyIterator()->append([
            'zone_id' => new Type\Integer([
                'description' => t('ID зоны')
            ]),
            'banner_id' => new Type\Integer([
                'description' => t('ID баннера')
            ])
        ]);
        $this->addIndex(['zone_id', 'banner_id'], self::INDEX_UNIQUE);
    }
}