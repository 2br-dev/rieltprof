<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\Orm\inventory;
use \RS\Orm\Type;

/**
 *  таблица с товарами архивных документов
 *
 * Class DocumentProductsArchive
 * @package Catalog\Model\Orm\inventory
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property string $title Название
 * @property string $amount Количество
 * @property string $uniq Уникальный Идентификатор
 * @property integer $product_id Id товара
 * @property integer $offer_id Id комплектации
 * @property integer $warehouse Id склада
 * @property integer $document_id Id документа
 * --\--
 */
class DocumentProductsArchive extends \Catalog\Model\Orm\Inventory\DocumentProducts
{
    protected static
        $table = 'document_products_archive'; //Имя таблицы в БД

}