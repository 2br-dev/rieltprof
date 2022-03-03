<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Controller\Front;

use Partnership\Model\Api as PartnershipApi;
use Partnership\Model\Orm\Partner;
use RS\Application\Application;
use RS\Application\Auth;
use RS\Controller\Front;
use RS\Controller\Result\Standard;
use RS\Module\Manager as ModuleManager;
use Shop\Model\Exception as ShopException;
use Shop\Model\Log\LogOnlinePay;
use Shop\Model\OnlinePayApi;
use Shop\Model\Orm\Order;
use Shop\Model\Orm\Payment;
use Shop\Model\Orm\Transaction;
use Shop\Model\Orm\TransactionChangeLog;
use Shop\Model\Orm\UserStatus;
use Shop\Model\PaymentApi;
use Shop\Model\PaymentType\InterfaceRecurringPayments;
use Shop\Model\TransactionApi;
use Shop\Model\TransactionProcessors\TransactionProcessorRecurringBind;
use Shop\Model\UserStatusApi;

/**
 * Контроллер для обработки Online-платежей
 */
class OnlinePay extends Front
{
    /**
     * @var $log LogOnlinePay
     */
    protected $log;

    public function init()
    {
        $this->log = LogOnlinePay::getInstance();
    }

    /**
     * @return Standard
     * @throws ShopException
     */
    public function actionPay()
    {
        $sign = $this->url->request('sign', TYPE_STRING);
        $json_params = htmlspecialchars_decode($this->url->request('json_params', TYPE_STRING));
        $json_decoded_params = (array)json_decode($json_params, true);
        $params = $this->url->request('params', TYPE_ARRAY, []);

        $sign_params = empty($json_decoded_params) ? $params : $json_decoded_params;
        if (OnlinePayApi::getPayParamsSign($sign_params) != $sign) {
            throw new ShopException(t('Ошибка подписи'));
        }

        $params = array_merge($params, $json_decoded_params);

        switch ($params['type']) {
            case OnlinePayApi::TYPE_ORDER_PAY:
                $order = Order::loadByWhere(['order_num' => $params['order_num']]);
                return $this->payOrder($params, $order);
            case OnlinePayApi::TYPE_BALANCE_ADD_FOUNDS:
                $payment = new Payment($params['payment']);
                $payment_type = $payment->getTypeObject();
                $cost = (float)$params['cost'];

                if ($cost <= 0) {
                    throw new ShopException(t('Не указана сумма пополнения'));
                }

                if ($payment_type instanceof InterfaceRecurringPayments) {
                    switch ($payment_type->getRecurringPaymentsType()) {
                        case InterfaceRecurringPayments::RECURRING_TYPE_SAVE_METHOD:
                            $user = Auth::getCurrentUser();
                            $saved_methods = $payment_type->getSavedPaymentMethods($user);

                            if (isset($params['payment_method']) && $params['payment_method'] != 0 && !isset($saved_methods[$params['payment_method']])) {
                                unset($params['payment_method']);
                            }

                            if (empty($saved_methods) || (isset($params['payment_method']) && $params['payment_method'] == 0)) {
                                $transaction = TransactionApi::getInstance()->createTransactionForAddFunds($user['id'], $payment['id'], $params['cost']);
                                TransactionChangeLog::new($transaction, t('Транзакция создана'));
                                return $this->actionPayTransaction($transaction);
                            }

                            if (!empty($params['payment_method'])) {
                                $payment_type->recurringPayBalanceFounds($user, $cost, $saved_methods[$params['payment_method']]);
                            }

                            $this->view->assign([
                                'params' => $params,
                                'json_params' => json_encode($params),
                                'sign' => OnlinePayApi::getPayParamsSign($params),
                                'saved_methods' => $saved_methods,
                                'recurring_type' => $payment_type->getRecurringPaymentsType(),
                            ]);
                            return $this->result->setTemplate('%shop%/onlinepay/select_method.tpl');
                            break;
                        case InterfaceRecurringPayments::RECURRING_TYPE_ONLY_SAVE_METHOD:
                            throw new ShopException(t('Данный способ оплаты нельзя использовать для пополнения лицевого счёта'));
                    }
                }

                $transaction = TransactionApi::getInstance()->createTransactionForAddFunds(Auth::getCurrentUser()['id'], $payment['id'], $params['cost']);
                if ($transaction['id']) {
                    return $this->actionPayTransaction($transaction);
                } else {
                    throw new ShopException($transaction->getErrorsStr());
                }
            case OnlinePayApi::TYPE_SAVE_PAYMENT_METHOD:
                $payment_id = $params['payment'];

                $transaction = TransactionApi::getInstance()->createTransactionForAddFunds(Auth::getCurrentUser()['id'], $payment_id, 1, t('Привязка нового способа оплаты'));
                $transaction->addProcessor(TransactionProcessorRecurringBind::getName());
                $transaction->update();
                TransactionChangeLog::new($transaction, t('Транзакция создана'));
                return $this->actionPayTransaction($transaction);
        }
    }

    /**
     * Онлайн оплата заказа
     *
     * @param array $params - параметры запроса
     * @param Order $order - заказ
     * @return Standard
     * @throws ShopException
     */
    protected function payOrder(array $params, Order $order): Standard
    {
        $this->checkOrder($order);

        $payment_type = $order->getPayment()->getTypeObject();
        if ($payment_type instanceof InterfaceRecurringPayments && $payment_type->isRecurringPaymentsActive()) {
            $saved_methods = $payment_type->getSavedPaymentMethods($order->getUser());

            if (isset($params['payment_method']) && $params['payment_method'] != 0 && !isset($saved_methods[$params['payment_method']])) {
                unset($params['payment_method']);
            }

            switch ($payment_type->getRecurringPaymentsType()) {
                case InterfaceRecurringPayments::RECURRING_TYPE_SAVE_METHOD:
                    if (empty($saved_methods) || (isset($params['payment_method']) && $params['payment_method'] == 0)) {
                        $transaction = TransactionApi::getInstance()->createTransactionFromOrder($order['order_num']);
                        TransactionChangeLog::new($transaction, t('Транзакция создана'));
                        return $this->actionPayTransaction($transaction);
                    }

                    if (!empty($params['payment_method'])) {
                        $payment_type->recurringPayOrder($order, $saved_methods[$params['payment_method']]);
                    }

                    $this->view->assign([
                        'params' => $params,
                        'json_params' => json_encode($params),
                        'sign' => OnlinePayApi::getPayParamsSign($params),
                        'saved_methods' => $saved_methods,
                        'recurring_type' => $payment_type->getRecurringPaymentsType(),
                    ]);
                    return $this->result->setTemplate('%shop%/onlinepay/select_method.tpl');
                case InterfaceRecurringPayments::RECURRING_TYPE_ONLY_SAVE_METHOD:
                    if (isset($params['payment_method']) && $params['payment_method'] == 0) {
                        $transaction = TransactionApi::getInstance()->createTransactionForAddFunds($order['user_id'], $order['payment'], 1, t('Привязка нового способа оплаты'));
                        $transaction->addProcessor(TransactionProcessorRecurringBind::getName());
                        $transaction->setExtra(TransactionProcessorRecurringBind::EXTRA_KEY_ORDER_ID, $order['id']);
                        $transaction->update();
                        TransactionChangeLog::new($transaction, t('Транзакция создана'));
                        return $this->actionPayTransaction($transaction);
                    }

                    if (!empty($params['payment_method'])) {
                        $order['saved_payment_method_id'] = $params['payment_method'];
                        $order['status'] = UserStatusApi::getStatusIdByType(UserStatus::STATUS_PAYMENT_METHOD_SELECTED);
                        $order->update();
                        $this->view->assign([
                            'type' => 'order',
                            'order' => $order,
                        ]);
                        return $this->result->setTemplate('%shop%/onlinepay/payment_method_selected.tpl');
                    }

                    $this->view->assign([
                        'params' => $params,
                        'json_params' => json_encode($params),
                        'sign' => OnlinePayApi::getPayParamsSign($params),
                        'saved_methods' => $saved_methods,
                        'recurring_type' => $payment_type->getRecurringPaymentsType(),
                    ]);
                    return $this->result->setTemplate('%shop%/onlinepay/select_method.tpl');
            }
        } else {
            $transaction = TransactionApi::getInstance()->createTransactionFromOrder($order['order_num']);
            TransactionChangeLog::new($transaction, t('Транзакция создана'));
            return $this->actionPayTransaction($transaction);
        }
    }

    /**
     * Проверяет заказ на возможность онлайн оплаты
     *
     * @param Order $order
     * @throws ShopException
     */
    protected function checkOrder(Order $order)
    {
        if (!$order['id']) {
            throw new ShopException(t('Заказ не найден'));
        }
        if ($order['is_payed']) {
            throw new ShopException(t('Заказ уже оплачен'));
        }
        $available_statuses = array_merge(UserStatusApi::getStatusesIdByType(UserStatus::STATUS_WAITFORPAY), UserStatusApi::getStatusesIdByType(UserStatus::STATUS_PAYMENT_METHOD_SELECTED));
        if (!in_array($order['status'], $available_statuses)) {
            throw new ShopException(t('Статус заказа не предполагает оплату'));
        }
        if (!$order->canOnlinePay()) {
            throw new ShopException(t('Данный заказ не может быть оплачен онлайн'));
        }
    }

    /**
     * Производит онлайн оплату транзакции
     *
     * @param Transaction|null $transaction - транзакция для оплаты
     * @return Standard
     * @throws ShopException
     */
    public function actionPayTransaction(Transaction $transaction = null)
    {
        if ($transaction === null) {
            $transaction = new Transaction($this->url->request('transaction', TYPE_INTEGER));
        }

        if (!$transaction['id']) {
            throw new ShopException(t('Транзакция не найдена'));
        }
        if ($transaction['status'] != Transaction::STATUS_NEW) {
            throw new ShopException(t('Статус транзакции не предполагает оплату'));
        }

        if ($transaction->getPayment()->getTypeObject()->isPostQuery()) {
            $url = $transaction->getPayUrl();
            $this->view->assign([
                'url' => $url,
                'transaction' => $transaction,
            ]);
            $this->wrapOutput(false);
            return $this->result->setTemplate("%shop%/onlinepay/post.tpl");
        } else {
            Application::getInstance()->redirect($transaction->getPayUrl());
        }
    }

    /**
     * Шаг 6. Редирект на страницу оплаты (переход к сервису online-платежей)
     * Вызывается только в случае Online типа оплаты.
     * Данный action выполняется при нажатии на кнопку "Перейти к оплате"
     * Перед редиректом создается новая транзакция со статусом 'new'. Её идентификатор будет фигурировать в URL оплаты
     *
     */
    function actionDoPay()
    {
        $this->wrapOutput(false);
        $order_id = $this->url->request('order_id', TYPE_STRING);

        $transactionApi = new TransactionApi();

        try {
            $transaction = $transactionApi->createTransactionFromOrder($order_id);
        } catch (ShopException $e) {
            if ($e->getCode() == ShopException::ERR_ORDER_ALREADY_PAYED) {
                $this->app->redirect($this->router->getUrl('shop-front-myorderview', ['order_id' => $order_id]));
            } else {
                throw $e;
            }
        }

        if ($transaction->getPayment()->getTypeObject()->isPostQuery()) { //Если нужен пост запрос
            $url = $transaction->getPayUrl();
            $this->view->assign([
                'url' => $url,
                'transaction' => $transaction
            ]);
            $this->wrapOutput(false);
            return $this->result->setTemplate("%shop%/onlinepay/post.tpl");
        } else {
            Application::getInstance()->redirect($transaction->getPayUrl());
        }
    }

    /**
     * Особый action, который вызвается с сервера online платежей
     * В REQUEST['PaymentType'] должен содержаться строковый идентификатор типа оплаты
     *
     * http://САЙТ.РУ/onlinepay/{PaymentType}/result/
     */
    function actionResult()
    {
        $request = $this->url;
        // Логируем запрос
        $this->log->write(t('Входящий запрос на адрес %0', [$request->getSelfUrl()]), LogOnlinePay::LEVEL_IN);
        $this->log->write(t('GET-параметры: %0', [json_encode($request->getSource(GET), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)]), LogOnlinePay::LEVEL_IN);
        $this->log->write(t('POST-параметры: %0', [json_encode($request->getSource(POST), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)]), LogOnlinePay::LEVEL_IN);
        $this->log->write(t('BODY-параметры: %0', [$request->getStreamInput()]), LogOnlinePay::LEVEL_IN);

        $this->wrapOutput(false);
        $payment_api = new PaymentApi();
        $payment_type = $this->url->get('PaymentType', TYPE_STRING);
        $transactionApi = new TransactionApi();
        $response = null;
        try {
            $transaction = $transactionApi->recognizeTransactionFromRequest($payment_type, $this->url);
            if (is_array($transaction)) {
                $response_array = [];
                foreach ($transaction as $one_transaction) {
                    $response_array[] = $one_transaction->onResult($this->url);
                }
                $payment_types = $payment_api->getTypes();
                $response = $payment_types[$payment_type]->wrapOnResultArray($response_array);
            } else {
                $response = $transaction->onResult($this->url);
            }
        } catch (\Exception $e) {
            return $e->getMessage();       // Вывод ошибки
        }
        // Логируем ответ
        $this->log->write(t('Ответ: %0', [$response]), LogOnlinePay::LEVEL_IN);
        return $response;
    }

    /**
     * Страница извещения об успешном совершении платежа
     * http://САЙТ.РУ/onlinepay/{PaymentType}/success/
     *
     * @return Standard|string
     */
    function actionSuccess()
    {
        $payment_type = $this->url->get('PaymentType', TYPE_STRING);
        $transactionApi = new TransactionApi();
        try {
            $transaction = $transactionApi->recognizeTransactionFromRequest($payment_type, $this->url);
            $transaction->getPayment()->getTypeObject()->onSuccess($transaction, $this->url);
        } catch (\Exception $e) {
            return $e->getMessage();       // Вывод ошибки
        }

        $this->redirectToPartner($transaction);

        $this->view->assign('transaction', $transaction);
        //Проверим, если это заказ и у типа оплаты стоит, флаг выбивания чека
        if ($transaction->getPayment()->create_cash_receipt) {
            $this->view->assign([
                'need_check_receipt' => true
            ]);
            $this->app->addJsVar('receipt_check_url', $this->router->getUrl('shop-front-onlinepay', [
                'Act' => 'checktransactionreceiptstatus',
                'id' => $transaction['id'],
            ]));
        }

        $this->app->setBodyClass('payment-ok', true); //Добавим класс для проверки в мобильным приложением
        return $this->result->setTemplate('onlinepay/success.tpl');
    }

    /**
     * Страница извещения о неудачи при проведении платежа (например если пользователь отказался от оплаты)
     * http://САЙТ.РУ/onlinepay/{PaymentType}/fail/
     *
     * @return Standard|string
     */
    function actionFail()
    {
        $payment_type = $this->url->get('PaymentType', TYPE_STRING);
        $transactionApi = new TransactionApi();
        try {
            $transaction = $transactionApi->recognizeTransactionFromRequest($payment_type, $this->url);
            $transaction->getPayment()->getTypeObject()->onFail($transaction, $this->url);
        } catch (\Exception $e) {
            return $e->getMessage();       // Вывод ошибки
        }

        $this->redirectToPartner($transaction);

        $this->view->assign('transaction', $transaction);
        $this->app->setBodyClass('payment-fail', true); //Добавим класс для проверки мобильным приложением
        return $this->result->setTemplate('onlinepay/fail.tpl');
    }

    /**
     * Страница извещения о результате проведения платежа
     * http://САЙТ.РУ/onlinepay/{PaymentType}/status/
     *
     * @return Standard|string
     */
    public function actionStatus()
    {
        $payment_type = $this->url->get('PaymentType', TYPE_STRING);
        $transactionApi = new TransactionApi();
        try {
            $transaction = $transactionApi->recognizeTransactionFromRequest($payment_type, $this->url);
            $transaction->getPayment()->getTypeObject()->onStatus($transaction, $this->url);
        } catch (\Exception $e) {
            return $e->getMessage();       // Вывод ошибки
        }

        $this->redirectToPartner($transaction);

        $recurring_bind = false;
        $recurring_bind_order = 0;
        foreach ($transaction->getProcessors() as $processor) {
            if ($processor instanceof TransactionProcessorRecurringBind) {
                $recurring_bind = true;
                $recurring_bind_order = $transaction->getExtra(TransactionProcessorRecurringBind::EXTRA_KEY_ORDER_ID, 0);
            }
        }

        $this->view->assign([
            'transaction' => $transaction,
            'recurring_bind' => $recurring_bind,
            'recurring_bind_order' => $recurring_bind_order,
        ]);
        $this->app->setBodyClass('payment-status', true); //Добавим класс для проверки мобильным приложением
        return $this->result->setTemplate('onlinepay/status.tpl');
    }

    /**
     * Проверяет статус транзакции
     *
     * @return Standard
     */
    function actionCheckTransactionStatus()
    {
        $id = $this->url->get('id', TYPE_INTEGER, 0);
        $transaction = new Transaction($id);
        if ($transaction['id']) {
            $payment_type = $transaction->getPayment()->getTypeObject();
            if ($transaction['status'] == Transaction::STATUS_NEW && $payment_type->canOnlinePay()) {
                $payment_type->checkPaymentStatus($transaction);
            }

            $this->result->setSuccess(true)->addSection('status', $transaction['status']);
        } else {
            $this->result->setSuccess(false);
        }
        return $this->result;
    }

    /**
     * Проверяет статус выбивания чека для транзакции
     *
     */
    function actionCheckTransactionReceiptStatus()
    {
        $id = $this->url->get('id', TYPE_INTEGER, 0);
        $transaction = new Transaction($id);
        $success = false;
        if ($transaction['id']) {
            if ($transaction['receipt'] == Transaction::RECEIPT_SUCCESS || $transaction['receipt'] == Transaction::RECEIPT_REFUND_SUCCESS) { //Если человек получен успешно
                $success = true;
            } elseif ($transaction['receipt'] == Transaction::RECEIPT_FAIL) {
                $success = true;
                $this->result->addSection('error', t('Ошибка при выписке чека. Пожалуйста обратитесь к менеджеру сайта.'));
            }
        }
        return $this->result->setSuccess($success);
    }

    /**
     * Перенаправляет на партнёрский сайт транзакции
     *
     * @param $transaction - объект транзакции
     */
    protected function redirectToPartner($transaction)
    {
        if (ModuleManager::staticModuleEnabled('partnership')) {
            if (!empty($transaction['partner_id']) && $transaction['partner_id'] != PartnershipApi::getCurrentPartner()->id) {
                $partner = new Partner($transaction['partner_id']);
                Application::getInstance()->redirect($this->url->getProtocol() . '://' . $partner->getMainDomain() . $this->url->selfUri());
            }
        }
    }
}
