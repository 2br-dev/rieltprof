<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model;

use RS\AccessControl\Rights;
use RS\Application\Auth;
use RS\Exception as RSException;
use RS\Http\Request as HttpRequest;
use RS\Module\AbstractModel\EntityList;
use RS\Orm\Request as OrmRequest;
use Shop\Model\Exception as ShopException;
use Shop\Model\Orm\Order;
use Shop\Model\Orm\Payment;
use Shop\Model\Orm\Transaction;
use Shop\Model\PaymentType\AbstractType as AbstractPaymentType;
use Shop\Model\PaymentType\PersonalAccount;
use Shop\Config\ModuleRights;

/**
 * API функции для работы с танзакциями
 */
class TransactionApi extends EntityList
{
    protected static $types;

    function __construct()
    {
        parent::__construct(new Transaction, [
            'multisite' => true,
        ]);
    }

    /**
     * Создаёт чек и оправляет его на ККТ
     *
     * @param Orm\Transaction $transaction - объект транзакции
     * @param string $operation_type - тип чека
     * @return bool|string
     * @throws RSException
     */
    function createReceipt(Transaction $transaction, $operation_type = 'sell')
    {
        $cashRegisterApi = new CashRegisterApi();
        return $cashRegisterApi->createReceipt($transaction, $operation_type);
    }

    /**
     * Создать транзакцию для данного заказа. Создает новую транзакцию со статусом new
     * Используется перед редиректом на внешнюю страницу оплаты
     *
     * @param string $order_num
     * @return Transaction
     * @throws ShopException
     */
    function createTransactionFromOrder($order_num)
    {
        $order = Order::loadByWhere([
            'order_num' => $order_num,
            'site_id' => $this->getSiteContext()
        ]);

        if ($order_num && !$order->id) {
            throw new ShopException(t('Не найден заказ %0', [$order_num]), ShopException::ERR_ORDER_NOT_FOUND);
        }
        if ($order_num && $order->is_payed) {
            throw new ShopException(t('Заказ %0 уже оплачен', [$order_num]), ShopException::ERR_ORDER_ALREADY_PAYED);
        }
        if ($order_num && (!$order->canOnlinePay() && !$order->getPayment()->create_order_transaction)) {
            throw new ShopException(t('Статус заказа %0 не предусматривает оплату', [$order_num]), ShopException::ERR_ORDER_BAD_STATUS);
        }

        $transaction = $this->buildTransactionFromOrder($order);
        $transaction->dateof = date('Y-m-d H:i:s');

        $transaction->insert();
        $transaction->sign = self::getTransactionSign($transaction);
        $transaction->update();
        return $transaction;
    }

    /**
     * Возвращает заполненный объект транзакции для заказа
     *
     * @param Order $order
     * @return Orm\Transaction
     */
    protected function buildTransactionFromOrder(Order $order)
    {
        $payment = $order->getPayment();

        $transaction = new Transaction();
        $transaction['payment'] = $payment['id'];
        // Флаг, означающий влияет ли эта транзакция на баланс лицевого счета
        $transaction['personal_account'] = $payment['class'] == PersonalAccount::SHORT_NAME;
        $transaction['order_id'] = $order['id'];
        $transaction['user_id'] = $order['user_id'];
        $transaction['cost'] = $order->getTotalPrice(false);
        $transaction['reason'] = t('Оплата заказа №%0', [$order['order_num']]);
        $transaction['status'] = Transaction::STATUS_NEW;

        return $transaction;
    }

    /**
     * Создать транзацкию для Добавления/Списания средств с лицевого счета
     *
     * @param int $user_id
     * @param int $payment_id
     * @param string $cost
     * @return Orm\Transaction
     * @throws ShopException
     */
    function createTransactionForAddFunds($user_id, $payment_id, $cost, $reason = '')
    {
        if (empty($reason)) {
            $reason = t('Пополнение баланса лицевого счета');
        }
        $transaction = new Transaction();
        $transaction['dateof'] = date('Y-m-d H:i:s');
        $transaction['payment'] = $payment_id;
        $transaction['personal_account'] = 1;
        $transaction['order_id'] = 0;
        $transaction['user_id'] = $user_id;
        $transaction['cost'] = $cost;
        $transaction['reason'] = $reason;
        $transaction['status'] = Transaction::STATUS_NEW;
        $ok = $transaction->insert();
        if ($ok) {
            $transaction->sign = self::getTransactionSign($transaction);
            $transaction->update();
        }
        return $transaction;
    }

    /**
     * Создает новую транзакцию на изменение баланса лицевого счета с учетом данных, полученных из $data.
     * В отличие от метода addFunds позволяет сохранять и произвольные поля к транзакции,
     * добавленные через другие модули к объекту \Shop\Model\Orm\Transaction
     * При успехе - возвращает true
     *
     * @param $data
     * @return bool
     * @throws RSException
     * @throws ShopException
     */
    function changeBalance($data)
    {
        /** @var Transaction $element */
        $element = $this->getElement();

        if (!$data['cost']) {
            $element->addError(t('Не указана сумма'), 'cost');
        }
        if (!$data['reason']) {
            $element->addError(t('Не указано назначение платежа'), 'reason');
        }
        if ($element->hasError()) {
            return false;
        }

        if ($element->save(null, [], $data)) {
            $element['sign'] = self::getTransactionSign($element);
            $element->update();
            return true;
        }
        return false;
    }

    /**
     * Добавить/Списать средства с лицевого счета
     *
     * @param int $user_id ИД пользователя
     * @param string $amount Сумма пополнения (списания)
     * @param bool $writeoff Флаг списания
     * @param string $reason Причина
     * @param bool $ckeck_rights Проверять права на запись для модуля Магазин
     * @param string $entity Сущность, к которой привязана транзакция (произвольный идентификатор)
     * @param string $entity_id ID сущности, к которой привязана транзакция
     * @param integer $payment_id ID способа оплаты, по умолчанию 0 - нет привязки к способу оплаты
     *
     * entity и entity_id может использоваться для выборки транзакций по необходимому критерию,
     * если такие критерии были записаны при создании транзакций.
     *
     * @return Transaction|bool(false)
     * @throws RSException
     */
    function addFunds($user_id, $amount, $writeoff, $reason, $ckeck_rights = true, $entity = null, $entity_id = null, $payment_id = 0)
    {
        if ($ckeck_rights) {
            if ($error = Rights::CheckRightError($this, ModuleRights::RIGHT_ADD_FUNDS)) {
                $this->addError($error);
            }
        }

        if (!$amount) {
            $this->addError(t('Не указана сумма'), t('Сумма'), 'amount');
        }
        if ($amount < 0) {
            $this->addError(t('Сумма не может быть отрицательной'), t('Сумма'), 'amount');
        }
        if ($this->hasError()) {
            return false;
        }

        try {
            $transaction = $this->createTransactionForAddFunds($user_id, $payment_id, $writeoff ? -$amount : $amount);
            if ($transaction['id']) {
                $transaction['reason'] = $reason;
                $transaction['status'] = Transaction::STATUS_SUCCESS;
                $transaction['entity'] = $entity;
                $transaction['entity_id'] = $entity_id;
                $transaction->update();
                return $transaction;
            } else {
                throw new ShopException($transaction->getErrorsStr(), ShopException::ERR_TRANSACTION_CREATION);
            }
        } catch (RSException $e) {
            $this->addError($e->getMessage());
            return false;
        }
    }

    /**
     * Разпознать в запросе идентификатор транзакции. Каждый конкретный тип оплаты это делает по своему.
     *
     * @param mixed $payment_type Короткое имя типа оплаты
     * @param HttpRequest $request
     * @return Transaction|Transaction[]
     * @throws ShopException
     */
    function recognizeTransactionFromRequest($payment_type, HttpRequest $request)
    {
        $pay_api = new PaymentApi();
        // Получаем ассоциативный массив типов оплаты. Ключ - коротное название типа оплаты. Значение - объект типа оплаты.
        $payment_types = $pay_api->getTypes();
        //
        if (!isset($payment_types[$payment_type])) {
            throw new ShopException(t('Неверный тип оплаты: %0', [$payment_type]), ShopException::ERR_BAD_PAYMENT_TYPE);
        }
        if (!($payment_types[$payment_type] instanceof AbstractPaymentType)) {
            throw new ShopException(t('Тип оплаты должен быть наследником PaymentType\AbstractType: %0', [$payment_type]), ShopException::ERR_BAD_PAYMENT_PARENT);
        }
        if (!$payment_types[$payment_type]->isAllowResultUrl()) {
            throw new ShopException(t('Тип оплаты не поддерживает online платежи: %0', [$payment_type]), ShopException::ERR_NOT_ONLINE_PAYMENT);
        }

        // Просим объект типа оплаты распознать ID транзакции из REQUEST. 
        $transaction_id = $payment_types[$payment_type]->getTransactionIdFromRequest($request);

        if (!$transaction_id) {
            throw new ShopException(t("Не передан идентификатор транзакции"), ShopException::ERR_NO_TRANSACTION_ID);
        }

        if (is_array($transaction_id)) {
            $transaction = [];
            foreach ($transaction_id as $id) {
                $transaction[] = new Transaction((int)$id);
            }
            if (empty($transaction)) {
                throw new ShopException(t("Транзакции с идентификаторами %0 не найдены", [implode(',', $transaction_id)]), ShopException::ERR_TRANSACTION_NOT_FOUND);
            }
        } else {
            $transaction = new Transaction((int)$transaction_id);
            if (!$transaction->id) {
                throw new ShopException(t("Транзакция с идентификатором %0 не найдена", [$transaction_id]), ShopException::ERR_TRANSACTION_NOT_FOUND);
            }
        }

        return $transaction;
    }

    /**
     * Возвращает подпись баланса пользователя
     *
     * @param string $balance
     * @param int $user_id
     * @return string
     */
    static function getBalanceSign($balance, $user_id)
    {
        if ($balance == 0) return "";
        $parts = [];
        $parts[] = round($balance, 2);
        $parts[] = (int)$user_id;
        $parts[] = \Setup::$SECRET_KEY;
        return sha1(join('&', $parts));
    }

    /**
     * Возвращает подпись транзакции
     *
     * @param Transaction $transaction
     * @return string
     * @throws ShopException
     */
    static function getTransactionSign(Transaction $transaction)
    {
        if (!$transaction['id']) {
            throw new ShopException(t('Невозможно подписать транзакцию с нулевым идентификатором'), ShopException::ERR_NO_TRANSACTION_ID);
        }
        $parts = [];
        $parts[] = (int)$transaction['id'];
        $parts[] = (int)$transaction['personal_account'];
        $parts[] = (int)$transaction['user_id'];
        $parts[] = round($transaction['cost'], 2);
        $parts[] = \Setup::$SECRET_KEY;
        return sha1(join('&', $parts));

    }

    /**
     * Возвращает баланс пользователя исходя из суммы транзакций
     *
     * @param mixed $user_id
     * @param mixed $except_transaction_ids
     * @return double
     */
    static function getBalance($user_id, array $except_transaction_ids = [])
    {
        // Получаем "Приход"
        $q = OrmRequest::make();
        $q->select('SUM(`cost`) as income');
        $q->from(new Transaction);
        $q->where(['user_id' => $user_id]);
        $q->where(['personal_account' => 1]);
        $q->where(['order_id' => 0]);
        $q->where(['status' => 'success']);
        if (!empty($except_transaction_ids)) {
            $q->where('`id` NOT IN(' . join(',', $except_transaction_ids) . ')');
        }
        $result = $q->exec()->fetchSelected(null, 'income');
        $income = reset($result);

        // Получаем "Расход"
        $q = OrmRequest::make();
        $q->select('SUM(`cost`) as debit');
        $q->from(new Transaction);
        $q->where(['user_id' => $user_id]);
        $q->where(['personal_account' => 1]);
        $q->where('order_id != 0');
        $q->where(['status' => 'success']);
        if (!empty($except_transaction_ids)) {
            $q->where('`id` NOT IN(' . join(',', $except_transaction_ids) . ')');
        }
        $result = $q->exec()->fetchSelected(null, 'debit');
        $debit = reset($result);

        return $income - $debit;
    }

    /**
     * Установка фильтра для получения только транзацкий поолнения/списания с лицевого счета
     *
     */
    function setPersonalAccountTransactionsFilter()
    {
        $this->queryObj()->select = 'A.*';
        $this->queryObj()->leftjoin(new Payment(), "A.payment=P.id", "P");
        $this->queryObj()->where("(A.order_id = 0 OR P.class='" . PersonalAccount::SHORT_NAME . "')");
        $this->queryObj()->where(['user_id' => Auth::getCurrentUser()->id]);
    }

    /**
     * Возвращает true, если для заказа существует хотя бы одна транзакция
     *
     * @param integer $order_id ID заказа
     * @return bool
     */
    function isExistsTransactionForOrder($order_id)
    {
        $transaction = Transaction::loadByWhere([
            'order_id' => $order_id
        ]);

        return $transaction['id'] > 0;
    }

    /**
     * Обновляет НОВУЮ, не оплаченную транзакцию при изменении заказа.
     * Должна присутствовать одна транзакция для оплаты заказа
     *
     * @param Order $order
     * @return bool
     * @throws ShopException
     */
    function updateTransactionFromOrder(Order $order)
    {
        $old_transaction = Transaction::loadByWhere([
            'order_id' => $order->id
        ]);

        if (!$old_transaction['id'] || $old_transaction['status'] != Transaction::STATUS_NEW) {
            return false;
        }

        $new_transaction_data = $this->buildTransactionFromOrder($order);
        foreach ($new_transaction_data->getValues() as $key => $value) {
            if ($old_transaction[$key] != $value) {

                $old_transaction->getFromArray($new_transaction_data->getValues());
                $old_transaction['sign'] = self::getTransactionSign($old_transaction);
                $old_transaction->update();
            }
        }

        return true;
    }
}
