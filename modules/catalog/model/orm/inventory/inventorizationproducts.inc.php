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
 *  Таблица с товарами документов инвентаризации
 *
 * Class InventorizationProducts
 * @package Catalog\Model\Orm\inventory
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property string $title Название
 * @property integer $fact_amount Фактическое кол-во
 * @property integer $calc_amount Расчетное кол-во
 * @property integer $dif_amount Разница
 * @property string $uniq uniq
 * @property integer $product_id id товара
 * @property integer $offer_id id комплектации
 * @property integer $document_id id документа
 * --\--
 */
class InventorizationProducts extends \RS\Orm\OrmObject
{
    protected static
        $table = 'document_inventory_products'; //Имя таблицы в БД

    /**
     * Инициализирует свойства ORM объекта
     *
     * @return void
     */
    function _init()
    {
        parent::_init()->append([
            'title' => new Type\Varchar([
                'maxLength' => '250',
                'description' => t('Название'),
            ]),
            'fact_amount' => new Type\Integer([
                'description' => t('Фактическое кол-во'),
                'visible' => false,
            ]),
            'calc_amount' => new Type\Integer([
                'description' => t('Расчетное кол-во'),
                'visible' => false,
            ]),
            'dif_amount' => new Type\Integer([
                'description' => t('Разница'),
                'visible' => false,
            ]),
            'uniq' => new Type\Varchar([
                'maxLength' => '250',
                'description' => t('uniq'),
            ]),
            'product_id' => new Type\Integer([
                'maxLength' => '250',
                'description' => t('id товара'),
            ]),
            'offer_id' => new Type\Integer([
                'maxLength' => '250',
                'description' => t('id комплектации'),
            ]),
            'document_id' => new Type\Integer([
                'maxLength' => '250',
                'description' => t('id документа'),
            ]),
        ]);
    }
}