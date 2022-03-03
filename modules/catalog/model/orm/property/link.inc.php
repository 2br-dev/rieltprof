<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Catalog\Model\Orm\Property;
use \RS\Orm\Type;

/**
 * Core-object - связь характеристик с товарами
 * --/--
 * @property integer $site_id ID сайта
 * @property integer $prop_id ID характеристики
 * @property integer $product_id ID товара
 * @property integer $group_id ID группы товаров
 * @property string $val_str Строковое значение
 * @property double $val_int Числовое значение
 * @property integer $val_list_id Списковое значение
 * @property integer $available Есть в наличии товары с такой характеристикой
 * @property integer $public Участие в фильтрах. Для group_id>0
 * @property integer $is_expanded Показывать всегда развернутым
 * @property string $xml_id Идентификатор товара в системе 1C
 * @property string $extra Дополнительное поле для данных
 * --\--
 */
class Link extends \RS\Orm\AbstractObject
{
    protected static
        $table = 'product_prop_link';
    
    function _init()
    {
        $this->getPropertyIterator()->append([
            'site_id' => new Type\CurrentSite(),
            'prop_id' => new Type\Integer([
                'description' => t('ID характеристики')
            ]),
            'product_id' => new Type\Integer([
                'description' => t('ID товара'),
                'maxLength' => '11',
            ]),
            'group_id' => new Type\Integer([
                'description' => t('ID группы товаров'),
                'maxLength' => '11',
            ]),
            'val_str' => new Type\Varchar([
                'description' => t('Строковое значение'),
                'maxLength' => '255',
            ]),
            'val_int' => new Type\Real([
                'description' => t('Числовое значение')
            ]),
            'val_list_id' => new Type\Integer([
                'description' => t('Списковое значение')
            ]),
            'available' => new Type\Integer([
                'description' => t('Есть в наличии товары с такой характеристикой'),
                'maxLength' => 1,
                'allowEmpty' => false,
                'default' => 1,
                'visible' => false
            ]),
            'public' => new Type\Integer([
                'description' => t('Участие в фильтрах. Для group_id>0'),
                'maxLength' => 1,
                'checkboxView' => [1,0]
            ]),
            'is_expanded' => new Type\Integer([
                'default' => 0,
                'allowEmpty' => false,
                'description' => t('Показывать всегда развернутым'),
                'hint' => t('Актуально для тем оформления, где используются сворачиваемые фильтры'),
                'maxLength' => 1,
                'checkboxView' => [1,0]
            ]),
            'xml_id' => new Type\Varchar([
                'maxLength' => '255',
                'description' => t('Идентификатор товара в системе 1C'),
            ]),
            'extra' =>  new Type\Varchar([
                'maxLength' => '255',
                'description' => t('Дополнительное поле для данных'),
                'visible' => false,
            ]),

        ]);
        
        $this
            ->addIndex(['site_id', 'product_id', 'group_id'], self::INDEX_UNIQUE)
            ->addIndex(['site_id', 'prop_id'], self::INDEX_KEY)
            ->addIndex(['product_id', 'prop_id', 'val_str', 'available'], self::INDEX_KEY)
            ->addIndex(['product_id', 'prop_id', 'val_int', 'available'], self::INDEX_KEY)
            ->addIndex(['product_id', 'prop_id', 'val_list_id', 'available'], self::INDEX_KEY)
            ->addIndex(['prop_id', 'val_list_id'], self::INDEX_KEY)
            ->addIndex(['group_id', 'public']);
    }
    
    /**
    * Заполняет данными объект в зависимости от типа данных
    * 
    * @param integer $product_id   - id товара
    * @param string|integer $value - значение характеристики
    * @param array $pdata          - массив с данными свойства
    * 
    * @return void
    */
    function fillData($product_id, $value, $pdata)
    {
       $this['prop_id']    = $pdata['id']; 
       $this['public']     = $pdata['public']; 
       $this['site_id']    = $pdata['site_id']; 
       $this['xml_id']     = $pdata['xml_id']; 
       $this['product_id'] = $product_id; 
       
       switch ($pdata['type']) {
           case Item::TYPE_NUMERIC:
           case Item::TYPE_BOOL: $this['val_int']  = $value; break;
           case item::TYPE_STRING: $this['val_str'] = $value; break;
           default: $this['val_list_id'] = $value;
       }
    }
    
    /**
    * Возвращает объект спискового значения характеристики
    * 
    * @return ItemValue
    */
    function getItemValue()
    {
        return new ItemValue($this['val_list_id']);
    }
    
    function beforeWrite($flag)
    {
        
        if ($this['val_list_id'] && !$this->isModified('val_str')) {
            //Если задан val_list_id, то дублируем строковое значение в val_str
            $this['val_str'] = ItemValue::getValueById($this['val_list_id']);
        }
    }
}

