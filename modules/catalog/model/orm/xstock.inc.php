<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\Orm;
use \RS\Orm\Type;

/**
 * Orm остатка на складах
 * --/--
 * @property integer $product_id ID товара
 * @property integer $offer_id ID комплектации
 * @property integer $warehouse_id ID склада
 * @property float $stock Доступно
 * @property float $reserve Резерв
 * @property float $waiting Ожидание
 * @property float $remains Остаток
 * --\--
 */
class Xstock extends \RS\Orm\AbstractObject
{
    protected static
        $table = "product_x_stock";
    
    function _init()
    {
        $this->getPropertyIterator()->append([
            'product_id' => new Type\Integer([
                'description' => t('ID товара')
            ]),
            'offer_id' => new Type\Integer([
                'description' => t('ID комплектации'),
                'index' => true
            ]),
            'warehouse_id' => new Type\Integer([
                'description' => t('ID склада'),
                'index' => true
            ]),
            'stock' => new Type\Decimal([
                'description' => t('Доступно'),
                'maxLength' => 11,
                'decimal' => 3,
                'default' => 0
            ]),
            'reserve' => new Type\Decimal([
                'description' => t('Резерв'),
                'maxLength' => 11,
                'decimal' => 3,
                'default' => 0
            ]),
            'waiting' => new Type\Decimal([
                'description' => t('Ожидание'),
                'maxLength' => 11,
                'decimal' => 3,
                'default' => 0
            ]),
            'remains' => new Type\Decimal([
                'description' => t('Остаток'),
                'maxLength' => 11,
                'decimal' => 3,
                'default' => 0
            ]),
        ]);
        
        $this->addIndex(['product_id', 'offer_id', 'warehouse_id'], self::INDEX_UNIQUE);
    }
    
    /**
    * Вызывается после загрузки объекта
    * @return void
    */
    function afterObjectLoad()
    {
        // Приведение типов
        $this['stock'] = (float)$this['stock'];
    }
}

