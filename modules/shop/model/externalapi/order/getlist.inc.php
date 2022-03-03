<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Order;
use \ExternalApi\Model\Exception as ApiException;

/**
* Возвращает список заказов
*/
class GetList extends \ExternalApi\Model\AbstractMethods\AbstractGetList
{
    const     
        RIGHT_COURIER = 2;
    
    protected
        $list_orders;
    
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
            self::RIGHT_COURIER => t('Загрузка только объектов курьера')
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
            'status' => [
                'func' => self::FILTER_TYPE_IN,
                'type' => 'integer[]',
            ]
        ];
    }
    
    /**
    * Возвращает возможные значения для сортировки
    * 
    * @return array
    */
    public function getAllowableOrderValues()
    {
        return ['dateof', 'dateof desc', 'id', 'id desc'];
    }
    
    /**
    * Возвращает объект выборки объектов 
    * 
    * @return \RS\Module\AbstractModel\EntityList
    */
    public function getDaoObject()
    {
        return new \Shop\Model\OrderApi();
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
        $dao->setFilter($this->makeFilter($filter));
        
        //Если пользователь курьер и не стоит флаг игнорирования, то сузим поиск
        if ($this->checkAccessError(self::RIGHT_COURIER) === false && !$this->method_params['ignore_user_group']) {
            //Курьерам отдаем только их заказы
            $dao->setFilter('courier_id', $this->token['user_id']);
        } elseif ($this->token->getApp()->getId() == 'mobilesiteapp') {
            $dao->setFilter('user_id', $this->token['user_id']);
        }
        
        if ($this->method_params['fulltext_filter']) {

            $q = $dao->queryObj();
            $q->select('A.*');
            if (!$q->issetTable(new \Shop\Model\Orm\OrderItem())) {
                $q->leftjoin(new \Shop\Model\Orm\OrderItem(), 'A.id=PRODUCT.order_id', 'PRODUCT');
                $q->leftjoin(new \Users\Model\Orm\User(), 'A.user_id=U.id', 'U');
            }
            $q->where("(order_num = '#term' OR PRODUCT.title like '%#term%' OR U.name like '%#term%' OR user_fio like '%#term%'  OR U.surname like '%#term%' OR U.e_mail like '%#term%')", [
                'term' => $this->method_params['fulltext_filter']
            ]);
            $q->groupby('A.id');

         //   echo $q; тестовый вывод результата запроса
        }
    }
    
    /**
    * Возвращает список объектов
    * 
    * @param \RS\Module\AbstractModel\EntityList $dao
    * @param integer $page
    * @param integer $pageSize
    * @return array
    */
    public function getResultList($dao, $page, $pageSize)
    {
        $this->list_orders = $dao->getList($page, $pageSize);
        
        $dao->getElement()->getPropertyIterator()->append([
            'dateof_iso' => new \RS\Orm\Type\MixedType([
                'description' => t('Дата создания в ISO'),
                'appVisible' => true
            ]),
            'dateof_date' => new \RS\Orm\Type\MixedType([
                'description' => t('Дата создания в dd.mm.YYYY'),
                'appVisible' => true
            ]),
            'dateof_datetime' => new \RS\Orm\Type\MixedType([
                'description' => t('Дата создания в dd.mm.YYYY HH:ii'),
                'appVisible' => true
            ]),
            'can_online_pay' => new \RS\Orm\Type\Integer([
                'description' => t('Возможна ли оплата online'),
                'appVisible' => true
            ]),
            'total' => new \RS\Orm\Type\Varchar([
                'description' => t('Общая отформатированная цена'),
                'appVisible' => true
            ])
        ]);
        
        foreach($this->list_orders as $order) {
            $timestamp_date = strtotime($order['dateof']);
            $order['dateof_iso']      = date('c', $timestamp_date);
            $order['dateof_date']     = date('d.m.Y', $timestamp_date);
            $order['dateof_datetime'] = date('d.m.Y H:i', $timestamp_date);
            $order['can_online_pay']  = $order->canOnlinePay(); //Можно ли оплатить заказ
            $order['total']           = \RS\Helper\CustomView::cost($order['totalcost'])." ".$order['currency_stitle']; //Можно ли оплатить заказ
        }
        
        return \ExternalApi\Model\Utils::extractOrmList( $this->list_orders );
    }    
    
 
    /**
    * Выполняет запрос на выборку заказов
    * 
    * @param string $token Авторизационный token
    * @param string $fulltext_filter Полнотекстовый фильтр. (В текущее время ищет по номеру заказа, электронной почте пользователя, фамилии и имени пользователя, названию товара)
    * @param array  $filter Фильтр, поддерживает в ключах поля: #filters-info
    * @param integer $ignore_user_group флаг указывает на, то игнорировать ли установленную группу у пользователя. Группа пользователя сужает доступный список заказов в соотвествиии с установленной группой. 
    * @param string $sort Сортировка по полю, поддерживает значения: #sort-info    
    * @param integer $page Номер страницы, начинается с 1
    * @param mixed $pageSize Размер страницы
    * @param array $sections Секции с дополнительными сведениями, которые должны быть включены в ответ. Возможные значения:
    * <b>users</b> - расширенная информация о пользователях, которые присутствуют у выводимых заказов
    * <b>statuses</b> - расширенная информация о статусах, которые присутствуют у выводимых заказов
    * <b>address</b> - расширенная информация об адресе доставки
    * 
    * @example GET /api/methods/order.getlist?token=b45d2bc3e7149959f3ed7e94c1bc56a2984e6a86&filter[status]=2&order=id desc&page=1&pageSize=20
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
    *                 "id": "159",
    *                 "order_num": "159",
    *                 "user_id": "2",
    *                 "currency": "RUB",
    *                 "currency_ratio": "1",
    *                 "currency_stitle": "р.",
    *                 "ip": "127.0.0.1",
    *                 "dateof": "2016-08-31 14:13:06",
    *                 "dateof_date": "31.08.2016",
    *                 "dateof_datetime": "31.08.2016 11:05",
    *                 "dateof_iso": "2016-08-31T14:13:06+03:00"
    *                 "dateofupdate": "2016-09-16 12:42:43",
    *                 "totalcost": "1836.45",
    *                 "total": "1 836.45 р",
    *                 "profit": "525.00",
    *                 "user_delivery_cost": "0.00",
    *                 "is_payed": "0",
    *                 "status": "2",
    *                 "admin_comments": "",
    *                 "user_text": "",
    *                 "hash": "0406df2de33b39217140f5b43bc664e6",
    *                 "contact_person": "",
    *                 "use_addr": "1",
    *                 "only_pickup_points": null,
    *                 "userfields_arr": [],
    *                 "delivery": "2",
    *                 "deliverycost": null,
    *                 "courier_id": "2",
    *                 "warehouse": "6",
    *                 "payment": "1",
    *                 "can_online_pay": true,
    *                 "comments": "",
    *                 "user_fio": null,
    *                 "user_email": null,
    *                 "user_phone": null,
    *                 "partner_id": null
    *             },
    *             ...  
    *         ],
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
    *                 "last_visit": "2016-09-19 16:02:09",
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
    *             "2": {
    *                 "id": "2",
    *                 "title": "Ожидает оплату",
    *                 "bgcolor": "#687482",
    *                 "type": "waitforpay"
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
    *         }
    *     }
    * }
    * </pre>
    * 
    * @return array Возвращает список заказов и связанные с заказом сведения.
    * Пользователям, состоящим в курьерской группе, отдаются только назначенные им заказы.
    */
    protected function process($token, 
                               $fulltext_filter = '', 
                               $filter = [],
                               $ignore_user_group = 0,
                               $sort = 'dateof desc', 
                               $page = "1", 
                               $pageSize = "20", 
                               $sections = ['user', 'status', 'address'])
    {
        $response = parent::process($token, $filter, $sort, $page, $pageSize);
        
        $users_ids = [];
        $status_ids = [];
        $addr_ids = [];
        
        if (isset($response['response']['list'])) {
            foreach($response['response']['list'] as $order) {
                $users_ids[$order['user_id']] = $order['user_id'];
                $status_ids[$order['status']] = $order['status'];
                $addr_ids[$order['use_addr']] = $order['use_addr'];
            }        
        }
        
        //Добавляем пользователей
        if (in_array('user', $sections)) {
            $users = [];
            if ($users_ids) {
                $user_api = new \Users\Model\Api();
                $user_api->setFilter('id', $users_ids, 'in');
                $users = $user_api->getAssocList('id');
            }
            $response['response']['user'] = \ExternalApi\Model\Utils::extractOrmList($users, 'id');
        }
        
        //Добавляем статусы
        if (in_array('status', $sections)) {
            $statuses = [];
            if ($status_ids) {
                $status_api = new \Shop\Model\UserStatusApi();
                $status_api->setFilter('id', $status_ids, 'in');
                $statuses = $status_api->getAssocList('id');
            }
            $response['response']['status'] = \ExternalApi\Model\Utils::extractOrmList($statuses, 'id');
        }        
        
        //Добавляем адреса
        if (in_array('address', $sections)) {
            $addresses = [];
            if ($addr_ids) {
                $address_api = new \Shop\Model\AddressApi();
                $address_api->setFilter('id', $addr_ids, 'in');
                $addresses = $address_api->getAssocList('id');
            }
            $response['response']['address'] = \ExternalApi\Model\Utils::extractOrmList($addresses, 'id');
        }        
        
        
        return $response;
    }
}
