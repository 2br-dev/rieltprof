<?php

namespace rieltprof\Config;

use RS\Orm\ConfigObject;
use RS\Orm\Type;

/**
 * Класс конфигурации модуля
 */
class File extends ConfigObject
{
    public function _init()
    {
        parent::_init()->append([
            t('Характеристики'),
                'prop_county' => new Type\Integer([
                    'description' => t('Характеристика - округ'),
                    'maxLength' => 11,
                    'list' =>array(['\Catalog\Model\PropertyApi', 'staticSelectList'], array(0 => t('не выбрано')))
                ]),
                'prop_district' => new Type\Integer([
                    'description' => t('Характеристика - район'),
                    'maxLength' => 11,
                    'list' =>array(['\Catalog\Model\PropertyApi', 'staticSelectList'], array(0 => t('не выбрано')))
                ]),
                'prop_street' => new Type\Integer([
                    'description' => t('Характеристика - улица'),
                    'maxLength' => 11,
                    'list' =>array(['\Catalog\Model\PropertyApi', 'staticSelectList'], array(0 => t('не выбрано')))
                ]),
                'prop_house' => new Type\Integer([
                    'description' => t('Характеристика - дом'),
                    'maxLength' => 11,
                    'list' =>array(['\Catalog\Model\PropertyApi', 'staticSelectList'], array(0 => t('не выбрано')))
                ]),
                'prop_liter' => new Type\Integer([
                    'description' => t('Характеристика - литер'),
                    'maxLength' => 11,
                    'list' =>array(['\Catalog\Model\PropertyApi', 'staticSelectList'], array(0 => t('не выбрано')))
                ]),
                'prop_rooms' => new Type\Integer([
                    'description' => t('Характеристика - количество комнат'),
                    'maxLength' => 11,
                    'list' =>array(['\Catalog\Model\PropertyApi', 'staticSelectList'], array(0 => t('не выбрано')))
                ]),
                'prop_rooms_list' => new Type\Integer([
                    'description' => t('Характеристика - количество комнат (для квартир и новостроек)'),
                    'maxLength' => 11,
                    'list' =>array(['\Catalog\Model\PropertyApi', 'staticSelectList'], array(0 => t('не выбрано')))
                ]),
                'prop_isolated' => new Type\Integer([
                    'description' => t('Характеристика - Все комнаты изолированы'),
                    'maxLength' => 11,
                    'list' =>array(['\Catalog\Model\PropertyApi', 'staticSelectList'], array(0 => t('не выбрано')))
                ]),
                'prop_split_wc' => new Type\Integer([
                    'description' => t('Характеристика - Раздельный санузел'),
                    'maxLength' => 11,
                    'list' =>array(['\Catalog\Model\PropertyApi', 'staticSelectList'], array(0 => t('не выбрано')))
                ]),
                'prop_material' => new Type\Integer([
                    'description' => t('Характеристика - Материал стен'),
                    'maxLength' => 11,
                    'list' =>array(['\Catalog\Model\PropertyApi', 'staticSelectList'], array(0 => t('не выбрано')))
                ]),
                'prop_year' => new Type\Integer([
                    'description' => t('Характеристика - Год постройки'),
                    'maxLength' => 11,
                    'list' =>array(['\Catalog\Model\PropertyApi', 'staticSelectList'], array(0 => t('не выбрано')))
                ]),
                'prop_state' => new Type\Integer([
                    'description' => t('Характеристика - Состояние'),
                    'maxLength' => 11,
                    'list' =>array(['\Catalog\Model\PropertyApi', 'staticSelectList'], array(0 => t('не выбрано')))
                ]),
                'prop_square' => new Type\Integer([
                    'description' => t('Характеристика - Площадь'),
                    'maxLength' => 11,
                    'list' =>array(['\Catalog\Model\PropertyApi', 'staticSelectList'], array(0 => t('не выбрано')))
                ]),
                'prop_square_kitchen' => new Type\Integer([
                    'description' => t('Характеристика - Площадь кухни'),
                    'maxLength' => 11,
                    'list' =>array(['\Catalog\Model\PropertyApi', 'staticSelectList'], array(0 => t('не выбрано')))
                ]),
                'prop_square_living' => new Type\Integer([
                    'description' => t('Характеристика - Площадь жилая'),
                    'maxLength' => 11,
                    'list' =>array(['\Catalog\Model\PropertyApi', 'staticSelectList'], array(0 => t('не выбрано')))
                ]),
                'prop_flat' => new Type\Integer([
                    'description' => t('Характеристика - Этаж'),
                    'maxLength' => 11,
                    'list' =>array(['\Catalog\Model\PropertyApi', 'staticSelectList'], array(0 => t('не выбрано')))
                ]),
                'prop_flat_house' => new Type\Integer([
                    'description' => t('Характеристика - Этажность дома'),
                    'maxLength' => 11,
                    'list' =>array(['\Catalog\Model\PropertyApi', 'staticSelectList'], array(0 => t('не выбрано')))
                ]),
                'prop_is_first' => new Type\Integer([
                    'description' => t('Характеристика - Первый этаж?'),
                    'maxLength' => 11,
                    'list' =>array(['\Catalog\Model\PropertyApi', 'staticSelectList'], array(0 => t('не выбрано')))
                ]),
                'prop_is_last' => new Type\Integer([
                    'description' => t('Характеристика - Последний этаж?'),
                    'maxLength' => 11,
                    'list' =>array(['\Catalog\Model\PropertyApi', 'staticSelectList'], array(0 => t('не выбрано')))
                ]),
                'prop_quickly' => new Type\Integer([
                    'description' => t('Характеристика - Срочно'),
                    'maxLength' => 11,
                    'list' =>array(['\Catalog\Model\PropertyApi', 'staticSelectList'], array(0 => t('не выбрано')))
                ]),
                'prop_mark' => new Type\Integer([
                    'description' => t('Характеристика - Закладка'),
                    'maxLength' => 11,
                    'list' =>array(['\Catalog\Model\PropertyApi', 'staticSelectList'], array(0 => t('не выбрано')))
                ]),
                'prop_only_cash' => new Type\Integer([
                    'description' => t('Характеристика - Только наличные'),
                    'maxLength' => 11,
                    'list' =>array(['\Catalog\Model\PropertyApi', 'staticSelectList'], array(0 => t('не выбрано')))
                ]),
                'prop_mortgage' => new Type\Integer([
                    'description' => t('Характеристика - Ипотека'),
                    'maxLength' => 11,
                    'list' =>array(['\Catalog\Model\PropertyApi', 'staticSelectList'], array(0 => t('не выбрано')))
                ]),
                'prop_breakdown' => new Type\Integer([
                    'description' => t('Характеристика - Нужна разбивка по сумме?'),
                    'maxLength' => 11,
                    'list' =>array(['\Catalog\Model\PropertyApi', 'staticSelectList'], array(0 => t('не выбрано')))
                ]),
                'prop_encumbrance' => new Type\Integer([
                    'description' => t('Характеристика - Обременение банка'),
                    'maxLength' => 11,
                    'list' =>array(['\Catalog\Model\PropertyApi', 'staticSelectList'], array(0 => t('не выбрано')))
                ]),
                'prop_child' => new Type\Integer([
                    'description' => t('Характеристика - Несовершеннолетние дети/опека'),
                    'maxLength' => 11,
                    'list' =>array(['\Catalog\Model\PropertyApi', 'staticSelectList'], array(0 => t('не выбрано')))
                ]),
                'prop_remodeling' => new Type\Integer([
                    'description' => t('Характеристика - Перепланировка'),
                    'maxLength' => 11,
                    'list' =>array(['\Catalog\Model\PropertyApi', 'staticSelectList'], array(0 => t('не выбрано')))
                ]),
                'prop_remodeling_legalized' => new Type\Integer([
                    'description' => t('Характеристика - Перепланировка узаконена?'),
                    'maxLength' => 11,
                    'list' =>array(['\Catalog\Model\PropertyApi', 'staticSelectList'], array(0 => t('не выбрано')))
                ]),
                'prop_exclusive' => new Type\Integer([
                    'description' => t('Характеристика - Чистый эксклюзив'),
                    'maxLength' => 11,
                    'list' =>array(['\Catalog\Model\PropertyApi', 'staticSelectList'], array(0 => t('не выбрано')))
                ]),
                'prop_advertise' => new Type\Integer([
                    'description' => t('Характеристика - От себя рекламирую в интернете'),
                    'maxLength' => 11,
                    'list' =>array(['\Catalog\Model\PropertyApi', 'staticSelectList'], array(0 => t('не выбрано')))
                ]),
                'prop_cost_product' => new Type\Integer([
                    'description' => t('Характеристика - Цена'),
                    'maxLength' => 11,
                    'list' =>array(['\Catalog\Model\PropertyApi', 'staticSelectList'], array(0 => t('не выбрано')))
                ]),
                'prop_cost_one' => new Type\Integer([
                    'description' => t('Характеристика - Цена за кв. м.'),
                    'maxLength' => 11,
                    'list' =>array(['\Catalog\Model\PropertyApi', 'staticSelectList'], array(0 => t('не выбрано')))
                ]),
                'prop_cost_rent' => new Type\Integer([
                    'description' => t('Характеристика - Цена аренды в мес.'),
                    'maxLength' => 11,
                    'list' =>array(['\Catalog\Model\PropertyApi', 'staticSelectList'], array(0 => t('не выбрано')))
                ]),
                'prop_land_area' => new Type\Integer([
                    'description' => t('Характеристика - Площадь участка (сот.)'),
                    'maxLength' => 11,
                    'list' =>array(['\Catalog\Model\PropertyApi', 'staticSelectList'], array(0 => t('не выбрано')))
                ]),
        ]);
    }

    public function dateFormat($item, $format)
    {
        $date_timestamp = strtotime($item);
        return date($format, $date_timestamp);
    }

    /*
     * Форматирование стоимости
     *  $sep - разделитель разрядов
     */
    public function formatCost($item, $sep, $cut = true)
    {
        if($cut){
            $end = substr($item, -3);
            if($end == '000'){
                return number_format((int)substr($item,0, -3), 0, '', $sep) . 'т.';
            }
        }
        return number_format((int)$item, 0, '', $sep);
    }

    /**
     * Получить объект пользователя по id
     * @param $id
     * @return \Users\Model\Orm\User
     */
    public function getUserById($id){
        return new \Users\Model\Orm\User($id);
    }

    /**
     * Получаем общее количество объявления в системе
     * @return int
     */
    public function getCountAllAds()
    {
        return \RS\Orm\Request::make()
                ->select('COUNT(*)')
                ->from(new \Catalog\Model\Orm\Product())
                ->where([
                    'public' => 1
                ])
                ->count();
    }

    /**
     * Получаем общее количество пользователей в системе
     * @return int
     */
    public function getCountAllUsers()
    {
        return \RS\Orm\Request::make()
            ->select('COUNT(*)')
            ->from(new \Users\Model\Orm\User())
            ->count();
    }

    /**
     * Проверка объявления на актуальность (не более 30 дней)
     * @param $ad
     * @param int $day
     * @return bool
     */
    public function isActualAd($ad, $day = 30)
    {
        /**
         * @var \Catalog\Model\Orm\Product $ad
         */
        $formated_date = explode(' ', $ad['dateof']); // отрезаем от даты добавления часовую часть

        if($formated_date[0] == $ad['actual_on_date']){
            $timestamp = strtotime($formated_date[0]);
        }else {
            $timestamp = strtotime($ad['actual_on_date']);
        }
        $end_timestamp = strtotime('+'.$day.'day', $timestamp);
        $today = time();
        if($end_timestamp < $today){
            return false;
        }
        return true;
    }

    /**
     * Получаем все катигории в которых есть объявления
     * @return array
     */
    public function getUniqCategoriesAllAds()
    {
        $categories = \RS\Orm\Request::make()
            ->select('DISTINCT `maindir`')
            ->from(new \Catalog\Model\Orm\Product())
            ->exec()->fetchSelected(null, 'maindir');
        $result = [];
        foreach ($categories as $key => $value){
            $dir_obj = new \Catalog\Model\Orm\Dir($value);
            $result[$key]['parent'] = $dir_obj->getParentDir()->name;
            $result[$key]['name'] = $dir_obj->name;
            $result[$key]['id'] = $value;
        }
        return $result;
    }

    /**
     * Получаем все объявления из категории по id
     * @param $id
     * @return \RS\Orm\AbstractObject[]
     */
    public function getAllAdsByCategoryId($id)
    {
        $ads = \RS\Orm\Request::make()
            ->from(new \Catalog\Model\Orm\Product())
            ->where([
                'maindir' => $id
            ])->objects();
        return $ads;
    }

    /**
     * Склонение существительных после числительных.
     *
     * @param string $value Значение
     * @param array $words Массив вариантов, например: array('товар', 'товара', 'товаров')
     * @param bool $show Включает значение $value в результирующею строку
     * @return string
     */
    function num_word($value, $words, $show = true)
    {
        $num = $value % 100;
        if ($num > 19) {
            $num = $num % 10;
        }

        $out = ($show) ?  $value . ' ' : '';
        switch ($num) {
            case 1:  $out .= $words[0]; break;
            case 2:
            case 3:
            case 4:  $out .= $words[1]; break;
            default: $out .= $words[2]; break;
        }

        return $out;
    }
}
