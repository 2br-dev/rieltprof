<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model\Orm;

use Catalog\Model\CurrencyApi;
use RS\Application\Auth;
use RS\Config\Loader as ConfigLoader;
use RS\Exception as RSException;
use RS\Helper\CustomView;
use RS\Http\Request;
use RS\Http\Request as HttpRequest;
use RS\Orm\OrmObject;
use RS\Orm\Request as OrmRequest;
use RS\Orm\Type;
use Shop\Model\CashRegisterApi;
use Shop\Model\Exception as ShopException;
use Shop\Model\ChangeTransaction;
use Shop\Model\PaymentType\InterfaceRecurringPayments;
use Shop\Model\PaymentType\ResultException as PaymentTypeResultException;
use Shop\Model\TransactionApi;
use Shop\Model\TransactionChangeLogApi;
use Shop\Model\TransactionProcessors\AbstractTransactionProcessor;
use Shop\Model\TransactionProcessors\TransactionProcessorApi;
use Shop\Model\TransactionProcessors\TransactionProcessorRecurringBind;
use Users\Model\Orm\User;

/**
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property string $dateof Дата транзакции
 * @property integer $creator_user_id Создатель транзакции
 * @property integer $user_id Пользователь
 * @property integer $order_id ID заказа
 * @property integer $personal_account Транзакция изменяющая баланс лицевого счета
 * @property float $cost Сумма
 * @property float $comission Сумма комиссии платежной системы
 * @property integer $payment Тип оплаты
 * @property integer $saved_payment_method_id id сохранённого способа оплаты
 * @property string $reason Назначение платежа
 * @property integer $force_create_receipt Создать чек
 * @property string $receipt_payment_subject Признак предмета товара для чека
 * @property string $error Ошибка
 * @property string $status Статус транзакции
 * @property string $receipt Последний статус получения чека
 * @property string $sign Подпись транзакции
 * @property string $entity Сущность к которой привязана транзакция
 * @property string $entity_id ID сущности, к которой привязана транзакция
 * @property string $processors Процессоры транзакции
 * @property string $extra Дополнительное поле для данных
 * @property array $extra_arr 
 * @property string $cashregister_last_operation_uuid Последний уникальный идентификатор полученный в ответ от кассы
 * --\--
 */
class Transaction extends OrmObject
{
    //Статусы транзакции
    const STATUS_NEW = 'new';
    const STATUS_HOLD = 'hold';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAIL = 'fail';
    //Статусы чека
    const NO_RECEIPT = 'no_receipt'; //Чека пока не создано
    const RECEIPT_IN_PROGRESS = 'receipt_in_progress'; //Чека пока не создано
    const RECEIPT_SUCCESS = 'receipt_success'; //Чек успешно выбит
    const RECEIPT_REFUND_SUCCESS = 'refund_success';
    const RECEIPT_FAIL = 'fail'; //Если была ошибка в чеке

    const ENTITY_SHIPMENT = 'shipment';
    const ENTITY_PRODUCTS_RETURN = 'products_return';

    private $order;
    private $user;
    private $receipt;

    protected static $table = 'transaction';

    protected $cache_payment;

    public $no_need_check_sign = false; //Эта транзакция до записи новых значений

    function _init()
    {
        parent::_init()->append([
            'site_id' => new Type\CurrentSite(),
            'dateof' => new Type\Datetime([
                'description' => t('Дата транзакции'),
                'visible' => false
            ]),
            'creator_user_id' => new Type\Integer([
                'description' => t('Создатель транзакции'),
                'visible' => false,
            ]),
            'user_id' => new Type\User([
                'maxLength' => '11',
                'description' => t('Пользователь'),
                'template' => '%shop%/form/transaction/user.tpl'
            ]),
            'order_id' => new Type\Integer([
                'maxLength' => '11',
                'description' => t('ID заказа'),
                'visible' => false
            ]),
            'personal_account' => new Type\Integer([
                'maxLength' => '1',
                'description' => t('Транзакция изменяющая баланс лицевого счета'),
                'visible' => false
            ]),
            'cost' => new Type\Decimal([
                'maxLength' => '15',
                'decimal' => 2,
                'description' => t('Сумма'),
            ]),
            'comission' => new Type\Decimal([
                'maxLength' => 15,
                'decimal' => 2,
                'description' => t('Сумма комиссии платежной системы')
            ]),
            'payment' => new Type\Integer([
                'description' => t('Тип оплаты'),
                'visible' => false
            ]),
            'saved_payment_method_id' => new Type\Integer([
                'description' => t('id сохранённого способа оплаты'),
                'visible' => false,
            ]),
            'reason' => new Type\Text([
                'description' => t('Назначение платежа'),
            ]),
            'force_create_receipt' => new Type\Integer([
                'description' => t('Создать чек'),
                'hint' => t('Будет создан чек, где:<br>Признак способа расчета - Полный расчет <br>Вид оплаты - Предварительная оплата (зачет аванса и (или) предыдущих платежей)'),
                'minusVisible' => true,
                'visible' => false,
                'checkboxView' => [1, 0],
                'runtime' => true,
                'template' => '%shop%/form/transaction/force_create_receipt.tpl'
            ]),
            'receipt_payment_subject' => new Type\Varchar([
                'description' => t('Признак предмета товара для чека'),
                'listFromArray' => [CashRegisterApi::getStaticPaymentSubjects()],
                'default' => 'service',
                'runtime' => true,
                'minusVisible' => true,
                'visible' => false,
            ]),
            'error' => new Type\Varchar([
                'description' => t('Ошибка'),
                'visible' => false
            ]),
            'status' => new Type\Enum(array_keys(self::handbookStatus()), [
                'allowEmpty' => false,
                'description' => t('Статус транзакции'),
                'listFromArray' => [self::handbookStatus()],
                'visible' => false
            ]),
            'receipt' => new Type\Enum(array_keys(self::handbookReceipt()), [
                'allowEmpty' => false,
                'description' => t('Последний статус получения чека'),
                'listFromArray' => [self::handbookReceipt()],
                'default' => self::NO_RECEIPT,
                'visible' => false
            ]),
            'sign' => new Type\Varchar([
                'description' => t('Подпись транзакции'),
                'visible' => false
            ]),
            'entity' => new Type\Varchar([
                'description' => t('Сущность к которой привязана транзакция'),
                'maxLength' => 50,
                'visible' => false
            ]),
            'entity_id' => new Type\Varchar([
                'description' => t('ID сущности, к которой привязана транзакция'),
                'maxLength' => 50,
                'visible' => false
            ]),
            'processors' => new Type\Varchar([
                'description' => t('Процессоры транзакции'),
                'visible' => false
            ]),
            'extra' => new Type\Varchar([
                'maxLength' => '4096',
                'description' => t('Дополнительное поле для данных'),
                'visible' => false
            ]),
            'extra_arr' => new Type\ArrayList([
                'visible' => false
            ]),
            'cashregister_last_operation_uuid' => new Type\Varchar([
                'description' => t('Последний уникальный идентификатор полученный в ответ от кассы'),
                'visible' => false
            ]),
        ]);

        $this->addIndex(['entity', 'entity_id'], self::INDEX_KEY);
    }

    /**
     * Вызывается после загрузки объекта
     * @return void
     */
    function afterObjectLoad()
    {
        if (!empty($this['extra'])) {
            $this['extra_arr'] = unserialize($this['extra']) ?: [];
        }
    }

    public function beforeWrite($save_flag)
    {
        $this['extra'] = serialize($this['extra_arr']);

        if ($save_flag == self::INSERT_FLAG) {
            $current_user = Auth::getCurrentUser();
            $this['creator_user_id'] = ($current_user['id'] > 0) ? $current_user['id'] : 0;
        }

        if ($save_flag == self::INSERT_FLAG || $this->no_need_check_sign) {
            return;
        }
        if (!$this->checkSign()) {
            throw new RSException(t('Неверная подпись транзакции %0. Изменение транзакции невозможно', [$this->id]));
        }
    }

    public function afterWrite($save_flag)
    {
        if ($this->no_need_check_sign) {
            return;
        }
        $user = $this->getUser();

        if ($user->id) {
            $transApi = new TransactionApi();
            $real_balance = $transApi->getBalance($user->id);

            // Перезагрузка объекта пользователя
            $user = $user->loadSingle($user->id);

            // Проверяем подпись баланса пользователя
            if (!$user->checkBalanceSign()) {
                throw new RSException(t('Неверная подпись баланса у пользователя id: %0', [$user->id]));
            }

            // Если баланс по сумме транзакций отличается от баланса, сохраненного в поле balance у пользователя
            if ($user->getBalance() != $real_balance) {
                // Проверяем верен ли старый баланс
                $old_balance = $transApi->getBalance($user->id, [$this->id]);      // Получаем сумму на счету без учета этой транзакции
                $sign = TransactionApi::getBalanceSign($old_balance, $this->user_id);   // Формируем подпись к старом балансу
                if ($user->balance_sign == $sign) {                                     // Проверяем верна ли подпись
                    // Устанавливаем новый баланс пользователю
                    $user->balance = $real_balance;
                    $user->balance_sign = TransactionApi::getBalanceSign($real_balance, $this->user_id);
                    $user->update();
                } else {
                    throw new RSException(t('Нарушение целостности истории транзакций'));
                }
            }
        }

        //Отправляем уведомление о пополнении лицевого счёта
        if ($save_flag == self::INSERT_FLAG && (!$this['status'] || $this['status'] == self::STATUS_NEW) && $this['cost'] > 0 && $this['payment'] > 0) { //если новая и баланс на пополнение
            $notice = new \Shop\Model\Notice\NewTransaction();
            $notice->init($this, $user);
            \Alerts\Model\Manager::send($notice);
        }
    }

    /**
     * Проверка подписи транзакции
     *
     * @return bool
     * @throws RSException
     * @throws ShopException
     */
    public function checkSign()
    {
        if (!$this->id) throw new RSException(t('Невозможно подписать транзакцию с нулевым идентификатором'));
        $ok = $this->sign == TransactionApi::getTransactionSign($this);
        return $ok;
    }

    /**
     * Возвращает список процессоров транзакции
     *
     * @return AbstractTransactionProcessor[]
     */
    public function getProcessors()
    {
        $result = [];

        $processors = explode(',', $this['processors']);
        $processor_list = TransactionProcessorApi::getProcessorList();
        foreach ($processors as $processor_name) {
            if (isset($processor_list[$processor_name])) {
                $clone_processor = clone $processor_list[$processor_name];
                $clone_processor->setTransaction($this);
                $result[] = $clone_processor;
            }
        }

        return $result;
    }

    public function addProcessor(string $processor_name)
    {
        if (empty($this['processors'])) {
            $this['processors'] = $processor_name;
        } else {
            $processors = explode(',', $this['processors']);
            $processors[] = $processor_name;
            $this['processors'] = implode(',', $processors);
        }
    }

    /**
     * Возращает объект заказа
     * @return Order
     */
    public function getOrder()
    {
        if ($this->order == null) {
            $this->order = new Order($this->order_id);
        }
        return $this->order;
    }

    /**
     * Возвращает объект пользователя
     * @return User
     */
    public function getUser()
    {
        if ($this->user == null) {
            if ($this->order_id > 0) {
                $this->user = $this->getOrder()->getUser();
            } else {
                $this->user = new User($this->user_id);
            }
        }
        return $this->user;
    }

    /**
     * Возвращает объект способа оплаты
     *
     * @return Payment
     */
    function getPayment()
    {
        if ($this->cache_payment === null) {
            $this->cache_payment = new Payment($this['payment'], true, $this->getOrder()->id ? $this->getOrder() : null, $this);
        }

        return $this->cache_payment;
    }

    /**
     * Возвращает привязанные к транзакции чеки
     *
     * @return Receipt[]
     */
    function getReceipts()
    {
        /** @var Receipt[] $receipts */
        $receipts = OrmRequest::make()
            ->from(new Receipt())
            ->where([
                'transaction_id' => $this->id
            ])->objects();

        return $receipts;
    }

    /**
     * Возвращает URL для перехода на сайт сервиса оплаты для совершения платежа
     *
     * @return string
     */
    function getPayUrl()
    {
        return $this->getPayment()->getTypeObject()->getPayUrl($this);
    }

    /**
     * Возвращает стоимость транзакции с учетом текущих параметров
     *
     * @param bool $use_currency - если true, то значение будет возвращено в текущей валюте, иначе в базовой
     * @param bool $format - если true, то форматировать возвращаемое значение, приписывать символ валюты
     * @return string
     */
    function getCost($use_currency = false, $format = false)
    {
        $cost = ($use_currency) ? CurrencyApi::applyCurrency($this['cost']) : $this['cost'];
        if ($use_currency) {
            return $format ? CustomView::cost($cost, CurrencyApi::getCurrecyLiter()) : $cost;
        } else {
            $base_currency = CurrencyApi::getBaseCurrency();
            return $format ? CustomView::cost($cost, $base_currency['stitle']) : $cost;
        }
    }

    /**
     * Вызывается при оплате.
     * Возвращает строку ответ серверу оплаты.
     *
     * Допускается повторный вызов метода. Действия по обновлению заказа, пробитию чека, отправке уведомлений будут
     * выполнены только при первой попытке успешной оплаты транзакции.
     *
     * @param HttpRequest $request
     * @return string
     * @throws RSException
     */
    function onResult(HttpRequest $request)
    {
        try {
            $response = $this->getPayment()->getTypeObject()->onResult($this, $request);

            if (gettype($response) == 'object') {
                /** @var ChangeTransaction $change */
                $change = $response;
            } else {
                $change = new ChangeTransaction($this);
                $change->setNewStatus(self::STATUS_SUCCESS)
                    ->setResponse($response)
                    ->setChangelog(t('Платёж успешно выполнен'));
            }
        } catch (\Exception $e) {
            $response = ($e instanceof PaymentTypeResultException) ? $e->getResponse() : $e->getMessage();

            if ($e instanceof PaymentTypeResultException && !$e->canUpdateTransaction()) {
                return $response; //Возвращаем ответ как есть
            }

            $change = new ChangeTransaction($this);
            $change->setNewStatus(self::STATUS_FAIL)
                ->setError($e->getMessage())
                ->setResponse($response)
                ->setChangelog(t('Транзакция отменена из-за ошибки'));
        }

        if (in_array($this['status'], [self::STATUS_NEW, self::STATUS_HOLD])) {
            $change->applyChanges();
        }
        return $change->getResponse();
    }

    /**
     * Возвращает true если по транзакции возможно отправить чек возврата
     *
     * @return bool
     */
    public function isPossibleRefundReceipt()
    {
        return $this['personal_account'] || ($this['order_id'] && !$this['entity']);
    }

    /**
     * Возвращает список возможных действий с транзакцией
     *
     * @param Order|null $order - объект заказа для которого нужно вернуть действия, если не указан - берётся из транзакции
     * @return string[]
     */
    public function getAvailableActionsList(Order $order = null): array
    {
        if (!$order) {
            $order = $this->getOrder();
        }
        return $this->getPayment()->getTypeObject()->getAvailableTransactionActions($this, $order);
    }

    /**
     * Исполняет доступное для транзакции действие
     *
     * @param string $action
     * @return string
     * @throws RSException
     */
    public function executeAction(string $action)
    {
        return $this->getPayment()->getTypeObject()->executeTransactionAction($this, $action);
    }

    /**
     * Возвращает список "изменений транзакции"
     *
     * @return TransactionChangeLog[]
     */
    public function getChangeLogs(): array
    {
        /** @var TransactionChangeLog[] $list */
        $list = (new TransactionChangeLogApi())->setFilter(['transaction_id' => $this['id']])->setOrder('id desc')->getList();
        return $list;
    }

    /**
     * Возвращает возможно ли пробитие чека для данной транзакции
     *
     * @return bool
     */
    function isReceiptEnabled(): bool
    {
        $shop_config = ConfigLoader::byModule('shop');
        $payment = $this->getPayment();
        $payment_type = $payment->getTypeObject();

        if ($payment_type instanceof InterfaceRecurringPayments && $payment_type->getOption('forbid_receipt_on_bind_method', false)) {
            foreach ($this->getProcessors() as $processor) {
                if ($processor instanceof TransactionProcessorRecurringBind) {
                    return false;
                }
            }
        }

        return $payment['create_cash_receipt'] && $shop_config['cashregister_class'];
    }

    /**
     * Возвращает занчение из массива дополнительных сведений по ключу
     *
     * @param string $key - ключ данных
     * @param null $default - значение по умолчанию
     * @return mixed
     */
    public function getExtra(string $key, $default = null)
    {
        return $this['extra_arr'][$key] ?? $default;
    }

    /**
     * Устанавливает занчение в массив дополнительных сведений
     *
     * @param string $key - ключ данных
     * @param mixed $value - значение
     */
    public function setExtra(string $key, $value)
    {
        $extra = $this['extra_arr'];
        $extra[$key] = $value;
        $this['extra_arr'] = $extra;
    }

    /**
     * Справочник статусов транзакции
     *
     * @return string[]
     */
    public static function handbookStatus(): array
    {
        return [
            self::STATUS_NEW => t('Платеж инициирован'),
            self::STATUS_HOLD => t('Платёж захолдирован'),
            self::STATUS_SUCCESS => t('Платеж успешно завершен'),
            self::STATUS_FAIL => t('Платеж завершен с ошибкой'),
        ];
    }

    /**
     * Справочник статусов чека
     *
     * @return string[]
     */
    protected static function handbookReceipt(): array
    {
        return [
            self::NO_RECEIPT => t('Чек пока не получен'),
            self::RECEIPT_IN_PROGRESS => t('Чек в очереди на получение'),
            self::RECEIPT_SUCCESS => t('Чек получен'),
            self::RECEIPT_REFUND_SUCCESS => t('Чек возвратата получен'),
            self::RECEIPT_FAIL => t('Ошибка в последнем чеке'),
        ];
    }
}
