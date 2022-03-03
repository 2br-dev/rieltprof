<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\Orm\Inventory;

/**
 *  Таблица с количеством архивных товаров
 *
 * Class StartNum
 * @package Catalog\Model\Orm\Inventory
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
class StartNum extends \Catalog\Model\Orm\Xstock
{
    protected static
        $table = 'document_products_start_num'; //Имя таблицы в БД
}