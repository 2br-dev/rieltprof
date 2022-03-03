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
 * Объект - единица измерения
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property integer $code Код ОКЕИ
 * @property string $icode Международное сокращение
 * @property string $title Полное название единицы измерения
 * @property string $stitle Короткое обозначение
 * @property float $amount_step Шаг изменения количества товара в корзине
 * @property float $min_order_quantity Минимальное количество товара для заказа
 * @property float $max_order_quantity Максимальное количество товара для заказа
 * @property integer $sortn Сорт. номер
 * --\--
 */
class Unit extends \RS\Orm\OrmObject
{
    protected static
        $table = 'product_unit';
        
    function _init()
    {
        parent::_init()->append([
            'site_id' => new Type\CurrentSite(),
            'code' => new Type\Integer([
                'description' => t('Код ОКЕИ'),
            ]),
            'icode' => new Type\Varchar([
                'maxLength' => '25',
                'description' => t('Международное сокращение'),
            ]),
            'title' => new Type\Varchar([
                'maxLength' => '70',
                'description' => t('Полное название единицы измерения'),
            ]),
            'stitle' => new Type\Varchar([
                'maxLength' => '25',
                'description' => t('Короткое обозначение'),
            ]),
            'amount_step' => new Type\Decimal([
                'description' => t('Шаг изменения количества товара в корзине'),
                'maxLength' => 11,
                'decimal' => 3,
                'allowEmpty' => false,
                'default' => 1,
            ]),
            'min_order_quantity' => new Type\Decimal([
                'maxLength' => 11,
                'decimal' => 3,
                'mevisible' => true,
                'description' => t('Минимальное количество товара для заказа'),
                'hint' => t('Если пустое поле, то контроля не будет')
            ]),
            'max_order_quantity' => new Type\Decimal([
                'maxLength' => 11,
                'decimal' => 3,
                'mevisible' => true,
                'description' => t('Максимальное количество товара для заказа'),
                'hint' => t('Если пустое поле, то контроля не будет')
            ]),
            'sortn' => new Type\Integer([
                'description' => t('Сорт. номер'),
                'visible' => false,
            ]),
        ]);
    }
    
    function beforeWrite($flag)
    {
        if ($flag == self::INSERT_FLAG) {
            $this['sortn'] = \RS\Orm\Request::make()
                ->select('MAX(sortn) as next_sort')
                ->from($this)
                ->exec()->getOneField('next_sort', 0) + 1;
        }
    }
    
    /**
    * Вызывается после загрузки объекта
    * @return void
    */
    function afterObjectLoad()
    {
        // Приведение типов
        $this['amount_step'] = (float)$this['amount_step'];
    }
}

