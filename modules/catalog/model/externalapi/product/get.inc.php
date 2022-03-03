<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\ExternalApi\Product;
use ExternalApi\Model\Utils;

/**
* Возвращает товар по ID
*/
class Get extends \ExternalApi\Model\AbstractMethods\AbstractGet
{
    const
        RIGHT_LOAD = 1,
        RIGHT_COST_LOAD = 2;
    
    protected
        $token_require = false,
        $current_currency; //Текущая валюта
    
    /**
    * Возвращает комментарии к кодам прав доступа
    * 
    * @return [
    *     КОД => КОММЕНТАРИЙ,
    *     КОД => КОММЕНТАРИЙ,
    *     ...
    * ]
    */
    public function getRightTitles()
    {
        return [
            self::RIGHT_LOAD => t('Загрузка объекта'),
            self::RIGHT_COST_LOAD => t('Загрузка полного списка цен товаров')
        ];
    }
    
    /**
    * Возвращает ORM объект с которым работаем
    * 
    */
    public function getOrmObject()
    {
        return new \Catalog\Model\Orm\Product();
    }
    
    
    /**
    * Возвращает товар по ID
    * 
    * @param string $token Авторизационный токен
    * @param integer $product_id ID товара
    * @param array $sections Секции с данными, которые следует включить в ответ. Возможные значения:
    * <b>image</b> - изображения
    * <b>cost</b> - цены
    * <b>cost_values</b> - цены по умолчанию и зачёркнутая
    * <b>recommended</b> - рекомендуемые товары
    * <b>concomitant</b> - сопутствующие товары
    * <b>current_currency</b> - текущая валюта
    * 
    * @example GET api/methods/product.get?token=2bcbf947f5fdcd0f77dc1e73e73034f5735de486&product_id=1
    * Ответ
    * <pre>
    * {
    *     "response": {
    *         "product": {
    *             "id": "1",
    *             "title": "Моноблок Acer Aspire Z5763",
    *             "alias": "Monoblok-Acer-Aspire-Z5763",
    *             "short_description": "Ноутбуки с оптимальным соотношением цены и возможностей. ...",
    *             "description": "Ноутбук (англ. notebook &mdash; блокнот, блокнотный ПК) &mdash; портативный персональный компьютер...",
    *             "barcode": "PW.SFNE2.033",
    *             "weight": "0",
    *             "dateof": "2013-08-06 06:08:05",
    *             "excost": null,
    *             "unit": "0",
    *             "min_order": "0",
    *             "public": "1",
    *             "xdir": null,
    *             "maindir": "5",
    *             "xspec": null,
    *             "reservation": "default",
    *             "brand_id": "4",
    *             "rating": "0.0",
    *             "group_id": "",
    *             "xml_id": null,
    *             "offer_caption": "",
    *             "meta_title": "",
    *             "meta_keywords": "",
    *             "meta_description": "",
    *             "tax_ids": "category",
    *             "image": [
    *                 {
    *                     "original_url": "http://full.readyscript.local/storage/photo/original/a/46s7ye2cobjx5j6.jpg",
    *                     "big_url": "http://full.readyscript.local/storage/photo/resized/xy_1000x1000/a/46s7ye2cobjx5j6_ded27759.jpg",
    *                     "small_url": "http://full.readyscript.local/storage/photo/resized/xy_300x300/a/46s7ye2cobjx5j6_7aa365e2.jpg"
    *                 }
    *             ],
    *             "xcost": [
    *                 {
    *                     "product_id": "1",
    *                     "cost_id": "1",
    *                     "cost_val": "50500.00",
    *                     "cost_original_val": "50500.00",
    *                     "cost_original_currency": "1"
    *                 },
    *                 {
    *                     "product_id": "1",
    *                     "cost_id": "2",
    *                     "cost_val": "52120.00",
    *                     "cost_original_val": "52120.00",
    *                     "cost_original_currency": "1"
    *                 },
    *                 {
    *                     "product_id": "1",
    *                     "cost_id": "11",
    *                     "cost_val": "0.00",
    *                     "cost_original_val": "0.00",
    *                     "cost_original_currency": "1"
    *                 }
    *             ],
    *             "cost_values": {
    *                   "cost": "16500.0 руб.",
    *                   "cost_format": 16 500,
    *                   "old_cost"  18500.0,
    *                   "old_cost_format": "18 500 руб."
    *             },  
    *             "property_values": [
    *                     {
    *                        "id": "76",
    *                        "title": "Аксессуары",
    *                        "hidden": "0",
    *                        "list": [
    *                           {
    *                              "id": "951",
    *                              "title": "Назначение",
    *                              "type": "list",
    *                              "unit": "",
    *                              "unit_export": null,
    *                              "name_for_export": null,
    *                              "parent_id": "76",
    *                              "int_hide_inputs": "0",
    *                              "hidden": "0",
    *                              "no_export": "0",
    *                              "value": "",
    *                              "text_value": "",
    *                              "parent_title": "Аксессуары"
    *                           },
    *                           ...
    *                       ]
    *                     },
    *                     ...
    *                  ]
    *             "recommended": [
    *                 {
    *                     "id": "41",
    *                     "title": "Планшет ViewSonic ViewPad 10",
    *                     "alias": "planshet-viewsonic-viewpad-10",
    *                     "short_description": "Ноутбуки серии специально разрабатывались для игр.",
    *                     "description": "Ноутбук (англ. notebook — блокнот, блокнотный ПК) — портативный персональный компьютер",
    *                     "barcode": "22257-DS4UTN2",
    *                     "weight": "0",
    *                     "dateof": "2013-08-07 11:02:54",
    *                     "excost": null,
    *                     "unit": "0",
    *                     "min_order": null,
    *                     "cost_values": {
    *                           "cost": "16500.0 руб.",
    *                           "cost_format": 16 500,
    *                           "old_cost"  18500.0,
    *                           "old_cost_format": "18 500 руб."
    *                     },
    *                     "public": "1",
    *                     "xdir": null,
    *                     "maindir": "16",
    *                     "xspec": null,
    *                     "reservation": "default",
    *                     "brand_id": "0",
    *                     "rating": "0.0",
    *                     "group_id": null,
    *                     "xml_id": null,
    *                     "offer_caption": "",
    *                     "meta_title": "",
    *                     "meta_keywords": "",
    *                     "meta_description": "",
    *                     "tax_ids": "category"
    *                 }
    *             ],
    *             "concomitant": [
    *                 {
    *                     "id": "41",
    *                     "title": "Планшет ViewSonic ViewPad 10",
    *                     "alias": "planshet-viewsonic-viewpad-10",
    *                     "short_description": "Ноутбуки серии специально разрабатывались для игр....",
    *                     "description": "Ноутбук (англ. notebook — блокнот, блокнотный ПК) — портативный персональный компьютер...",
    *                     "barcode": "22257-DS4UTN2",
    *                     "weight": "0",
    *                     "dateof": "2013-08-07 11:02:54",
    *                     "excost": null,
    *                     "unit": "0",
    *                     "min_order": null,
    *                     "public": "1",
    *                     "xdir": null,
    *                     "maindir": "16",
    *                     "xspec": null,
    *                     "reservation": "default",
    *                     "brand_id": "0",
    *                     "rating": "0.0",
    *                     "group_id": null,
    *                     "xml_id": null,
    *                     "offer_caption": "",
    *                     "meta_title": "",
    *                     "meta_keywords": "",
    *                     "meta_description": "",
    *                     "tax_ids": "category"
    *                 }
    *             ]
    *         },
    *         "cost": {
    *             "1": {
    *                 "id": "1",
    *                 "title": "Розничная",
    *                 "type": "manual"
    *             },
    *             "2": {
    *                 "id": "2",
    *                 "title": "Зачеркнутая цена",
    *                 "type": "manual"
    *             },
    *             "11": {
    *                 "id": "11",
    *                 "title": "Типовое соглашение с клиентом",
    *                 "type": "manual"
    *             }
    *         },
    *         "currency": {
    *             "1": {
    *                 "id": "1",
    *                 "title": "RUB",
    *                 "stitle": "р.",
    *                 "is_base": "1",
    *                 "ratio": "1",
    *                 "public": "1",
    *                 "default": "1"
    *             }
    *         },
    *         "current_currency": {
    *                "id": "1",
    *                "title": "RUB",
    *                "stitle": "р.",
    *                "is_base": "1",
    *                "ratio": "1",
    *                "public": "1",
    *                "default": "1"
    *         }
    *     }
    * }
    * </pre>
    * @return array
    */
    function process($token = null, $product_id, $sections = ['image', 'cost', 'recommended', 'concomitant', 'property', 'current_currency'])
    {
        $response = parent::process($token, $product_id);
        $this->current_currency = \Catalog\Model\CurrencyApi::getCurrentCurrency(); //Текущая валюта
        
        if (isset($response['response']['product'])){
            $response['response']['product']['short_description'] = nl2br($response['response']['product']['short_description']);    
            $response['response']['product']['description'] = Utils::prepareHTML($response['response']['product']['description']);
        }
        
        //Загружаем изображения
        if (in_array('image', $sections)) {
            foreach($this->object->getImages() as $image) {
                $response['response']['product']['image'][] = \Catalog\Model\ApiUtils::prepareImagesSection($image);
            }
        }
        
        //Загружаем цены
        if (in_array('cost', $sections)) {
            $this->object->fillCost();
            if (!$this->checkAccessError(self::RIGHT_COST_LOAD)) {
                $currency_ids = [];
                $cost_ids     = [];
                foreach($this->object->excost as $cost_id => $data) {
                    $response['response']['product']['xcost'][] = $data;
                    $currency_ids[] = $data['cost_original_currency'];
                    $cost_ids[] = $data['cost_id'];
                }
                
                //Загружаем справочник цен
                if ($cost_ids) {
                    $cost_api = new \Catalog\Model\CostApi();
                    $cost_api->setFilter('type', \Catalog\Model\Orm\Typecost::TYPE_MANUAL);
                    $cost_api->setFilter('id', $cost_ids, 'in');
                    foreach($cost_api->getList() as $cost) {
                        $response['response']['cost'][$cost['id']] = \ExternalApi\Model\Utils::extractOrm($cost);
                    }
                }
                
                //Загружаем справочник валют
                if ($currency_ids) {
                    $currency_api = new \Catalog\Model\CurrencyApi();
                    $currency_api->setFilter('id', $currency_ids, 'in');
                    foreach($currency_api->getList() as $currency) {
                        $response['response']['currency'][$currency['id']] = \ExternalApi\Model\Utils::extractOrm($currency);
                    }
                }    
            }
            
            //Подгружает только цены По умолчанию и зачеркнутую
            \Catalog\Model\ApiUtils::addProductCostValuesSection([$this->object]);
            $response['response']['product']['cost_values'] = $this->object['cost_values'];
        }
        
        
        
        
        //Загружаем характеристикики
        if (in_array('property', $sections)) {
            $this->object->getPropertyIterator()->append([
                'property_values' => new \RS\Orm\Type\ArrayList([
                    'description' => t('Характеристики товара'),
                    'appVisible' => true
                ])
            ]);
            
            $this->object->fillProperty();
            
            $product_props = [];
            foreach($this->object['properties'] as $data) {
                if (!$data['group']['hidden']) {
                    $group = \ExternalApi\Model\Utils::extractOrm($data['group']); //Группа характеристик
                    $group['list'] = [];
                    foreach($data['properties'] as $prop_id => $prop) {
                        if (!$prop['hidden']) {
                            /**
                             * @var \Catalog\Model\Orm\Property\Item $prop
                             */
                            $prop_data = \ExternalApi\Model\Utils::extractOrm($prop);
                            $prop_data['value'] = $prop->textView();
                            $prop_data['text_value'] = trim($prop_data['value'] . " " . $prop_data['unit']);
                            $prop_data['parent_title'] = $group['title'];
                            $group['list'][] = $prop_data;
                        }
                    }
                    $product_props[] = $group;
                }
            }
            
            $response['response']['product']['property_values'] = $product_props;
        }        
        
        //Загружаем рекомендуемые товары
        if (in_array('recommended', $sections)) {
            $recommended = $this->object->getRecommended();
            \Catalog\Model\ApiUtils::addProductCostValuesSection($recommended);
            foreach($recommended as $product) {
                $response['response']['product']['recommended'][] = \ExternalApi\Model\Utils::extractOrm($product);
            }
        }
        
        //Загружаем сопутствующие товары
        if (in_array('concomitant', $sections)) {
            $concomitant = $this->object->getConcomitant();
            \Catalog\Model\ApiUtils::addProductCostValuesSection($concomitant);
            foreach($concomitant as $product) {
                $response['response']['product']['concomitant'][] = \ExternalApi\Model\Utils::extractOrm($product);
            }
        }
        
        //Валюта товара
        if (in_array('current_currency', $sections)) {      
            $result['response']['current_currency'] = \ExternalApi\Model\Utils::extractOrm(\Catalog\Model\CurrencyApi::getCurrentCurrency());
        }
        
        return $response;
    }
}