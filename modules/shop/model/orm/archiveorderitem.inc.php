<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model\Orm;

use RS\Orm\AbstractObject;

/**
 * --/--
 * @property string $uniq ID в рамках одной корзины
 * @property string $type Тип записи товар, услуга, скидочный купон
 * @property string $entity_id ID объекта type
 * @property integer $offer Комплектация
 * @property string $multioffers Многомерные комплектации
 * @property float $amount Количество
 * @property string $title Название
 * @property string $extra Дополнительные сведения (сериализованные)
 * @property array $extra_arr Дополнительные сведения
 * @property integer $order_id ID заказа
 * @property string $barcode Артикул
 * @property string $sku Штрихкод
 * @property string $model Модель
 * @property double $single_weight Вес
 * @property float $single_cost Цена за единицу продукции
 * @property float $price Стоимость
 * @property float $profit Доход
 * @property float $discount Скидка
 * @property integer $unit_id Единица измерения
 * @property integer $sortn Порядок
 * --\--
 */
class ArchiveOrderItem extends AbstractObject
{
    protected static $table = 'archive_order_items';

    function _init()
    {
        $properties = $this->getProperties();

        foreach ((new OrderItem())->getProperties() as $property) {
            $properties->append([
                $property->getName() => $property,
            ]);
        }
    }
}
