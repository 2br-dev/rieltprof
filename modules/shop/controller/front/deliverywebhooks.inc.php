<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Controller\Front;

use RS\Controller\Front;
use Shop\Model\DeliveryApi;

/**
 * Контроллер возвращает документы на оплату заказов
 */
class DeliveryWebHooks extends Front
{
    public function actionIndex()
    {
        $this->wrapOutput(false);

        $type_name = $this->url->request('DeliveryType', TYPE_STRING);
        $delivery_types = DeliveryApi::getTypes();

        if (!isset($delivery_types[$type_name])) {
            return t('Некорректный тип доставки');
        }

        return $delivery_types[$type_name]->executeWebHook($this->url);
    }
}
