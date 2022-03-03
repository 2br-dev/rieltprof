<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\Orm;
use \RS\Orm\Type;

/**
 * Причина отмены заказов
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property string $title Название статуса
 * @property string $alias Псевдоним
 * @property integer $sortn Порядок сортировки
 * --\--
 */
class SubStatus extends \RS\Orm\OrmObject
{
    protected static
        $table = 'order_substatus';

    function _init()
    {
        parent::_init()->append([
            'site_id' => new Type\CurrentSite(),
            'title' => new Type\Varchar([
                'description' => t('Название статуса')
            ]),
            'alias' => new Type\Varchar([
                'description' => t('Псевдоним')
            ]),
            'sortn' => new Type\Integer([
                'description' => t('Порядок сортировки'),
                'visible' => false
            ])
        ]);

        $this->addIndex(['site_id', 'alias'], self::INDEX_UNIQUE);
    }

    function beforeWrite($flag)
    {
        if ($flag == self::INSERT_FLAG) {
            $this['sortn'] = \RS\Orm\Request::make()
                    ->select('MAX(sortn) as max')
                    ->from($this)
                    ->where([
                        'site_id' => $this->__site_id->get()
                    ])
                    ->exec()->getOneField('max', 0) + 1;
        }
    }
}