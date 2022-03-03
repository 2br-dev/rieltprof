<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model;

use Alerts\Model\Manager as AlertsManager;
use Shop\Model\Notice\AddBalance as NoticeAddBalance;
use Shop\Model\Notice\OrderPayed as NoticeOrderPayed;
use Shop\Model\Orm\Transaction;
use Shop\Model\Orm\TransactionChangeLog;
use Shop\Model\Orm\UserStatus;
use RS\Exception as RSException;

/**
 * Вспомогательный класс для внесения изменений в транзакции
 */
class ChangeTransaction
{
    /** @var Transaction */
    protected $old_transaction;
    /** @var Transaction */
    protected $transaction;
    /** @var string */
    protected $response = '';
    /** @var string */
    protected $changelog;
    /** @var string */
    protected $changelog_entity_type;
    /** @var int */
    protected $changelog_entity_id;

    public function __construct(Transaction $transaction)
    {
        $this->old_transaction = $transaction;
        $this->transaction = clone $transaction;
    }

    /**
     * Применяет изменения к транзакции
     *
     * @return void
     * @throws RSException
     */
    public function applyChanges()
    {
        $start_status = $this->old_transaction['status'];
        $new_status = $this->transaction['status'];

        $this->transaction->update();
        if ($this->changelog) {
            TransactionChangeLog::new($this->transaction, $this->changelog, $this->changelog_entity_type, $this->changelog_entity_id);
        }
        $payment = $this->transaction->getPayment();

        if ($new_status == Transaction::STATUS_SUCCESS && in_array($start_status, [Transaction::STATUS_NEW, Transaction::STATUS_HOLD])) {
            if ($this->transaction['order_id']) {
                $order = $this->transaction->getOrder();

                if ($this->transaction->isReceiptEnabled()) {
                    $order['status'] = UserStatusApi::getStatusIdByType(UserStatus::STATUS_NEEDRECEIPT);
                } else {
                    if ($payment['success_status']) {
                        $order['status'] = $payment['success_status'];
                    }
                }

                $order['is_payed'] = 1;
                $order['is_exported'] = 0;
                $order->update();

                $notice = new NoticeOrderPayed;
                $notice->init($order);
                AlertsManager::send($notice);
            } else {
                $notice = new NoticeAddBalance();
                $notice->init($this->transaction, $this->transaction->getUser());
                AlertsManager::send($notice);
            }

            if ($this->transaction->isReceiptEnabled()) {
                $transaction_api = new TransactionApi();
                $transaction_api->createReceipt($this->transaction);
            }

            foreach ($this->transaction->getProcessors() as $processor) {
                $processor->onSuccess();
            }
        }

        if ($start_status == Transaction::STATUS_NEW && $new_status == Transaction::STATUS_HOLD) {
            if ($this->transaction['order_id'] && $payment['holding_status']) {
                $order = $this->transaction->getOrder();
                $order['status'] = $payment['holding_status'];
                $order->update();
            }
        }

        if ($start_status == Transaction::STATUS_HOLD && $new_status == Transaction::STATUS_FAIL) {
            if ($this->transaction['order_id'] && $payment['holding_cancel_status']) {
                $order = $this->transaction->getOrder();
                $order['status'] = $payment['holding_cancel_status'];
                $order->update();
            }
        }

        //Изменяем переданный в конструктор объект транзакции
        $this->old_transaction->getFromArray($this->transaction->getValues());
    }

    /**
     * Устанавливает новый статус
     *
     * @param string $new_status - новый статус
     * @return self
     */
    public function setNewStatus(string $new_status): self
    {
        $this->transaction['status'] = $new_status;
        return $this;
    }

    /**
     * Устанавливает новую сумму
     *
     * @param float $new_cost - новая сумма
     * @return self
     * @throws Exception
     */
    public function setNewCost(float $new_cost): self
    {
        $this->transaction['cost'] = $new_cost;
        $this->transaction['sign'] = TransactionApi::getTransactionSign($this->transaction);
        return $this;
    }

    /**
     * Устанавливает текст ошибки
     *
     * @param string $error
     * @return self
     */
    public function setError(string $error): self
    {
        $this->transaction['error'] = $error;
        return $this;
    }

    /**
     * Возвращает ответ для внешней системы
     * Используется когда изменение транзакции вызвано запросом из вне
     *
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Устанавливает ответ для внешней системы
     * Используется когда изменение транзакции вызвано запросом из вне
     *
     * @param mixed $response
     * @return self
     */
    public function setResponse($response): self
    {
        $this->response = $response;
        return $this;
    }

    /**
     * Возвращает информацию об изменениях
     *
     * @return string
     */
    public function getChangeLog(): string
    {
        return $this->changelog;
    }

    /**
     * Устанавливает сведения об изменениях
     *
     * @param string $change - информация об изменениях
     * @param string|null $entity_type - тип связанной сущности
     * @param int|null $entity_id - id связанной сущности
     */
    public function setChangelog(string $change, string $entity_type = null, int $entity_id = null)
    {
        $this->changelog = $change;
        $this->changelog_entity_type = $entity_type;
        $this->changelog_entity_id = $entity_id;
    }
}
