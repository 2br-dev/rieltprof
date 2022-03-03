<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Catalog\Model\Orm\Property;
use \RS\Orm\Type;

/**
 * Класс объектов - группа характеристик
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property string $title Название
 * @property integer $hidden Не отображать в карточке товара
 * @property integer $sortn Сорт. номер
 * --\--
 */
class Dir extends \RS\Orm\OrmObject
{
    protected static
        $table = 'product_prop_dir';
        
    function _init()
    {
        parent::_init()->append([
            'site_id' => new Type\CurrentSite(),
            'title' => new Type\Varchar([
                'description' => t('Название')
            ]),
            'hidden' => new Type\Integer([
                'description' => t('Не отображать в карточке товара'),
                'checkboxView' => [1,0],
                'maxLength' => 1,
                'default' => 0
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
    */
    function delete()
    {
        $ret = parent::delete();
        if ($ret) {
            \RS\Orm\Request::make()->delete('I, L')
            ->from(new Item())->asAlias('I')
            ->leftjoin(new Link(), 'I.id = L.prop_id', 'L')
            ->where('I.parent_id="#parent"', ['parent' => $this['id']])
            ->exec();
        }
        return $ret;
    }
    
    function getChildren()
    {
        return \RS\Orm\Request::make()
            ->from(new \Catalog\Model\Orm\Property\Item)
            ->where(['parent_id' => (int)$this->id])
            ->objects();
    }
}

