<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model;

use RS\Module\AbstractModel\EntityList;
use RS\Orm\Request as OrmRequest;
use Shop\Model\Orm\Xregion;
use Shop\Model\Orm\Zone;

class ZoneApi extends EntityList
{
    function __construct()
    {
        parent::__construct(new Zone(), [
            'nameField' => 'title',
            'multisite' => true,
            'defaultOrder' => 'title'
        ]);
    }

    /**
     * Возвращает Зоны, которые соответствуют необходимому региону
     *
     * @param integer $region_id ID региона, области, края
     * @param integer|null $country_id ID страны
     * @param integer|null $city_id ID города
     * @return array
     */
    static public function getZonesByRegionId($region_id, $country_id = null, $city_id = null)
    {
        $q = OrmRequest::make()
            ->from(new Xregion())
            ->where('zone_id IS NOT NULL')
            ->where(['region_id' => $region_id]);

        if ($country_id) {
            $q->where(['region_id' => $country_id], null, 'OR');
        }

        if ($city_id) {
            $q->where(['region_id' => $city_id], null, 'OR');
        }

        return $q->exec()->fetchSelected(null, 'zone_id');
    }

    /**
     * Возвращает объект зоны по названию
     *
     * @param string $zone_title
     * @return Zone
     */
    static public function getZoneByTitle($zone_title)
    {
        return Zone::loadByWhere(['title' => $zone_title]);
    }
}
