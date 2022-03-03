<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model;

use RS\Config\Loader as ConfigLoader;
use RS\Event\Manager as EventManager;
use RS\Exception as RSException;
use Shop\Model\CashRegisterType\AbstractProxy;
use Shop\Model\CashRegisterType\AbstractType;
use Shop\Model\CashRegisterType\Stub as CashRegisterStub;
use Shop\Model\Orm\Order;
use Shop\Model\Orm\Receipt;
use Shop\Model\Orm\Transaction;

/**
 * Апи для работы с кассами онлайн
 */
class CashRegisterApi
{
    //Список платформ ОФД
    const PLATFORM_PLATFORM_OFD = 'platformofd';
    const PLATFORM_FIRST_OFD    = '1-ofd';
    const PLATFORM_OFD_YA       = 'ofd-ya';
    const PLATFORM_SBIS         = 'sbis';
    const PLATFORM_OFD_RU       = 'ofd.ru';
    const PLATFORM_TAXCOM       = 'taxcom';
    const PLATFORM_YANDEX_OFD   = 'yandexofd';
    const PLATFORM_KONTUR_OFD   = 'kontur';

    //Список признаков способа расчёта
    const PAYMENT_METHOD_FULL_PREPAYMENT = 'full_prepayment';
    const PAYMENT_METHOD_PREPAYMENT      = 'prepayment';
    const PAYMENT_METHOD_ADVANCE         = 'advance';
    const PAYMENT_METHOD_FULL_PAYMENT    = 'full_payment';
    const PAYMENT_METHOD_PARTIAL_PAYMENT = 'partial_payment';
    const PAYMENT_METHOD_CREDIT          = 'credit';
    const PAYMENT_METHOD_CREDIT_PAYMENT  = 'credit_payment';

    const PAYMENT_SUBJECT_COMMODITY = 'commodity';
    const PAYMENT_SUBJECT_PAYMENT = 'payment';
    const PAYMENT_SUBJECT_EXCISE = 'excise';
    const PAYMENT_SUBJECT_SERVICE = 'service';
    const PAYMENT_SUBJECT_ANOTHER = 'another';

    const TAX_MODE_OSN = 'osn';
    const TAX_MODE_USN_INCOME = 'usn_income';
    const TAX_MODE_USN_INCOME_OUTCOME = 'usn_income_outcome';
    const TAX_MODE_ENVD = 'envd';
    const TAX_MODE_ESN = 'esn';
    const TAX_MODE_PATENT = 'patent';

    protected $shop_config;

    public static $types; //Массив типов онлайн касс

    /**
     * CashRegisterApi constructor.
     */
    function __construct()
    {
        $this->shop_config = ConfigLoader::byModule($this);
    }

    /**
     * Возвращает список из типов модулей интеграции с кассами онлайн
     *
     * @return array
     * @throws RSException
     */
    public static function getTypes()
    {
        if (self::$types === null) {
            $event_result = EventManager::fire('cashregister.gettypes', []);
            $list = $event_result->getResult();
            self::$types = [];
            foreach ($list as $cashregister_type_object) {
                if (!($cashregister_type_object instanceof AbstractType) && !($cashregister_type_object instanceof AbstractProxy)) {
                    throw new RSException(t('Тип интеграции с ККТ онлайн должен быть наследником \Shop\Model\CashRegisterType\AbstractType или \Shop\Model\CashRegisterType\AbstractProxy'));
                }
                self::$types[$cashregister_type_object->getShortName()] = $cashregister_type_object;
            }
        }

        return self::$types;
    }

    /**
     * Возвращает список провайдеров касс для выпадающего списка
     *
     * @return string[]
     * @throws RSException
     */
    public static function getStaticTypes()
    {
        $arr = ['' => 'Не выбрано'];
        $list = self::getTypesAssoc();
        if (!empty($list)) {
            foreach ($list as $key => $item) {
                $arr[$key] = $item;
            }
        }

        return $arr;
    }

    /**
     * Возвращает массив ключ => название типа доставки
     *
     * @return string[]
     * @throws RSException
     */
    public static function getTypesAssoc()
    {
        $_this = new self();
        $result = ['' => 'Не выбрано'];
        foreach ($_this->getTypes() as $key => $object) {
            $result[$key] = $object->getTitle();
        }
        return $result;
    }

    /**
     * Возвращает объект типа онлайн касс по идентификатору
     *
     * @param string $name - короткий идентификатор класса онлайн касс
     * @return AbstractType|AbstractProxy|CashRegisterStub
     * @throws RSException
     */
    public static function getTypeByShortName($name)
    {
        $_this = new self();
        $list = $_this->getTypes();
        return isset($list[$name]) ? $list[$name] : new CashRegisterStub($name);
    }

    /**
     * Возвращает текущий класс обмена информацией с кассами
     *
     * @return AbstractType
     * @throws RSException
     */
    function getCurrentCashRegisterClass()
    {
        return $this->getTypeByShortName($this->shop_config['cashregister_class']);
    }

    /**
     * Создаёт чек для ККТ и отправляет его на ККТ
     *
     * @param Orm\Transaction $transaction - объект транзакции
     * @param string $operation_type - тип чека
     * @return bool|string
     * @throws RSException
     */
    function createReceipt(Transaction $transaction, $operation_type = AbstractType::OPERATION_SELL)
    {
        $cash_register = $this->getCurrentCashRegisterClass();
        if ($cash_register instanceof CashRegisterStub) {
            $cash_register->addError(t('Укажите провайдера ККТ для транзакции'));
        } else {
            $cash_register->createReceipt($transaction, $operation_type);
        }

        if ($cash_register->hasError()) {
            return $cash_register->getErrorsStr();
        }
        return true;
    }

    /**
     * Делает возврат средств заказа по онлайн чеку из успешной транзакции
     *
     * @param Orm\Order $order - объект заказа
     */
    function makeOrderRefund(Order $order)
    {
        //Получим успешную транзакцию
        $transaction_api = new TransactionApi();
        /** @var Transaction $transaction */
        $transaction = $transaction_api->setFilter('order_id', $order['id'])
                                       ->setFilter('status', Orm\Transaction::STATUS_SUCCESS)
                                       ->getFirst();
        if ($transaction['id']) {
            $transaction_api->createReceipt($transaction, AbstractType::OPERATION_SELL_REFUND);
        }
    }

    /**
     * Производит запрос на получение чека по транзакции принадлежащей переданному заказу
     *
     * @param Orm\Order $order - объект заказа
     */
    function makeOrderReceipt(Order $order)
    {
        //Получим успешную транзакцию
        $transaction_api = new TransactionApi();
        /** @var Orm\Transaction $transaction */
        $transaction = $transaction_api->setFilter('order_id', $order['id'])
            ->setFilter('status', Orm\Transaction::STATUS_SUCCESS)
            ->getFirst();
        if ($transaction['id']) {
            $transaction_api->createReceipt($transaction);
        }
    }

    /**
     * Возвращает список ОФД для списка выбора
     * @return array
     */
    public static function getStaticOFDList()
    {
        return [
            self::PLATFORM_PLATFORM_OFD => t('Платформа ОФД'),
            self::PLATFORM_FIRST_OFD => t('Первый ОФД'),
            self::PLATFORM_OFD_YA => t('ОФД-Я'),
            self::PLATFORM_SBIS => t('сбис'),
            self::PLATFORM_OFD_RU => t('OFD.RU'),
            self::PLATFORM_TAXCOM => t('ТАКСКОМ'),
            self::PLATFORM_YANDEX_OFD => t('Яндекс.ОФД'),
            self::PLATFORM_KONTUR_OFD => t('Контур ОФД')
        ];
    }

    /**
     * Возвращает список признаков способа расчета
     *
     * @param array $first Первый элемент для списка
     * @return array
     */
    public static function getStaticPaymentMethods($first = [])
    {
        $result = [
            self::PAYMENT_METHOD_FULL_PREPAYMENT => t('Предоплата 100%'),
            self::PAYMENT_METHOD_PREPAYMENT => t('Частичная предоплата'),
            self::PAYMENT_METHOD_ADVANCE => t('Аванс'),
            self::PAYMENT_METHOD_FULL_PAYMENT => t('Полный расчет'),
            self::PAYMENT_METHOD_PARTIAL_PAYMENT => t('Частичный расчет и кредит'),
            self::PAYMENT_METHOD_CREDIT => t('Передача в кредит'),
            self::PAYMENT_METHOD_CREDIT_PAYMENT => t('Оплата кредита')
        ];

        return (array)$first + $result;
    }

    /**
     * Возвращает список признаков предмета расчета
     *
     * @param array $first Первый элемент для списка
     * @return array
     */
    public static function getStaticPaymentSubjects($first = [])
    {
        $result = [
            self::PAYMENT_SUBJECT_COMMODITY => t('Товар'),
            self::PAYMENT_SUBJECT_EXCISE => t('Подакцизный товар'),
            self::PAYMENT_SUBJECT_SERVICE => t('Услуга'),
            self::PAYMENT_SUBJECT_PAYMENT => t('Платеж'),
            self::PAYMENT_SUBJECT_ANOTHER => t('Другое'),
        ];

        return (array)$first + $result;
    }

    /**
     * Возвращает список возможных систем налогообложения
     *
     * @param array $first Первый элемент для списка
     * @return array
     */
    public static function getStaticSnoList($first = [])
    {
        return $first + [
                self::TAX_MODE_OSN => t('Общая СН'),
                self::TAX_MODE_USN_INCOME => t('УСН доходы'),
                self::TAX_MODE_USN_INCOME_OUTCOME => t('УСН доходы минус расходы'),
                self::TAX_MODE_ENVD => t('Единый налог на вменённый доход'),
                self::TAX_MODE_ESN => t('Единый сельскохозяйственный налог'),
                self::TAX_MODE_PATENT => t('Патентная СН')
            ];
    }

    /**
     * Возвращает ссылку для проверки своего чека
     *
     * @param string $ofd_type - тип ОФД
     * @return string
     */
    public static function getOFDReceiptUrlMask($ofd_type)
    {
        switch ($ofd_type) {
            case self::PLATFORM_PLATFORM_OFD:
                $url = "https://lk.platformaofd.ru/web/noauth/cheque?fn=%fn_number&i=%fiscal_document_number&fp=%fiscal_document_attribute";
                break;
            case self::PLATFORM_FIRST_OFD:
                $url = "https://consumer.1-ofd.ru/#/landing";
                break;
            case self::PLATFORM_OFD_YA:
                $url = "https://ofd-ya.ru/check";
                break;
            case self::PLATFORM_SBIS:
                $url = "https://ofd.sbis.ru";
                break;
            case self::PLATFORM_OFD_RU:
                $url = "https://ofd.ru/checkinfo";
                break;
            case self::PLATFORM_TAXCOM:
                $url = "http://taxcom.ru/ofd/";
                break;
            case self::PLATFORM_YANDEX_OFD:
                $url = "http://ofd.yandex.ru/";
                break;
            case self::PLATFORM_KONTUR_OFD:
                $url = "https://kontur.ru/ofd/features/check";
                break;
            default:
                $url = "";
                break;
        }

        return $url;
    }

    /**
     * Возвращает массив сведений о чеке в виде массива ключ=>значение
     *
     * @param Receipt $receipt - чек для которого нужно сделать ссылку на провайдера
     * @return array
     */
    private function getReceiptExtraInfoArray(Receipt $receipt)
    {
        $extra_info = [];
        $info = $receipt->getExtraInfo('success_info');
        foreach ($info as $key => $value) {
            $extra_info[$key] = $value;
        }

        $values = $receipt->getValues();
        foreach ($values as $key => $value) {
            if (is_string($value) || is_integer($value)) {
                $extra_info[$key] = $value;
            }
        }
        return $extra_info;
    }

    /**
     * Возвращает URL для просмотра выписаного чека
     *
     * @param Receipt $receipt - чек для которого нужно сделать ссылку на провайдера
     * @return string
     */
    function getReceiptUrl(Receipt $receipt)
    {
        $url = self::getOFDReceiptUrlMask($this->shop_config['ofd']);
        $extra_info = $this->getReceiptExtraInfoArray($receipt);

        foreach ($extra_info as $key => $value) {
            $url = str_replace("%" . $key, $value, $url);
        }

        return $url;
    }
}
