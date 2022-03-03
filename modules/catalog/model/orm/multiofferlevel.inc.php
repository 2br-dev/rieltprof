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
 * ORM Объект - уровень многомерной комплектации
 * --/--
 * @property integer $product_id id товара
 * @property integer $prop_id id характеристики
 * @property string $title Название уровня
 * @property integer $is_photo Представление в виде фото?
 * @property integer $sortn Индекс сортировки
 * --\--
 */
class MultiOfferLevel extends \RS\Orm\AbstractObject
{

    protected static
            $table = 'product_multioffer';    
            


    function _init()
    {

        $this->getPropertyIterator()->append([
                'product_id' => new Type\Integer([
                    'maxLength' => 11,
                    'description' => t('id товара'),
                ]),
                'prop_id' => new Type\Integer([
                    'maxLength' => 11,
                    'description' => t('id характеристики'),
                ]),
                'title' => new Type\Varchar([
                    'maxLength' => '255',
                    'description' => t('Название уровня'),
                ]),
                'is_photo' =>  new Type\Integer([
                    'maxLength' => '11',
                    'description' => t('Представление в виде фото?'),
                    'default' => 0
                ]),
                'sortn' => new Type\Integer([
                    'maxLength' => '11',
                    'default' => 0,
                    'description' => t('Индекс сортировки'),
                ]),


        ]);

        $this->addIndex(['product_id', 'prop_id'], self::INDEX_UNIQUE);
    }
    
    /**
    * Возвращает объект характеристики для уровня многомерной комплектации
    * 
    * @return Property\Item
    */
    function getPropertyItem()
    {
        if ($this['is_virtual']) {
            //Если это виртуальная многомерная комплектация, 
            //то пытаемся найти харантеристику по названию, иначе возвращаем виртуальную характеристику
            $fake_property = Property\Item::loadByWhere([
                'site_id' => \RS\Site\Manager::getSiteId(),
                'title' => $this['title'],
            ]);
            if (empty($fake_property['id'])) {
                $fake_property['type'] = Property\Item::TYPE_LIST;
                $fake_property['title'] = $this['title'];
            }
            return $fake_property;
        }
        return new Property\Item($this['prop_id']);
    }
    
}
