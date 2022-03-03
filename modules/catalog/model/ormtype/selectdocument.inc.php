<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\OrmType;

use RS\Orm\Type\User;
use Shop\Model\Orm\Order;

/**
 * Поле - поиск документа по id
 */
class SelectDocument extends User
{

    private
        $type ;

    /**
     * @return Order
     */
    function getSelectedObject()
    {
        $deal_id = ($this->get()>0) ? $this->get() : null;
        if ($deal_id>0) {
            if (!isset(self::$cache[$deal_id])) {
                $deal = new Order($deal_id);
                self::$cache[$deal_id] = $deal;
            }
            return self::$cache[$deal_id];
        }
        return new Order();
    }

    function setType($type)
    {
        $this->type = $type;
    }

    function getType()
    {
        return $this->type;
    }

    /**
     * Возвращает URL, который будет возвращать результат поиска
     *
     * @return string
     */
    function getRequestUrl()
    {
        return $this->request_url ?: \RS\Router\Manager::obj()->getAdminUrl('ajaxSearchDocument', ['document_type' => $this->getType()], 'catalog-inventoryctrl');
    }

    /**
     * Возвращает наименование найденного объекта
     *
     * @return string
     */
    function getPublicTitle()
    {
        $order = $this->getSelectedObject();

        return t('Заказ №%num от %date', [
            'num' => $order['order_num'],
            'date' => date('d.m.Y', strtotime($order['date_of']))
        ]);
    }

    /**
     *  Установить минимальную длину строки для начала запроса.
     *
     * @param $min_length
     */
    function setMinLength($min_length)
    {
        $this->view_attr = array_merge( $this->view_attr, ['minLength' => $min_length]);
    }

    /**
     * Возвращает класс иконки zmdi
     *
     * @return string
     */
    function getIconClass()
    {
        return 'assignment';
    }
}