<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Checkout;
use Main\Model\StatisticEvents;

/**
* Возвращает список с товарами и элементами в корзине для заказа
*/
class GetCartData extends \ExternalApi\Model\AbstractMethods\AbstractMethod
{
    const
        RIGHT_LOAD = 1;
    
    protected
        $token_require = false, //Токен не обязателен
        $cart;
        
    public
        /**
        * @var \Shop\Model\Orm\OrderApi
        */
        $order_api,
        /**
        * @var \Shop\Model\Orm\Order
        */
        $order,
        $shop_config;
        
    function __construct()
    {
        parent::__construct();
        $this->order     = \Shop\Model\Orm\Order::currentOrder();
        $this->order_api = new \Shop\Model\OrderApi();
        $this->order->clearErrors(); //Очистим ошибки предварительно    
        $this->shop_config = \RS\Config\Loader::byModule('shop'); //Конфиг магазина
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
            self::RIGHT_LOAD => t('Загрузка списка элементов корзины')
        ];
    }
    
    /**
    * Возвращает корзину пользователя для заказа с учётом элементов заказа
    * 
    * @param string $token Авторизационный токен
    * 
    * @example GET /api/methods/checkout.getcartdata
    * Ответ:
    * <pre>
    * {
    *        "response": {
    *            "cartdata": {
    *                "total": "21 056.70 р.",
    *                "total_base": "20 054 р.",
    *                "total_discount": "0 р.",
    *                "items": [
    *                {
    *                    "id": "0onvsl2qm4",
    *                    "cost": "20 054 р.",
    *                    "base_cost": "21 110 р.",
    *                    "single_cost": "21 110 р.",
    *                    "single_weight": 0,
    *                    "discount": "1 056 р.",
    *                    "sub_products": [
    *                        {
    *                            "amount": "1",
    *                            "cost": "41 268 р.",
    *                            "single_cost": "41 268 р.",
    *                            "checked": false,
    *                            "discount": "2 172 р.",
    *                            "title": "Планшет Fujitsu STYLISTIC Q550",
    *                            "product_id": "0onvsl2qm4",
    *                            "image": {
    *                                "id": "2340",
    *                                "title": null,
    *                                "original_url": "http://mega.readyscript.rustorage/photo/original/e/ubg07um3qxseroq.jpg",
    *                                "big_url": "http://mega.readyscript.rustorage/photo/resized/xy_1000x1000/e/ubg07um3qxseroq_444ae71f.jpg",
    *                                "middle_url": "http://mega.readyscript.rustorage/photo/resized/xy_600x600/e/ubg07um3qxseroq_239bd8fd.jpg",
    *                                "small_url": "http://mega.readyscript.rustorage/photo/resized/xy_300x300/e/ubg07um3qxseroq_4a423e3f.jpg",
    *                                "micro_url": "http://mega.readyscript.rustorage/photo/resized/xy_100x100/e/ubg07um3qxseroq_ab5254c1.jpg"
    *                                "nano_url": "http://mega.readyscript.rustorage/photo/resized/xy_100x100/e/ubg07um3qxseroq_ab5254c1.jpg"
    *                            },
    *                            "id": "575",
    *                            "unit": null,
    *                            "allow_concomitant_count_edit": false
    *                        }
    *                    ],
    *                    "title": "Ноутбук HP Compaq Mini 5103 XM602AA",
    *                    "image": {
    *                        "id": "2361",
    *                        "title": null,
    *                        "original_url": "http://mega.readyscript.rustorage/photo/original/i/06eq2uxurfz9l3n.jpg",
    *                        "big_url": "http://mega.readyscript.rustorage/photo/resized/xy_1000x1000/i/06eq2uxurfz9l3n_c2e6b23d.jpg",
    *                        "middle_url": "http://mega.readyscript.rustorage/photo/resized/xy_600x600/i/06eq2uxurfz9l3n_ce60b3b1.jpg",
    *                        "small_url": "http://mega.readyscript.rustorage/photo/resized/xy_300x300/i/06eq2uxurfz9l3n_a7b95573.jpg",
    *                        "micro_url": "http://mega.readyscript.rustorage/photo/resized/xy_100x100/i/06eq2uxurfz9l3n_46a93f8d.jpg"
    *                        "nano_url": "http://mega.readyscript.rustorage/photo/resized/xy_100x100/i/06eq2uxurfz9l3n_46a93f8d.jpg"
    *                    },
    *                    "entity_id": "578",
    *                    "amount": "1",
    *                    "amount_error": "",
    *                    "offer": "0",
    *                    "model": null,
    *                    "multioffers": null,
    *                    "multioffers_string": "",
    *                    "unit": null
    *                }
    *            ],
    *            "items_count": 1,
    *            "total_weight": 0,
    *            "checkcount": 2,
    *            "currency": "р.",
    *            "errors": [],
    *            "has_error": false,
    *            "taxes": [],
    *            "total_without_delivery": "20 054 р.",
    *            "total_without_delivery_unformatted": 20054,
    *            "total_without_payment_commission": "20 054 р.",
    *            "total_without_payment_commission_unformatted": 20054,
    *            "payment_commission": {
    *                "id": "2",
    *                "title": "Квитанция банка",
    *                "description": "Система предложит распечатать бланк, для оплаты в любом отделении банка.",
    *                "picture": null,
    *                "first_status": "0",
    *                "success_status": "0",
    *                "user_type": "user",
    *                "target": "all",
    *                "delivery": [],
    *                "public": "1",
    *                "default_payment": "0",
    *                "commission": "5",
    *                "class": "formpd4",
    *                "cost": "1 055.50 р."
    *            },
    *            "delivery": {
    *                "id": "1",
    *                "title": "Самовывоз",
    *                "description": "Пункты выдачи товаров см. в разделе контакты",
    *                "picture": "bae1170283c9bfc322365c76e977ce8b.png_20.png",
    *                "xzone": null,
    *                "min_price": "0",
    *                "max_price": "0",
    *                "min_cnt": "0",
    *                "first_status": "0",
    *                "user_type": "user",
    *                "extrachange_discount": "-10",
    *                "public": "1",
    *                "class": "myself",
    *                "delivery_periods": [
    *                    {
    *                        "zone": "4",
    *                        "text": "от 1 до 2х дней"
    *                    },
    *                    {
    *                        "zone": "2",
    *                        "text": "от 2 до 3х дней"
    *                    }
    *                ],
    *                "cost": "0 р."
    *            },
    *            "total_unformatted": 21056.7,
    *            "coupons": [
    *                {
    *                    "id": "i72mszdxin",
    *                    "code": "aaa"
    *                }
    *            ],
    *            "user": {
    *                "id": "1",
    *                "name": "Супервизор тест тест",
    *                "surname": " Моя фамилия",
    *                "midname": " ",
    *                "e_mail": "admin@admin.ru",
    *                "login": "admin@admin.ru",
    *                "phone": "+7(962)867-84-30",
    *                "sex": "",
    *                "subscribe_on": "0",
    *                "dateofreg": "2016-03-14 19:58:58",
    *                "ban_expire": null,
    *                "last_visit": "2016-12-19 12:03:29",
    *                "is_company": "0",
    *                "company": "",
    *                "company_inn": "",
    *                "data": [],
    *                "push_lock": null,
    *                "user_cost": null
    *            },
    *            "only_pickup_points": 1,
    *            "use_addr": 0,
    *            "payment": {
    *                "id": "2",
    *                "title": "Квитанция банка",
    *                "description": "Система предложит распечатать бланк, для оплаты в любом отделении банка.",
    *                "picture": null,
    *                "first_status": "0",
    *                "success_status": "0",
    *                "user_type": "user",
    *                "target": "all",
    *                "delivery": [],
    *                "public": "1",
    *                "default_payment": "0",
    *                "commission": "5",
    *                "class": "formpd4"
    *            },
    *            "warehouse": {
    *                "id": "1",
    *                "title": "Основной склад",
    *                "alias": "sklad",
    *                "image": null,
    *                "description": "<p>Наш склад находится в центре города. Предусмотрена удобная парковка для автомобилей и велосипедов.</p>",
    *                "adress": "г. Краснодар, улица Красных Партизан, 246",
    *                "phone": "+7(123)456-78-90",
    *                "work_time": "с 9:00 до 18:00",
    *                "coor_x": "45.0483",
    *                "coor_y": "38.9745",
    *                "default_house": "1",
    *                "public": "0",
    *                "checkout_public": "1",
    *                "use_in_sitemap": "0",
    *                "xml_id": null,
    *                "meta_title": "",
    *                "meta_keywords": "",
    *                "meta_description": ""
    *            },
    *            "address": {
    *                "id": null,
    *                "user_id": null,
    *                "order_id": null,
    *                "zipcode": null,
    *                "country": null,
    *                "region": null,
    *                "city": null,
    *                "address": null,
    *                "city_id": null,
    *                "region_id": null,
    *                "country_id": null,
    *                "deleted": null
    *            }
    *        }
    *    }
    * </pre>
    * 
    * @return array Возвращает список со сведения об элементах в корзине
    */
    protected function process($token = null)
    {      
        //Если корзины на этот момент уже не существует.
        if ( $this->order['expired'] || !$this->order->getCart() ){ 
            $errors[] = "Корзина заказа пуста. Необходимо наполнить корзину.";
            $response['response']['errors'] = $errors;
            $response['response']['error_status'] = 2;
            return $response;
        } 
        
        $cart_data = $this->order['basket'] ? $this->order->getCart()->getCartData() : null;
        if ($cart_data === null || !count($cart_data['items']) || $cart_data['has_error'] || $this->order['expired']) {
            //Если корзина пуста или заказ уже оформлен или имеются ошибки в корзине, то выполняем redirect на главную сайта
            $errors[] = "Корзина заказа пуста, истекла сессия или в ней имеются ошибки. Оформите корзину заново.";
            $response['response']['errors']  = $errors;
            $response['response']['error_status'] = 3;
            return $response;
        } 
        
        $api = new \Shop\Model\ApiUtils();
        $response['response']['cartdata'] = $api->fillProductItemsData($this->order);

        return $response;
    }
}