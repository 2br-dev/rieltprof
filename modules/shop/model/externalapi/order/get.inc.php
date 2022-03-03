<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Order;
use Catalog\Model\Orm\WareHouse;
use ExternalApi\Model\Exception as ApiException;
use Partnership\Model\Orm\Partner;
use Shop\Model\Orm\Address;
use Shop\Model\Orm\Delivery;
use Shop\Model\Orm\Payment;
use Shop\Model\Orm\UserStatus;
use Users\Model\Orm\User;

/**
* Загружает объект
*/
class Get extends \ExternalApi\Model\AbstractMethods\AbstractGet
{
    const
       RIGHT_COURIER = 2;



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
        return parent::getRightTitles() + [
            self::RIGHT_COURIER => t('Загрузка курьерских объектов')
            ];
    }

    /**
    * Возвращает список прав, требуемых для запуска метода API
    * По умолчанию для запуска метода нужны все права, что присутствуют в методе
    *
    * @return [код1, код2, ...]
    */
    public function getRunRights()
    {
        return [self::RIGHT_LOAD];
    }


    /**
    * Добавляет к заказу секцию с товарами
    *
    * @param \Shop\Model\Orm\Order $order
    * @return \Shop\Model\Orm\Order
    */
    private function addOrderItems(\Shop\Model\Orm\Order $order)
    {
        $order->getPropertyIterator()->append([
            'dateof_iso' => new \RS\Orm\Type\MixedType([
                'description' => t('Дата создания в формате ISO 8601'),
                'appVisible' => true
            ]),
            'dateof_timestamp' => new \RS\Orm\Type\Integer([
                'description' => t('Дата создания в формате TIMESTAMP'),
                'appVisible' => true
            ]),
            'dateof_date' => new \RS\Orm\Type\Varchar([
                'description' => t('Дата создания в формате dd.mm.YYYYY'),
                'appVisible' => true
            ]),
            'dateof_datetime' => new \RS\Orm\Type\Varchar([
                'description' => t('Дата создания в формате dd.mm.YYYYY HH:ii'),
                'appVisible' => true
            ]),
            'items' => new \RS\Orm\Type\MixedType([
                'description' => t('Состав заказа'),
                'appVisible' => true
            ]),
            'track_url' => new \RS\Orm\Type\Varchar([
                'description' => t('Url для отслеживания'),
                'appVisible' => true
            ]),
            'files' => new \RS\Orm\Type\MixedType([
                'description' => t('Файлы прикреплённые к заказу'),
                'appVisible' => true
            ]),
            'additional_fields' => new \RS\Orm\Type\MixedType([
                'description' => t('Дополнительные поля заказа из модуля Магазин'),
                'appVisible' => true
            ]),
        ]);

        $orm_items = \RS\Orm\Request::make()
            ->from(new \Shop\Model\Orm\OrderItem())
            ->where([
                'order_id' => $order['id']
            ])
            ->orderby('sortn')
            ->objects();

        $items = [];

        $prototype = new \Shop\Model\Orm\OrderItem();
        $prototype->getPropertyIterator()->append([
            'image' => new \RS\Orm\Type\MixedType([
                'description' => t('Фото'),
                'appVisible' => true
            ]),
            'url' => new \RS\Orm\Type\Varchar([
                'description' => t('Фото'),
                'appVisible' => true
            ])
        ]);

        foreach($orm_items as $item) {
            //Подгрузим картинки товаров
            if ($item['type'] == \Shop\Model\Orm\OrderItem::TYPE_PRODUCT) {
                $product = new \Catalog\Model\Orm\Product($item['entity_id']);
                if ($product['id']) {
                    $item['image'] = \Catalog\Model\ApiUtils::prepareImagesSection($product->getMainImage());
                }
                $item['url'] = $product->getUrl(true);
            }

            $one_item = \ExternalApi\Model\Utils::extractOrm($item);
            $multioffers_arr = [];
            $multioffers = @unserialize($one_item['multioffers']);
            if (!empty($multioffers)){

                foreach ($multioffers as $multioffer){
                    $multioffers_arr[] = $multioffer;
                }
            }
            $one_item['multioffers'] = $multioffers_arr;
            $items[] = $one_item;
        }

        $timestamp_date = strtotime($order['dateof']);
        if ($url=$order->getTrackUrl()){
            $order['track_url'] = $url;
        }


        //Если есть файлы привязанные к заказу
        if ($files = $order->getFiles()) {
            $order_files = [];
            foreach ($files as $file){
                $order_files[] = [
                    'title' => $file['name'],
                    'link'  => $file->getUrl(true)
                ];
            }
            $order['files'] = $order_files;
        }

        //Если есть дополнительные поля прописанные в модуле магазин
        $fm = $order->getFieldsManager();

        if ($additional_fields = $fm->getStructure()){
            $order_fields = [];
            foreach ($additional_fields as $order_field){
                 $order_fields[] = [
                    'title' => $order_field['title'],
                    'value'  => $order_field['current_val'],
                 ];
            }
            $order['additional_fields'] = $order_fields;
        }

        $order['dateof_iso']       = date('c', $timestamp_date);
        $order['dateof_timestamp'] = $timestamp_date;
        $order['dateof_date']      = date('d.m.Y', $timestamp_date);
        $order['dateof_datetime']  = date('d.m.Y H:i', $timestamp_date);
        $order['dateof_timestamp'] = strtotime($order['dateof']);
        $order['items']            = $items;
    }


    /**
    * Возвращает ORM объект, который следует загружать
    */
    public function getOrmObject()
    {
        return new \Shop\Model\Orm\Order();
    }

    /**
    * Загружает объект "заказ"
    *
    * @param string $token Авторизационный токен
    * @param integer $order_id ID заказа
    * @param integer $ignore_user_group флаг указывает на, то игнорировать ли установленную группу у пользователя. Группа пользователя сужает доступный список заказов в соотвествиии с установленной группой.
    *
    * @example GET /api/methods/order.get?token=b45d2bc3e7149959f3ed7e94c1bc56a2984e6a86&order_id=1
    *
    * Ответ:
    * <pre>
    * {
    *     "response": {
    *         "order": {
    *             "id": "159",
    *             "order_num": "159",
    *             "user_id": "2",
    *             "currency": "RUB",
    *             "currency_ratio": "1",
    *             "currency_stitle": "р.",
    *             "ip": "127.0.0.1",
    *             "track_url": "http://edostavka.ru/look/34098435809",
    *             "dateof": "2016-08-31 14:13:06",
    *             "dateof_date": "31.08.2016",
    *             "dateof_datetime": "31.08.2016 11:05",
    *             "dateofupdate": "2016-09-22 01:00:27",
    *             "totalcost": "1468.95",
    *             "profit": "175.00",
    *             "user_delivery_cost": "0.00",
    *             "is_payed": "0",
    *             "status": "1",
    *             "admin_comments": "",
    *             "user_text": "",
    *             "hash": "0406df2de33b39217140f5b43bc664e6",
    *             "contact_person": "",
    *             "use_addr": "1",
    *             "only_pickup_points": null,
    *             "userfields_arr": [],
    *             "delivery": "2",
    *             "deliverycost": null,
    *             "courier_id": "2",
    *             "warehouse": "6",
    *             "payment": "1",
    *             "comments": "",
    *             "user_fio": null,
    *             "user_email": null,
    *             "user_phone": null,
    *             "partner_id": null,
    *             "dateof_iso": "2016-08-31T14:13:06+03:00",
    *             "items": [
    *                 {
    *                     "order_id": "159",
    *                     "uniq": "vf4dtvv2ml",
    *                     "type": "product",
    *                     "entity_id": "134",
    *                     "multioffers": false,
    *                     "offer": "4",
    *                     "amount": "1",
    *                     "barcode": "ALI-W-36-1",
    *                     "title": "Коньки фигурные Nordway Alice",
    *                     "model": "new2",
    *                     "single_weight": "0",
    *                     "single_cost": "1749.00",
    *                     "price": "1749.00",
    *                     "price_formatted": "1 749  р."
    *                     "profit": "175.00",
    *                     "discount": "350.00",
    *                     "sortn": "0",
    *                     "data": {
    *                         "tax_ids": [
    *                             "2",
    *                             "5"
    *                         ],
    *                         "unit": "шт."
    *                     },
    *                     "image": {
    *                         "id": "2361",
    *                         "title": null,
    *                         "original_url": "http://mega.readyscript.ru/storage/photo/original/i/06eq2uxurfz9l3n.jpg",
    *                         "big_url": "http://mega.readyscript.ru/storage/photo/resized/xy_1000x1000/i/06eq2uxurfz9l3n_c2e6b23d.jpg",
    *                         "middle_url": "http://mega.readyscript.ru/storage/photo/resized/xy_600x600/i/06eq2uxurfz9l3n_ce60b3b1.jpg",
    *                         "small_url": "http://mega.readyscript.ru/storage/photo/resized/xy_300x300/i/06eq2uxurfz9l3n_a7b95573.jpg",
    *                         "micro_url": "http://mega.readyscript.ru/storage/photo/resized/xy_100x100/i/06eq2uxurfz9l3n_46a93f8d.jpg"
    *                         "nano_url": "http://mega.readyscript.ru/storage/photo/resized/xy_100x100/i/06eq2uxurfz9l3n_46a93f8d.jpg"
    *                     }
    *                     "url": "http://full.readyscript.local/product/134/"
    *                 },
    *                 {
    *                     "order_id": "159",
    *                     "uniq": "9ff8c24e4d",
    *                     "type": "coupon",
    *                     "entity_id": "1",
    *                     "multioffers": false,
    *                     "offer": null,
    *                     "amount": "1",
    *                     "barcode": null,
    *                     "title": "Купон на скидку demo",
    *                     "model": null,
    *                     "single_weight": null,
    *                     "single_cost": null,
    *                     "price": "0.00",
    *                     "profit": "0.00",
    *                     "discount": "0.00",
    *                     "sortn": "1",
    *                     "data": false,
    *                     "image": null,
    *                     "url": null
    *                 },
    *                 {
    *                     "order_id": "159",
    *                     "uniq": "042ykmj1v7",
    *                     "type": "subtotal",
    *                     "entity_id": null,
    *                     "multioffers": false,
    *                     "offer": null,
    *                     "amount": "1",
    *                     "barcode": null,
    *                     "title": "Товаров на сумму",
    *                     "model": null,
    *                     "single_weight": null,
    *                     "single_cost": null,
    *                     "price": "1175.63",
    *                     "profit": "0.00",
    *                     "discount": "1175.63",
    *                     "sortn": "2",
    *                     "data": false,
    *                     "image": null,
    *                     "url": null
    *                 },
    *                 {
    *                     "order_id": "159",
    *                     "uniq": "ssyudt964c",
    *                     "type": "tax",
    *                     "entity_id": "2",
    *                     "multioffers": false,
    *                     "offer": null,
    *                     "amount": "1",
    *                     "barcode": null,
    *                     "title": "НДС, 18%(включен в стоимость)",
    *                     "model": null,
    *                     "single_weight": null,
    *                     "single_cost": null,
    *                     "price": "223.37",
    *                     "profit": "0.00",
    *                     "discount": "223.37",
    *                     "sortn": "3",
    *                     "data": false,
    *                     "image": null,
    *                     "url": null
    *                 },
    *                 {
    *                     "order_id": "159",
    *                     "uniq": "2jukioa3c1",
    *                     "type": "tax",
    *                     "entity_id": "5",
    *                     "multioffers": false,
    *                     "offer": null,
    *                     "amount": "1",
    *                     "barcode": null,
    *                     "title": "НДС, 10%(включен в стоимость)",
    *                     "model": null,
    *                     "single_weight": null,
    *                     "single_cost": null,
    *                     "price": "0.00",
    *                     "profit": "0.00",
    *                     "discount": "0.00",
    *                     "sortn": "4",
    *                     "data": false,
    *                     "image": null,
    *                     "url": null
    *                 },
    *                 {
    *                     "order_id": "159",
    *                     "uniq": "ykf11msejt",
    *                     "type": "delivery",
    *                     "entity_id": "2",
    *                     "multioffers": false,
    *                     "offer": null,
    *                     "amount": "1",
    *                     "barcode": null,
    *                     "title": "Доставка: Доставка по г.Краснодару",
    *                     "model": null,
    *                     "single_weight": null,
    *                     "single_cost": null,
    *                     "price": "0.00",
    *                     "profit": "0.00",
    *                     "discount": "0.00",
    *                     "sortn": "5",
    *                     "data": false,
    *                     "image": null,
    *                     "url": null
    *                 },
    *                 {
    *                     "order_id": "159",
    *                     "uniq": "ojbz3ku152",
    *                     "type": "",
    *                     "entity_id": "1",
    *                     "multioffers": false,
    *                     "offer": null,
    *                     "amount": "1",
    *                     "barcode": null,
    *                     "title": "Комиссия при оплате через Безналичный расчет 5%",
    *                     "model": null,
    *                     "single_weight": null,
    *                     "single_cost": null,
    *                     "price": "69.95",
    *                     "profit": "0.00",
    *                     "discount": "0.00",
    *                     "sortn": "6",
    *                     "data": false,
    *                     "image": null,
    *                     "url": null
    *                 }
    *             ]
    *         },
    *         "user": {
    *             "2": {
    *                 "id": "2",
    *                 "name": "Артем",
    *                 "surname": "Иванов",
    *                 "midname": "Петрович",
    *                 "e_mail": "mail@readyscript.ru",
    *                 "login": "demo@example.com",
    *                 "phone": "+700000000000",
    *                 "sex": "",
    *                 "subscribe_on": "0",
    *                 "dateofreg": "0000-00-00 00:00:00",
    *                 "ban_expire": null,
    *                 "last_visit": "2016-09-22 11:33:19",
    *                 "is_company": "1",
    *                 "company": "ООО Ромашка",
    *                 "company_inn": "1234567890",
    *                 "data": {
    *                     "passport": "00000012233"
    *                 },
    *                 "passport": "серия 03 06, номер 123456, выдан УВД Западного округа г. Краснодар, 04.03.2006",
    *                 "company_kpp": "0987654321",
    *                 "company_ogrn": "1234567890",
    *                 "company_v_lice": "директора Сидорова Семена Петровича",
    *                 "company_deistvuet": "устава",
    *                 "company_bank": "ОАО УРАЛБАНК",
    *                 "company_bank_bik": "1234567890",
    *                 "company_bank_ks": "10293847560192837465",
    *                 "company_rs": "19283746510293847560",
    *                 "company_address": "350089, г. Краснодар, ул. Чекистов, 12",
    *                 "company_post_address": "350089, г. Краснодар, ул. Чекистов, 15",
    *                 "company_director_post": "директор",
    *                 "company_director_fio": "Сидоров С.П.",
    *                 "user_cost": null
    *             }
    *         },
    *         "status": {
    *             "1": {
    *                 "id": "1",
    *                 "title": "Новый",
    *                 "bgcolor": "#83b7b3",
    *                 "type": "new"
    *             }
    *         },
    *         "address": {
    *             "1": {
    *                 "id": "1",
    *                 "user_id": "2",
    *                 "order_id": "0",
    *                 "zipcode": "350000",
    *                 "country": "Россия",
    *                 "region": "Краснодарский край",
    *                 "city": "Краснодар",
    *                 "address": "ул. Тестовая, 404, кв. 503",
    *                 "city_id": "307",
    *                 "region_id": "13",
    *                 "country_id": "1",
    *                 "deleted": "0"
    *             }
    *         },
    *         "warehouse": {
    *             "6": {
    *                 "id": "6",
    *                 "title": "Розничный склад",
    *                 "alias": "roznichnyy-sklad",
    *                 "image": null,
    *                 "description": null,
    *                 "adress": null,
    *                 "phone": null,
    *                 "work_time": null,
    *                 "coor_x": "55.7533",
    *                 "coor_y": "37.6226",
    *                 "default_house": "0",
    *                 "public": null,
    *                 "checkout_public": null,
    *                 "use_in_sitemap": "0",
    *                 "xml_id": "3564ef2c-517e-11e6-8505-001a7dda7113",
    *                 "meta_title": null,
    *                 "meta_keywords": null,
    *                 "meta_description": null,
    *                 "affiliate_id": "0"
    *             }
    *         },
    *         "delivery": {
    *             "2": {
    *                 "id": "2",
    *                 "title": "Доставка по г.Краснодару",
    *                 "description": "Доставка осуществляется на следующие день после оплаты заказа",
    *                 "picture": "",
    *                 "xzone": null,
    *                 "min_price": "0",
    *                 "max_price": "0",
    *                 "min_cnt": "0",
    *                 "first_status": "0",
    *                 "user_type": "all",
    *                 "extrachange_discount": "0",
    *                 "public": "1",
    *                 "class": "fixedpay",
    *                 "show_in_cost_block": "0"
    *             }
    *         },
    *         "payment": {
    *             "1": {
    *                 "id": "1",
    *                 "title": "Безналичный расчет",
    *                 "description": "Оплата должна производиться с расчетного счета предприятия",
    *                 "picture": null,
    *                 "first_status": "0",
    *                 "success_status": "0",
    *                 "user_type": "all",
    *                 "target": "all",
    *                 "delivery": [],
    *                 "public": "1",
    *                 "default_payment": "1",
    *                 "commission": "5",
    *                 "docs": [
    *                   {
    *                       "title": "Счёт",
    *                       "link": "http://mega.readyscript.ru/files/bills/06eq2uxurfz9l3n/",
    *                   }
    *                 ]
    *                 "class": "bill"
    *             }
    *         },
    *         "site_uid": "7e94c1bc56a2984e6a86b45d2bc3e7149959f3ed"
    *     }
    * }
    * </pre>
    *
    * @return array Возвращает объект заказа и все связанные с ним объекты из справочников
    */
    protected function process($token, $order_id, $ignore_user_group = 0)
    {
        /**
        * @var \Shop\Model\Orm\Order $object
        */
        $object = $this->getOrmObject();

        if ($object->load($order_id)) {
            //Курьер может просматривать только свои заказы

            if ($this->checkAccessError(self::RIGHT_COURIER) === false && !$ignore_user_group) {
                if ($object['courier_id'] != $this->token['user_id']) {
                    throw new ApiException(t('Курьеры могут загружать только назначенны им заказы'), ApiException::ERROR_METHOD_ACCESS_DENIED);
                }
            }

            $this->addOrderItems($object);

            $result = [
                'response' => [
                    strtolower(basename(str_replace('\\', '/', get_class($object)))) => \ExternalApi\Model\Utils::extractOrm($object)
                ]
            ];

            if (!empty($result['response']['order']['items'])){
                foreach ($result['response']['order']['items'] as &$cartitem){ //Добавим форматированную
                    $cartitem['price_formatted'] = \RS\Helper\CustomView::cost($cartitem['price'])." ".$object['currency_stitle'];
                }
            }

            //Можно ли оплатить заказ
            $result['response']['order']['can_online_pay']  = $object->canOnlinePay();


            if ($user_id = $object['user_id']) {
                $result['response']['user'][$user_id] =
                    \ExternalApi\Model\Utils::extractOrm(new User($user_id));
            }

            if ($object['courier_id'] != $object['user_id']) {
                $result['response']['user'][$object['courier_id']] =
                    \ExternalApi\Model\Utils::extractOrm(new User($object['courier_id']));
            }

            if ($status_id = $object['status']) {
                $result['response']['status'][$status_id] =
                    \ExternalApi\Model\Utils::extractOrm(new UserStatus($status_id));
            }

            if ($address_id = $object['use_addr']) {
                $result['response']['address'][$address_id] =
                    \ExternalApi\Model\Utils::extractOrm(new Address($address_id));
            }

            if ($warehouse_id = $object['warehouse']) {
                $result['response']['warehouse'][$warehouse_id] =
                    \ExternalApi\Model\Utils::extractOrm(new WareHouse($warehouse_id));
            }

            if ($delivery_id = $object['delivery']) {
                $result['response']['delivery'][$delivery_id] =
                    \ExternalApi\Model\Utils::extractOrm(new Delivery($delivery_id));
            }

            if ($payment_id = $object['payment']) {
                $payment = $object->getPayment();
                if ($payment->hasDocs()){ //Если есть документы для оплаты
                    $payment->getPropertyIterator()->append([
                        'docs' => new \RS\Orm\Type\ArrayList([
                            'description' => t('Список документов'),
                            'appVisible' => true
                        ]),
                    ]);
                    $type_object = $payment->getTypeObject();
                    $docs = [];
                    foreach ($type_object->getDocsName() as $key=>$doc){
                       $docs[] = [
                          'title' => $doc['title'],
                          'link' => $type_object->getDocUrl($key, true),
                       ];
                    }
                    $payment['docs'] = $docs;
                }
                $result['response']['payment'][$payment_id] = \ExternalApi\Model\Utils::extractOrm($payment);
            }

            if ($partner_id = $object['partner_id']) {
                $result['response']['partner'][$partner_id] =
                    \ExternalApi\Model\Utils::extractOrm(new Partner($partner_id));
            }





            $site = new \Site\Model\Orm\Site($object['__site_id']->get());
            $result['response']['site_uid'] = $site->getSiteHash();

           // $result['response']['order']['comments'] = html_entity_decode($result['response']['order']['comments']);

            return $result;
        }

        throw new ApiException(t('Объект с таким ID не найден'), ApiException::ERROR_OBJECT_NOT_FOUND);
    }
}
