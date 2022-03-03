<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model\Orm;

use RS\Orm\OrmObject;
use RS\Orm\Type;

/**
 * ORM объект товара в возврате товаров
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id 
 * @property string $uniq Уникальный идентификатор
 * @property integer $return_id Id возврата
 * @property integer $entity_id Id товара
 * @property integer $offer Номер комплектации
 * @property integer $amount Количество товара
 * @property float $cost Цена товара
 * @property string $barcode Артикул
 * @property string $model Модель
 * @property string $title Название
 * --\--
 */
class ProductsReturnOrderItem extends OrmObject
{
    protected static $table = 'order_products_return_item';

    function _init() //инициализация полей класса. конструктор метаданных
    {
        return parent::_init()->append([
            'site_id' => new Type\Integer(),
            'uniq' => new Type\Varchar([
                'maxLength' => '20',
                'description' => t('Уникальный идентификатор'),
            ]),
            'return_id' => new Type\Integer([
                'maxLength' => '20',
                'index' => true,
                'description' => t('Id возврата'),
            ]),
            'entity_id' => new Type\Integer([
                'maxLength' => '11',
                'index' => true,
                'description' => t('Id товара'),
            ]),
            'offer' => new Type\Integer([
                'maxLength' => '11',
                'description' => t('Номер комплектации'),
            ]),
            'amount' => new Type\Integer([
                'maxLength' => '20',
                'description' => t('Количество товара'),
            ]),
            'cost' => new Type\Decimal([
                'maxLength' => '20',
                'description' => t('Цена товара'),
            ]),
            'barcode' => new Type\Varchar([
                'description' => t('Артикул'),
            ]),
            'model' => new Type\Varchar([
                'description' => t('Модель'),
            ]),
            'title' => new Type\Varchar([
                'description' => t('Название'),
            ]),
        ]);
    }

    /**
     * Возвращает объект заявления на возврат
     *
     * @return ProductsReturn
     */
    function getReturn()
    {
        static $cache = [];

        $return_id = $this['return_id'];

        if (!isset($cache[$return_id])) {
            $cache[$return_id] = new ProductsReturn($return_id);
        }
        return $cache[$return_id];
    }
}
