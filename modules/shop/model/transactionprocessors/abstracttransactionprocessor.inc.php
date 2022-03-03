<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model\TransactionProcessors;

use Shop\Model\Orm\Transaction;

/**
 * Классы процессоров наделяют транзакции определённым поведением
 */
abstract class AbstractTransactionProcessor
{
    /** @var Transaction */
    protected $transaction;

    /**
     * Устанавливает транзакцию
     *
     * @param Transaction $transaction - транзакция
     * @return void
     */
    public function setTransaction(Transaction $transaction): void
    {
        $this->transaction = $transaction;
    }

    /**
     * Возвращает идентификатор процессора
     *
     * @return string
     */
    abstract public static function getName(): string;

    /**
     * действие успешном завершении транзакции
     *
     * @return void
     */
    public function onSuccess(): void
    {
    }
}
