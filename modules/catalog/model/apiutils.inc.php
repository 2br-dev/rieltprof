<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model;

/**
 * Класс содержит API функции дополтельные для работы в системе в рамках задач по модулю каталога
 */
class ApiUtils
{

    /**
     * Возвращает секцию с дополнительными полями купить в один клик из конфига для внешнего API
     *
     */
    public static function getAdditionalBuyOneClickFieldsSection()
    {
        //Добавим доп поля для покупки в один клик корзины
        $click_fields_manager = \RS\Config\Loader::byModule('catalog')->getClickFieldsManager();
        $click_fields_manager->setErrorPrefix('clickfield_');
        $click_fields_manager->setArrayWrapper('clickfields');

        //Пройдёмся по полям
        $fields = [];
        foreach ($click_fields_manager->getStructure() as $field){
            if ($field['type'] == 'bool'){  //Если тип галочка
                $field['val'] = $field['val'] ? true : false;
            }
            $fields[] = $field;
        }

        return $fields;
    }

    /**
    * Добавляет секцию цены товарам, розничную и зачёркнутую
    * 
    * @param $list - список из объектов товаров
    */
    static function addProductCostValuesSection($list){
        $product = new \Catalog\Model\Orm\Product();    
        $product->getPropertyIterator()->append([
                'cost_values' => new \RS\Orm\Type\ArrayList([
                    'description' => t('Розничная и зачёркнутая цена товара'),
                    'appVisible' => true
                ])
        ]);
        foreach($list as $product) {         
            $product_cost = [];
            $product_cost['cost']            = $product->getCost(null, null, false);
            $product_cost['cost_format']     = \RS\Helper\CustomView::cost($product_cost['cost'], \Catalog\Model\CurrencyApi::getCurrentCurrency()->stitle);
            $product_cost['old_cost']        = $product->getOldCost(null, false);
            $product_cost['old_cost_format'] = \RS\Helper\CustomView::cost($product_cost['old_cost'], \Catalog\Model\CurrencyApi::getCurrentCurrency()->stitle);
            $product['cost_values']          = $product_cost;            
        }
        
        return $list;
    }
    
    /**
    * Расширяет объекты характеристик для фильтров. Добавляет секции для видимости в экспорты.
    * 
    */
    private static function extendFiltersObjects()
    {
        $prop_group = new \Catalog\Model\Orm\Property\Dir();
        $prop_group->getPropertyIterator()->append([
            'properties' => new \RS\Orm\Type\ArrayList([
                'description' => t('Характеристики со значениями'),
                'appVisible' => true
            ])
        ]);
        $prop_item = new \Catalog\Model\Orm\Property\Item();
        $prop_item->getPropertyIterator()->append([
            'allowed_values' => new \RS\Orm\Type\ArrayList([
                'description' => t('Характеристики со значениями'),
                'appVisible' => true
            ]),
            'sortn' => new \RS\Orm\Type\Integer([
                'maxLength' => '11',
                'description' => t('Сорт. индекс'),
                'appVisible' => true
            ]),
            'group_id' => new \RS\Orm\Type\Integer([
                'maxLength' => '11',
                'runtime' => true,
                'appVisible' => true
            ]),
            'public' => new \RS\Orm\Type\Integer([
                'runtime' => true,
                'appVisible' => true
            ]),
            'interval_from' => new  \RS\Orm\Type\Real([
                'description' => t('Минимальное значение'),
                'Attr' => [['size' => 8]],
                'appVisible' => true,
                'runtime' => true
            ]),
            'interval_to' => new  \RS\Orm\Type\Real([
                'description' => t('Максимальное значение'),
                'Attr' => [['size' => 8]],
                'appVisible' => true,
                'runtime' => true
            ]),
        ]);
        $prop_item_value = new \Catalog\Model\Orm\Property\ItemValue();
        $prop_item_value->getPropertyIterator()->append([
            'color' => new \RS\Orm\Type\Color([
                'description' => t('Цвет'),
                'appVisible' => true,
            ])
        ]);
    }
    
    /**
    * Подготавливает секцию с картинками
    * 
    * @param mixed $image_orm - объект картинки
    * @return array
    */
    static function prepareImagesSection($image_orm)
    {
        if ($image_orm instanceof \RS\Orm\Type\Image) {
            $data = [
                'original_url' => $image_orm->getLink(true),
                'big_url' => $image_orm->getUrl(1000, 1000, 'xy', true),
                'middle_url' => $image_orm->getUrl(600, 600, 'xy', true),
                'small_url' => $image_orm->getUrl(300, 300, 'xy', true),
                'micro_url' => $image_orm->getUrl(100, 100, 'xy', true),
                'nano_url' => $image_orm->getUrl(50, 50, 'xy', true),
            ];
        } else {
            $data = [
                'id' => $image_orm['id'],
                'title' => $image_orm['title'],
                'original_url' => $image_orm->getOriginalUrl(true),
                'big_url' => $image_orm->getUrl(1000, 1000, 'xy', true),
                'middle_url' => $image_orm->getUrl(600, 600, 'xy', true),
                'small_url' => $image_orm->getUrl(300, 300, 'xy', true),
                'micro_url' => $image_orm->getUrl(100, 100, 'xy', true),
                'nano_url' => $image_orm->getUrl(50, 50, 'xy', true),
            ];
        }
        return $data;
    }
    
    /**
    * Подготавливает секцию для списковых значений в виде картинок характеристики.
    * 
    * @param \Catalog\Model\Orm\Property\Item $prop - значение характеристики
    * @return \Catalog\Model\Orm\Property\Item
    */
    private static function preparePropertyImageSection($prop){
        //Посмотрим на допустимые значения
        $values = [];
        $allowed_values = $prop->getAllowedValuesObjects();
        foreach ($allowed_values as $allowed_value){
            /**
            * @var \Catalog\Model\Orm\Property\ItemValue $allowed_value
            */
            $value          = \ExternalApi\Model\Utils::extractOrm($allowed_value);
            if ($allowed_value['image']){
                $value['image'] = self::prepareImagesSection($allowed_value->__image);
            }
            $values[]       = $value;
        }
        $prop['allowed_values'] = $values;
        return $prop;
    }
    
    /**
    * Подготавливает секцию для списковых значений в виде цвета характеристики.
    * 
    * @param \Catalog\Model\Orm\Property\Item $prop - значение характеристики
    *
    * @return \Catalog\Model\Orm\Property\Item
    */
    private static function preparePropertyColorSection($prop){
        //Посмотрим на допустимые значения
        $values = [];
        $allowed_values = $prop->getAllowedValuesObjects();
        foreach ($allowed_values as $allowed_value){
            /**
            * @var \Catalog\Model\Orm\Property\ItemValue $allowed_value
            */
            $value          = \ExternalApi\Model\Utils::extractOrm($allowed_value);
            if ($allowed_value['image']){
                $value['image'] = self::prepareImagesSection($allowed_value->__image);    
            }
            $values[]       = $value;
        }
        $prop['allowed_values'] = $values;
        return $prop;
    }
    
    /**
    * Подготавливает секцию для списковых значений характеристики.
    * 
    * @param \Catalog\Model\Orm\Property\Item $prop - значение характеристики
    * @return \Catalog\Model\Orm\Property\Item
    */
    private static function preparePropertyListSection($prop){
        $prop['allowed_values'] = \ExternalApi\Model\Utils::extractOrmList($prop->getAllowedValuesObjects());
        return $prop;
    }
    
    /**
    * Подготавливает секцию для числовых значений характеристик.
    * 
    * @param \Catalog\Model\Orm\Property\Item $prop - значение характеристики
    * @return \Catalog\Model\Orm\Property\Item
    */
    private static function preparePropertyIntSection($prop){
        return $prop;
    }
    
    /**
    * Подготавливает секцию для строковых значений характеристик.
    * 
    * @param \Catalog\Model\Orm\Property\Item $prop - значение характеристики
    * @return \Catalog\Model\Orm\Property\Item
    */
    private static function preparePropertyStringSection($prop){
        if (!empty($prop['allowed_values'])){
            $values = [];
            foreach($prop['allowed_values'] as $value){
                $values[] = $value;
            }
            $prop['allowed_values'] = $values;
        }
        return $prop;
    }

    private static function preparePropertyBoolSection($prop)
    {
        return $prop;
    }
    
    /**
    * Преобразует характеристики для фильтров таким образом, чтобы появлялись секции для экспорта значений
    * 
    * @param array $prop_list - массив характеристик фильтров для преобразования
    *
    * @return array
    */
    static function prepareFiltersPropertyListSections($prop_list)
    {
        //Значения характеристик для фильтра
        $filters_list = [];
        //Расширим нужные объект для видимости секций
        self::extendFiltersObjects();
        
        foreach($prop_list as $item){
            $properties = [];
            foreach ($item['properties'] as $prop){
                $method_name  = "prepareProperty".$prop['type']."Section"; //Вызовем метод для обработки значений
                $property     = \ExternalApi\Model\Utils::extractOrm(self::$method_name($prop));   
                if ($property['type']!='int'){
                    unset($property['interval_from']);
                    unset($property['interval_to']);
                }
                $properties[] = $property;
            }
            //Добавим характеристики в группу
            $item['group']['properties'] = $properties; //Добавим преобразованные характеристики
            $filters_list[] = \ExternalApi\Model\Utils::extractOrm($item['group']);
        }
        
        return $filters_list;
    }
}