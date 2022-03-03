<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model\DeliveryType;

use Shop\Model\Orm\Delivery;
use Shop\Model\Orm\Order;

/**
 * Интерфейс для мобильного приложения, возвращающий HTML
 */
interface InterfaceIonicMobile
{
    /**
     * Возвращает шаблон с дополнительной информацией для мобильного приложения
     *
     * @param \Shop\Model\Orm\Order $order - объект заказа
     * @param \Shop\Model\Orm\Delivery $delivery - объект доставки
     */
    public function getIonicMobileAdditionalHTML(Order $order, Delivery $delivery);
}
