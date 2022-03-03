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
 * Orm объект - ставка налога
 * --/--
 * @property integer $tax_id Название
 * @property integer $region_id ID региона
 * @property float $rate Ставка налога
 * --\--
 */
class TaxRate extends \RS\Orm\AbstractObject
{
    protected static
        $table = 'order_tax_rate';
    
    function _init()
    {
        $this->getPropertyIterator()->append([
            'tax_id' => new Type\Integer([
                'description' => t('Название')
            ]),
            'region_id' => new Type\Integer([
                'description' => t('ID региона')
            ]),
            'rate' => new Type\Decimal([
                'description' => t('Ставка налога'),
                'maxLength' => 12,
                'decimal' => 4
            ])
        ]);
        $this->addIndex(['tax_id', 'region_id'], self::INDEX_UNIQUE);
    }
}