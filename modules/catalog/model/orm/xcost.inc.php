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
 * --/--
 * @property integer $product_id ID товара
 * @property integer $cost_id ID цены
 * @property float $cost_val Рассчитанная цена в базовой валюте
 * @property float $cost_original_val Оригинальная цена товара
 * @property integer $cost_original_currency ID валюты оригинальной цены товара
 * --\--
 */
class Xcost extends \RS\Orm\AbstractObject
{
    protected static
        $table = "product_x_cost",
        $cache_currency = [];
    
    function _init()
    {
        $this->getPropertyIterator()->append([
            'product_id' => new Type\Integer([
                'description' => t('ID товара')
            ]),
            'cost_id' => new Type\Integer([
                'description' => t('ID цены')
            ]),
            'cost_val' => new Type\Decimal([
                'description' => t('Рассчитанная цена в базовой валюте'),
                'allowempty' => false,
                'maxlength' => 20,
                'decimal' => 2
            ]),
            'cost_original_val' => new Type\Decimal([
                'description' => t('Оригинальная цена товара'),
                'allowempty' => false,
                'maxlength' => 20,
                'decimal' => 2
            ]),
            'cost_original_currency' => new Type\Integer([
                'description' => t('ID валюты оригинальной цены товара'),
                'allowempty' => false,
                'default' => 0
            ])
        ]);
        
        $this->addIndex(['product_id', 'cost_id'], self::INDEX_UNIQUE);
    }
    
    /**
    * Заполняет текущий объект либо краткими данными, если $data - число, либо полными, если $data - массив
    * 
    * @param integer $cost_id - ID типа цены
    * @param integer $product_id - ID товара
    * @param mixed $data - цена или массив с ключами:
    * cost_original_val - цена в валюте
    * cost_original_currency - id валюты
    * 
    * @return void
    */
    function fillData($cost_id, $product_id, $data)
    {
        $this['cost_id']    = $cost_id;
        $this['product_id'] = $product_id;
               
        if (is_array($data)) {
            $data += [
                'cost_original_val' => 0,
                'cost_original_currency' => 0
            ];
            
            $curr = Currency::loadSingle($data['cost_original_currency']);
            
            $this['cost_original_val'] = $data['cost_original_val'];
            $this['cost_original_currency'] = $curr['id'] ? $data['cost_original_currency'] : 0;
            $this['cost_val'] = $curr['id'] ? \Catalog\Model\CurrencyApi::convertToBase($data['cost_original_val'], $curr) : $data['cost_original_val'];
        } else {
            $this['cost_original_val'] = $data;
            $this['cost_original_currency'] = 0;
            $this['cost_val'] = $data;
        }       
    }
}

