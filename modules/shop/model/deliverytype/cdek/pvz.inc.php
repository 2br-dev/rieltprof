<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\DeliveryType\Cdek;

/**
 * Класс отвечает за работу с Пунктами выдачи заказа
 */
class Pvz extends \Shop\Model\DeliveryType\Helper\Pvz
{
    protected $cash_on_delivery = 0;

    /**
     * Вовзращает Ограничение оплаты наличными при получении
     *
     * @return mixed
     */
    function getCashOnDelivery()
    {
        return $this->cash_on_delivery;
    }

    /**
     * Устанавливает Ограничение оплаты наличными при получении
     *
     * @param string $cash_on_delivery
     */
    function setCashOnDelivery($cash_on_delivery)
    {
        $this->cash_on_delivery = $cash_on_delivery;
    }

    /**
     * Возвращает наименование пункта доставки
     *
     * @return string
     */
    function getPickPointTitle(): string
    {
        return implode(", ", [$this->getCity(), $this->getAddress()]);
    }

    /**
     * Возвращает дополнительный HTML для показа при выборе пункта выдачи заказа
     *
     * @return string
     * @throws \Exception
     * @throws \SmartyException
     */
    function getAdditionalHTML(): string
    {
        $view = new \RS\View\Engine();
        $view->assign([
            'pickpoint' => $this,
            ] + \RS\Module\Item::getResourceFolders($this));

        return $view->fetch("%shop%/delivery/cdek/pvz.tpl");
    }

    /**
     * Возвращает данные по ПВЗ, которые необходимы для оформления заказа
     *
     * @return string
     * @throws \Exception
     * @throws \SmartyException
     */
    function getDeliveryExtraJson()
    {
        return $this->jsonEncodeParams([
                'code' => $this->getCode(),
                'addressInfo' => $this->getFullAddress(),
                'address' => $this->getAddress(),
                'city' => $this->getCity(),
                'phone' => $this->getPhone(),
                'coordX' => $this->getCoordX(),
                'coordY' => $this->getCoordY(),
                'info' => $this->getAdditionalHTML(),
                'note' => $this->getNote()
            ] + $this->getExtra());
    }
}