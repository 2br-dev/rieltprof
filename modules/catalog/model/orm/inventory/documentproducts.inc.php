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
 *  Таблица с товарами документов: оприходование, списание, резервирование, ожидание
 *
 * Class DocumentProducts
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
class DocumentProducts extends \RS\Orm\OrmObject
{
    protected static
        $table = 'document_products'; //Имя таблицы в БД

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
            'amount' => new Type\Varchar([
                'maxLength' => '250',
                'description' => t('Количество'),
            ]),
            'uniq' => new Type\Varchar([
                'maxLength' => '250',
                'description' => t('Уникальный Идентификатор'),
            ]),
            'product_id' => new Type\Integer([
                'maxLength' => '250',
                'description' => t('Id товара'),
            ]),
            'offer_id' => new Type\Integer([
                'maxLength' => '250',
                'description' => t('Id комплектации'),
            ]),
            'warehouse' => new Type\Integer([
                'maxLength' => '250',
                'description' => t('Id склада'),
            ]),
            'document_id' => new Type\Integer([
                'maxLength' => '250',
                'description' => t('Id документа'),
                'index' => true
            ]),
        ]);
        $this->addIndex(['product_id']);
        $this->addIndex(['product_id', 'offer_id', 'warehouse']);
    }
}