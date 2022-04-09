<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\Orm\Cargo;

use RS\Orm\OrmObject;
use RS\Orm\Request as OrmRequest;
use RS\Orm\Type;

/**
 * Orm-объект - одна коробка для справочника коробок
 */
class CargoPreset extends OrmObject
{
    protected static $table = 'order_cargo_preset';

    function _init()
    {
        parent::_init()->append([
            'site_id' => (new Type\CurrentSite()),
            'title' => (new Type\Varchar())
                ->setDescription(t('Название коробки')),
            'width' => (new Type\Integer)
                ->setDescription(t('Ширина в мм')),
            'height' => (new Type\Integer)
                ->setDescription(t('Высота в мм')),
            'dept' => (new Type\Integer)
                ->setDescription(t('Глубина в мм')),
            'weight' => (new Type\Integer)
                ->setDescription(t('Вес коробки, грамм')),
            'sortn' => (new Type\Integer)
                ->setDescription(t('Сортировочный индекс'))
                ->setVisible(false)
        ]);
    }

    function beforeWrite($flag)
    {
        if ($flag == self::INSERT_FLAG) {
            $this['sortn'] = OrmRequest::make()
                    ->select('MAX(sortn) as max')
                    ->from($this)
                    ->where([
                        'site_id' => $this['site_id'],
                    ])
                    ->exec()->getOneField('max', 0) + 1;
        }
    }
}