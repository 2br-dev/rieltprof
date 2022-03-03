<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Payment;
use \ExternalApi\Model\Exception as ApiException;

/**
* Возвращает список оплат
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
        return new \Shop\Model\PaymentApi();
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
    * Возвращает список оплат по текущему оформляемому заказу из сессии
    * 
    * @param string sortn - сортировка элементов
    * 
    * @return array
    */
    private function getPaymentListByCurrentOrder($sortn)
    {
        //Если корзины на этот момент уже не существует.
        if ( $this->order['expired'] || !$this->order->getCart() ){ 
            $errors[] = "Корзина заказа пуста. Необходимо наполнить корзину.";
            $response['response']['errors'] = $errors;
            $response['response']['error_status'] = 2;
            return $response;
        }
        
        $response['response'] = \Shop\Model\ApiUtils::getOrderPaymentListSection($this->token, $this->order, $sortn);

        return $response;
    }    
    
    
 
    /**
    * Выполняет запрос на выборку способов оплаты
    * Если указан параметр filter_by_current_order=1, то в ответ будет получен 
    * массив из текущего оформляемого заказа из сессии, т.е. секция $filter в это случае работать не будет, 
    * если filter_by_current_order=0, то фильтр будет работать по указанным дополнительных параметрам указанным ниже
    * Сортировка работает в обоих случаях
    * Если filter_by_current_order=0, и в модуле магазин выключен показ страницы оплат, то список будет пустой 
    * 
    * Токен надо передавать тогда, когда пользователь предварительно авторизован
    * 
    * filter_by_current_delivery нужно передавать только вместе с filter_by_current_order
    * 
    * 
    * @param string $token Авторизационный token
    * @param integer  $filter_by_current_order Фильтр по текущему заказу, если он установлен в 1, то останые фильтры игнорируются. Фильтр проверяет текущий заказ пользователя, кроме filter_by_current_delivery
    * @param integer filter_by_current_delivery Добавляет фильтр по установлнной доставке, сюда нужно передать id самой доставки, либо ничего.
    * @param array  $filter Фильтр, поддерживает в ключах поля: #filters-info
    * @param string $sort Сортировка по полю, поддерживает значения: #sort-info
    * @param integer $page Номер страницы, начинается с 1
    * @param mixed $pageSize Размер страницы
    * 
    * @example GET /api/methods/payment.getlist?token=b45d2bc3e7149959f3ed7e94c1bc56a2984e6a86
    * GET /api/methods/payment.getlist?filter_by_current_order=1
    * 
    * Ответ:
    * <pre>
    * {
    *     "response": {
    *         "summary": {
    *             "page": "1",
    *             "pageSize": "20",
    *             "total": "1"
    *         },
    *         "list": [
    *             {
    *                 "id": "9",
    *                 "title": "Оплата электронными деньгами",
    *                 "description": "Оплата с помощью платежного агрегатора Интеркасса",
    *                 "first_status": "0",
    *                 "success_status": "0",
    *                 "user_type": "all",
    *                 "target": "all",
    *                 "delivery": [],
    *                 "public": "1",
    *                 "default_payment": "0",
    *                 "commission": "0",
    *                 "image": {
    *                    "original_url": "http://mega.readyscript.ru/storage/system/original/bae1170283c9bfc322365c76e977ce8b.png",
    *                    "big_url": "http://mega.readyscript.ru/storage/system/resized/xy_1000x1000/bae1170283c9bfc322365c76e977ce8b_10e21916.png",
    *                    "middle_url": "http://mega.readyscript.ru/storage/system/resized/xy_600x600/bae1170283c9bfc322365c76e977ce8b_56fabebd.png",
    *                    "small_url": "http://mega.readyscript.ru/storage/system/resized/xy_300x300/bae1170283c9bfc322365c76e977ce8b_3f23587f.png",
    *                    "micro_url": "http://mega.readyscript.ru/storage/system/resized/xy_100x100/bae1170283c9bfc322365c76e977ce8b_de333281.png"
    *                    "nano_url": "http://mega.readyscript.ru/storage/system/resized/xy_100x100/bae1170283c9bfc322365c76e977ce8b_de333281.png"
    *                },  
    *                 "class": "interkassa"
    *             },
    *             ...
    *         ]
    *     }
    * }
    * </pre>
    * 
    * @return array Возвращает список публичных способов оплаты
    */
    protected function process($token = null, 
                               $filter_by_current_order = 0,  
                               $filter_by_current_delivery = 0,  
                               $filter = [],
                               $sort = 'sortn',
                               $page = "1", 
                               $pageSize = "20")
    {
        if ($filter_by_current_order){
            if ($filter_by_current_delivery > 0){ //Если передана ещё и доставка, то мы у заказа её установим
               $this->order['delivery'] = $filter_by_current_delivery; 
            }
            return $this->getPaymentListByCurrentOrder($sort); 
        }else{
            return parent::process($token, $filter, $sort, $page, $pageSize);
        }
    }
}
