<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model\DeliveryType;

use RS\Exception as RSException;
use RS\Helper\CustomView;
use Shop\Model\Orm\Address;
use Shop\Model\Orm\Delivery;
use Shop\Model\Orm\Order;

/**
 * Тип доставки - Устанавливаема вручную стоимость
 */
class Manual extends AbstractType implements InterfaceIonicMobile
{
    protected $form_tpl = 'type/fixed.tpl';

    function getTitle()
    {
        return t('Изменяемая вручную цена');
    }

    function getDescription()
    {
        return t('Стоимость рассчитывается менеджером, после оформления заказа');
    }

    function getShortName()
    {
        return 'manual';
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
     * Возвращает цену в текстовом формате, т.е. здесь может быть и цена и надпись, например "Бесплатно"
     *
     * @param Order $order - объект заказа
     * @param Address $address - объект адреса
     * @param Delivery $delivery - объект доставки
     * @return string
     * @throws RSException
     */
    function getDeliveryCostText(Order $order, Address $address = null, Delivery $delivery)
    {
        $cost = $this->getDeliveryFinalCost($order, $address);
        return ($cost) ? CustomView::cost($cost) : t('Будет рассчитана менеджером');
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
