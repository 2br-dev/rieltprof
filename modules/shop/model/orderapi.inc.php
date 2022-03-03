<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model;

use Main\Model\NoticeSystem\HasMeterInterface;
use Main\Model\NoticeSystem\MeterApi;
use RS\Cache\Manager as CacheManager;
use RS\Config\Loader as ConfigLoader;
use RS\Helper\Tools as HelperTools;
use RS\Orm\Request as OrmRequest;
use RS\Site\Manager as SiteManager;
use Shop\Model\Orm\CartItem;
use Shop\Model\Orm\Order;
use Shop\Model\Orm\OrderItem;
use Shop\Model\Orm\Region;
use Shop\Model\Orm\UserStatus;
use RS\Module\AbstractModel;
use Users\Model\Orm\User;
use Users\Model\Orm\UserInGroup;

/**
 * API для работы с заказами
 */
class OrderApi extends AbstractModel\EntityList implements HasMeterInterface
{
    /** Идентификатор счетчика заказов */
    const METER_ORDER = 'rs-admin-menu-allorders';
    const ORDER_FILTER_ALL = 'all';
    const ORDER_FILTER_SUCCESS = 'success';
    const ORDER_SHOW_TYPE_NUM = 'num';
    const ORDER_SHOW_TYPE_SUMM = 'summ';

    public function __construct()
    {
        parent::__construct(new Order, [
            'multisite' => true,
            'aliasField' => 'order_num',
            'defaultOrder' => 'id DESC',
        ]);
    }

    /**
     * Возвращает класс, который отвечает за управление счетчиками просмотров
     *
     * @param integer|null user_id ID пользователя. Если пользователь не задан, то используется текущий пользователь
     * @return \Main\Model\NoticeSystem\MeterApi
     */
    public function getMeterApi($user_id = null)
    {
        return new MeterApi($this->obj_instance, self::METER_ORDER, $this->getSiteContext(), $user_id);
    }

    /**
     * Возвращает статистику по заказам
     *
     * @return array
     */
    public function getStatistic()
    {
        $query = OrmRequest::make()
            ->select('COUNT(*) cnt')
            ->from($this->obj_instance);

        $queries = [];
        $queries['total_orders'] = $query;
        $queries['open_orders'] = clone $query->where("status NOT IN (#statuses)", [
            'statuses' => implode(',', UserStatusApi::getStatusesIdByType(UserStatus::STATUS_SUCCESS))
        ]);
        $queries['closed_orders'] = clone $query->whereIn('status', UserStatusApi::getStatusesIdByType(UserStatus::STATUS_SUCCESS));
        $queries['last_order_date'] = OrmRequest::make()
            ->select('dateof cnt')
            ->from($this->obj_instance)
            ->where(['site_id' => SiteManager::getSiteId()])
            ->orderby('dateof DESC')->limit(1);

        foreach ($queries as &$one) {
            $one = $one->exec()->getOneField('cnt', 0);
        }
        return $queries;
    }

    /**
     * Возвращает года, за которые есть статистика
     *
     * @param string $order_filter
     * @param int $lastrange максимальное количество годов в списке
     * @return array
     */
    public function getOrderYears($order_filter = self::ORDER_FILTER_ALL, $lastrange = 5)
    {
        $site_id = SiteManager::getSiteId();
        $q = OrmRequest::make()
            ->select('YEAR(dateof) as year')
            ->from($this->obj_instance)
            ->where('dateof >= NOW()-INTERVAL #lastrange YEAR', ['lastrange' => $lastrange])
            ->where(['site_id' => $site_id])
            ->groupby('YEAR(dateof)');

        if ($order_filter == self::ORDER_FILTER_SUCCESS) {
            $statuses_id = UserStatusApi::getStatusesIdByType(UserStatus::STATUS_SUCCESS);
            $q->whereIn('status', $statuses_id);
        }

        return $q->exec()->fetchSelected(null, 'year');
    }

    /**
     * Возвращает даты заказов, сгруппированные по годам. Для видета статистики
     *
     * @param string $order_filter фильтр заказов. Если all - то все заказы, success - только завершенные
     * @param string $show_type
     * @param mixed $lastrange
     * @param bool $cache - флаг кэширования, если true, то кэш будет использоваться
     * @return array
     */
    public function ordersByYears($order_filter = self::ORDER_FILTER_ALL, $show_type = self::ORDER_SHOW_TYPE_NUM, $lastrange = 5, $cache = true)
    {
        $site_id = SiteManager::getSiteId();

        if ($cache) {
            $result = CacheManager::obj()
                ->expire(300)
                ->request([$this, 'ordersByYears'], $order_filter, $show_type, $lastrange, false, $site_id);
        } else {
            $q = OrmRequest::make()
                ->select('dateof, COUNT(*) cnt, SUM(totalcost) total_cost')
                ->from($this->obj_instance)
                ->where('dateof >= NOW()-INTERVAL #lastrange YEAR', ['lastrange' => $lastrange])
                ->where(['site_id' => $site_id])
                ->groupby('YEAR(dateof), MONTH(dateof)')
                ->orderby('dateof');

            if ($order_filter == self::ORDER_FILTER_SUCCESS) {
                $statuses_id = UserStatusApi::getStatusesIdByType(UserStatus::STATUS_SUCCESS);
                $q->whereIn('status', $statuses_id);
            }

            $res = $q->exec();
            $result = [];
            while ($row = $res->fetchRow()) {
                $date = strtotime($row['dateof']);
                $year = date('Y', $date);
                $result[$year]['label'] = $year;
                $result[$year]['data'][date('n', $date) - 1] = [
                    'x' => mktime(4, 0, 0, date('n', $date), 1) * 1000,
                    'y' => $show_type == self::ORDER_SHOW_TYPE_NUM ? $row['cnt'] : $row['total_cost'],
                    'pointDate' => $date * 1000,
                    'total_cost' => $row['total_cost'],
                    'count' => $row['cnt']
                ];
            }

            //Добавляем нулевые месяцы
            foreach ($result as $year => $val) {
                $month_list = [];
                for ($month = 1; $month <= 12; $month++) {
                    $month_list[$month - 1] = isset($result[$year]['data'][$month - 1]) ? $result[$year]['data'][$month - 1] : [
                        'x' => mktime(4, 0, 0, $month, 1) * 1000,
                        'y' => 0,
                        'pointDate' => mktime(4, 0, 0, $month, 1, $year) * 1000,
                        'total_cost' => 0,
                        'count' => 0
                    ];
                }
                $result[$year]['data'] = $month_list;
            }

        }
        return $result;
    }

    /**
     * Возвращает даты заказов, сгруппированные по годам. Для видета статистики
     *
     * @param mixed $lastrange
     * @param string $show_type
     * @param string $order_filter фильтр заказов. Если all - то все заказы, success - только завершенные
     * @param bool $cache - флаг кэширования, если true, то кэш будет использоваться
     * @return array
     */
    public function ordersByMonth($order_filter = self::ORDER_FILTER_ALL, $show_type = self::ORDER_SHOW_TYPE_NUM, $lastrange = 1, $cache = true)
    {
        $site_id = SiteManager::getSiteId();

        if ($cache) {
            $result = CacheManager::obj()
                ->expire(300)
                ->request([$this, 'ordersByMonth'], $order_filter, $show_type, $lastrange, false, $site_id);
        } else {
            $start_time = strtotime('-1 month');

            $q = OrmRequest::make()
                ->select('dateof, COUNT(*) cnt, SUM(totalcost) total_cost')
                ->from($this->obj_instance)
                ->where("dateof >= '#starttime'", ['starttime' => date('Y-m-d', $start_time)])
                ->where(['site_id' => $site_id])
                ->groupby('DATE(dateof)')
                ->orderby('dateof');

            if ($order_filter == self::ORDER_FILTER_SUCCESS) {
                $statuses_id = UserStatusApi::getStatusesIdByType(UserStatus::STATUS_SUCCESS);
                $q->whereIn('status', $statuses_id);
            }

            $res = $q->exec();
            $min_date = null;
            $max_date = null;
            $result = [];
            while ($row = $res->fetchRow()) {
                $date = strtotime($row['dateof']);
                if ($min_date === null || $date < $min_date) {
                    $min_date = $date;
                }
                if ($max_date === null || $date > $max_date) {
                    $max_date = $date;
                }
                $ymd = date('Ymd', $date);
                $result[0][$ymd] = [
                    'x' => $date * 1000,
                    'y' => $show_type == self::ORDER_SHOW_TYPE_NUM ? $row['cnt'] : $row['total_cost'],
                    'total_cost' => $row['total_cost'],
                    'count' => $row['cnt']
                ];
            }

            //Заполняем пустые дни
            $i = 0;
            $today = mktime(23, 59, 59);
            while (($time = strtotime("+$i day", $start_time)) && $time <= $today) {
                $ymd = date('Ymd', $time);
                if (!isset($result[0][$ymd])) {
                    $result[0][$ymd] = [
                        'x' => $time * 1000,
                        'y' => 0,
                        'total_cost' => 0,
                        'count' => 0
                    ];
                }
                $i++;
            }
            ksort($result[0]);
            $result[0] = array_values($result[0]);
        }
        return $result;
    }

    /**
     * Возвращает количество заказов для каждого из существующих статусов
     *
     * @return array
     */
    public function getStatusCounts()
    {
        $q = clone $this->queryObj();
        $q->select = 'status, COUNT(*) cnt';
        $q->groupby('status');
        return $q->exec()->fetchSelected('status', 'cnt');
    }

    /**
     * Генерирует уникальный идентификатор заказа
     *
     * @param Order $order - объект заказа
     *
     * @return string
     */
    public function generateOrderNum($order)
    {
        $config = ConfigLoader::byModule('shop');
        $mask = $config['generated_ordernum_mask'];
        $numbers = $config['generated_ordernum_numbers'];

        $order_num = HelperTools::generatePassword($numbers, '0123456789');

        $code = mb_strtoupper(str_replace("{n}", $order_num, $mask));

        //Посмотрим есть ли такой код уже в базе
        $found_order = OrmRequest::make()
            ->from(new Order())
            ->where([
                'order_num' => $code,
                'site_id' => SiteManager::getSiteId()
            ])
            ->object();

        if ($found_order) {
            return $this->generateOrderNum($order);
        }

        return $code;
    }

    /**
     * Возвращает количество заказов, оформленных пользователем
     *
     * @param integer $user_id - id пользователя
     *
     * @return Order|false
     */
    function getUserOrdersCount($user_id)
    {
        return OrmRequest::make()
            ->from(new Order)
            ->where([
                'user_id' => $user_id,
                'site_id' => SiteManager::getSiteId()
            ])->count();
    }

    /**
     * Создаёт отчёт о заказах за выбранный период и при заданных параметрах
     *
     * @param OrmRequest $request - объект запроса списка заказов
     * @return array
     */
    public function getReport(OrmRequest $request)
    {
        $list = [];
        //Соберём общую статистику
        $request->select = "";
        $request->limit = "";
        $list['all'] = $request
            ->select('
                COUNT(id) as orderscount,
                SUM(totalcost) as totalcost,
                SUM(user_delivery_cost) as user_delivery_cost,
                SUM(deliverycost) as single_deliverycost
            ')
            ->exec()
            ->fetchRow();

        //Цена без учёта стоимости доставки
        $list['all']['deliverycost'] = $list['all']['user_delivery_cost'] + $list['all']['single_deliverycost'];
        $list['all']['total_without_delivery'] = $list['all']['totalcost'] - $list['all']['deliverycost'];
        //Соберём статистику по типам оплаты
        $request->select = "";
        $list['payment'] = $request
            ->select('
                payment,
                COUNT(id) as orderscount,
                SUM(totalcost) as totalcost,
                SUM(user_delivery_cost) as user_delivery_cost,
                SUM(deliverycost) as single_deliverycost
            ')
            ->groupby('payment')
            ->exec()
            ->fetchSelected('payment');

        if (!empty($list['payment'])) {

            foreach ($list['payment'] as $key => $item) {
                $list['payment'][$key]['deliverycost'] = $item['user_delivery_cost'] + $item['single_deliverycost'];
                $list['payment'][$key]['total_without_delivery'] = $item['totalcost'] - $list['payment'][$key]['deliverycost'];
            }
        }

        //Соберём статистику по типам доставки
        $request->select = "";
        $request->groupby = "";
        $list['delivery'] = $request
            ->select('
                delivery,
                COUNT(id) as orderscount,
                SUM(totalcost) as totalcost,
                SUM(user_delivery_cost) as user_delivery_cost,
                SUM(deliverycost) as single_deliverycost
            ')
            ->groupby('delivery')
            ->exec()
            ->fetchSelected('delivery');

        if (!empty($list['delivery'])) {

            foreach ($list['delivery'] as $key => $item) {
                $list['delivery'][$key]['deliverycost'] = $item['user_delivery_cost'] + $item['single_deliverycost'];
            }
        }

        return $list;
    }

    /**
     * Добавляет массив дополнительных экстра данных в заказ, которые будут отображаться в заказе в админ панели
     *
     * @param Order $order - объект заказа
     * @param string $step - идентификатор шага оформления (address, delivery, payment, confirm)
     * @param array $order_extra - массив доп. данных
     *
     * @return void
     */
    public function addOrderExtraDataByStep(Order $order, $step = 'order', $order_extra = [])
    {
        if (!empty($order_extra)) { //Заносим дополнительные данные, если они есть
            $arr = [$step => $order_extra];
            $order['order_extra'] = array_merge((array)$order['order_extra'], $arr);
        }
    }

    /**
     * Ищет город по части слова
     *
     * @param string $query - часть слова города для его запроса
     * @param int $region_id - id региона
     * @param int $country_id - id страны
     *
     * @return Region[]
     */
    public function searchCityByRegionOrCountry($query, $region_id = null, $country_id = null)
    {
        $q = OrmRequest::make()
            ->from(new Region(), "R1")
            ->where("R1.title like '%" . $query . "%'")
            ->where([
                'R1.site_id' => SiteManager::getSiteId()
            ])
            ->orderby('title');
        if ($region_id) { //Если задан регион
            $q->where([
                'R1.parent_id' => $region_id
            ]);
        } elseif ($country_id) { //Если задана только страна
            $sql = OrmRequest::make()
                ->select('R2.id')
                ->from(new Region(), 'R2')
                ->where([
                    'R2.site_id' => SiteManager::getSiteId(),
                    'R2.parent_id' => $country_id
                ])
                ->toSql();
            $q->where('R1.parent_id IN (' . $sql . ')');
        }
        $cities = $q->objects();
        return $cities ? $cities : [];
    }

    /**
     * Пересчитывает доходность ранее оформленных заказов на основе Закупочной цены товаров.
     * Процесс выполняется пошагово по $timeout сек.
     *
     * @param integer $position - стартовая позиция
     * @param integer $timeout - лимит по времени выполнения одного шага в секундах
     *
     * @return bool(true) | int - True в случае успешного завершения, иначе позиция для следующего запуска
     */
    public static function calculateOrdersProfit($position = 0, $timeout = 20)
    {
        $order_item = new OrderItem();
        $start_time = microtime(true);

        $offset = $position;
        $limit = 50;

        $q = OrmRequest::make()
            ->from($order_item)
            ->where('entity_id > 0')
            ->where([
                'type' => OrderItem::TYPE_PRODUCT
            ])
            ->limit($limit);

        /** @var OrderItem[] $items */
        while ($items = $q->offset($offset)->objects()) {
            $i = 0;
            foreach ($items as $item) {
                $item['profit'] = $item->getProfit();
                if ($item['profit'] !== null) {
                    $item->update();
                }
                $i++;
                if ($timeout > 0 && microtime(true) - $start_time > $timeout) return $offset + $i;
            }
            $offset += $limit;
        }

        $subsql = OrmRequest::make()
            ->select('SUM(profit)')
            ->from($order_item, 'I')
            ->where('I.order_id = O.id');

        OrmRequest::make()
            ->update()
            ->from(new Order(), 'O')
            ->set('profit = (' . $subsql . ')')
            ->exec();

        return true;
    }

    /**
     * Возвращает список пользователей-менеджеров заказов.
     * Группа, пользователи которой считаются менеджерами устанавливается в настройках модуля Магазин
     *
     * @return array
     */
    public static function getUsersManagers()
    {
        $config = ConfigLoader::byModule(__CLASS__);
        if ($manager_group = $config['manager_group']) {
            return OrmRequest::make()
                ->select('U.*')
                ->from(new User(), 'U')
                ->join(new UserInGroup(), 'G.user = U.id', 'G')
                ->where([
                    'G.group' => $config['manager_group'],
                ])
                ->orderby('surname asc, name asc, midname asc')
                ->objects(null, 'id');
        }
        return [];
    }

    /**
     * Возвращает список пользователей-менеджеров заказов.
     * Группа, пользователи которой считаются менеджерами устанавливается в настройках модуля Магазин
     *
     * @param string[] $root - список который будет добавлен в начало
     * @return string[]
     */
    public static function getUsersManagersName($root = [])
    {
        $result = [];
        foreach (self::getUsersManagers() as $user) {
            $result[$user['id']] = $user->getFio() . " ({$user['id']})";
        }
        return $root + $result;
    }

    /**
     * Создаёт новый заказ из переданного
     *
     * @param integer $order_id - id заказа, который надо повторить
     * @return Order $order
     */
    public function repeatOrder($order_id)
    {
        $old_order = new Order($order_id);
        $new_order = new Order();
        $new_order->getFromArray($old_order->getValues());
        //Удалим ненужные поля
        $new_order->setTemporaryId();
        unset($new_order['order_num']);
        unset($new_order['ip']);
        unset($new_order['manager_user_id']);
        unset($new_order['create_refund_receipt']);
        unset($new_order['is_payed']);
        unset($new_order['status']);
        unset($new_order['basket']);
        unset($new_order['admin_comments']);
        unset($new_order['user_text']);
        unset($new_order['is_exported']);
        unset($new_order['userfields']);
        unset($new_order['profit']);
        unset($new_order['contact_person']);
        unset($new_order['comments']);
        unset($new_order['substatus']);
        unset($new_order['courier_id']);
        unset($new_order['user_delivery_cost']);
        $new_order['extra'] = $old_order['extra'];
        //Добавим в корзину тоже товары, которые присутствуют на сайте
        $order_cart = $old_order->getCart()->getOrderData(); //Получим данные по корзине

        $items = [];
        foreach ($order_cart['items'] as $uniq_id => $item) {
            /** @var CartItem $cartitem */
            $cartitem = $item['cartitem'];
            $items[$uniq_id] = $cartitem->getValues();
            $items[$uniq_id]['multioffers'] = unserialize($items[$uniq_id]['multioffers']);
            foreach ($cartitem->getDiscounts() as $discount) {
                if ($discount->getSource() == Cart::DISCOUNT_SOURCE_OLD_COST) {
                    $items[$uniq_id]['discount_from_old_cost'] = $discount->getAmountOfDiscount();
                }
            }
        }
        $new_order->getCart()->updateOrderItems($items);

        //Посчитаем доставку
        $new_order['user_delivery_cost'] = null;

        return $new_order;
    }

    /**
     * Обновляет свойства у группы объектов
     *
     * @param array $data - ассоциативный массив со значениями обновляемых полей
     * @param array $ids - список id объектов, которые нужно обновить
     * @return integer - возвращает количество обновленных элементов
     */
    public function multiUpdate(array $data, $ids = [])
    {
        if ($this->noWriteRights()) return false;

        if (!empty($data)) {
            foreach ($ids as $id) {
                $order = new Order($id);
                if (isset($data['notify_user']) && $data['notify_user'] == 0) {
                    $order['notify_user'] = false;
                    unset($data['notify_user']);
                }
                $order->getFromArray($data);
                $order->update();
            }
        }
        return count($ids);
    }

    /**
     * Ищет заказ по различным полям
     *
     * @param string $term поисковая строка
     * @param array $fields массив с полями, в которых необходимо произвести поиск
     * @param integer $limit максимальное количество результирующих строк
     * @return array
     */
    public function search($term, $fields, $limit)
    {
        $this->resetQueryObject();
        $q = $this->queryObj();
        $q->select = 'A.*';

        $q->openWGroup();
        if (in_array('user', $fields)) {
            $q->leftjoin(new User(), 'U.id = A.user_id', 'U');
            $q->where("CONCAT(`U`.`surname`, ' ', `U`.`name`,' ', `U`.`midname`) like '%#term%'", [
                'term' => $term
            ]);
        }

        foreach ($fields as $field) {
            if ($field == 'user') continue;
            $this->setFilter($field, $term, '%like%', 'OR');
        }

        $q->closeWGroup();

        return $this->getList(1, $limit);
    }

    /**
     * Возвращает id сайта, которому принадлежит заказ
     *
     * @param int $order_id - id заказа
     * @return int
     */
    public function getSiteIdByOrderId($order_id)
    {
        $order_site_id = OrmRequest::make()
            ->select('site_id')
            ->from(new Order())
            ->where(['id' => $order_id])
            ->exec()->getOneField('site_id');

        return $order_site_id;
    }
}
