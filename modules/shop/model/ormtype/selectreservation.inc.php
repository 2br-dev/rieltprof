<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\OrmType;

use RS\Orm\Type\User;
use Shop\Model\Orm\Reservation;

/**
 * Поле - поиск ID предзаказа по строке
 */
class SelectReservation extends User
{
    protected
        $cross_multisite;

    function __construct(array $options = null)
    {
        $this->view_attr = ['size' => 40, 'placeholder' => t('id, номер предзаказа, ФИО')];
        parent::__construct($options);
    }

    /**
     * Устанавливает, нужно ли выбирать заказы без учета мультисайтовости
     *
     * @param $bool
     */
    function setCrossMultisite($bool)
    {
        $this->cross_multisite = $bool;
    }

    /**
     * Возвращает, нужно ли выбирать заказы
     *
     * @return bool
     */
    function isCrossMultisite()
    {
        return $this->cross_multisite;
    }

    /**
     * @return Reservation
     */
    function getSelectedObject()
    {
        $deal_id = ($this->get()>0) ? $this->get() : null;
        if ($deal_id>0) {
            if (!isset(self::$cache[$deal_id])) {
                $deal = new Reservation($deal_id);
                self::$cache[$deal_id] = $deal;
            }
            return self::$cache[$deal_id];
        }
        return new Reservation();
    }

    /**
     * Возвращает URL, который будет возвращать результат поиска
     *
     * @return string
     */
    function getRequestUrl()
    {
        return $this->request_url ?: \RS\Router\Manager::obj()->getAdminUrl('ajaxSearchReservation', [
            'cross_multisite' => (int)$this->isCrossMultisite()
        ], 'shop-tools');
    }

    /**
     * Возвращает наименование найденного объекта
     *
     * @return string
     */
    function getPublicTitle()
    {
        $reservation = $this->getSelectedObject();

        return t('Предзаказ №%num от %date', [
            'num' => $reservation['id'],
            'date' => date('d.m.Y', strtotime($reservation['date_of']))
        ]);
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