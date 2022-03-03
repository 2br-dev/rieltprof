<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model;

use RS\Config\Loader as ConfigLoader;
use RS\Event\Manager as EventManager;
use RS\Exception as RSException;
use RS\Module\AbstractModel\EntityList;
use RS\Orm\Request as OrmRequest;
use RS\Site\Manager as SiteManager;
use Shop\Model\Orm\Delivery;
use Shop\Model\Orm\DeliveryDir;
use Shop\Model\Orm\DeliveryXZone;
use Shop\Model\Orm\Order;
use Users\Model\Orm\User;
use Users\Model\Orm\UserInGroup;

/**
 * API функции для работы со способами доставки для текущего сайта
 */
class DeliveryApi extends EntityList
{
    protected static $types;

    function __construct()
    {
        parent::__construct(new Delivery(),
            [
                'nameField' => 'title',
                'multisite' => true,
                'defaultOrder' => 'sortn',
                'sortField' => 'sortn'
            ]);
    }

    /**
     * Возвращает Имеющиеся в системе обработчики типов доставок.
     *
     * @return DeliveryType\AbstractType[]
     * @throws RSException
     */
    public static function getTypes()
    {
        if (self::$types === null) {
            $event_result = EventManager::fire('delivery.gettypes', []);
            $list = $event_result->getResult();
            self::$types = [];
            foreach ($list as $delivery_type_object) {
                if (!($delivery_type_object instanceof DeliveryType\AbstractType)) {
                    throw new RSException(t('Тип доставки должен быть наследником \Shop\Model\DeliveryType\AbstractType'));
                }
                self::$types[$delivery_type_object->getShortName()] = $delivery_type_object;
            }
        }

        return self::$types;
    }

    /**
     * Возвращает массив ключ => название типа доставки
     *
     * @return string[]
     */
    public static function getTypesAssoc()
    {
        $result = [];
        foreach (self::getTypes() as $key => $object) {
            $result[$key] = $object->getTitle();
        }
        return $result;
    }

    /**
     * Получение списка доставок для типа оплаты
     *
     * @return array
     */
    public static function getListForOrder()
    {
        $groups = [0 => ['title' => t('Без группы')]] +
            OrmRequest::make()
                ->from(new DeliveryDir())
                ->orderby('sortn')
                ->where([
                    'site_id' => SiteManager::getSiteId()
                ])
                ->objects(null, 'id');

        $_this = new self();
        $deliveries = $_this->clearFilter()
            ->setOrder('sortn')
            ->getList();

        $list = [];
        //Составим двухуровневый список
        if (!empty($deliveries)) {
            foreach ($deliveries as $delivery) {
                $title = $groups[$delivery['parent_id']]['title'] ?: t('Без группы');
                $list[$title][$delivery['id']] = $delivery;
            }
        }
        return $list;
    }

    /**
     * Получение списка доставок для типа оплаты
     *
     */
    public static function getListForPayment()
    {
        $list = parent::staticSelectList();
        $list = ['0' => t('-Все-')] + $list;
        return $list;
    }

    /**
     * Возвращает объект типа доставки по идентификатору
     *
     * @param string $name - идентификатор типа доставкиъ
     * @return DeliveryType\AbstractType
     */
    public static function getTypeByShortName($name)
    {
        $_this = new self();
        $list = $_this->getTypes();
        return isset($list[$name]) ? $list[$name] : new DeliveryType\Stub($name);
    }

    public static function getZonesList()
    {
        return [0 => t(' - все - ')] + ZoneApi::staticSelectList();
    }

    /**
     * Устанавливает фильтр по магистральным поясам
     *
     * @param array $zone - массив с магистральными поясами
     */
    public function setZoneFilter($zone)
    {
        $zone = array_merge([0], (array)$zone);
        $q = $this->queryObj();
        $q->join(new Orm\DeliveryXZone(), 'DZ.delivery_id = A.id AND DZ.zone_id IN (' . implode(',', $zone) . ')', 'DZ');
    }

    /**
     * Возвращает массив классов отвечающих за самовывоз в системе
     *
     * @return string[]
     */
    public static function getPickupPointClasses()
    {
        $classes = [];
        foreach (self::getTypes() as $short_name => $delivery_type) {
            if ($delivery_type->isMyselfDelivery()) {
                $classes[] = $short_name;
            }
        }
        return $classes;
    }

    /**
     * Возвращает список доступных доставок, указанного класса
     *
     * @param array $classes - список классов
     * @param bool $inverse - если true - будут выбраны доставки не входящие в указанный список классов
     * @param Order $order - если указан объект заказа - вернутся доставки, подходящие под данный заказ
     * @return Delivery[]
     */
    static function getDeliveriesByClasses($classes, $inverse = false, $order = null)
    {
        $_this = new self();
        $type = $inverse ? 'notin' : 'in';

        //Посмотрим есть ли доставки с типом самовывоз
        $_this->setFilter('public', 1)
            ->setFilter('class', $classes, $type);

        $_this->setOrderDeliveryFilters($order);

        /** @var Delivery[] $list */
        $list = $_this->getList();
        return $list;
    }

    /**
     * Возвращает список пунктов самовывоза в системе.
     * Если передать объект заказа, то вернутся пункты подходящие под данный заказ
     *
     * @param Order $order - объект заказа
     * @return Delivery[]
     */
    public static function getPickUpPoints($order = null)
    {
        return self::getDeliveriesByClasses(self::getPickupPointClasses(), false, $order);
    }

    /**
     * Проверяет есть пункты самовывоза в системе. Проверяет наличие таких доставок, а затем пунктов самовывоза, если на находит доставок этого типа
     *
     * @param Order $order - объект заказа
     *
     * @return boolean
     */
    public static function isHavePickUpPoints($order = null)
    {
        $delivery_points = self::getPickUpPoints($order);
        return (!empty($delivery_points)) ? true : false;
    }

    /**
     * Проверяет наличие доступных в системе доставок по адресу
     *
     * @param Order $order - объект заказа
     *
     * @return boolean
     */
    public static function isHaveToAddressDelivery($order = null)
    {
        $delivery_points = self::getDeliveriesByClasses(self::getPickupPointClasses(), true, $order);
        return (!empty($delivery_points)) ? true : false;
    }

    /**
     * Возвращает список пользователей-курьеров
     *
     * @return array
     */
    public static function getCourierList()
    {
        $config = ConfigLoader::byModule(__CLASS__);
        $courier_group = $config['courier_user_group'];

        if ($courier_group) {
            return OrmRequest::make()
                ->select('U.*')
                ->from(new User(), 'U')
                ->join(new UserInGroup(), 'G.user = U.id', 'G')
                ->where([
                    'G.group' => $courier_group
                ])->objects(null, 'id');
        }
        return [];
    }

    /**
     * Возвращает ассоциативный массив для отображения списка курьеров
     *
     * @param array $root - корневой элемент
     * @return array
     */
    public static function getCourierSelectList($root = ['- Не выбрано -'])
    {
        $couriers = self::getCourierList();

        $result = [];
        foreach ($couriers as $courier) {
            $result[$courier['id']] = $courier->getFio();
        }
        return $root + $result;
    }

    /**
     * Возвращает доставки, которые необходимо отобразить на
     * этапе оформления заказа
     *
     * @param User $user - объект пользователя
     * @param Order $order - объект заказа
     * @param bool $check_public - возвращать только публичные доставки
     * @param bool $strict_to_address_filter - не включать самовывоз к доставкам до адреса
     * @return Delivery[]
     */
    function getCheckoutDeliveryList(User $user, Order $order, $check_public = true, $strict_to_address_filter = false)
    {
        $my_type = $user['is_company'] ? 'company' : 'user';

        if ($check_public) {
            $this->setFilter('public', 1);
        }
        $this->setFilter('user_type', ['all', $my_type], 'in');

        if (!$order['only_pickup_points']) { //Если не только самовывозом
            //Получим все зоны
            $zone_api = new ZoneApi();
            $address = $order->getAddress();
            $zones = $zone_api->getZonesByRegionId($address['region_id'], $address['country_id'], $address['city_id']);

            $this->setZoneFilter($zones);

            if ($strict_to_address_filter) {
                $this->setFilter('class', $this::getPickupPointClasses(), 'notin');
            }
        } else { //Если только пункты самовывоза
            $this->setFilter('class', $this::getPickupPointClasses(), 'in');
        }

        $this->setOrderDeliveryFilters($order);

        $this->queryObj()->groupby('id');
        $delivery_list = $this->getAssocList('id');

        // TODO описать событие 'checkout.delivery.list' в документации
        // Событие для модификации списка доставок
        $result = EventManager::fire('checkout.delivery.list', [
            'list' => $delivery_list,
            'order' => $order,
            'user' => $user,
        ]);
        list($delivery_list) = $result->extract();

        return $delivery_list;
    }

    /**
     * Устанавливает фильтры для отбора доставок по параметрам заказа
     *
     * @param Order $order
     * @return bool Возвращает true, если фильтры были установлены
     */
    public function setOrderDeliveryFilters($order)
    {
        if ($order && ($cart = $order->getCart())) {
            $price_items_data = $cart->getPriceItemsData();
            //Проверим условие минимальной цены
            $this->setFilter([
                [
                    'min_price' => null,
                    '|min_price:<=' => $price_items_data['total'],
                ]
            ]);
            //Проверим условие максимальной цены
            $this->setFilter([
                [
                    'max_price' => null,
                    '|max_price:>=' => $price_items_data['total'],
                ]
            ]);
            //Проверим условие минимального веса
            $this->setFilter([
                [
                    'min_weight' => null,
                    '|min_weight:<=' => $price_items_data['total_weight'],
                ]
            ]);
            //Проверим условие максимального веса
            $this->setFilter([
                [
                    'max_weight' => null,
                    '|max_weight:>=' => $price_items_data['total_weight'],
                ]
            ]);
            //Проверим условие минимального количества товаров
            $this->setFilter([
                [
                    'min_cnt' => 0,
                    '|min_cnt:<=' => $price_items_data['items_count'],
                ]
            ]);
            return true;
        }

        return false;
    }

    /**
     * Обновляет свойства у группы объектов
     *
     * @param array $data - ассоциативный массив со значениями обновляемых полей
     * @param array $ids - список id объектов, которые нужно обновить
     * @return int - возвращает количество обновленных элементов
     */
    function multiUpdate(array $data, $ids = [])
    {
        $x_zone = [];

        if (isset($data['xzone'])) {
            $x_zone = (array_search(0, $data['xzone']) === false) ? $data['xzone'] : [0];
            unset($data['xzone']);
        }

        $null_fields = ['min_price', 'max_price', 'min_weight', 'max_weight'];
        foreach($null_fields as $field) {
            if (isset($data[$field]) && $data[$field] === '') {
                $data[$field] = null;
            }
        }

        $return = parent::multiUpdate($data, $ids);

        if ($return && $x_zone) {
            OrmRequest::make()->delete()
                ->from(new DeliveryXZone())
                ->whereIn('delivery_id', $ids)
                ->exec();

            foreach ($ids as $delivery_id) {
                foreach ($x_zone as $zone_id) {
                    $link = new DeliveryXZone();
                    $link['delivery_id'] = $delivery_id;
                    $link['zone_id'] = $zone_id;
                    $link->insert();
                }
            }
        }

        return $return;
    }


}
