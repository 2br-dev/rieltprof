<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model\TransactionProcessors;

use Shop\Model\Orm\Order;
use Shop\Model\Orm\UserStatus;
use Shop\Model\PaymentType\InterfaceRecurringPayments;
use Shop\Model\UserStatusApi;

/**
 * Процессор "привязка метода оплаты"
 */
class TransactionProcessorRecurringBind extends AbstractTransactionProcessor
{
    const EXTRA_KEY_ORDER_ID = 'recurring_order_id';

    /**
     * Возвращает идентификатор процессора
     *
     * @return string
     */
    public static function getName(): string
    {
        return 'recurring_bind';
    }

    /**
     * действие успешном завершении транзакции
     *
     * @return void
     */
    public function onSuccess(): void
    {
        $payment_type = $this->transaction->getPayment()->getTypeObject();
        if ($payment_type instanceof InterfaceRecurringPayments) {
            $saved_payment_method_id = $this->transaction->getExtra(InterfaceRecurringPayments::TRANSACTION_EXTRA_KEY_SAVED_METHOD);
            $order_id = $this->transaction->getExtra(self::EXTRA_KEY_ORDER_ID);

            if ($saved_payment_method_id && $order_id) {
                $order = new Order($order_id);
                $order['status'] = UserStatusApi::getStatusIdByType(UserStatus::STATUS_PAYMENT_METHOD_SELECTED);
                $order['saved_payment_method_id'] = $saved_payment_method_id;
                $order->update();
            }

            $payment_type->refundBindingTransaction($this->transaction);
        }
    }
}
