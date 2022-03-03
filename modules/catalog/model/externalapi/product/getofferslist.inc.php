<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\ExternalApi\Product;

/**
* Возвращает комплектации товара по ID товара
*/
class GetOffersList extends \ExternalApi\Model\AbstractMethods\AbstractGet
{
    const
        RIGHT_LOAD = 1;
    
    protected $token_require = false;
    protected $costs_loaded = false; //Цены были уже загружены?
    protected $current_currency; //Текущая валюта
    /**
     * @var \Catalog\Model\Orm\WareHouse[] $warehouses
     */
    protected $warehouses_by_id;

    /**
     * GetOffersList constructor.
     */
    function __construct()
    {
        parent::__construct();
        //Соберем склады
        $this->warehouses_by_id = \RS\Orm\Request::make()
                                ->from(new \Catalog\Model\Orm\WareHouse())
                                ->where([
                                    'site_id' => \RS\Site\Manager::getSiteId()
                                ])
                                ->objects(null, 'id');
    }

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
            self::RIGHT_LOAD => t('Загрузка списка объектов'),
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
    * Возвращает тип комплектаций товара. Всего 4 - ('none', 'offers', 'multioffers', 'offers + multioffers', 'virtual multioffers')
    *
    * @param \Catalog\Model\Orm\Product $product - объект товара
    *
    * @return string
    */
    protected function getProductOfferType($product)
    {
        if ($product->isVirtualMultiOffersUse()){ //Если есть виртуальные многомерные комплектации
            return 'virtual multioffers';
        }
        if ($product->isMultiOffersUse()){
            $multioffers = $product['multioffers']['levels'];
            $first_multioffer = reset($multioffers);

            if (!empty($first_multioffer['values'])){
                if ($product->isOffersUse()){ //Если есть многомерные комплектации и комплектации
                    $multioffers = $product['multioffers']['levels'];
                    return 'offers + multioffers';
                }
                return 'multioffers';
            }
        }

        $product->fillOffers();
        return 'offers'; //Если нет комплектаций
    }

    /**
    * Возвращает массив из комплектаций товара
    *
    * @param \Catalog\Model\Orm\Product $product - объект товара
    * @return array
    */
    protected function getProductOffers($product)
    {
        $offer = new \Catalog\Model\Orm\Offer();
        //Разрешим показ полей для выгрузки
        $offer->getPropertyIterator()->append([
                    'num' => new \RS\Orm\Type\Integer([
                        'description' => t('Остаток на складе'),
                        'visible' => true,
                    ]),
                    'sortn' => new \RS\Orm\Type\Integer([
                        'description' => t('Сортировочный индекс'),
                        'visible' => true,
                    ]),
                    'propsdata_arr' => new \RS\Orm\Type\ArrayList([
                        'description' => t('Характеристики комплектации'),
                        'visible' => true,
                    ]),
                    '_propsdata' => new \RS\Orm\Type\ArrayList([
                        'description' => t('Характеристики комплектации'),
                        'visible' => false,
                    ]),
                    'cost_values' => new \RS\Orm\Type\ArrayList([
                        'description' => t('Розничная и зачёркнутая цена товара'),
                        'appVisible' => true,
                    ]),
                    'all_cost_values' => new \RS\Orm\Type\ArrayList([
                        'description' => t('Все цены'),
                        'appVisible' => true,
                    ]),
                    'button_type' => new \RS\Orm\Type\Integer([
                        'description' => t('Тип кнопки в зависимости от комплектации, наличия и т.д.'),
                        'visible' => true,
                    ]),
                    'stock_sticks' => new \RS\Orm\Type\MixedType([
                        'description' => t('Риски запонености на складах'),
                        'visible' => true,
                    ]),
        ]);
        //Пройдём по комплектациям
        $offers = [];
        if (isset($product['offers']['items']) && !empty($product['offers']['items'])){
            if (in_array('stock', $this->method_params['sections'])) { //Если нужно добавить риски остатков
                $product->fillOffersStockStars(); //Запоним данные по рискам
            }
            $offers = $product['offers']['items'];
            /**
            * @var \Catalog\Model\Orm\Offer $offer
            */
            foreach ($offers as $sortn=>$offer){
                $offer->fillStockNum();
                $stock_num = [];
                if (in_array('stock', $this->method_params['sections'])){ //Если нужно добавить риски остатков
                    if (!empty($offer['stock_num'])){
                        foreach ($offer['stock_num'] as $warehouse_id=>$num){
                            $warehouse_info['warehouse_id']  = $warehouse_id;
                            $warehouse_info['num'] = $num;
                            $stock_num[] = $warehouse_info;
                        }
                        $offer['stock_num'] = $stock_num;
                    }

                    if (!empty($offer['sticks'])) {
                        $sticks = [];
                        foreach ($offer['sticks'] as $warehouse_id => $num) {
                            $sticks_info['warehouse_id'] = $warehouse_id;
                            $sticks_info['count'] = $num;
                            $sticks[] = $sticks_info;
                        }

                        $offer['stock_sticks'] = $sticks;
                    }
                }

                $offer['button_type'] = $product->getButtonTypeByOffer($sortn);
                //Добавим секцию с ценами
                $cost_values = [
                    'cost' => $product->getCost(null, $offer['sortn'], false),
                    'old_cost' => $product->getOldCost($offer['sortn'], false)
                ];
                $cost_values['cost_format'] = \RS\Helper\CustomView::cost($cost_values['cost'], $this->current_currency['stitle']);
                $cost_values['old_cost_format'] = \RS\Helper\CustomView::cost($cost_values['old_cost'], $this->current_currency['stitle']);

                $offer['cost_values'] = $cost_values;

                //Если нужно дописывать все цены
                if (in_array('costs', $this->method_params['sections'])) {
                    $all_cost_values = [];
                    $costs = \Catalog\Model\CostApi::staticSelectList();
                    foreach ($costs as $cost_id=>$cost_title){
                        $offer_cost = $product->getCost($cost_id, $offer['sortn'], false);
                        $all_cost_values[] = [
                            "id" => $cost_id,
                            "title" => $cost_title,
                            "cost" => $offer_cost,
                            "cost_format" => \RS\Helper\CustomView::cost($offer_cost, $this->current_currency['stitle']),
                        ];
                    }
                    $offer['all_cost_values'] = $all_cost_values;
                }
            }
        }

        return \ExternalApi\Model\Utils::extractOrmList($offers);
    }

    /**
    * Заполняет значение списка картинками
    *
    * @param \Catalog\Model\Orm\Property\ItemValue $value - объект значения
    * @param mixed $image - картинка для добавления
    */
    private function fillValueImages($value, $image)
    {
        $value->getPropertyIterator()->append([
            'images' => new \RS\Orm\Type\ArrayList([
                'description' => t('Список с картинками'),
                'visible' => true,
                'Appvisible' => true,
            ])
        ]);

        $value['images'] = \Catalog\Model\ApiUtils::prepareImagesSection($image);
    }
    

    /**
    * Возвращает массив из многомерных комплектаций товара
    *
    * @param \Catalog\Model\Orm\Product $product
    * @return array
    */
    protected function getProductMultiOffers($product)
    {
        $product->fillMultiOffersPhotos();
        $multioffers = $product['multioffers']['levels'];

        if (!empty($multioffers)){
            /**
            * @var \Catalog\Model\Orm\MultiOfferLevel $multioffer_level
            */
            foreach($multioffers as $multioffer_level){
                $property = $multioffer_level->getPropertyItem();
                $multioffer_level->getPropertyIterator()->append([
                    'values' => new \RS\Orm\Type\Integer([
                        'description' => t('Значения уровня многомерных комплектаций'),
                        'visible' => true,
                        'Appvisible' => true,
                    ]),
                    'property_type' => new \RS\Orm\Type\Varchar([
                        'description' => t('Тип характеристики'),
                        'visible' => true,
                        'Appvisible' => true,
                    ]),
                ]);

                $multioffer_level['property_type'] = $property['type']; //Укажем тип характеристики
                $multioffer_level['title']  = empty($multioffer_level['title']) ?  $multioffer_level['prop_title'] : $multioffer_level['title'];

                if (!empty($multioffer_level['values'])){
                    /**
                    * @var \Catalog\Model\Orm\Property\ItemValue $value
                    */
                    foreach ($multioffer_level['values'] as $key=>$value){
                        switch($multioffer_level['property_type']){ //Укажем значения для типов характеристики цвет и изображение
                            case 'color':
                                    $value->getPropertyIterator()->append([
                                        'color' => new \RS\Orm\Type\Varchar([
                                            'description' => t('Цвет'),
                                            'visible' => true,
                                            'Appvisible' => true,
                                        ]),
                                    ]);
                                    $value['images'] = [];
                                    if ($value['image']){ //Если есть картинка в виде цвета
                                        $this->fillValueImages($value, $value->__image);
                                    }
                                    break;
                            case 'image':
                                    $value['images'] = [];
                                    if ($value['image']){ //Если есть картинка
                                        $this->fillValueImages($value, $value->__image);
                                    }
                                    break;
                        }
                        if ($multioffer_level['is_photo']){ //Если нужно значения отображать как фото
                            $value['images'] = [];
                            if ($multioffer_level['values_photos'] && isset($multioffer_level['values_photos'][$value['value']])){ //Если фото присутствуют отмеченные.
                                $this->fillValueImages($value, $multioffer_level['values_photos'][$value['value']]);
                            }
                        }
                    }

                }

                $multioffer_level['values'] = \ExternalApi\Model\Utils::extractOrmList($multioffer_level['values']);
            }
        }
        return \ExternalApi\Model\Utils::extractOrmList($multioffers);
    }

    /**
    * Возвращает комплектации из виртуальных многомерных
    *
    * @param \Catalog\Model\Orm\Product $product - объект товара
    * @return array
    */
    protected function getProductOffersFromVirtual($product)
    {
       $virtual_offers = [];
       $virtual_multioffers = $product['virtual_multioffers']['items'];
       foreach ($virtual_multioffers as $product_id=>$offer){
           $virtual_offers[] = [
               'values' => $offer['values'],
               'product_id' => $product_id
           ];
       }
       return $virtual_offers;
    }

    /**
     * Возвращает комплектации, многомерные комплектации или виртуальные многомерные комплектации товара по ID товара
     *
     * @param string $token Авторизационный токен
     * @param integer $product_id ID товара
     * @param array $sections дополнительные секции
     *
     * <b>sections</b>
     * stock - сведения по складам
     * costs - цены
     *
     * stick_info - имформация о складах и градации рисок
     * В зависимости от того, какой товар и что у него есть, вощвращается разный тип ответа в секции type и разные секции с информацией.
     * Возможные типы:
     * offer - комплектации
     * multioffers - многомерные комплектации без комплектаций
     * offers + multioffers - комплектации и многоморные комплектации
     * virtual multioffers - виртуальные многомерные комплектации
     *
     *
     * Возможные секции:
     * offers - комплектации
     * multioffers - многомерные комплектации или вирткльные многомерные компелектации
     * virtual_offers - виртальнык комлпектации (идут только в связке с виртуальными многомерными комлпектациями)
     *
     * @example GET api/methods/product.getofferslist?token=2bcbf947f5fdcd0f77dc1e73e73034f5735de486&product_id=1
     * Ответ
     * <pre>
     * {
     *        "response": {
     *            "no_product": true, //Если был сделан запрос к несуществующему товару
     *            "type": "offers + multioffers", //Тип возвращаемой информации (offer|multioffers|offers + multioffers|virtual multioffers)
     *            "stick_info": {
     *               "warehouses": [
     *                   {
     *                   "id": "1",
     *                       "title": "Метро Дворец Cпорта",
     *                       "alias": "mavi-step-trc-gulliver",
     *                       "image": {
     *                           "original_url": "http://192.168.1.199/storage/system/original/",
     *                           "big_url": "http://192.168.1.199/storage/system/resized/xy_1000x1000/_a7a4c5b4.jpg",
     *                           "middle_url": "http://192.168.1.199/storage/system/resized/xy_600x600/_22fa5e2.jpg",
     *                           "small_url": "http://192.168.1.199/storage/system/resized/xy_300x300/_6bf64320.jpg",
     *                           "micro_url": "http://192.168.1.199/storage/system/resized/xy_100x100/_8ae629de.jpg",
     *                           "nano_url": "http://192.168.1.199/storage/system/resized/xy_50x50/_8d50bd69.jpg"
     *                       },
     *                       "description": "<p>Наш склад находится в центре города. Предусмотрена удобная парковка для автомобилей и велосипедов.</p>\r\n<p><span style=\"color: #0000ff;\">Уважаемые наши покупатели, рекомендуем перед&nbsp; посещением магазина сделать следующие действия.</span></p>\r\n<p><span style=\"color: #0000ff;\">Позвонить по номеру &nbsp;телефона локального магазина <span>☎</span>&nbsp;<span style=\"text-decoration: underline;\">+380930186817</span>&nbsp; и получить консультацию по интересующем вас товарам или услугам.</span></p>\r\n<p><span style=\"color: #0000ff;\">Потом</span></p>\r\n<p><span style=\"color: #0000ff;\">Положить товары в корзину с указанием магазина в котором хотите купить этот товары. Или услугу.</span></p>\r\n<p><strong></strong><span style=\"color: #0000ff;\"></span></p>",
     *                       "adress": "Спортивная площадь, 1А",
     *                       "phone": "+380930186817",
     *                       "work_time": "с 10:00 до 22:00",
     *                       "coor_x": "50.4386",
     *                       "coor_y": "30.5229",
     *                       "default_house": "1",
     *                       "public": "1",
     *                       "checkout_public": "1",
     *                       "use_in_sitemap": "1",
     *                       "xml_id": null,
     *                       "meta_title": "",
     *                       "meta_keywords": "",
     *                       "meta_description": "",
     *                       "affiliate_id": "1"
     *                   }
     *               ],
     *               "stick_ranges": [
     *                   1,
     *                   2,
     *                   3,
     *                   4,
     *                   5
     *               ]
     *           },
     *            "offers": [ //Если есть комплектации
     *                {
     *                    "id": "1181",
     *                    "title": "Нетбук, DDR3",
     *                    "barcode": "ПФ-28",
     *                    "propsdata_arr": {
     *                        "Форм-фактор": "Нетбук",
     *                        "Тип памяти": "DDR3"
     *                    },
     *                    "num": "0", //Количество на складе общее для выбранной компелктации
     *                    "stock_num": [ //Наличие на определённом складе (Только если указан флаг - stock)
     *                        {
     *                           "warehouse_id": 1,
     *                           "num": "5.000"
     *                       },
     *                       {
     *                           "warehouse_id": 3,
     *                           "num": "1.000"
     *                       },
     *                       {
     *                       ...
     *                    ],
     *                    "stock_sticks": [ //Наличие на определённом складе в виде рисок (Только если указан флаг - stock)
     *                        {
     *                            "warehouse_id": 1,
     *                            "count": 0
     *                        },
     *                        {
     *                            "warehouse_id": 3,
     *                            "count": 1
     *                        },
     *                        ...
     *                    ]
     *                    "photos_arr": [
     *                        "2583",
     *                        "2584"
     *                    ],
     *                    "unit": "0",
     *                    "cost_values": { //Цена по умолчанию и зачеркнутая цены
     *                        "cost": "15590.00",
     *                        "old_cost": "0.00",
     *                        "cost_format": "15 590 р.",
     *                        "old_cost_format": "0 р."
     *                    },
     *                    "button_type": "buy" //Тип кнопки для показа для выбранной комплектации (buy|reservation|none) (купить|заказать|скрыть кнокпку)
     *                },
     *                ...
     *            ],
     *            "multioffers": [ //Многомерные комплектации или виртуальные многомерные комплектации
     *                {
     *                    "product_id": "616",
     *                    "prop_id": "8",
     *                    "title": "Тип памяти",
     *                    "is_photo": "1", //Если отображать как фото стоит у многомерной комплпектации
     *                    "sortn": "1",
     *                    "values": [
     *                        {
     *                            "id": "6",
     *                            "value": "DDR3",
     *                            "color" : "#ffffff",  //Только если тип цвет
     *                            "images": { //Может и не быть
     *                                "big_url": "http://mega.readyscript.ru/storage/photo/resized/xy_600x600/f/asft9ztxcacl124_4c7b0a96.jpg",
     *                                "small_url": "http://mega.readyscript.ru/storage/photo/resized/xy_200x200/f/asft9ztxcacl124_552ad92b.jpg",
     *                                "micro_url": "http://mega.readyscript.ru/storage/photo/resized/xy_60x60/f/asft9ztxcacl124_54cdbe34.jpg",
     *                                "nano_url": "http://mega.readyscript.ru/storage/photo/resized/xy_60x60/f/asft9ztxcacl124_54cdbe34.jpg",
     *                                "original_url": "http://mega.readyscript.ru/storage/photo/original/f/asft9ztxcacl124.jpg"
     *                            }
     *                        },
     *                        {
     *                            "id": "7",
     *                            "value": "DDR2",
     *                            "color" : "#000000", //Только если тип цвет
     *                            "images": { //Может и не быть
     *                                "big_url": "http://mega.readyscript.ru/storage/photo/resized/xy_600x600/d/79z2j7uyr69trcb_ff95897a.jpg",
     *                                "small_url": "http://mega.readyscript.ru/storage/photo/resized/xy_200x200/d/79z2j7uyr69trcb_e6c45ac7.jpg",
     *                                "micro_url": "http://mega.readyscript.ru/storage/photo/resized/xy_60x60/d/79z2j7uyr69trcb_a555a0ec.jpg",
     *                                "nano_url": "http://mega.readyscript.ru/storage/photo/resized/xy_60x60/d/79z2j7uyr69trcb_a555a0ec.jpg",
     *                                "original_url": "http://mega.readyscript.ru/storage/photo/original/d/79z2j7uyr69trcb.jpg"
     *                            }
     *                        }
     *                        ...
     *                    ],
     *                    "property_type": "list" //Тип отображения многомерной комплектации (list|radio|image|color)
     *                    ...
     *                }
     *                ...
     *            ],
     *            "virtual_offers": [ //Виртуальные комплектации
     *                {
     *                    "values": {
     *                        "Цвет": "Желтый",
     *                        "Размер": "8"
     *                    },
     *                    "product_id": 578
     *                },
     *                ...
     *            ]
     *        }
     *    }
     * </pre>
     * @return array
     * @throws \ExternalApi\Model\Exception
     */
    function process($token = null, $product_id, $sections = ['stock', 'costs'])
    {
        //Загруженный товар
        $product  = new \Catalog\Model\Orm\Product($product_id);
        $response = parent::process($token, $product_id);
        
        if ($product['id']){
            $this->current_currency = \Catalog\Model\CurrencyApi::getCurrentCurrency(); //Текущая валюта
            unset($response['response']['product']); //Удалим конкретную информацию о товаре
            //Посмотрим какой тип комплектаций существует для данного товара
            
            $response['response']['type'] = $this->getProductOfferType($product);

            $multioffers = [];
            $virtual_offers = [];
            //Загрузим комплектации или многогомерные комплектации   
            switch($response['response']['type']){
                case 'virtual multioffers':
                    $multioffers    = $this->getProductMultiOffers($product);
                    $virtual_offers = $this->getProductOffersFromVirtual($product);
                    
                    foreach ($multioffers as $key=>$level) { // временная заглушка для виртуальных многомерок
                        $multioffers[$key]['property_type'] = 'list';
                    }
                    
                    break;
                case 'offers + multioffers':
                case 'multioffers':
                    $multioffers = $this->getProductMultiOffers($product);
                    break; 
            } 
            $offers = $this->getProductOffers($product);

            if (!$product->shouldReserve() && in_array('stock', $sections)){//Если нужна секция со сведениями о складах и склады есть
                $stick_info=$product->getWarehouseStickInfo();
                if (!empty($stick_info['warehouses'])){
                    $warehouses = [];
                    foreach ($stick_info['warehouses'] as $warehouse){
                        $warehouse_info = \ExternalApi\Model\Utils::extractOrm($warehouse);
                        if ($warehouse['image']){
                            $warehouse_info['image'] = \Catalog\Model\ApiUtils::prepareImagesSection($warehouse->__image);
                        }
                        $warehouses[] = $warehouse_info;
                    }
                    $stick_info['warehouses'] = $warehouses;
                    $response['response']['stick_info'] = $stick_info;
                }
            }
            
            if (!empty($offers)){
                $response['response']['offers'] = $offers;
            }
            if (!empty($multioffers)){
                $response['response']['multioffers'] = $multioffers;
            }
            if (!empty($virtual_offers)){
                $response['response']['virtual_offers'] = $virtual_offers;
            }
        }else{
            $response['response']['no_product'] = true;
            $response['response']['type']       = null;
            $response['response']['offers']     = [];
        }
        
        return $response;
    }
}