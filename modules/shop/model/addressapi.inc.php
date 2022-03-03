<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model;

use RS\Event\Manager as EventManager;
use RS\Module\AbstractModel\EntityList;
use Shop\Model\Orm\Address;
use Shop\Model\Orm\Order;
use Users\Model\Orm\User;

class AddressApi extends EntityList
{
    function __construct()
    {
        parent::__construct(new Address, [
            'multisite' => true,
        ]);
    }

    /**
     *  Возвращает список доступых адресов, доступных при оформлении заказа для данного пользователя
     *
     * @param Order $order - заказ
     * @param User $user - пользователь
     * @param bool $add_order_filters - добавить фильтры по данным заказа
     * @return Address[]
     */
    public function getCheckoutUserAddresses(Order $order, User $user, bool $add_order_filters = false)
    {
        $this->clearFilter();
        $this->setFilter([
            'user_id' => $user['id'],
            'deleted' => 0,
        ]);

        if ($add_order_filters) {
            $this->addOrderAddressFilter($order);
            $this->addOrderDeliveryFilter($order);
        }

        /** @var Address[] $addr_list */
        $addr_list = $this->queryObj()->objects(null, 'id');

        // TODO описать событие 'checkout.useraddress.list' в документации
        $event_result = EventManager::fire('checkout.useraddress.list', [
            'addr_list' => $addr_list,
            'order' => $order,
            'user' => $user,
            'add_order_filters' => $add_order_filters,
        ]);
        list($addr_list) = $event_result->extract();

        return $addr_list;
    }

    /**
     * Добавляет филтр по выбраному в заказе региону
     *
     * @param Order $order - объект заказа
     * @return void
     */
    protected function addOrderAddressFilter(Order $order): void
    {
        $address = $order->getAddress();
        if ($address['city_id']) {
            $this->setFilter([
                'city_id' => $address['city_id'],
            ]);
        } elseif ($address['region_id']) {
            $this->setFilter([
                'city' => $address['city'],
                'region_id' => $address['region_id'],
            ]);
        } elseif ($address['country_id']) {
            $this->setFilter([
                'city' => $address['city'],
                'region' => $address['region'],
                'country_id' => $address['country_id'],
            ]);
        } else {
            $this->setFilter([
                'city' => $address['city'],
                'region' => $address['region'],
                'country' => $address['country'],
            ]);
        }
    }

    /**
     * Добавляет фильтр по выбраному в заказе способу доставки
     *
     * @param Order $order - объект заказа
     * @return void
     */
    protected function addOrderDeliveryFilter(Order $order)
    {
        if (!empty($order['delivery'])) {
            foreach ($order->getDelivery()->getTypeObject()->getRequiredAddressFields() as $field) {
                $this->setFilter([$field . ':>' => '']);
            }
        }
    }

    /**
     * Возвращает адрес по id города
     *
     * @param integer $city_id
     * @return Orm\Address
     */
    static function getAddressByCityid($city_id)
    {
        $city = new Orm\Region($city_id);
        if (!$city['is_city']) {
            return false;
        }
        $region = $city->getParent();
        $country = $region->getParent();

        $address = new Orm\Address();
        $address['zipcode']    = $city['zipcode'];
        $address['city']       = $city['title'];
        $address['city_id']    = $city['id'];
        $address['region']     = $region['title'];
        $address['region_id']  = $region['id'];
        $address['country']    = $country['title'];
        $address['country_id'] = $country['id'];

        return $address;
    }
}
