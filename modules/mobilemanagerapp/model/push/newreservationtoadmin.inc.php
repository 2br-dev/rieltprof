<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace MobileManagerApp\Model\Push;
use RS\Http\Request;
use Shop\Model\Orm\Reservation;

/**
 * Push уведомление администратору о заказе товара
 */
class NewReservationToAdmin extends AbstractPushToAdmin
{
    public
        $reservation;

    public function init(Reservation $reservation)
    {
        $this->reservation = $reservation;
    }

    /*
    * Возвращает описание уведомления для внутренних нужд системы и
    * отображения в списках админ. панели
    *
    * @return string
    */
    public function getTitle()
    {
        return t('Новый предварительный заказ(администратору)');
    }


    /**
     * Возвращает Заголовок для Push уведомления
     *
     * @return string
     */
    public function getPushTitle()
    {
        $request = Request::commonInstance();
        return t('Новый предзаказ N%num на сайте %site', [
            'num' => $this->reservation['id'],
            'site' => $request->getDomainStr(),
        ]);
    }

    /**
     * Возвращает текст Push уведомления
     *
     * @return string
     */
    public function getPushBody()
    {
        return t('Товар: %product', [
            'product' => $this->reservation['product_title'].' '.$this->reservation['offer'],
        ]);
    }

    /**
     * Возвращает произвольные данные ключ => значение, которые должны быть переданы с уведомлением
     *
     * @return array
     */
    public function getPushData()
    {
        $site = new \Site\Model\Orm\Site($this->reservation['__site_id']->get());
        return [
            'site_uid' => $site->getSiteHash()
        ];
    }

}