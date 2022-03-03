<?php

namespace rieltprof\Model;

use RS\Module\AbstractModel\EntityList;

/**
 * Класс для организации выборок ORM объекта.
 * В этом классе рекомендуется также реализовывать любые дополнительные методы, связанные с заявленной в конструкторе моделью
 */
class DistrictApi extends EntityList
{
    function __construct()
    {
        parent::__construct(new \Rieltprof\Model\Orm\District(),[
            'multisite' => false,
            'nameField' => 'title',
            'defaultOrder' => 'id']);
    }


    public static function getArea($params){
        $config = \RS\Config\Loader::byModule('rieltprof');
        $prop = new \Catalog\Model\Orm\Property\Item($config['prop_county']);
        $values = $prop->getAllowedValues();
        return array_merge($params, $values);
    }
}
