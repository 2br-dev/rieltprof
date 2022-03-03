<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model;

use RS\Module\AbstractModel\EntityList;
use RS\Orm\Request as OrmRequest;
use RS\Site\Manager as SiteManager;
use Shop\Model\Orm\Receipt;
use Shop\Model\Orm\Transaction;

/**
 * API функции для работы с чеками
 */
class ReceiptApi extends EntityList
{
    function __construct()
    {
        parent::__construct(new Receipt(),
            [
                'nameField' => 'title',
                'defaultOrder' => 'dateof DESC',
                'multisite' => true
            ]);
    }

    /**
     * Возвращает объект транзакции для заказа у которого есть выбитые чеки возврата или false если нет
     *
     * @param integer $order_id - id заказа
     *
     * @return Transaction|false
     */
    public static function getTransactionForRefundReceiptByOrderId($order_id)
    {
        $transaction_api = new TransactionApi();
        /** @var Transaction $transaction */
        $transaction = $transaction_api->setFilter('order_id', $order_id)
            ->setFilter('status', Transaction::STATUS_SUCCESS)
            ->getFirst();

        if ($transaction) { //Если транзакция есть, то запросим чеки
            $_this = new self();
            $list = $_this->setFilter('transaction_id', $transaction['id'])
                ->setFilter('type', Receipt::TYPE_REFUND)
                ->getList();

            return (count($list)) ? $transaction : false;
        }

        return false;
    }

    /**
     * Проверяет чеки, которые в статусе ожидания отправляют запрос на проверку чека.
     *
     * @param integer|null $site_id - текущий id сайта
     * @return void
     */
    function checkWaitReceipts($site_id = null)
    {
        if (!$site_id) {
            $site_id = SiteManager::getSiteId();
        }
        /** @var Receipt[] $list */
        $list = OrmRequest::make()
            ->from(new Receipt())
            ->where([
                'site_id' => $site_id,
                'status' => Receipt::STATUS_WAIT
            ])
            ->objects();

        if (!empty($list)) {
            foreach ($list as $receipt) {
                try {
                    $cashregister_api = new CashRegisterApi();
                    /** @var CashRegisterType\AbstractType $provider */
                    $provider = $cashregister_api->getTypeByShortName($receipt['provider']);
                    $provider->getReceiptStatus($receipt);
                } catch (\Exception $e) {
                    //Ничего не делаем. Пока.
                }
            }
        }
    }

    /**
     * Возвращает чек по подписи
     *
     * @param $sign
     * @return bool|Receipt
     */
    function getReceiptBySign($sign)
    {
        return OrmRequest::make()
            ->from(new Receipt())
            ->where([
                'sign' => $sign
            ])->object();
    }
}
