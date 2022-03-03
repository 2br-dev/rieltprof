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
* Возвращает комплектации товара по ID товара
*/
class GetRecommendedList extends \ExternalApi\Model\AbstractMethods\AbstractGet
{
    const RIGHT_LOAD = 1;


    protected $token_require = false;
    protected $costs_loaded = false; //Цены были уже загружены?
    protected $current_currency; //Текущая валюта
    protected $list_products;
    protected $dirs_x_id = [];
    /**
     * @var \Catalog\Model\Api $product_api
     */
    protected $product_api;

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
            self::RIGHT_LOAD => t('Загрузка списка объектов')
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
     * Возвращает категорию по идентификатору
     *
     * @param integer $id - id категории
     * @return array
     */
    protected function getDirByID($id)
    {
        if (!isset($this->dirs_x_id[$id])){
            $dir = new \Catalog\Model\Orm\Dir($id);
            if ($dir['image']){
                \Catalog\Model\ApiUtils::prepareImagesSection($dir->__image);
            }
            $this->dirs_x_id[$id] = $dir;
        }
        return $this->dirs_x_id[$id];
    }

    /**
     * Добавяляет сведения по категориям
     *
     * @param array $recommended - массив рекомендованных товаров
     *
     * @return array
     */
    protected function addDirData($recommended)
    {
        //Расширим товар
        $product = new \Catalog\Model\Orm\Product();
        $product->getPropertyIterator()->append([
            'specdirs' => new \RS\Orm\Type\ArrayList([
                'description' => t('Спец. категории'),
                'appVisible' => true
            ]),
            'category' => new \RS\Orm\Type\MixedType([
                'description' => t('Главная директория'),
                'appVisible' => true
            ]),
        ]);
        //Разберём спец. категории
        $specdirs = $product->getSpecDirs();
        $recommended = $this->product_api->addProductsDirs($recommended); //Добавим сведения по категориям
        if (!empty($recommended)){
            foreach ($recommended as $product){
                if (!empty($specdirs)){
                    /**
                     * @var \Catalog\Model\Orm\Product $product
                     */
                    $arr = [];
                    foreach ($specdirs as $specdir){
                        if (in_array($specdir['id'], $product['xspec'])){
                            if ($specdir['image']){
                                \Catalog\Model\ApiUtils::prepareImagesSection($specdir->__image);
                            }
                            $arr[] = \ExternalApi\Model\Utils::extractOrm($specdir);
                        }
                    }
                    $product['specdirs'] = $arr;
                    $product['category'] = \ExternalApi\Model\Utils::extractOrm($this->getDirByID($product['maindir']));
                }
            }
        }

        return $recommended;
    }

    /**
     * Добавляет секцию с изображениями к товару
     *
     * @param array $recommended - массив рекомендованных товаров
     * @return array
     */
    protected function addImageData($recommended)
    {
        //Загружаем изображения
        if (in_array('image', $this->method_params['sections'])) {
            $this->product_api->getElement()->getPropertyIterator()->append([
                'image' => new \RS\Orm\Type\ArrayList([
                    'description' => t('Изображения'),
                    'appVisible' => true
                ])
            ]);
            $recommended = $this->product_api->addProductsPhotos($recommended);
            foreach($recommended as &$product) {
                $images = [];
                foreach($product->getImages() as $image) {
                    $images[] = \Catalog\Model\ApiUtils::prepareImagesSection($image);
                }
                $product['image'] = $images;
            }
        }

        return $recommended;
    }

    /**
     * Добавляет секцию с ценами к товару
     *
     * @param array $recommended - массив рекомендованных товаров
     * @return array
     */
    protected function addCostData($recommended)
    {
        //Загружаем цены
        if (in_array('cost', $this->method_params['sections'])) {
            if (!$this->costs_loaded){
                $recommended = $this->product_api->addProductsCost($recommended);
                $this->costs_loaded  = true;
            }
        } else {
            $this->product_api->getElement()->__excost->setVisible(false, 'app');
        }
        return $recommended;
    }

    /**
     * Добавляет секцию с характеристиками к товару
     *
     * @param array $recommended - массив рекомендованных товаров
     * @return array
     */
    protected function addPropertyData($recommended)
    {
        if (in_array('property', $this->method_params['sections'])) {
            $this->product_api->getElement()->getPropertyIterator()->append([
                'property_values' => new \RS\Orm\Type\ArrayList([
                    'description' => t('Характеристики товара'),
                    'appVisible' => true
                ])
            ]);

            $recommended = $this->product_api->addProductsProperty($recommended);

            foreach($recommended as $product){
                /**
                 * @var \Catalog\Model\Orm\Product $product
                 */
                $product_props = [];
                if ($product['properties']){
                    foreach($product['properties'] as $data) {
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
                }
                $product['property_values'] = $product_props;
            }

        }
        return $recommended;
    }

    /**
     * Добавляет секцию с сопутствующими товарами к товару
     *
     * @param array $recommended - массив рекомендованных товаров
     * @return array
     */
    protected function addConcomitantData($recommended)
    {
        if (in_array('concomitant', $this->method_params['sections'])) {
            $this->product_api->getElement()->getPropertyIterator()->append([
                'concomitant' => new \RS\Orm\Type\ArrayList([
                    'description' => t('Сопутствующие товары'),
                    'appVisible' => true
                ])
            ]);

            foreach($recommended as &$product) {
                /**
                 * @var \Catalog\Model\Orm\Product $product
                 */
                $concomitants = $product->getConcomitant();

                $product['concomitant'] = [];
                if (!empty($concomitants)){
                    $concomitants = $this->product_api->addProductsCost($concomitants);
                    $concomitants = $this->product_api->addProductsPhotos($concomitants);
                    \Catalog\Model\ApiUtils::addProductCostValuesSection($concomitants);

                    $arr = [];
                    foreach($concomitants as $concomitant){
                        /**
                         * @var \Catalog\Model\Orm\Product $concomitant
                         */
                        $concomitant['image'] = \Catalog\Model\ApiUtils::prepareImagesSection($concomitant->getMainImage());
                        $arr[] = \ExternalApi\Model\Utils::extractOrm($concomitant);
                    }
                    $product['concomitant'] = $arr;
                }
            }
        }
        return $recommended;
    }
    
    /**
    * Возвращает рекоммендованные товара по ID товара
    * 
    * @param string $token Авторизационный токен
    * @param integer $product_id ID товара
    * @param integer $return_hidden возвращать скрытые товары. Если 1 - да, 0 - нет
    * @param integer $add_dir_recommended добавлять рекоммендуемые из основной категории товара. Если 1 - да, 0 - нет
    * @param integer $only_in_stock флаг показывать только те что в наличии. Если 1 - да, 0 - нет
    * @param array $sections Дополнительные секции, которые должны быть представлены в результате.
    * Возможные значения:
    * <b>image</b> - изображения товара
    * <b>cost</b> - цены товара
    * <b>property</b> - характеристики товаров
    * <b>concomitant</b> - сопутствующие товары
    * <b>unit</b> - единица измерения
    * <b>current_currency</b> - текущая валюта
    *
    * 
    * @example GET /api/methods/product.getrecommendedlist?token=2bcbf947f5fdcd0f77dc1e73e73034f5735de486&product_id=1
    * Ответ
    * <pre>
    * {
    *    "response": {
    *        "cost": {
    *            "1": {
    *                "id": "1",
    *                "title": "Розничная",
    *                "type": "manual"
    *            },
    *            ...
    *        },
    *        "unit": [],
    *        "current_currency": {
    *            "id": "1",
    *            "title": "RUB",
    *            "stitle": "р.",
    *            "is_base": "1",
    *            "ratio": "1",
    *            "public": "1",
    *            "default": "1"
    *       },
    *       "list": [
    *               {
    *                   "id": "687",
    *                   "title": "Платье Love &amp; Light",
    *                   "alias": "plate-love-light",
    *                   "short_description": "",
    *                   "description": "<p>Платье Love &amp; Light решено в зеленом цвете. Модель выполнена из плотного эластичного материала. Детали: сложный крой лифа, потайная застежка на пуговицах, два кармана в боковых швах. К модели прилагается пояс.</p>",
    *                   "barcode": "LO790EWBCK91",
    *                   "weight": "0",
    *                   "dateof": "2014-05-14 12:30:39",
    *                   "excost": {
    *                       "1": {
    *                           "product_id": "687",
    *                           "cost_id": "1",
    *                           "cost_val": "2990.00",
    *                           "cost_original_val": "2990.00",
    *                           "cost_original_currency": "1",
    *                           "old_cost_original_val": "0"
    *                       }
    *                       ...
    *                   },
    *                   "unit": "0",
    *                   "min_order": null,
    *                   "max_order": null,
    *                   "public": "1",
    *                   "no_export": "0",
    *                   "xdir": [
    *                       "226",
    *                       "232"
    *                   ],
    *                   "maindir": "226",
    *                   "xspec": [
    *                       "232"
    *                   ],
    *                   "reservation": "default",
    *                   "brand_id": "0",
    *                   "rating": "0.0",
    *                   "group_id": null,
    *                   "xml_id": null,
    *                   "sku": null,
    *                   "offer_caption": "Размер",
    *                   "meta_title": "",
    *                   "meta_keywords": "",
    *                   "meta_description": "",
    *                   "tax_ids": "category",
    *                   "is_productset": "0",
    *                   "sets_arr": null,
    *                   "image": [
    *                       {
    *                           "id": "2862",
    *                           "title": null,
    *                           "original_url": "http://mega.readyscript.ru/storage/photo/original/f/tr03lhgbwb67cue.jpg",
    *                           "big_url": "http://mega.readyscript.ru/storage/photo/resized/xy_1000x1000/f/tr03lhgbwb67cue_5a997cb9.jpg",
    *                           "middle_url": "http://mega.readyscript.ru/storage/photo/resized/xy_600x600/f/tr03lhgbwb67cue_47d0b7db.jpg",
    *                           "small_url": "http://mega.readyscript.ru/storage/photo/resized/xy_300x300/f/tr03lhgbwb67cue_2e095119.jpg",
    *                           "micro_url": "http://mega.readyscript.ru/storage/photo/resized/xy_100x100/f/tr03lhgbwb67cue_cf193be7.jpg",
    *                           "nano_url": "http://mega.readyscript.ru/storage/photo/resized/xy_50x50/f/tr03lhgbwb67cue_ac99231c.jpg"
    *                       },
    *                       ...
    *                   ],
    *                   "concomitant": [
    *                   {
    *                       "id": "41",
    *                       "title": "Планшет ViewSonic ViewPad 10",
    *                       "alias": "planshet-viewsonic-viewpad-10",
    *                       "short_description": "Ноутбуки серии специально разрабатывались для игр....",
    *                       "description": "Ноутбук (англ. notebook — блокнот, блокнотный ПК) — портативный персональный компьютер...",
    *                       "barcode": "22257-DS4UTN2",
    *                       "weight": "0",
    *                       "dateof": "2013-08-07 11:02:54",
    *                       "excost": null,
    *                       "unit": "0",
    *                       "min_order": null,
    *                       "public": "1",
    *                       "xdir": null,
    *                       "maindir": "16",
    *                       "xspec": null,
    *                       "reservation": "default",
    *                       "brand_id": "0",
    *                       "rating": "0.0",
    *                       "group_id": null,
    *                       "xml_id": null,
    *                       "offer_caption": "",
    *                       "meta_title": "",
    *                       "meta_keywords": "",
    *                       "meta_description": "",
    *                       "tax_ids": "category"
    *                   },
    *                   ...
    *                   ],
    *                   "specdirs": [
    *                       {
    *                           "id": "232",
    *                           "name": "Новые поступления",
    *                           "alias": "novye-postupleniya",
    *                           "parent": "0",
    *                           "public": "0",
    *                           "image": null,
    *                           "weight": "0",
    *                           "description": "",
    *                           "meta_title": "",
    *                           "meta_keywords": "",
    *                           "meta_description": "",
    *                           "product_meta_title": null,
    *                           "product_meta_keywords": null,
    *                           "product_meta_description": null,
    *                           "in_list_properties_arr": false,
    *                           "export_name": null,
    *                           "tax_ids": "1",
    *                           "mobile_background_color": "#fff",
    *                           "mobile_tablet_background_image": null,
    *                           "mobile_tablet_icon": null
    *                        }
    *                        ...
    *                    ],
    *                    "category": {
    *                        "id": "226",
    *                        "name": "Одежда, обувь",
    *                        "alias": "odezhda-obuv",
    *                        "parent": "0",
    *                        "public": "1",
    *                        "image": "",
    *                        "weight": "0",
    *                        "description": "",
    *                        "meta_title": "",
    *                        "meta_keywords": "",
    *                        "meta_description": "",
    *                        "product_meta_title": "",
    *                        "product_meta_keywords": "",
    *                        "product_meta_description": "",
    *                        "in_list_properties_arr": [],
    *                        "export_name": "",
    *                        "tax_ids": "1",
    *                        "mobile_background_color": "#b28efa",
    *                        "mobile_tablet_background_image": {
    *                            "original_url": "http://mega.readyscript.ru/storage/system/original/2d2cd90de56ee189130dda79f79ce05d.png",
    *                            "big_url": "http://mega.readyscript.ru/storage/system/resized/xy_1000x1000/2d2cd90de56ee189130dda79f79ce05d_315d23fe.png",
    *                            "middle_url": "http://mega.readyscript.ru/storage/system/resized/xy_600x600/2d2cd90de56ee189130dda79f79ce05d_4378ab72.png",
    *                            "small_url": "http://mega.readyscript.ru/storage/system/resized/xy_300x300/2d2cd90de56ee189130dda79f79ce05d_2aa14db0.png",
    *                            "micro_url": "http://mega.readyscript.ru/storage/system/resized/xy_100x100/2d2cd90de56ee189130dda79f79ce05d_cbb1274e.png",
    *                            "nano_url": "http://mega.readyscript.ru/storage/system/resized/xy_50x50/2d2cd90de56ee189130dda79f79ce05d_4a33269b.png"
    *                         },
    *                        "mobile_tablet_icon": {
    *                            "original_url": "http://mega.readyscript.ru/storage/system/original/0e290db0f9f48b310546cb9d97b7efe7.png",
    *                           "big_url": "http://mega.readyscript.ru/storage/system/resized/xy_1000x1000/0e290db0f9f48b310546cb9d97b7efe7_9eced40c.png",
    *                           "middle_url": "http://mega.readyscript.ru/storage/system/resized/xy_600x600/0e290db0f9f48b310546cb9d97b7efe7_a697b871.png",
    *                           "small_url": "http://mega.readyscript.ru/storage/system/resized/xy_300x300/0e290db0f9f48b310546cb9d97b7efe7_cf4e5eb3.png",
    *                           "micro_url": "http://mega.readyscript.ru/storage/system/resized/xy_100x100/0e290db0f9f48b310546cb9d97b7efe7_2e5e344d.png",
    *                           "nano_url": "http://mega.readyscript.ru/storage/system/resized/xy_50x50/0e290db0f9f48b310546cb9d97b7efe7_526c25fa.png"
    *                       }
    *                   },
    *                   "property_values": [
    *                       {
    *                           "id": "346",
    *                           "title": "Ширина",
    *                           "type": "int",
    *                           "unit": "",
    *                           "parent_id": "0",
    *                           "int_hide_inputs": "0",
    *                           "hidden": "0",
    *                           "no_export": "0",
    *                          "value": "40",
    *                          "text_value": "40",
    *                          "parent_title": null
    *                      },
    *                      ...
    *                  ],
    *                  "cost_values": {
    *                      "cost": "2990.00",
    *                      "cost_format": "2 990 р.",
    *                      "old_cost": "0.00",
    *                      "old_cost_format": "0 р."
    *                   }
    *              },
    *           ]
    *       }
    * }
    * </pre>
    * @throws \RS\Exception
    * @return array
    */
    function process($token = null,
                     $product_id,
                     $return_hidden = 0,
                     $add_dir_recommended = 1,
                     $only_in_stock = 1,
                     $sections = ['image', 'cost', 'property', 'concomitant', 'unit', 'current_currency'])
    {
        //Загруженный товар
        $product  = new \Catalog\Model\Orm\Product($product_id);
        $response = parent::process($token, $product_id);
        
        if ($product['id']){
            $this->product_api = new \Catalog\Model\Api();
            $this->current_currency = \Catalog\Model\CurrencyApi::getCurrentCurrency(); //Текущая валюта
            unset($response['response']['product']); //Удалим конкретную информацию о товаре

            //Получим рекоммендуемые нашего товара
            $recommended = $product->getRecommended($return_hidden, $add_dir_recommended);
            if (!empty($recommended)){
                foreach ($recommended as $k=>$recommended_product){
                    if ($only_in_stock && ($recommended_product['num'] <= 0)){
                        unset($recommended[$k]);
                    }
                }

                $recommended = $this->addImageData($recommended);
                $recommended = $this->addDirData($recommended);
                $recommended = $this->addCostData($recommended);
                $recommended = $this->addPropertyData($recommended);
                $recommended = $this->addConcomitantData($recommended);

                $units = [];
                $costs = [];
                foreach ($recommended as $recommended_product){
                    $costs += (array)$recommended_product['excost'];
                    $units[$product['unit']] = true;
                }

                //Загружаем справочник цен
                if (in_array('cost', $sections)) {
                    if ($costs) {
                        $cost_api = new \Catalog\Model\CostApi();
                        $cost_api->setFilter('id', array_keys($costs), 'in');
                        $cost_objects = $cost_api->getList();
                    } else {
                        $cost_objects = [];
                    }
                    $response['response']['cost'] = \ExternalApi\Model\Utils::extractOrmList($cost_objects, 'id');
                }

                //Загружаем единицы измерения
                if (in_array('unit', $sections)) {
                    $default_unit = (int)\RS\Config\Loader::byModule($this)->default_unit;

                    $unit_api = new \Catalog\Model\UnitApi();
                    $unit_api->setFilter('id', array_merge(array_keys($units), [$default_unit]), 'in');
                    $unit_objects = \ExternalApi\Model\Utils::extractOrmList($unit_api->getList(), 'id');

                    //Загружаем единицу измерения по умолчанию.
                    if ($default_unit && isset($units[0])) {
                        $unit_objects[0] = $unit_objects[$default_unit];
                    }
                    $response['response']['unit'] = $unit_objects;
                }

                //Валюта товара
                if (in_array('current_currency', $sections)) {
                    $response['response']['current_currency'] = \ExternalApi\Model\Utils::extractOrm(\Catalog\Model\CurrencyApi::getCurrentCurrency());
                }

                \Catalog\Model\ApiUtils::addProductCostValuesSection( $recommended );

                $list = Utils::extractOrmList( $recommended );

                foreach ($list as &$product){
                    $product['short_description'] = nl2br($product['short_description']);
                    $product['description']       = Utils::prepareHTML($product['description']);
                }

                $response['response']['list'] = $list;
            }else{
                $response['response']['list'] = [];
            }
        }else{
            $response['response']['no_product'] = true;
            $response['response']['list'] = [];
        }
        
        return $response;
    }
}