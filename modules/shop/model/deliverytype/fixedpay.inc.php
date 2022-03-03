<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model\DeliveryType;

use RS\Orm\FormObject;
use RS\Orm\PropertyIterator;
use RS\Orm\Type;
use Shop\Model\Orm\Address;
use Shop\Model\Orm\Delivery;
use Shop\Model\Orm\Order;

/**
 * Тип доставки - Фиксированная цена доставки
 */
class FixedPay extends AbstractType implements InterfaceIonicMobile
{
    protected $form_tpl = 'type/fixed.tpl';

    function getTitle()
    {
        return t('Фиксированная цена');
    }

    function getDescription()
    {
        return t('Стоимость доставки не зависит ни от каких параметров');
    }

    function getShortName()
    {
        return 'fixedpay';
    }

    function getFormObject()
    {
        $properties = new PropertyIterator([
            'cost' => new Type\Real([
                'description' => t('Стоимость')
            ])
        ]);

        $form_object = new FormObject($properties);
        $form_object->setParentObject($this);
        $form_object->setParentParamMethod('Form');
        return $form_object;
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
        return $this->getOption('cost', 0);
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
