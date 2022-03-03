<?php

namespace rieltprof\Model;

use RS\Module\AbstractModel\EntityList;

/**
 * Класс для организации выборок ORM объекта.
 * В этом классе рекомендуется также реализовывать любые дополнительные методы, связанные с заявленной в конструкторе моделью
 */
class ParamsApi extends EntityList
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
    public static function getMaterialList($params){
        $config = \RS\Config\Loader::byModule('rieltprof');
        $prop = new \Catalog\Model\Orm\Property\Item($config['prop_material']);
        $values = $prop->getAllowedValues();
        return array_replace($params, $values);
    }
    /**
     * Получаем список значений характирстики Количество комнат (квартиры, новостройки)
     * @param $params
     * @return array
     */
    public static function getRoomsList($params){
        $config = \RS\Config\Loader::byModule('rieltprof');
        $prop = new \Catalog\Model\Orm\Property\Item($config['prop_rooms_list']);
        $values = $prop->getAllowedValues();
        return array_replace($params, $values);
    }
    /**
     * Получаем список значений характирстики Округ
     * @param $params
     * @return array
     */
    public static function getCountyList($params){
        $config = \RS\Config\Loader::byModule('rieltprof');
        $prop = new \Catalog\Model\Orm\Property\Item($config['prop_county']);
        $values = $prop->getAllowedValues();
        return array_replace($params, $values);
    }

    public static function getDistrictList($params)
    {
        $config = \RS\Config\Loader::byModule('rieltprof');
        $prop = new \Catalog\Model\Orm\Property\Item($config['prop_district']);
        $values = $prop->getAllowedValues();
        return array_replace($params, $values);
    }

    public static function getStateList($params)
    {
        $config = \RS\Config\Loader::byModule('rieltprof');
        $prop = new \Catalog\Model\Orm\Property\Item($config['prop_state']);
        $values = $prop->getAllowedValues();
        return array_replace($params, $values);
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

    public static function checkSquare($_this, $value, $error_text)
    {
        if ((empty($_this['square']) || !$value)) {
            return $error_text.' не должно быть пустым' ;
        }else {
            $check = $_this['square_kitchen'] + $_this['square_living'];
            if($_this['square'] < $check){
                return $error_text.' заполненно некорректно';
            }
        }
        return true;
    }

    public static function checkFlat($_this, $value, $error_text)
    {
        if ((empty($_this['flat']) || !$value)) {
            return $error_text.' не должно быть пустым' ;
        }else {
            if($_this['flat'] > $_this['flat_house']){
                return $error_text.' заполненно некорректно';
            }
        }
        return true;
    }

    public static function checkFlatHouse($_this, $value, $error_text)
    {
        if ((empty($_this['flat_house']) || !$value)) {
            return $error_text.' не должно быть пустым' ;
        }else {
            if($_this['flat_house'] < $_this['flat']){
                return $error_text.' заполненно некорректно';
            }
        }
        return true;
    }

    public static function checkFlatSquareKitchen($_this, $value, $error_text)
    {
        $config = \RS\Config\Loader::byModule('rieltprof');
        $prop = new \Catalog\Model\Orm\Property\Item($config['prop_rooms_list']);
        $values = $prop->getAllowedValues();

        if($values[$_this['rooms_list'][0]] == 'Студия'){
            return true;
        }elseif(!$value){
            return $error_text;
        }
        return true;
    }
}
