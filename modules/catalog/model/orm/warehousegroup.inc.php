<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Catalog\Model\Orm;

use RS\Orm\OrmObject;
use RS\Orm\Request as OrmRequest;
use RS\Orm\Type;

/**
 * ORM Объект - группа складов
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property string $alias Идентификатор (англ.яз)
 * @property string $title Название
 * @property string $short_description Короткое описание
 * @property integer $sortn Индекс сортировки
 * --\--
 */
class WareHouseGroup extends OrmObject
{
    protected static $table = 'warehouse_group';

    public function _init()
    {
        parent::_init()->append([
            t('Основные'),
            'site_id' => new Type\CurrentSite(),
            'alias' => new Type\Varchar([
                'maxLength' => '50',
                'description' => t('Идентификатор (англ.яз)'),
                'Checker' => ['chkPattern', t('Идентификатор может содержать только латинские буквы, цифры и символ нижнего подчёркивания.'), '/^[_a-zA-Z0-9]+$/'],
            ]),
            'title' => new Type\Varchar([
                'description' => t('Название'),
                'Checker' => ['chkEmpty', t('Укажите название группы')],
            ]),
            'short_description' => new Type\Varchar([
                'description' => t('Короткое описание'),
            ]),
            'sortn' => new Type\Integer([
                'description' => t('Индекс сортировки'),
                'visible' => false
            ]),
        ]);

        $this->addIndex(['site_id', 'alias'], self::INDEX_UNIQUE);
    }

    /**
     * Функция срабатывает перед записью объекта
     *
     * @param string $flag - флаг текущего действия. update или insert.
     * @return void
     * @throws \RS\Db\Exception
     */
    public function beforeWrite($flag)
    {
        if ($flag == self::INSERT_FLAG) {
            $this['sortn'] = OrmRequest::make()
                ->from(new WareHouseGroup())
                ->select("MAX(sortn)+1 as sortn")
                ->where([
                    'site_id' => $this['site_id'],
                ])
                ->exec()->getOneField('sortn', 0);
        }
    }

    /**
     * Удаление группы складов
     */
    function delete()
    {
        OrmRequest::make()
            ->update(new WareHouse())
            ->set(['group_id' => 0])
            ->where(['group_id' => $this['id']])
            ->exec();

        return parent::delete(); //Удаляем текущий объект из базы.
    }
}
