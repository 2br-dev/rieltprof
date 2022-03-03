<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Delivery;
use \ExternalApi\Model\Exception as ApiException;

/**
* Возвращает список доставок
*/
class GetList extends \ExternalApi\Model\AbstractMethods\AbstractGetList
{
    
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
        $order;
        
        
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
    * Возвращает возможный ключи для фильтров
    * 
    * @return [
    *   'поле' => [
    *       'type' => 'тип значения'
    *   ]
    * ]
    */
    public function getAllowableFilterKeys()
    {
        return [
            'title' => [
                'func' => self::FILTER_TYPE_LIKE,
                'type' => 'string',
            ],
            'user_type' => [
                'func' => self::FILTER_TYPE_EQ,
                'type' => 'string',
                'values' => [
                    'all', 'user', 'company'
                ]
            ],
            'target' => [
                'func' => self::FILTER_TYPE_EQ,
                'type' => 'string',            
                'values' => [
                    'all', 'orders', 'refill'
                ]
            ],
            'class' => [
                'func' => self::FILTER_TYPE_EQ,
                'type' => 'string',
            ]
        ];
    }

    /**
    * Устанавливает фильтр для выборки
    * 
    * @param \RS\Module\AbstractModel\EntityList $dao
    * @param array $filter
    * @return void
    */
    public function setFilter($dao, $filter)
    {
        parent::setFilter($dao, $filter);
        $dao->setFilter('public', 1);
    }    
    
    /**
    * Возвращает объект выборки объектов 
    * 
    * @return \RS\Module\AbstractModel\EntityList
    */
    public function getDaoObject()
    {
        return new \Shop\Model\DeliveryApi();
    }

    /**
     * Возвращает возможные значения для сортировки
     *
     * @return array
     */
    public function getAllowableOrderValues()
    {
        return ['id', 'id desc', 'sortn'];
    }
    
    /**
    * Возвращает список доставок по текущему оформляемому заказу из сессии
    * 
    * @param string sortn - сортировка элементов
    * 
    * @return array
    */
    private function getDeliveryListByCurrentOrder($sortn)
    {
        //Если корзины на этот момент уже не существует.
        if ( $this->order['expired'] || !$this->order->getCart() ){ 
            $errors[] = "Корзина заказа пуста. Необходимо наполнить корзину.";
            $response['response']['errors'] = $errors;
            $response['response']['error_status'] = 2;
            return $response;
        }
        
        $response['response'] = \Shop\Model\ApiUtils::getOrderDeliveryListSection($this->token, $this->order, $sortn);
        return $response;
    }
 
    /**
    * Выполняет запрос на выборку способов оплаты
    * Если указан параметр filter_by_current_order=1, то в ответ будет получен 
    * массив из текущего оформляемого заказа из сессии, т.е. секция $filter в это случае работать не будет, 
    * если filter_by_current_order=0, то фильтр будет работать по указанным дополнительных параметрам указанным ниже
    * Сортировка работает в обоих случаях
    * Если filter_by_current_order=0, и в модуле магазин выключен показ страницы доставок, то список будет пустой 
    * Токен надо передавать тогда, когда пользователь предварительно авторизован
    * 
    * @param string $token Авторизационный token
    * @param integer $filter_by_current_order Фильтрует по текущему оформляемому заказу 1 - фильтровать, 0 - фильтрация по указанным параметрам 
    * @param array $filter Фильтр, поддерживает в ключах поля: #filters-info
    * @param string $sort Сортировка по полю, поддерживает значения: #sort-info
    * @param integer $page Номер страницы, начинается с 1
    * @param mixed $pageSize Размер страницы
    * 
    * @example GET /api/methods/delivery.getlist?token=b45d2bc3e7149959f3ed7e94c1bc56a2984e6a86
    * GET /api/methods/delivery.getlist?filter_by_current_order=1
    * 
    * Ответ:
    * <pre>
    * {
    *     "response": {
    *         "summary": {  //Если filter_by_current_order=0
    *             "page": "1",
    *             "pageSize": "20",
    *             "total": "1"
    *         },
    *         "list": [
    *             {
    *                "id": "1",
    *                "title": "Самовывоз",
    *                "description": "Пункты выдачи товаров см. в разделе контакты",
    *                "xzone": null,
    *                "min_price": "0",
    *                "max_price": "0",
    *                "min_cnt": "0",
    *                "first_status": null,
    *                "user_type": "all",
    *                "extrachange_discount": "0",
    *                "extrachange_discount_type": "0",   
    *                "public": "1",
    *                "class": "myself",
    *                "picture": {
    *                    "original_url": "http://mega.readyscript.ru/storage/system/original/bae1170283c9bfc322365c76e977ce8b.png",
    *                    "big_url": "http://mega.readyscript.ru/storage/system/resized/xy_1000x1000/bae1170283c9bfc322365c76e977ce8b_10e21916.png",
    *                    "middle_url": "http://mega.readyscript.ru/storage/system/resized/xy_600x600/bae1170283c9bfc322365c76e977ce8b_56fabebd.png",
    *                    "small_url": "http://mega.readyscript.ru/storage/system/resized/xy_300x300/bae1170283c9bfc322365c76e977ce8b_3f23587f.png",
    *                    "micro_url": "http://mega.readyscript.ru/storage/system/resized/xy_100x100/bae1170283c9bfc322365c76e977ce8b_de333281.png"
    *                    "nano_url": "http://mega.readyscript.ru/storage/system/resized/xy_100x100/bae1170283c9bfc322365c76e977ce8b_de333281.png"
    *                },
    *                "extra_text": null, //filter_by_current_order=0
    *                "cost": "бесплатно", //filter_by_current_order=0
    *                "additional_html": "", //filter_by_current_order=0
    *                "error": false, //filter_by_current_order=0
    *                "delivery_periods": [  //filter_by_current_order=0
    *                    {
    *                        "zone": "4",
    *                        "text": "от 1 до 2х дней"
    *                    },
    *                    {
    *                        "zone": "2",
    *                        "text": "от 2 до 3х дней"
    *                    },
    *                    ...   
    *                ]
    *             },
    *             ...
    *         ],
    *         "warehouses": [ //Склады для пунктов самовывоза. Если filter_by_current_order=0
    *             {
    *                "id": "1",
    *                "title": "Основной склад",
    *                "alias": "sklad",
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
    *                "image": {
    *                    "original_url": "http://mega.readyscript.ru/storage/system/original/bae1170283c9bfc322365c76e977ce8b.png",
    *                    "big_url": "http://mega.readyscript.ru/storage/system/resized/xy_1000x1000/bae1170283c9bfc322365c76e977ce8b_10e21916.png",
    *                    "middle_url": "http://mega.readyscript.ru/storage/system/resized/xy_600x600/bae1170283c9bfc322365c76e977ce8b_56fabebd.png",
    *                    "small_url": "http://mega.readyscript.ru/storage/system/resized/xy_300x300/bae1170283c9bfc322365c76e977ce8b_3f23587f.png",
    *                    "micro_url": "http://mega.readyscript.ru/storage/system/resized/xy_100x100/bae1170283c9bfc322365c76e977ce8b_de333281.png"
    *                    "nano_url": "http://mega.readyscript.ru/storage/system/resized/xy_100x100/bae1170283c9bfc322365c76e977ce8b_de333281.png"
    *                },
    *                "meta_title": "",
    *                "meta_keywords": "",
    *                "meta_description": ""
    *            }
    *         ],
    *         "errors" : ['Ошибка'], //Если filter_by_current_order=0 
    *         "errors_status" : 2 //Появляется, если присутствует особый статус ошибки (истекла сессия, ошибки в корзине, корзина пуста). Если filter_by_current_order=0  
    *     }
    * }
    * </pre>
    * 
    * @return array Возвращает список публичных способов оплаты
    */
    protected function process($token = null, 
                               $filter_by_current_order = 0, 
                               $filter = [],
                               $sort = 'sortn',
                               $page = "1", 
                               $pageSize = "20")
    {
        if ($filter_by_current_order){
           return $this->getDeliveryListByCurrentOrder($sort); 
        }else{
           return parent::process($token, $filter, $sort, $page, $pageSize); 
        }
        
    }
}
