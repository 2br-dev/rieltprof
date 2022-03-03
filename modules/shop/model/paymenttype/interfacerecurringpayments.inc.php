<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/ declare(strict_types=1);

namespace Shop\Model\PaymentType;

use Shop\Model\Orm\Order;
use Shop\Model\Orm\SavedPaymentMethod;
use Shop\Model\Orm\Transaction;
use Shop\Model\Exception as ShopException;
use Users\Model\Orm\User;

/**
 * Интерфейс рекуррентных платежей
 * Используется вместе с трейтом Shop\Model\PaymentType\TraitInterfaceRecurringPayments
 */
interface InterfaceRecurringPayments
{
    const RECURRING_TYPE_NONE = 'none';
    const RECURRING_TYPE_SAVE_METHOD = 'save_method';
    const RECURRING_TYPE_ONLY_SAVE_METHOD = 'only_save_method';
    const TRANSACTION_EXTRA_KEY_SAVED_METHOD = 'saved_payment_method';
    const RECURRING_ACTION_SAVED_METHODS_FORM = 'saved_methods_form';
    const RECURRING_ACTION_SELECT_SAVED_METHOD = 'select_saved_method';
    const RECURRING_ACTION_PAY_WITH_SAVED_METHOD = 'pay_with_saved_method';

    /**
     * Возвращает включены ли рекуррентные платежи
     *
     * @return bool
     */
    public function isRecurringPaymentsActive(): bool;

    /**
     * Возвращает режим работы "рекуррентных платежей" (возможные значения описаны в интерфейсе)
     *
     * @return string
     */
    public function getRecurringPaymentsType(): string;

    /**
     * Производит "рекуррентную" оплату заказа.
     * Автоматически списывает с $saved_payment_method средства за заказ $order
     *
     * @param Order $order - заказ
     * @param SavedPaymentMethod $saved_payment_method - сохранённый способ платежа
     * @return void
     */
    public function recurringPayOrder(Order $order, SavedPaymentMethod $saved_payment_method): void;

    /**
     * Производит "рекуррентное" пополнение лицевого счёта
     * Автоматически списывает с $saved_payment_method средства и увеличивает баланс лицевого счета $user на сумму $cost
     *
     * @param User $user - пользователь
     * @param float $cost - сумма пополнения
     * @param SavedPaymentMethod $saved_payment_method - сохранённый способ платежа
     * @return void
     */
    public function recurringPayBalanceFounds(User $user, float $cost, SavedPaymentMethod $saved_payment_method): void;

    /**
     * Производит возврат транзакции, привязывающей новый способ платежа
     * Создает транзакцию RS на списание суммы, указанной в $transaction и выполняет запрос на полный возврат
     * средств к платежной системе.
     *
     * @param Transaction $transaction - транзакция
     * @return void
     */
    public function refundBindingTransaction(Transaction $transaction): void;

    /**
     * Возвращает список сохранённых способов оплаты для указанного пользователя
     *
     * @param User $user - объект пользователя
     * @return SavedPaymentMethod[]
     */
    public function getSavedPaymentMethods(User $user): array;

    /**
     * Удаляет сохранённый способ платежа
     *
     * @param SavedPaymentMethod $saved_payment_method - Сохранённый способ платежа
     * @return void
     * @throws ShopException
     */
    public function deleteSavedPaymentMethod(SavedPaymentMethod $saved_payment_method): void;

    /**
     * Возвращает дополнительный HTML для админ части в заказе
     *
     * @param Order $order - заказ
     * @return string
     */
    public function getAdminRecurringPaymentsHtml(Order $order): string;

    /**
     * Исполняет действие из административной панели "рекуррентных платежей" с указанным заказом.
     * На текущий момент действий бывает несколько:
     * - Показать форму выбора сохраненных способов платежей
     * - Выбрать другой способ платежа
     * - Оплатить выбранным способом платежа
     *
     * @param Order $order - заказ
     * @param string $action - действие
     * @return array
     */
    public function executeInterfaceRecurringPaymentsAction(Order $order, string $action): array;
}
