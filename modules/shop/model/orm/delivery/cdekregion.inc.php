<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model\Orm\Delivery;

use RS\Orm\AbstractObject;
use RS\Orm\Type;

/**
 * ORM объект описывает один регион из справочника СДЭК.
 * Справочник регионов доставки СДЭК теперь находится в БД, он будет обновляться либо
 * по кнопке в настройках модуля Магазин, либо по крону
 */
class CdekRegion extends AbstractObject
{
    protected static $table = 'delivery_cdek_regions';

    function _init()
    {
        $this->getPropertyIterator()->append([
            'code' => (new Type\Integer())
                ->setDescription(t('Код населенного пункта СДЭК')),
            'city' => (new Type\Varchar())
                ->setDescription(t('Название населенного пункта'))
                ->setMaxLength(100),
            'fias_guid' => (new Type\Varchar())
                ->setDescription(t('Уникальный идентификатор ФИАС населенного пункта'))
                ->setMaxLength(36),
            'kladr_code' => (new Type\Varchar())
                ->setDescription(t('Код КЛАДР населенного пункта')),
            'country' => (new Type\Varchar())
                ->setDescription(t('Название страны населенного пункта'))
                ->setMaxLength(50),
            'region' => (new Type\Varchar())
                ->setDescription(t('Название региона населенного пункта'))
                ->setMaxLength(100),
            'sub_region' => (new Type\Varchar())
                ->setDescription(t('Название района региона населенного пункта'))
                ->setMaxLength(50),
            'processed' => (new Type\Integer())
                ->setDescription(t('Флаг "обработан"'))
                ->setVisible(false),
        ]);

        $this->addIndex(['country', 'region', 'sub_region', 'city'], self::INDEX_UNIQUE);
    }
}
