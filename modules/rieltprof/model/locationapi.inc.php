<?php

namespace rieltprof\Model;

use RS\Module\AbstractModel\EntityList;

/**
 * Класс для организации выборок ORM объекта.
 * В этом классе рекомендуется также реализовывать любые дополнительные методы, связанные с заявленной в конструкторе моделью
 */
class LocationApi extends EntityList
{
    function __construct()
    {
        parent::__construct(new \Rieltprof\Model\Orm\District(),[
            'multisite' => false,
            'nameField' => 'title',
            'defaultOrder' => 'id']);
    }

    /**
     * Получаем список значений характирстики Округ
     * @param $params
     * @return array
     */
    public static function getCounty($params){
        $config = \RS\Config\Loader::byModule('rieltprof');
        $prop = new \Catalog\Model\Orm\Property\Item($config['prop_county']);
        $values = $prop->getAllowedValues();
        return array_merge($params, $values);
    }

    public static function getDistrict($params)
    {
        $config = \RS\Config\Loader::byModule('rieltprof');
        $prop = new \Catalog\Model\Orm\Property\Item($config['prop_district']);
        $values = $prop->getAllowedValues();
        return array_merge($params, $values);
    }

    public static function checkEmpty($_this, $value, $error_text)
    {
        if(is_array($value)){
            if (($value[0] == 0)) {
                return $error_text;
            }
        }else{
            if (($value == 0)) {
                return $error_text;
            }
        }
        return true;
    }
}
