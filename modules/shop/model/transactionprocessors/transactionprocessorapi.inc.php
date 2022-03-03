<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model\TransactionProcessors;

/**
 * API процессоров транзакций
 */
class TransactionProcessorApi
{
    /**
     * Возвращает список всех процессоров
     *
     * @return AbstractTransactionProcessor[]
     */
    public static function getProcessorList(): array
    {
        static $list;
        if ($list === null) {
            $list = [
                TransactionProcessorRecurringBind::getName() => new TransactionProcessorRecurringBind(),
            ];
        }
        return $list;
    }
}
