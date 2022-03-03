<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model\DeliveryType;

use Catalog\Model\WareHouseApi;
use RS\Module\AbstractModel\TreeList\AbstractTreeListIterator;
use RS\Orm\FormObject;
use RS\Orm\PropertyIterator;
use RS\Orm\Type;
use Shop\Model\DeliveryType\Helper\Pvz;
use Shop\Model\Orm\Address;
use Shop\Model\Orm\Delivery;
use Shop\Model\Orm\Order;

/**
 * Тип доставки - Самовывоз. стоимость - 0
 */
class Myself extends AbstractType implements InterfaceIonicMobile
{
    /**
     * Возвращает название
     *
     * @return string
     */
    function getTitle()
    {
        return t('Самовывоз');
    }

    /**
     * Возвращает ORM объект для генерации формы или null
     *
     * @return FormObject | null
     */
    function getFormObject()
    {
        $properties = new PropertyIterator([
            'myself_addr' => new Type\Integer([
                'description' => t('Месторасположение пункта самовывоза'),
                'maxLength' => '11',
                'tree' => [['\Shop\Model\RegionApi', 'staticTreeList']],
                'attr' => [[
                    AbstractTreeListIterator::ATTRIBUTE_DISALLOW_SELECT_BRANCHES => true,
                ]],
            ]),
            'pvz_list' => new Type\ArrayList([
                'description' => t('Доступные пункты самовывоза'),
                'hint' => t('Если не указаны, используются все пуннкты'),
                'list' => [['\Catalog\Model\WareHouseApi', 'staticPickupPointsSelectList'], [0 => t('- Все -')]],
                'runtime' => false,
                'attr' => [[
                    'multiple' => true
                ]],
            ]),
        ]);

        $form_object = new FormObject($properties);
        $form_object->setParentObject($this);
        $form_object->setParentParamMethod('Form');
        return $form_object;
    }

    /**
     * Возвращает описание
     *
     * @return string
     */
    function getDescription()
    {
        return t('Не предполагает взимание оплаты');
    }

    /**
     * Возвращает короткое системное имя
     *
     * @return string
     */
    function getShortName()
    {
        return 'myself';
    }

    /**
     * Возвращает какие поля адреса необходимы данной доставке
     *
     * @return string[]
     */
    public function getRequiredAddressFields(): array
    {
        return [];
    }

    /**
     * Возвращает, поддерживает ли данный способ доставки ПВЗ
     *
     * @return bool
     */
    public function hasPvz(): bool
    {
        return true;
    }

    /**
     * Возвращает список ПВЗ на основе адреса
     *
     * @param Address $address - адрес получателя
     * @return Pvz[]
     */
    public function getPvzByAddress(Address $address)
    {
        $city = $address->getCity();
        if ($city['id']) {
            $warehouse_api = new WareHouseApi();
            $warehouse_api->setFilter([
                'linked_region_id' => $address->getCity()['id'],
                'checkout_public' => 1,
            ]);
            $pvz_filter = $this->getOption('pvz_list');
            if (!empty($pvz_filter) && !in_array(0, $pvz_filter)) {
                $warehouse_api->setFilter([
                    'id:in' => implode(',', $pvz_filter),
                ]);
            }

            $pvz_list = [];
            foreach ($warehouse_api->getList() as $warehouse) {
                $pvz = new Pvz();
                $pvz->setCode($warehouse['id']);
                $pvz->setTitle($warehouse['title']);
                $pvz->setAddress($warehouse['adress']);
                $pvz->setCoordX($warehouse['coor_y']);
                $pvz->setCoordY($warehouse['coor_x']);
                $pvz_list[] = $pvz;
            }

            return $pvz_list;
        }
        return [];
    }

    /**
     * Корректировка заказа перед его сохранением
     *
     * @param Order $order - объект заказа
     */
    public function beforeOrderWrite(Order $order)
    {
        if ($pvz = $order->getSelectedPvz()) {
            $order['warehouse'] = $pvz->getCode();
        }
    }

    /**
     * Возвращает стоимость доставки для заданного заказа. Только число.
     *
     * @param Order $order - объект заказа
     * @param Address $address - адрес доставки
     * @param Delivery $delivery - объект доставки
     * @param boolean $use_currency - использовать валюту?
     * @return double
     */
    function getDeliveryCost(Order $order, Address $address = null, Delivery $delivery, $use_currency = true)
    {
        return 0;
    }

    /**
     * Возвращает true, если тип доставки предполагает самовывоз
     *
     * @return bool
     */
    function isMyselfDelivery()
    {
        return true;
    }

    /**
     * Возвращает HTML для приложения на Ionic
     *
     * @param Order $order - объект заказа
     * @param Delivery $delivery - объект доставки
     * @return string
     */
    function getIonicMobileAdditionalHTML(Order $order, Delivery $delivery)
    {
        return "";
    }
}
