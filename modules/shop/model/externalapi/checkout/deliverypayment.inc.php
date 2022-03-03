<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Checkout;
use Main\Model\StatisticEvents;
use \ExternalApi\Model\Exception as ApiException;

/**
* Реализует второй шаг оформления заказа. Этап отправления доставок и оплат.
*/
class DeliveryPayment extends \ExternalApi\Model\AbstractMethods\AbstractMethod
{
    const
        RIGHT_LOAD = 1;
        
    protected
        $token_require = false;
        
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
            self::RIGHT_LOAD => t('Отправка данных')
        ];
    }

    /**
    * Реализует второй шаг оформления заказа. Этап отправления выбранных доставок и оплат. 
    * 
    * @param string $token Авторизационный токен
    * @param integer $delivery id выбранной доставки
    * @param mixed $delivery_extra дополнительные данные для установки у доставки
    * @param integer $payment id выбранной оплаты
    * @param integer $warehouse id выбранного склада
    * 
    * @example POST /api/methods/checkout.deliverypayment?delivery=1&payment=2&warehouse=3
    * 
    * Ответ:
    * <pre>
    * {
    *        "response": {
    *            "success" : false,
    *            "errors" : ['Ошибка'],    
    *            "errors_status" : 2 //Появляется, если присутствует особый статус ошибки (истекла сессия, ошибки в корзине, корзина пуста),
    *            "next_step": {
    *            "cartdata": {
    *                "total": "21 360 р.",
    *                "total_base": "21 110 р.",
    *                "total_discount": "0 р.",
    *                "items": [
    *                    {
    *                        "id": "5ch7wp1ygz",
    *                        "cost": "21 110 р.",
    *                        "base_cost": "21 110 р.",
    *                        "single_cost": "21 110 р.",
    *                        "single_weight": 200,
    *                        "discount": "0 р.",
    *                        "sub_products": [
    *                            {
    *                                "amount": "1",
    *                                "cost": "43 440 р.",
    *                                "single_cost": "43 440 р.",
    *                                "checked": false,
    *                                "discount": "0 р.",
    *                                "title": "Планшет Fujitsu STYLISTIC Q550",
    *                                "product_id": "5ch7wp1ygz",
    *                                "image": {
    *                                    "id": "2340",
    *                                    "title": null,
    *                                    "original_url": "http://mega.readyscript.local/storage/photo/original/e/ubg07um3qxseroq.jpg",
    *                                    "big_url": "http://mega.readyscript.local/storage/photo/resized/xy_1000x1000/e/ubg07um3qxseroq_444ae71f.jpg",
    *                                    "middle_url": "http://mega.readyscript.local/storage/photo/resized/xy_600x600/e/ubg07um3qxseroq_239bd8fd.jpg",
    *                                    "small_url": "http://mega.readyscript.local/storage/photo/resized/xy_300x300/e/ubg07um3qxseroq_4a423e3f.jpg",
    *                                    "micro_url": "http://mega.readyscript.local/storage/photo/resized/xy_100x100/e/ubg07um3qxseroq_ab5254c1.jpg"
    *                                    "nano_url": "http://mega.readyscript.local/storage/photo/resized/xy_100x100/e/ubg07um3qxseroq_ab5254c1.jpg"
    *                                },
    *                                "id": "575",
    *                                "unit": null,
    *                                "allow_concomitant_count_edit": false
    *                            }
    *                        ],
    *                        "bonuses": 2111,
    *                        "title": "Ноутбук HP Compaq Mini 5103 XM602AA",
    *                        "image": {
    *                            "id": "2361",
    *                            "title": null,
    *                            "original_url": "http://mega.readyscript.local/storage/photo/original/i/06eq2uxurfz9l3n.jpg",
    *                            "big_url": "http://mega.readyscript.local/storage/photo/resized/xy_1000x1000/i/06eq2uxurfz9l3n_c2e6b23d.jpg",
    *                            "middle_url": "http://mega.readyscript.local/storage/photo/resized/xy_600x600/i/06eq2uxurfz9l3n_ce60b3b1.jpg",
    *                            "small_url": "http://mega.readyscript.local/storage/photo/resized/xy_300x300/i/06eq2uxurfz9l3n_a7b95573.jpg",
    *                            "micro_url": "http://mega.readyscript.local/storage/photo/resized/xy_100x100/i/06eq2uxurfz9l3n_46a93f8d.jpg"
    *                            "nano_url": "http://mega.readyscript.local/storage/photo/resized/xy_100x100/i/06eq2uxurfz9l3n_46a93f8d.jpg"
    *                        },
    *                        "entity_id": "578",
    *                        "amount": "1",
    *                        "amount_error": "",
    *                        "offer": "0",
    *                        "model": null,
    *                        "multioffers": null,
    *                        "multioffers_string": "",
    *                        "unit": null
    *                    }
    *                ],
    *                "items_count": 1,
    *                "total_weight": 200,
    *                "checkcount": 1,
    *                "currency": "р.",
    *                "errors": [],
    *                "has_error": false,
    *                "taxes": [
    *                    {
    *                        "title": "НДС, 18%",
    *                        "cost": "3 220.17 р."
    *                    }
    *                ],
    *                "subtotal": 17889.83,
    *                "total_without_delivery": "21 110 р.",
    *                "total_without_delivery_unformatted": 21110,
    *                "total_without_payment_commission": "21 110 р.",
    *                "total_without_payment_commission_unformatted": 21110,
    *                "delivery": {
    *                    "id": "4",
    *                    "title": "СДЭК",
    *                    "description": "",
    *                    "picture": null,
    *                    "xzone": null,
    *                    "min_price": "0",
    *                    "max_price": "0",
    *                    "min_cnt": "0",
    *                    "first_status": "0",
    *                    "user_type": "all",
    *                    "extrachange_discount": "0",
    *                    "public": "1",
    *                    "class": "cdek",
    *                    "delivery_periods": [],
    *                    "mobilesiteapp_additional_html": null,
    *                    "cost": "250 р."
    *                },
    *                "total_unformatted": 21360,
    *                "total_bonuses": 2111,
    *                "coupons": [],
    *                "total_discount_unformatted": 0,
    *                "user": {
    *                    "id": "1",
    *                    "name": "Супервизор тест тест",
    *                    "surname": " Моя фамилия",
    *                    "midname": " ",
    *                    "e_mail": "admin@admin.ru",
    *                    "login": "admin@admin.ru",
    *                    "phone": "+7(962)867-84-30",
    *                    "sex": "",
    *                    "subscribe_on": "0",
    *                    "dateofreg": "2016-03-14 19:58:58",
    *                    "ban_expire": null,
    *                    "last_visit": "2017-03-09 11:15:05",
    *                    "is_company": "0",
    *                    "company": "",
    *                    "company_inn": "",
    *                    "data": [],
    *                    "push_lock": null,
    *                    "user_cost": null,
    *                    "birthday": null,
    *                    "bonuses": "200"
    *                },
    *                "only_pickup_points": 0,
    *                "use_addr": 3,
    *                "payment": {
    *                    "id": "1",
    *                    "title": "Безналичный расчет",
    *                    "description": "Оплата должна производиться с расчетного счета предприятия",
    *                    "picture": {
    *                        "original_url": "http://mega.readyscript.local/storage/system/original/128c59be0869e95d4f084be06a036b82.png",
    *                        "big_url": "http://mega.readyscript.local/storage/system/resized/xy_1000x1000/128c59be0869e95d4f084be06a036b82_bbe591bf.png",
    *                        "middle_url": "http://mega.readyscript.local/storage/system/resized/xy_600x600/128c59be0869e95d4f084be06a036b82_35a9791d.png",
    *                        "small_url": "http://mega.readyscript.local/storage/system/resized/xy_300x300/128c59be0869e95d4f084be06a036b82_5c709fdf.png",
    *                        "micro_url": "http://mega.readyscript.local/storage/system/resized/xy_100x100/128c59be0869e95d4f084be06a036b82_bd60f521.png"
    *                        "nano_url": "http://mega.readyscript.local/storage/system/resized/xy_100x100/128c59be0869e95d4f084be06a036b82_bd60f521.png"
    *                    },
    *                    "first_status": "0",
    *                    "success_status": "0",
    *                    "user_type": "all",
    *                    "target": "all",
    *                    "delivery": [],
    *                    "public": "1",
    *                    "default_payment": "0",
    *                    "commission": "0",
    *                    "class": "bill"
    *                },
    *                "warehouse": {
    *                    "id": "1",
    *                    "title": "Основной склад",
    *                    "alias": "sklad",
    *                    "image": null,
    *                    "description": "<p>Наш склад находится в центре города. Предусмотрена удобная парковка для автомобилей и велосипедов.</p>",
    *                    "adress": "г. Краснодар, улица Красных Партизан, 246",
    *                    "phone": "+7(123)456-78-90",
    *                    "work_time": "с 9:00 до 18:00",
    *                    "coor_x": "45.0483",
    *                    "coor_y": "38.9745",
    *                    "default_house": "1",
    *                    "public": "0",
    *                    "checkout_public": "1",
    *                    "use_in_sitemap": "0",
    *                    "xml_id": null,
    *                    "meta_title": "",
    *                    "meta_keywords": "",
    *                    "meta_description": ""
    *                },
    *                "address": {
    *                    "id": "3",
    *                    "user_id": "1",
    *                    "order_id": "0",
    *                    "zipcode": "350000",
    *                    "country": "Россия",
    *                    "region": "Краснодарский край",
    *                    "city": "Краснодар",
    *                    "address": "ул. Ленина 97",
    *                    "city_id": "1506",
    *                    "region_id": "1284",
    *                    "country_id": "1208",
    *                    "deleted": "0"
    *                }
    *            }
    *        }
    *        }
    *    }
    * </pre>
    * 
    * @return array Возращает, либо пустой массив ошибок, если успешно
    * @throws \RS\Event\Exception
    */
    protected function process($token = null, 
                               $delivery = 0,
                               $delivery_extra = [],
                               $payment = 0,
                               $warehouse = 0)
    {   
        $errors = [];
        $response['response']['success'] = false; 
              
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
        
        $this->order['delivery']  = $delivery; //Доставка
        $this->order['warehouse'] = $warehouse; //Выбранный склад
        $this->order['payment']   = $payment; //Оплата
        
        if ($delivery_extra){ //Добавим дополнительные данные в заказ
            if (strpos($delivery_extra, '{') !== false){
                $delivery_extra = json_decode(htmlspecialchars_decode($delivery_extra), true);
            }
            $this->order->addExtraKeyPair('delivery_extra', $delivery_extra);
        }
        
        $errors = $this->order->getErrors();
        $response['response']['errors']  = $errors;
        if (!$this->order->hasError()){
           $response['response']['success'] = true;
           
           // Событие для модификации корзины
           \RS\Event\Manager::fire('checkout.confirm', [
               'order' => $this->order,
               'cart' => $this->order->getCart()
           ]);
           
           //Получем данные для следующего шага
           $api = new \Shop\Model\ApiUtils();
           $response['response']['next_step']['cartdata'] = $api->fillProductItemsData($this->order); 
           
           // Фиксация события "Выбран способ оплаты" для статистики
           \RS\Event\Manager::fire('statistic', ['type' => StatisticEvents::TYPE_SALES_SELECT_DELIVERY]);
           \RS\Event\Manager::fire('statistic', ['type' => StatisticEvents::TYPE_SALES_SELECT_PAYMENT_METHOD]);
        }

        return $response;
    }
}