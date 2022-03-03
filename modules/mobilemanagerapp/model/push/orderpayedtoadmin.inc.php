<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace MobileManagerApp\Model\Push;
use Catalog\Model\CurrencyApi;
use RS\Http\Request;
use RS\Site\Manager as SiteManager;
use Shop\Model\Orm\Order;
use Shop\Model\Orm\Transaction;
use Users\Model\Orm\User;

/**
 * Push уведомление администратору об оплате заказа
 */
class OrderPayedToAdmin extends AbstractPushToAdmin
{
    public
        $order;

    /**
     * Инициализирует данный класс
     *
     * @param Transaction $transaction
     * @param User $user
     */
    public function init(Order $order)
    {
        $this->order = $order;
    }

    /*
    * Возвращает описание уведомления для внутренних нужд системы и
    * отображения в списках админ. панели
    *
    * @return string
    */
    public function getTitle()
    {
        return t('Заказ оплачен (администратору)');
    }


    /**
     * Возвращает Заголовок для Push уведомления
     *
     * @return string
     */
    public function getPushTitle()
    {
        $request = Request::commonInstance();
        return t('Оплачен заказ на сайте %site', [
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
        return t('Заказ №%order_num на сумму %cost', [
            'order_num' => $this->order->order_num,
            'cost' => $this->order['totalcost'].' '.CurrencyApi::getBaseCurrency()->stitle,
        ]);
    }

    /**
     * Возвращает произвольные данные ключ => значение, которые должны быть переданы с уведомлением
     *
     * @return array
     */
    public function getPushData()
    {
        $site = SiteManager::getSite();
        return [
            'site_uid' => $site->getSiteHash()
        ];
    }

}