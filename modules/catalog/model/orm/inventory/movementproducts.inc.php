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
 *  Таблица с товарами документов перемещения
 *
 * Class MovementProducts
 * @package Catalog\Model\Orm\inventory
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property string $title Название
 * @property integer $amount Количество
 * @property string $uniq uniq
 * @property integer $product_id id товара
 * @property integer $offer_id id комплектации
 * @property integer $document_id id документа
 * --\--
 */
class MovementProducts extends \RS\Orm\OrmObject
{
    protected static
        $table = 'document_movement_products'; //Имя таблицы в БД

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
            'amount' => new Type\Integer([
                'description' => t('Количество'),
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