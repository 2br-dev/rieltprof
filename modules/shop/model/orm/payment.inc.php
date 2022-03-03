<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model\Orm;

use RS\Config\Loader as ConfigLoader;
use RS\Orm\OrmObject;
use RS\Orm\Request as OrmRequest;
use \RS\Orm\Type;
use Shop\Model\PaymentApi;
use Shop\Model\PaymentType\AbstractType as AbstractPaymentType;
use Shop\Model\PaymentType\InterfaceRecurringPayments;
use Shop\Model\TransactionProcessors\TransactionProcessorRecurringBind;
use \Shop\Model\UserStatusApi;

/**
 * Способ оплаты текущего сайта, присутствующий в списке выбора при оформлении заказа. 
 * Содержит связь с модулем процессинга.
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property string $title Название
 * @property string $admin_suffix Пояснение
 * @property string $description Описание
 * @property string $picture Логотип
 * @property integer $first_status Стартовый статус заказа
 * @property string $user_type Категория пользователей для данного типа оплаты
 * @property string $target Область применения
 * @property array $delivery Связь с доставками
 * @property string $_delivery Связь с доставками
 * @property integer $public Публичный
 * @property integer $default_payment Оплата по-умолчанию?
 * @property double $commission Комиссия за оплату в %
 * @property integer $commission_include_delivery Включать стоимость доставки в комиссию
 * @property integer $commission_as_product_discount Присваивать комиссию в качестве скидки или наценки к товарам
 * @property string $class Расчетный класс (тип оплаты)
 * @property string $_serialized Параметры рассчетного класса
 * @property array $data 
 * @property integer $sortn Сорт. индекс
 * @property string $min_price Минимальная сумма заказа
 * @property string $max_price Максимальная сумма заказа
 * @property integer $success_status Статус заказа в случае успешной оплаты
 * @property integer $holding_status Статус заказа в случае холдирования
 * @property integer $holding_cancel_status Статус заказа в случае отмены холдирования
 * @property integer $create_cash_receipt Выбить чек после оплаты?
 * @property string $payment_method Признак способа расчета в чеке у заказа с этим способом оплаты
 * @property integer $create_order_transaction Создавать транзакцию при создании заказа
 * --\--
 */
class Payment extends OrmObject
{
    protected static
        $table = 'order_payment';
    
    protected
        $cache_payment,
        $order,
        $transaction;
    
    function __construct($id = null, $cache = true, Order $order = null, Transaction $transaction = null)
    {
        parent::__construct($id, $cache);
        $this->order        = $order;
        $this->transaction  = $transaction;
    }
        
    function _init()
    {
        parent::_init()->append([
            t('Основные'),
                'site_id' => new Type\CurrentSite(),
                'title' => new Type\Varchar([
                    'maxLength' => '255',
                    'description' => t('Название'),
                ]),
                'admin_suffix' => new Type\Varchar([
                    'maxLength' => '255',
                    'description' => t('Пояснение'),
                    'hint' => t('Отображается только в диалогах административной части<br>
                                    используйте если у вас есть доставки с одинаковым названем')
                ]),
                'description' => new Type\Text([
                    'description' => t('Описание'),
                ]),
                'picture' => new Type\Image([
                    'max_file_size' => 10000000,
                    'allow_file_types' => ['image/pjpeg', 'image/jpeg', 'image/png', 'image/gif'],
                    'description' => t('Логотип'),
                ]),
                'first_status' => new Type\Integer([
                    'description' => t('Стартовый статус заказа'),
                    'tree' => [['\Shop\Model\UserStatusApi', 'staticTreeList'], 0, [0 => t('По умолчанию (как в настройках модуля Магазин)')]],
                ]),
                'user_type' => new Type\Enum(['all', 'user', 'company'], [
                    'allowEmpty' => false,
                    'description' => t('Категория пользователей для данного типа оплаты'),
                    'listFromArray' => [[
                        'all' => t('Все'),
                        'user' => t('Физические лица'),
                        'company' => t('Юридические лица')
                    ]]
                ]),
                'target' => new Type\Enum(['all', 'orders', 'refill'], [
                    'allowEmpty' => false,
                    'description' => t('Область применения'),
                    'listFromArray' => [[
                        'all' => t('Везде'),
                        'orders' => t('Оплата заказов'),
                        'refill' => t('Пополнение баланса')
                    ]]
                ]),
                'delivery' => new Type\ArrayList([
                    'description' => t('Связь с доставками'),
                    'hint' => t('Если не указно, то способ оплаты будет отображен при выборе любого способа доставки'),
                    'list' => [['\Shop\Model\DeliveryApi', 'getListForPayment']],
                    'attr' => [[
                        'size' => 10,
                        'multiple' => true,
                    ]],
                ]),
                '_delivery' => new Type\Varchar([
                    'maxLength' => '1500',
                    'description' => t('Связь с доставками'),
                    'visible' => false
                ]),
                'public' => new Type\Integer([
                    'description' => t('Публичный'),
                    'maxLength' => 1,
                    'default' => 1,
                    'checkboxView' => [1,0]
                ]),
                'default_payment' => new Type\Integer([
                    'description' => t('Оплата по-умолчанию?'),
                    'hint' => t('Если включено, то будет выбрано при оформлении заказа'),
                    'maxLength' => 1,
                    'default' => 0,
                    'checkboxView' => [1,0]
                ]),
                'commission' => new Type\Real([
                    'description' => t('Комиссия за оплату в %'),
                    'hint' => t('Если 0 или ничего, то учитываться не будет'),
                    'maxLength' => 11,
                    'decimal' => 4,
                    'default' => 0,
                ]),
                'commission_include_delivery' => new Type\Integer([
                    'description' => t('Включать стоимость доставки в комиссию'),
                    'maxLength' => 1,
                    'default' => 0,
                    'checkboxView' => [1,0]
                ]),
                'commission_as_product_discount' => new Type\Integer([
                    'description' => t('Присваивать комиссию в качестве скидки или наценки к товарам'),
                    'hint' => t('Распределяет комиссию по товарам в заказе, для корректной работы накладных у доставок и онлайн чеков'),
                    'maxLength' => 1,
                    'default' => 0,
                    'checkboxView' => [1,0],
                    'hitn' => t('При включении опция "Включать стоимость доставки в комиссию" становится недействительной')
                ]),
                'class' => new Type\Varchar([
                    'maxLength' => '255',
                    'description' => t('Расчетный класс (тип оплаты)'),
                    'template' => '%shop%/form/payment/other.tpl',
                    'list' => [['\Shop\Model\PaymentApi', 'getTypesAssoc']]
                ]),
                '_serialized' => new Type\Text([
                    'description' => t('Параметры рассчетного класса'),
                    'visible' => false,
                ]),
                'data' => new Type\ArrayList([
                    'visible' => false
                ]),
                'sortn' => new Type\Integer([
                    'maxLength' => '11',
                    'allowEmpty' => true,
                    'description' => t('Сорт. индекс'),
                    'visible' => false,
                ]),
            t('Дополнительные условия показа'),
                'min_price' => new Type\Decimal([
                    'maxLength' => 20,
                    'decimal' => 2,
                    'phpType' => 'string',
                    'description' => t('Минимальная сумма заказа'),
                    'hint' => t('Условие при котором, будет показываться оплата.<br/>пустое поле - условие не действует.'),
                ]),
                'max_price' => new Type\Decimal([
                    'maxLength' => 20,
                    'phpType' => 'string',
                    'decimal' => 2,
                    'description' => t('Максимальная сумма заказа'),
                    'hint' => t('Условие при котором, будет показываться оплата.<br/>пустое поле - условие не действует.'),
                ]),
            t('Смена статуса заказа'),
                'success_status' => new Type\Integer([
                    'description' => t('Статус заказа в случае успешной оплаты'),
                    'tree' => [['\Shop\Model\UserStatusApi', 'staticTreeList'], 0, [0 => t('Не изменять')]],
                    'default' => 0,
                ]),
                'holding_status' => new Type\Integer([
                    'description' => t('Статус заказа в случае холдирования'),
                    'hint' => t('Используется только в рассчётных классах, поддерживающих холдирование'),
                    'tree' => [['\Shop\Model\UserStatusApi', 'staticTreeList'], 0, [0 => t('Не изменять')]],
                    'default' => 0,
                ]),
                'holding_cancel_status' => new Type\Integer([
                    'description' => t('Статус заказа в случае отмены холдирования'),
                    'hint' => t('Используется только в рассчётных классах, поддерживающих холдирование'),
                    'tree' => [['\Shop\Model\UserStatusApi', 'staticTreeList'], 0, [0 => t('Не изменять')]],
                    'default' => 0,
                ]),
            t('Фискализация'),
                'create_cash_receipt' => new Type\Integer([
                    'description' => t('Выбить чек после оплаты?'),
                    'hint' => t('Если флаг установлен, то при записи заказа после его оплаты или возрата будет отправлен запрос к Вашей подключённой кассе'),
                    'maxLength' => 1,
                    'default' => 0,
                    'checkboxView' => [1,0]
                ]),
                'payment_method' => new Type\Varchar([
                    'description' => t('Признак способа расчета в чеке у заказа с этим способом оплаты'),
                    'hint' => t('Перекрывается настройками товара.'),
                    'list' => [['\Shop\Model\CashRegisterApi', 'getStaticPaymentMethods'], [0 => t('По умолчанию')]],
                    'default' => 0,
                ]),
                'create_order_transaction' => new Type\Integer([
                    'description' => t('Создавать транзакцию при создании заказа'),
                    'hint' => t('Актуально только для рассчетных классов, не поддерживающих автоматический процессинг. Например: квитанция банка. Данный флажок позволит отмечать заказ оплаченным и выбивать чек вручную, как только деньги поступят от клиента в разделе Управление -> Транзакции.'),
                    'default' => 0,
                    'checkboxView' => [1,0]
                ]),
        ]);
    }

    /**
     * Действия перед записью объекта
     *
     * @param string $flag - insert или update
     */
    function beforeWrite($flag)
    {
        if ($flag == self::INSERT_FLAG) {
            $this['sortn'] = OrmRequest::make()
                ->select('MAX(sortn) as max')
                ->from($this)
                ->where([
                    'site_id' => $this->__site_id->get()
                ])
                ->exec()->getOneField('max', 0) + 1;
        }
        $this['_serialized'] = serialize($this['data']);

        $null_fields = ['min_price', 'max_price'];
        foreach($null_fields as $field) {
            if ($this[$field] === '') {
                $this[$field] = null;
            }
        }

        if (!empty($this['delivery'])){
            if (in_array(0,$this['delivery'])){
               $this['delivery'] = [0];
            }
        }
        $this['_delivery']   = serialize($this['delivery']);
    }
    
    function afterObjectLoad()
    {
        $this['data']     = @unserialize($this['_serialized']);
        $this['delivery'] = @unserialize($this['_delivery']);
    }
    
    /**
    * Возвращает объект расчетного класса (типа оплаты)
    * 
    * @return AbstractPaymentType
    */
    function getTypeObject()
    {
        if ($this->cache_payment === null) 
        {
            $this->cache_payment = clone PaymentApi::getTypeByShortName($this['class']);
            $this->cache_payment->loadOptions((array)$this['data'], $this->order, $this->transaction);
            $this->cache_payment->setPayment($this);
        }
        
        return $this->cache_payment;
    }

    /**
     * Возвращает true, если тип оплаты готов отобразить документы на оплату
     *
     * @return bool
     */
    final function hasDocs()
    {
        return in_array($this->order['status'], UserStatusApi::getStatusesIdByType(UserStatus::STATUS_WAITFORPAY)) && $this->getTypeObject()->getDocsName();
    }

    /**
     * Возвращает объект компании(с реквизитами), которая поставляет услуги для данного типа оплаты
     *
     * @return Company
     */
    function getShopCompany()
    {
        return $company = $this->getTypeObject()->getCompany();
    }

    /**
     * Возвращает клонированный объект оплаты
     *
     * @return Payment
     */
    function cloneSelf()
    {
        $clone = parent::cloneSelf();

        //Клонируем фото, если нужно
        if ($clone['picture']) {
            $clone['picture'] = $clone->__picture->addFromUrl($clone->__picture->getFullPath());
        }
        return $clone;
    }
}
