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
use Shop\Model\Orm\Transaction;
use Users\Model\Orm\User;

/**
 * Push уведомление администратору о покупке в 1 клик
 */
class AddBalanceToAdmin extends AbstractPushToAdmin
{
    public
        $transaction,
        $user;

    /**
     * Инициализирует данный класс
     *
     * @param Transaction $transaction
     * @param User $user
     */
    public function init($transaction, $user)
    {
        $this->transaction = $transaction;
        $this->user = $user;
    }

    /*
    * Возвращает описание уведомления для внутренних нужд системы и
    * отображения в списках админ. панели
    *
    * @return string
    */
    public function getTitle()
    {
        return t('Пополнен баланс (администратору)');
    }


    /**
     * Возвращает Заголовок для Push уведомления
     *
     * @return string
     */
    public function getPushTitle()
    {
        $request = Request::commonInstance();
        return t('Пополнен баланс на сайте %site', [
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
        return t('Сумма: %cost, Клиент: %user', [
            'cost' => $this->transaction['cost'].' '.CurrencyApi::getBaseCurrency()->stitle,
            'user' => $this->user->getFio()
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