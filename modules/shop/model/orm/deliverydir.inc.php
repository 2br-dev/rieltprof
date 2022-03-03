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
 * Способ категория доставки
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property string $title Название
 * @property integer $sortn Сорт. номер
 * --\--
 */
class DeliveryDir extends \RS\Orm\OrmObject
{
    protected static
        $table = 'order_delivery_dir';
        
    function _init()
    {
        parent::_init()->append([
            t('Основные'),
                'site_id' => new Type\CurrentSite(),
                'title' => new Type\Varchar([
                    'maxLength' => '255',
                    'description' => t('Название'),
                ]),
                'sortn' => new Type\Integer([
                    'description' => t('Сорт. номер'),
                    'visible' => false
                ])
        ]);
    }

    /**
     * Действия перед записью объекта
     *
     * @param string $flag - insert или update
     * @return void
     * @throws \RS\Db\Exception
     */
    function beforeWrite($flag)
    {
        if ($flag == self::INSERT_FLAG) {
            $this['sortn'] = \RS\Orm\Request::make()
                    ->select('MAX(sortn) as max')
                    ->from($this)
                    ->exec()->getOneField('max', 0) + 1;
        }
    }

    /**
     * Удаление
     *
     * @return bool
     * @throws \RS\Db\Exception
     */
    function delete()
    {
        $ret = parent::delete();
        if ($ret) {
            //Обновим родителя у доставки
            \RS\Orm\Request::make()->update()
                ->from(new Delivery())->asAlias('I')
                ->set([
                    'parent_id' => 0
                ])
                ->where('I.parent_id=#parent', ['parent' => $this['id']])
                ->exec();
        }
        return $ret;
    }
}
