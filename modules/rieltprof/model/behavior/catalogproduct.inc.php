<?php
namespace Rieltprof\Model\Behavior;

use Users\Model\Orm\User;

/**
* Объект - Расширения пользователя
*/
class CatalogProduct extends \RS\Behavior\BehaviorAbstract
{
    public function getAllAds()
    {
        $all = \RS\Orm\Request::make()
            ->from(new \Catalog\Model\Orm\Product())
            ->where([
                'public' => 1
            ])->exec()->fetchAll();
        return $all ? $all : false;
    }

    public function getProductPropValue($prop_id, $product_row)
    {
        $orm = $this->owner;
        $row = '_'.$product_row;
        $row_value = @unserialize($orm[$row]);
        $prop = new \Catalog\Model\Orm\Property\Item($prop_id);
        $values = $prop->getAllowedValues();
        if(is_array($row_value)){
            return $values[$row_value[0]];
        }
        return $values[$row_value];
    }

    public function dateFormat($format, $item)
    {
        $orm = $this->owner;
        $date_timestamp = strtotime($orm[$item]);
        return date($format, $date_timestamp);
    }

    public function getOwner()
    {
        $orm = $this->owner;
        $owner = \RS\Orm\Request::make()
            ->from(new \Users\Model\Orm\User())
            ->where([
                'id' => $orm['owner']
            ])->object();
        return $owner ? $owner : false;
    }
}

