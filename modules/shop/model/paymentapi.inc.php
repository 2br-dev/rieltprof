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
use RS\Helper\QrCode\QrCodeGenerator;
use RS\Module\AbstractModel\EntityList;
use Shop\Model\Exception as ShopException;
use Shop\Model\Orm\Company;
use Shop\Model\Orm\Order;
use Shop\Model\Orm\Payment;
use Shop\Model\Orm\Transaction;
use Shop\Model\PaymentType\AbstractType as AbstractPaymentType;
use Users\Model\Orm\User;

/**
 * API функции для работы со способами доставки для текущего сайта
 */
class PaymentApi extends EntityList
{
    /** @var AbstractPaymentType[] */
    protected static $types;

    function __construct()
    {
        parent::__construct(new Payment(), [
            'nameField' => 'title',
            'multisite' => true,
            'defaultOrder' => 'sortn',
            'sortField' => 'sortn',
        ]);
    }

    /**
     * Возвращает Имеющиеся в системе обработчики типов доставок.
     *
     * @return AbstractPaymentType[]
     * Исключение \Shop\Model\Exception оставлено на ручной контроль
     */
    function getTypes()
    {
        if (self::$types === null) {
            $event_result = EventManager::fire('payment.gettypes', []);
            $list = $event_result->getResult();
            self::$types = [];
            foreach ($list as $payment_type_object) {
                if (!($payment_type_object instanceof AbstractPaymentType)) {
                    throw new ShopException(t('Тип оплаты должен быть наследником \Shop\Model\PaymentType\AbstractType'));
                }
                self::$types[$payment_type_object->getShortName()] = $payment_type_object;
            }
        }

        return self::$types;
    }

    /**
     * Возвращает массив ключ => название типа доставки
     *
     * @return string[]
     * @throws ShopException
     */
    public static function getTypesAssoc()
    {
        $_this = new self();
        $result = [];
        foreach ($_this->getTypes() as $key => $object) {
            $result[$key] = $object->getTitle();
        }
        return $result;
    }

    /**
     * Возвращает объект типа доставки по идентификатору
     *
     * @param string $name
     * @return AbstractPaymentType
     * @throws ShopException
     */
    public static function getTypeByShortName($name)
    {
        $_this = new self();
        $list = $_this->getTypes();
        return isset($list[$name]) ? $list[$name] : new PaymentType\Stub($name);
    }

    /**
     * Возвращает оплаты, которые необходимо отобразить на этапе
     * оформления заказа
     *
     * @param User
     * @param Order $order
     * @return Payment[]
     * @throws RSException
     */
    public function getCheckoutPaymentList($user, $order)
    {
        $my_type = $user['is_company'] ? 'company' : 'user';

        $this->setFilter('public', 1);
        $this->setFilter('user_type', ['all', $my_type], 'in');
        $this->setFilter('target', ['all', 'orders'], 'in');
        $this->setGroup('id');

        $delivery_id = $order['delivery'];

        $cart = $order->getCart();

        if ($cart) {
            $cartdata = $cart->getCartData(false);

            //Проверим условие минимальной цены
            $this->setFilter([
                [
                    'min_price' => null,
                    '|min_price:<=' => $cartdata['total_unformatted'],
                ]
            ]);

            //Проверим условие максимальной цены
            $this->setFilter([
                [
                    'max_price' => null,
                    '|max_price:>=' => $cartdata['total_unformatted'],
                ]
            ]);
        }

        if ($order['user_type'] == Order::USER_TYPE_NOREGISTER) {
            $this->setFilter('class', 'personalaccount', '!=');
        }

        $payment_list = $this->queryObj()->objects(null, 'id');

        foreach ($payment_list as $k => $pay_item) {  //Перевод оплат
            if (is_array($pay_item['delivery']) && !empty($pay_item['delivery']) && !in_array(0, $pay_item['delivery'])) { //Если есть прявязанные доставки
                if (!in_array($delivery_id, $pay_item['delivery'])) {
                    unset($payment_list[$k]);
                }
            }
        }

        // TODO описать событие 'checkout.payment.list' в документации
        // Событие для модификации списка оплат
        $result = EventManager::fire('checkout.payment.list', [
            'list' => $payment_list,
            'order' => $order,
            'user' => $user
        ]);
        list($payment_list) = $result->extract();

        return $payment_list;
    }

    /**
     * Возвращает ассоциативный массив с ID и названиями оплат
     *
     * @param array $root - произвольный набор элементов, который будет помещен вначало
     * @return array
     */
    public static function staticSelectList($root = [])
    {
        $list = parent::staticSelectList();
        return $root + $list;
    }

    /**
     * Возвращает URL на QR код для оплаты заказа или пополнения лицевого счета
     *
     * @param Order|Transaction $order_or_transaction Объект заказа или транзакции
     * @param int $width Ширина QR-кода
     * @param int $height Высота QR-кода
     * @param bool $absolute Если true, то абсолютный URL
     * @return string|null
     */
    public static function getQrCodeUrl($order_or_transaction, $width = 200, $height = 200, $absolute = false)
    {
        if ($order_or_transaction instanceof Order) {
            $company = $order_or_transaction->getShopCompany();
            $user = $order_or_transaction->getUser();

            $purpose = self::cutStr(t('Оплата заказа №%num от %date', [
                'num' => $order_or_transaction->order_num,
                'date' => date('d.m.Y', strtotime($order_or_transaction->dateof))
            ]), 210);

            $payerAddress = self::cutStr($order_or_transaction->getAddress()->getLineView(), 210);
            $sum = $order_or_transaction->totalcost * 100;
        } elseif ($order_or_transaction instanceof Transaction) {
            $company = new Company();
            $company->getFromArray(ConfigLoader::getSiteConfig($order_or_transaction->site_id)->getValues());

            $user = $order_or_transaction->getUser();

            $purpose = self::cutStr(t('Пополнение баланса лицевого счёта №%num от %date', [
                'num' => $order_or_transaction->id,
                'date' => date('d.m.Y', strtotime($order_or_transaction->dateof))
            ]), 210);

            $payerAddress = null;
            $sum = $order_or_transaction->cost * 100;
        } else {
            return null;
        }

        $parts = [
            'Name' => self::cutStr($company->firm_name, 160),
            'PersonalAcc' => self::cutStr($company->firm_rs, 20),
            'BankName' => self::cutStr($company->firm_bank, 45),
            'BIC' => self::cutStr($company->firm_bik, 9),
            'CorrespAcc' => self::cutStr($company->firm_ks, 20),
            'Sum' => $sum,
            'Purpose' => $purpose,
            'PayeeINN' => self::cutStr($company->firm_inn, 12),
            'KPP' => self::cutStr($company->firm_kpp, 9),

            'LastName' => self::cutStr($user->surname, 50),
            'FirstName' => self::cutStr($user->name, 50),
            'PayerAddress' => $payerAddress,
            'Phone' => self::cutStr($user->phone, 20),
        ];

        $key_value = [];
        foreach ($parts as $key => $value) {
            if (trim($value) !== '') {
                $key_value[] = $key . '=' . $value;
            }
        }

        $data = 'ST00012|' . implode('|', $key_value);

        return QrCodeGenerator::buildUrl($data, [
            'w' => $width,
            'h' => $height,
            's' => 'dmtx'
        ], null, $absolute);
    }

    /**
     * Обрезает строку и подготавливает ее для размещения в QR коде
     *
     * @param string $string
     * @param integer $max_length
     * @return string
     */
    protected static function cutStr($string, $max_length = 0)
    {
        return mb_substr(str_replace('|', '', htmlspecialchars_decode($string)), 0, $max_length);
    }
}
