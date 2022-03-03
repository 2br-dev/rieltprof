<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model\PaymentType;

use Catalog\Model\CurrencyApi;
use Catalog\Model\Orm\Product;
use Main\Model\Requester\ExternalRequest;
use RS\Application\Application;
use RS\Config\Loader as ConfigLoader;
use RS\Exception as RSException;
use RS\Helper\Tools as HelperTools;
use RS\Http\Request as HttpRequest;
use RS\Orm\FormObject;
use RS\Orm\PropertyIterator;
use RS\Orm\Type;
use RS\Router\Manager as RouterManager;
use Shop\Model\Cart;
use Shop\Model\ChangeTransaction;
use Shop\Model\Exception as ShopException;
use Shop\Model\Log\LogPaymentYandexKassaApi;
use Shop\Model\Orm\Order;
use Shop\Model\Orm\OrderItem;
use Shop\Model\Orm\SavedPaymentMethod;
use Shop\Model\Orm\Transaction;
use Shop\Model\Orm\TransactionChangeLog;
use Shop\Model\TaxApi;
use Shop\Model\TransactionAction;
use Shop\Model\TransactionApi;
use Users\Model\Orm\User;

/**
 * Способ оплаты - ЮKassa (ранее Яндекс.Касса)
 */
class YandexKassaApi extends AbstractType implements InterfaceRecurringPayments
{
    use TraitInterfaceRecurringPayments;

    const API_URL = 'https://api.yookassa.ru/v3/';
    const TRANSACTION_ACTION_HOLD_CAPTURE = 'hold_capture';
    const TRANSACTION_ACTION_HOLD_CANCEL = 'hold_cancel';
    const TRANSACTION_EXTRA_KEY_PAYMENT_ID = 'payment_id';

    /** @var LogPaymentYandexKassaApi */
    protected $log;

    public function __construct()
    {
        $this->log = LogPaymentYandexKassaApi::getInstance();
    }

    /**
     * Возвращает название расчетного модуля (типа доставки)
     *
     * @return string
     */
    public function getTitle()
    {
        return t('ЮKassa');
    }

    /**
     * Возвращает описание типа оплаты. Возможен HTML
     *
     * @return string
     */
    public function getDescription()
    {
        return t('Оплата через сервис "ЮKassa"');
    }

    /**
     * Возвращает идентификатор данного типа оплаты. (только англ. буквы)
     *
     * @return string
     */
    public function getShortName()
    {
        return 'yandexkassaapi';
    }

    /**
     * Возвращает true, если данный тип поддерживает проведение платежа через интернет
     *
     * @return bool
     */
    public function canOnlinePay()
    {
        return true;
    }

    /**
     * Возвращает ORM объект для генерации формы или null
     *
     * @return FormObject | null
     */
    public function getFormObject()
    {
        $properties = [
            '__help__' => (new Type\MixedType())
                ->setDescription(t(''))
                ->setVisible(true)
                ->setTemplate('%shop%/form/payment/yandexkassaapi/help.tpl'),
            'shop_id' => (new Type\Integer())
                ->setDescription(t('Идентификатор магазина (shopId)'))
                ->setHint(t('Можно узнать в личном кабинете ЮКассы'))
                ->setChecker(Type\Checker::CHECK_EMPTY, t('Не указан "Идентификатор магазина (shopId)"')),
            'key_secret' => (new Type\Varchar())
                ->setDescription(t('Секретный ключ'))
                ->setHint(t('Можно узнать в личном кабинете ЮКассы'))
                ->setChecker(Type\Checker::CHECK_EMPTY, t('Не указан "Секретный ключ"')),
            'is_holding' => (new Type\Integer())
                ->setDescription(t('Холдирование платежей'))
                ->setMaxLength(1)
                ->setDefault(0)
                ->setCheckboxView(1, 0),
            'enable_log' => (new Type\Integer())
                ->setDescription(t('Вести лог запросов?'))
                ->setMaxLength(1)
                ->setDefault(0)
                ->setCheckboxView(1, 0),
        ];

        $properties += $this->getFormCommonProperties();

        $form_object = new FormObject(new PropertyIterator($properties));
        $form_object->setParentObject($this);
        $form_object->setParentParamMethod('Form');
        return $form_object;
    }

    /**
     * Возвращает идентификатор, уникализирующий продавца в рамках типа оплаты
     *
     * @return string
     */
    public function getTypeUnique(): string
    {
        return $this->getOption('shop_id');
    }

    /**
     * Возвращает URL для перехода на сайт сервиса оплаты для совершения платежа
     * Используется только для Online-платежей
     *
     * @param Transaction $transaction
     * @return string
     * @throws RSException
     */
    public function getPayUrl(Transaction $transaction)
    {
        $response = $this->createPayment($transaction);
        if (empty($response['confirmation']['confirmation_url'])) {
            // todo обработать ошибку
        }

        return $response['confirmation']['confirmation_url'];
    }

    /**
     * Возвращает ID заказа исходя из REQUEST-параметров соотвествующего типа оплаты
     * Используется только для Online-платежей
     *
     * @param HttpRequest $request
     * @return mixed
     */
    public function getTransactionIdFromRequest(HttpRequest $request)
    {
        $content = json_decode($request->getStreamInput(), true);
        if (!empty($content['object']['metadata']['transaction_id'])) {
            return $content['object']['metadata']['transaction_id'];
        }
        return $request->request('transaction', TYPE_INTEGER, false);
    }

    /**
     * Вызывается при оплате сервером платежной системы.
     * Возвращает строку - ответ серверу платежной системы.
     * В случае неверной подписи бросает исключение
     * Используется только для Online-платежей
     *
     * @param Transaction $transaction
     * @param HttpRequest $request
     * @return string
     * @throws ResultException
     * @throws ShopException
     */
    public function onResult(Transaction $transaction, HttpRequest $request)
    {
        $this->log->write(t('Web-hook для транзакции №%0', [$transaction['id']]), $this->log::LEVEL_WEB_HOOK);
        $payment_id = $transaction->getExtra(self::TRANSACTION_EXTRA_KEY_PAYMENT_ID);
        if ($payment_id) {
            $response = $this->apiRequest("payments/$payment_id", 'GET', [], $this->log::LEVEL_WEB_HOOK);
            $change = $this->changeTransactionFromResponse($transaction, $response, true);
            $this->log->write($change->getChangeLog(), $this->log::LEVEL_WEB_HOOK);
            return $change;
        } else {
            throw (new ResultException(t('Транзакция не содержит идентификатор платежа')))->setUpdateTransaction(false);
        }
    }

    /**
     * Производит "рекуррентную" оплату заказа
     *
     * @param Order $order - заказ
     * @param SavedPaymentMethod $saved_payment_method - сохранённый спосб оплаты
     * @return void
     * @throws RSException
     * @throws ShopException
     */
    public function recurringPayOrder(Order $order, SavedPaymentMethod $saved_payment_method): void
    {
        $transaction = TransactionApi::getInstance()->createTransactionFromOrder($order['order_num']);
        $transaction['saved_payment_method_id'] = $saved_payment_method['id'];
        $transaction->update();
        TransactionChangeLog::new($transaction, t('Транзакция создана'));
        $response = $this->createPayment($transaction);
        $this->changeTransactionFromResponse($transaction, $response)->applyChanges();

        $redirect = RouterManager::obj()->getUrl('shop-front-onlinepay', [
            'PaymentType' => $this->getShortName(),
            'Act' => 'status',
            'transaction' => $transaction['id'],
        ]);

        Application::getInstance()->redirect($redirect);
    }

    /**
     * Производит "рекуррентное" пополнение лицевого счёта
     *
     * @param User $user - пользователь
     * @param float $cost - сумма пополнения
     * @param SavedPaymentMethod $saved_payment_method - сохранённый спосб оплаты
     * @return void
     * @throws RSException
     * @throws ShopException
     */
    public function recurringPayBalanceFounds(User $user, float $cost, SavedPaymentMethod $saved_payment_method): void
    {
        $transaction = TransactionApi::getInstance()->createTransactionForAddFunds($user['id'], $this->getPayment()['id'], $cost);
        $transaction['saved_payment_method_id'] = $saved_payment_method['id'];
        $transaction->update();
        TransactionChangeLog::new($transaction, t('Транзакция создана'));
        $this->createPayment($transaction);

        $redirect = RouterManager::obj()->getUrl('shop-front-onlinepay', [
            'PaymentType' => $this->getShortName(),
            'Act' => 'status',
            'transaction' => $transaction['id'],
        ]);

        Application::getInstance()->redirect($redirect);
    }

    /**
     * Производит возврат транзакции, привязывающей новый способ оплаты
     *
     * @param Transaction $transaction - транзакция
     * @return void
     * @throws ShopException
     * @throws RSException
     */
    public function refundBindingTransaction(Transaction $transaction): void
    {
        $this->log->write(t('Возврат транзакции привязки карты №%0', [$transaction['id']]), $this->log::LEVEL_MESSAGES);
        $payment_id = $transaction->getExtra(self::TRANSACTION_EXTRA_KEY_PAYMENT_ID);
        if (!$payment_id) {
            throw new ShopException(t('Транзакция не содержит идентификатор платежа'));
        }

        $refund_transaction = TransactionApi::getInstance()->createTransactionForAddFunds($transaction['user_id'], $transaction['payment'], -$transaction['cost'], t('Возврат оплаты при привязке способа оплаты'));
        $this->log->write(t('Создана транзакция №%0', [$refund_transaction['id']]), $this->log::LEVEL_MESSAGES);

        try {
            $params = [
                'payment_id' => $payment_id,
                'amount' => [
                    'value' => -$refund_transaction['cost'],
                    'currency' => CurrencyApi::getBaseCurrency()['title'],
                ],
            ];
            $this->apiRequest('refunds', ExternalRequest::METHOD_POST, $params, $this->log::LEVEL_OUTGOING_REQUEST, $refund_transaction['id']);

            $change = new ChangeTransaction($refund_transaction);
            $change->setNewStatus(Transaction::STATUS_SUCCESS);
            $change->setChangelog(t('Возврат успешно произведён'));
            $change->applyChanges();
            $this->log->write(t('Возврат успешно произведён'), $this->log::LEVEL_MESSAGES);
        } catch (RSException $e) {
            $change = new ChangeTransaction($refund_transaction);
            $change->setNewStatus(Transaction::STATUS_FAIL);
            $change->setError($e->getMessage());
            $change->setChangelog($e->getMessage());
            $change->applyChanges();
            $this->log->write(t('[Ошибка] ') . $e->getMessage(), $this->log::LEVEL_MESSAGES);
        }
    }

    /**
     * Удаляет сохранённый способ платежа
     *
     * @param SavedPaymentMethod $saved_payment_method - Сохранённый способ платежа
     * @return void
     * @throws ShopException
     */
    public function deleteSavedPaymentMethod(SavedPaymentMethod $saved_payment_method): void
    {
        if ($saved_payment_method['payment_type'] != $this->getShortName() || $saved_payment_method['payment_type_unique'] != $this->getTypeUnique()) {
            throw new ShopException(t('Сохранённый способ платежа не принадлежит данному способу оплаты'));
        }
        $saved_payment_method['deleted'] = 1;
        $saved_payment_method->update();
    }

    /**
     * Создаёт платёж в ЯКассе
     *
     * @param Transaction $transaction
     * @return array
     * @throws ShopException
     * @throws RSException
     */
    public function createPayment(Transaction $transaction)
    {
        $this->log->write(t('Создание платежа для транзакции №%0', [$transaction['id']]), $this->log::LEVEL_MESSAGES);
        $params = [
            'amount' => [
                'value' => $transaction['cost'],
                'currency' => CurrencyApi::getBaseCurrency()['title'],
            ],
            'description' => $transaction['reason'],
            'receipt' => $this->getParamsForFZ54Check($transaction),
            'confirmation' => [
                'type' => 'redirect',
                'return_url' => RouterManager::obj()->getUrl('shop-front-onlinepay', [
                    'PaymentType' => $this->getShortName(),
                    'Act' => 'status',
                    'transaction' => $transaction['id'],
                ], true),
            ],
            'capture' => ($this->getOption('is_holding')) ? false : true,
            'metadata' => [
                'transaction_id' => $transaction['id'],
            ],
        ];

        $order = $transaction->getOrder();
        if ($this->getRecurringPaymentsType() == InterfaceRecurringPayments::RECURRING_TYPE_NONE || empty($transaction['saved_payment_method_id'])) {
            $params['confirmation'] = [
                'type' => 'redirect',
                'return_url' => RouterManager::obj()->getUrl('shop-front-onlinepay', [
                    'PaymentType' => $this->getShortName(),
                    'Act' => 'status',
                    'transaction' => $transaction['id'],
                ], true),
            ];
        }

        if ($this->isRecurringPaymentsActive()) {
            if (empty($transaction['saved_payment_method_id'])) {
                $params['save_payment_method'] = true;
                $params['payment_method_data'] = [
                    'type' => 'bank_card',
                ];
            } else {
                $saved_payment_method = new SavedPaymentMethod($transaction['saved_payment_method_id']);
                $params['payment_method_id'] = $saved_payment_method['external_id'];
            }
        }

        $response = $this->apiRequest('payments', 'POST', $params, $this->log::LEVEL_OUTGOING_REQUEST, $transaction['id']);
        $transaction->setExtra(self::TRANSACTION_EXTRA_KEY_PAYMENT_ID, $response['id']);
        $transaction->update();
        $this->log->write(t('Платеж успешно создан'), $this->log::LEVEL_MESSAGES);

        return $response;
    }

    /**
     * Возвращает дополнительные параметры для печати чека по ФЗ-54
     *
     * @param Transaction $transaction - объект транзакции
     * @return array
     * @throws RSException
     */
    protected function getParamsForFZ54Check($transaction)
    {
        $result = [
            'customer' => $this->getFZ54CheckCustomerData($transaction),
            'items' => $this->getFZ54CheckItemsData($transaction),
        ];

        return $result;
    }

    /**
     * Возвращает данные секции "пользователь" для чека ФЗ-54
     *
     * @param Transaction $transaction - объект транзакции
     * @return array
     */
    protected function getFZ54CheckCustomerData(Transaction $transaction)
    {
        $user = $transaction->getUser();
        $full_name = ($user['is_company'] && !empty($user['company'])) ? $user['company'] : $user->getFio();

        $customer = [
            'full_name' => mb_substr($full_name, 0,256),
        ];
        if (!empty($user['company_inn'])) {
            $customer['inn'] = $user['company_inn'];
        }
        if (!empty($user['e_mail'])) {
            $customer['email'] = $user['e_mail'];
        }
        if (!empty($user['company_inn'])) {
            $customer['phone'] = preg_replace(['/[^\d]/'], [''], $user['phone']);
        }

        return $customer;
    }

    /**
     * Возвращает данные секции "список товаров" для чека ФЗ-54
     *
     * @param Transaction $transaction - объект транзакции
     * @return array
     * @throws RSException
     */
    protected function getFZ54CheckItemsData(Transaction $transaction)
    {
        $user = $transaction->getUser();
        $base_currency = CurrencyApi::getBaseCurrency();
        $items = [];

        if ($transaction['order_id'] && !$transaction['entity']) {
            $order = $transaction->getOrder();
            $address = $order->getAddress();
            if ($cart = $order->getCart()) {
                foreach ($cart->getProductItems() as $item) {
                    /** @var OrderItem $order_item */
                    $order_item = $item[Cart::CART_ITEM_KEY];
                    /** @var Product $product */
                    $product = $order_item->getEntity();
                    $title = $order_item['title'] . (($order_item['model']) ? " ({$order_item['model']})" : '');
                    $title = str_replace(["\"", "'"],'`', HelperTools::unEntityString($title));
                    $single_cost = ($order_item['price'] - $order_item['discount']) / $order_item['amount'];

                    $items[] = [
                        'description' => mb_substr($title, 0, 128),
                        'quantity' => (string)$order_item['amount'],
                        'amount' => [
                            'value' => sprintf('%0.2f', round($single_cost, 2)),
                            'currency' => $base_currency['title'],
                        ],
                        'vat_code' => $this->getNdsCode(TaxApi::getProductTaxes($product, $user, $address), $address),
                        'payment_subject' => $product['payment_subject'],
                        'payment_mode' => $product['payment_method'] ?: $order->getDefaultPaymentMethod(),
                    ];
                }

                foreach ($cart->getCartItemsByType(Cart::TYPE_DELIVERY) as $delivery_item) {
                    $delivery = $order->getDelivery();
                    $title = str_replace(["\"", "'"],'`', HelperTools::unEntityString($delivery_item['title']));

                    $items[] = [
                        'description' => mb_substr($title, 0, 128),
                        'quantity' => "1",
                        'amount' => [
                            'value' => sprintf('%0.2f', round($delivery_item['price'] - $delivery_item['discount'], 2)),
                            'currency' => $base_currency['title'],
                        ],
                        'vat_code' => $this->getNdsCode(TaxApi::getDeliveryTaxes($order->getDelivery(), $user, $address), $address),
                        'payment_subject' => 'service',
                        'payment_mode' => $delivery['payment_method'] ?: $order->getDefaultPaymentMethod(),
                    ];
                }
            }
        } elseif ($transaction['personal_account']) {
            $shop_config = ConfigLoader::byModule($this);

            $items[] = [
                'description' => mb_substr($transaction['reason'], 0, 128),
                'quantity' => "1",
                'amount' => [
                    'value' => sprintf('%0.2f', round($transaction['cost'], 2)),
                    'currency' => $base_currency['title'],
                ],
                'vat_code' => self::handbookNds()[$shop_config['nds_personal_account']] ?? self::handbookNds()[TaxApi::TAX_NDS_NONE],
                'payment_subject' => $shop_config['personal_account_payment_subject'],
                'payment_mode' => $shop_config['personal_account_payment_method'],
            ];
        }

        return $items;
    }

    /**
     * Отправляет запрос по API
     *
     * @param string $url - адрес запроса
     * @param string $method - метод запроса
     * @param array $params - параметры
     * @param string $log_level - уровень логирования
     * @param string $idempotence_key - ключ идемпотентности
     * @return array
     * @throws ShopException
     */
    protected function apiRequest(string $url, string $method, array $params, string $log_level, string $idempotence_key = '')
    {
        $shop_id = $this->getOption('shop_id');
        $secret = $this->getOption('key_secret');

        $external_response = (new ExternalRequest('payment_' . $this->getShortName(), self::API_URL . $url))
            ->setMethod($method)
            ->setParams($params)
            ->setBasicAuth($shop_id, $secret)
            ->setContentType(ExternalRequest::CONTENT_TYPE_JSON)
            ->addHeader('Idempotence-Key', $idempotence_key)
            ->setEnableCache(false)
            ->setLog($this->log, $log_level)
            ->executeRequest();

        if ($external_response->getStatus()) {
            if ($external_response->getStatus() != 200) {
                throw new ShopException(t("Ошибка запроса \"%0\": код %1", [$url, $external_response->getStatus()]), 0, null, $external_response->getRawResponse());
            }
        } else {
            throw new ShopException(t("Ошибка запроса \"%0\": не получен код ответа", [$url]));
        }

        $result = $external_response->getResponseJson();

        if (!$result) {
            throw new ShopException(t("Ошибка запроса \"%0\": получено пустое тело ответа", [$url]));
        }

        return $result;
    }

    /**
     * Возвращает список возможных действий с транзакцией
     *
     * @param Transaction $transaction
     * @param Order $order - объект заказа для которого нужно вернуть действия
     * @return TransactionAction[]
     */
    public function getAvailableTransactionActions(Transaction $transaction, Order $order): array
    {
        $result = [];
        if ($transaction['status'] == Transaction::STATUS_HOLD) {
            if ($transaction['order_id'] && $order->getTotalPrice(false) < $transaction['cost']) {
                $capture_confirm_text = t('Вы действительно хотите завершить оплату на сумму %0?', [$order->getTotalPrice()]);
            } else {
                $capture_confirm_text = t('Вы действительно хотите завершить оплату на всю сумму?');
            }
            $result[] = (new TransactionAction($transaction, self::TRANSACTION_ACTION_HOLD_CAPTURE, t('Завершить оплату')))
                ->setConfirmText($capture_confirm_text)
                ->setCssClass(' btn-primary');
            $result[] = (new TransactionAction($transaction, self::TRANSACTION_ACTION_HOLD_CANCEL, t('Отменить оплату')))
                ->setConfirmText(t('Вы действительно хотите отменить оплату?'))
                ->setCssClass(' btn-danger');
        }
        return $result;
    }

    /**
     * Исполняет действие с транзакцией
     * При успехе - возвращает текст сообщения для администратора, при неудаче - бросает исключение
     *
     * @param Transaction $transaction - транзакция
     * @param string $action - идентификатор исполняемого действия
     * @return string
     * @throws ShopException
     * @throws RSException
     */
    public function executeTransactionAction(Transaction $transaction, string $action): string
    {
        $this->log->write(t('Действие "%0" с транзакцией №%1', [$action, $transaction['id']]), $this->log::LEVEL_MESSAGES);
        try {
            $payment_id = $transaction->getExtra(self::TRANSACTION_EXTRA_KEY_PAYMENT_ID);
            if (!$payment_id) {
                throw new ShopException(t('Транзакция не содержит идентификатор платежа'));
            }

            switch ($action) {
                case self::TRANSACTION_ACTION_HOLD_CAPTURE:
                    if ($transaction['status'] != Transaction::STATUS_HOLD) {
                        throw new ShopException(t('Списание холдирования возможно только у транзакции в статусе "%0"', [
                            Transaction::handbookStatus()[Transaction::STATUS_HOLD]
                        ]));
                    }
                    if ($transaction['order_id'] && $transaction['cost'] < $transaction->getOrder()->getTotalPrice(false)) {
                        throw new ShopException(t('Сумма заказа превышает сумму холдирования'));
                    }

                    if ($transaction['order_id'] && $transaction['cost'] > $transaction->getOrder()->getTotalPrice(false)) {
                        $capture_amount = $transaction->getOrder()->getTotalPrice(false);
                    } else {
                        $capture_amount = $transaction['cost'];
                    }

                    $request_params = [
                        'amount' => [
                            'value' => $capture_amount,
                            'currency' => CurrencyApi::getBaseCurrency()['title'],
                        ],
                        'receipt' => $this->getParamsForFZ54Check($transaction),
                    ];
                    $response = $this->apiRequest("payments/$payment_id/capture", ExternalRequest::METHOD_POST, $request_params, $this->log::LEVEL_OUTGOING_REQUEST, (string)rand());

                    $change = $this->changeTransactionFromResponse($transaction, $response);
                    if ($capture_amount < $transaction['cost']) {
                        $change->setNewCost($capture_amount);
                    }
                    $change->applyChanges();

                    return t('Оплата успешно завершена');
                case self::TRANSACTION_ACTION_HOLD_CANCEL:
                    if ($transaction['status'] != Transaction::STATUS_HOLD) {
                        throw new ShopException(t('Отмена холдирования возможна только у транзакции в статусе "%0"', [
                            Transaction::handbookStatus()[Transaction::STATUS_HOLD]
                        ]));
                    }

                    $response = $this->apiRequest("payments/$payment_id/cancel", ExternalRequest::METHOD_POST, [], $this->log::LEVEL_OUTGOING_REQUEST, (string)rand());
                    $this->changeTransactionFromResponse($transaction, $response)->applyChanges();

                    return t('Холдирование отменено');
                default:
                    throw new ShopException(t('Вызванное действие не поддерживается данным типом оплаты'));
            }
        } catch (ShopException $e) {
            $this->log->write(t('[Ошибка] ') . $e->getMessage(), $this->log::LEVEL_MESSAGES);
            throw $e;
        }
    }

    /**
     * Создаёт "изменение транзакции" на основе данных о платеже
     *
     * @param Transaction $transaction - транзакция
     * @param array $response - данные о платеже
     * @param bool $is_notice - изменение вызвано уведомлением от ЯКассы
     * @return ChangeTransaction
     */
    protected function changeTransactionFromResponse(Transaction $transaction, array $response, bool $is_notice = false): ChangeTransaction
    {
        $payment_method_saved = $this->savePaymentMethod($transaction, $response);

        $change = new ChangeTransaction($transaction);
        $changelog = ($is_notice) ? t('Уведомление: ') : '';
        switch ($response['status']) {
            case 'succeeded':
                $change->setNewStatus(Transaction::STATUS_SUCCESS);
                if ($transaction['status'] == Transaction::STATUS_HOLD) {
                    $changelog .= t('Холдирование успешно завершено на сумму %0', [$response['amount']['value']]);
                } else {
                    $changelog .= t('Платёж успешно выполнен');
                }
                break;
            case 'waiting_for_capture':
                $change->setNewStatus(Transaction::STATUS_HOLD);
                $changelog .= t('Платёж захолдирован на сумму %0', [$transaction['cost']]);
                break;
            case 'canceled':
                $error_text = t('Платёж отменён');
                $cancellation_reason = $response['cancellation_details']['reason'] ?? false;
                if ($cancellation_reason) {
                    $error_text .= ': ' . (self::handbookPaymentCancellationReasons()[$cancellation_reason] ?? $cancellation_reason);
                }

                $change->setNewStatus(Transaction::STATUS_FAIL)->setError($error_text);
                $changelog .= $error_text;

                if ($this->isRecurringPaymentsActive() && $cancellation_reason == 'permission_revoked') {
                    $saved_method = new SavedPaymentMethod($transaction['saved_payment_method_id']);
                    $saved_method['deleted'] = 1;
                    $saved_method->update();
                }
                break;
        }
        if ($payment_method_saved) {
            $changelog .= ' (способ платежа сохранён)';
        }

        $change->setChangelog($changelog);
        return $change;
    }

    /**
     * Сохраняет способ платежа
     *
     * @param Transaction $transaction - транзакция
     * @param array $response - данные о платеже
     * @return bool
     */
    protected function savePaymentMethod(Transaction $transaction, array $response): bool
    {
        if (!empty($response['payment_method']['saved'])) {
            $payment_method_data = $response['payment_method'];
            $saved_method = SavedPaymentMethod::loadByWhere(['external_id' => $payment_method_data['id']]);
            if (empty($saved_method['id'])) {
                $payment_method = new SavedPaymentMethod();
                $payment_method['external_id'] = $payment_method_data['id'];
                $payment_method['type'] = SavedPaymentMethod::TYPE_CARD;
                $payment_method['subtype'] = $payment_method_data['card']['card_type'];
                $payment_method['title'] =  "*{$payment_method_data['card']['last4']}";
                $payment_method['user_id'] = $transaction->getUser()['id'];
                $payment_method['payment_type'] = $this->getShortName();
                $payment_method['payment_type_unique'] = $this->getTypeUnique();
                $payment_method['data'] = $payment_method_data['card'];
                if ($payment_method->insert()) {
                    $transaction->setExtra(InterfaceRecurringPayments::TRANSACTION_EXTRA_KEY_SAVED_METHOD, $payment_method['id']);
                    $transaction->update();
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Справочник "Причины отмены платежа"
     *
     * @return string[]
     */
    protected static function handbookPaymentCancellationReasons(): array
    {
        static $reasons;
        if ($reasons === null) {
            $reasons = [
                '3d_secure_failed' => t('Не пройдена аутентификация по 3-D Secure'),
                'call_issuer' => t('Оплата данным платежным средством отклонена по неизвестным причинам'),
                'canceled_by_merchant' => t('Платеж отменен по API при оплате в две стадии'),
                'card_expired' => t('Истек срок действия банковской карты'),
                'country_forbidden' => t('Нельзя заплатить банковской картой, выпущенной в этой стране'),
                'expired_on_capture' => t('Истек срок списания оплаты у двухстадийного платежа'),
                'expired_on_confirmation' => t('Истек срок оплаты: пользователь не подтвердил платеж за отведенное время'),
                'fraud_suspected' => t('Платеж заблокирован из-за подозрения в мошенничестве'),
                'general_decline' => t('Причина не детализирована'),
                'identification_required' => t('Превышены ограничения на платежи для кошелька ЮMoney'),
                'insufficient_funds' => t('Не хватает денег для оплаты'),
                'internal_timeout' => t('Технические неполадки на стороне ЮKassa: не удалось обработать запрос в течение 30 секунд'),
                'invalid_card_number' => t('Неправильно указан номер карты'),
                'invalid_csc' => t('Неправильно указан код CVV2 (CVC2, CID)'),
                'issuer_unavailable' => t('Организация, выпустившая платежное средство, недоступна'),
                'payment_method_limit_exceeded' => t('Исчерпан лимит платежей для данного платежного средства или вашего магазина'),
                'payment_method_restricted' => t('Запрещены операции данным платежным средством (платежное средство заблокировано)'),
                'permission_revoked' => t('Нельзя провести безакцептное списание: пользователь отозвал разрешение на автоплатежи'),
                'unsupported_mobile_operator' => t('Нельзя заплатить с номера телефона этого мобильного оператора'),
            ];
        }
        return $reasons;
    }

    /**
     * Справочник кодов НДС
     * Ключи справочника должны соответствовать списку кодов НДС в TaxApi
     *
     * @return string[]
     */
    protected static function handbookNds()
    {
        static $nds = [
            TaxApi::TAX_NDS_NONE => 1,
            TaxApi::TAX_NDS_0 => 2,
            TaxApi::TAX_NDS_10 => 3,
            TaxApi::TAX_NDS_20 => 4,
            TaxApi::TAX_NDS_110 => 5,
            TaxApi::TAX_NDS_120 => 6,
        ];
        return $nds;
    }
}
