<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model\Orm;

use Alerts\Model\Manager as AlertsManager;
use Catalog\Model\CurrencyApi;
use Catalog\Model\DocumentLinkManager;
use Catalog\Model\Inventory\DocumentApi as InventoryDocumentApi;
use Catalog\Model\Orm\Currency;
use Catalog\Model\Orm\WareHouse;
use Catalog\Model\StockManager;
use Catalog\Model\WareHouseApi;
use Files\Model\FileApi;
use Files\Model\Orm\File;
use RS\Config\Loader as ConfigLoader;
use RS\Config\Loader;
use RS\Event\Manager as EventManager;
use RS\Exception as RSException;
use RS\Helper\CustomView;
use RS\Module\Manager as ModuleManager;
use RS\Orm\OrmObject;
use RS\Orm\Request as OrmRequest;
use RS\Orm\Type;
use RS\Router\Manager as RouterManager;
use Shop\Config\File as ShopConfig;
use Shop\Model\CashRegisterApi;
use Shop\Model\DeliveryApi;
use Shop\Model\DeliveryType\Helper\Pvz;
use Shop\Model\OnlinePayApi;
use Shop\Model\PaymentType\InterfaceRecurringPayments;
use Shop\Model\PrintForm\AbstractPrintForm;
use Shop\Model\ReceiptApi;
use Shop\Model\RegionApi;
use Shop\Model\SelectedAddress;
use Shop\Model\TransactionApi;
use Shop\Model\Verification\Action\TwoStepRegisterCheckout;
use Users\Config\File as UsersConfig;
use Users\Model\Orm\User;
use Users\Model\OrmType as UsersCustomType;
use Shop\Model\AddressApi;
use Shop\Model\Cart;
use Shop\Model\Notice;
use Shop\Model\OrderApi;
use Shop\Model\UserStatusApi;

/**
 * ORM Объект - заказ.
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property string $order_num Уникальный идентификатор номера заказа
 * @property integer $user_id ID покупателя
 * @property string $currency Трехсимвольный идентификатор валюты на момент оформления заказа
 * @property double $currency_ratio Курс относительно базовой валюты
 * @property string $currency_stitle Символ валюты
 * @property string $ip IP
 * @property integer $notify_user Уведомлять пользователя об изменении в заказе
 * @property integer $manager_user_id Менеджер заказа
 * @property integer $create_refund_receipt Выбить чек возврата
 * @property string $dateof Дата заказа
 * @property string $dateofupdate Дата обновления
 * @property float $totalcost Общая стоимость
 * @property float $profit Доход
 * @property float $user_delivery_cost Стоимость доставки, определенная администратором
 * @property integer $is_payed Заказ полностью оплачен?
 * @property integer $status Статус
 * @property string $admin_comments Комментарии администратора (не отображаются пользователю)
 * @property string $user_text Текст для покупателя
 * @property string $_serialized Дополнительные сведения
 * @property string $userfields Дополнительные сведения
 * @property array $extra 
 * @property string $hash 
 * @property integer $is_exported Выгружен ли заказ
 * @property string $delivery_order_id Идентификатор заказа доставки
 * @property string $delivery_shipment_id Идентификатор партии заказов доставки
 * @property string $track_number Трек-номер
 * @property integer $saved_payment_method_id Выбранный "сохранённый метод оплаты"
 * @property integer $trigger_cart_change Применить обработчики "изменений в корзине"
 * @property array $special_params Специальные параметры
 * @property string $user_type 
 * @property string $reg_fio Ф.И.О
 * @property string $reg_name Имя
 * @property string $reg_surname Фамилия
 * @property string $reg_midname Отчество
 * @property string $reg_phone Телефон
 * @property string $reg_login Логин
 * @property string $reg_e_mail E-mail
 * @property integer $reg_autologin 
 * @property string $reg_openpass Пароль
 * @property string $reg_pass2 
 * @property string $reg_company Наименование компании
 * @property string $reg_company_inn ИНН
 * @property string $login 
 * @property string $password 
 * @property string $contact_person Контактное лицо
 * @property integer $use_addr ID адреса доставки
 * @property integer $only_pickup_points Использовать только самовывоз
 * @property string $addr_country_id Страна
 * @property string $addr_country Страна
 * @property string $addr_region_id Область/Край
 * @property string $addr_region Область/край
 * @property string $addr_city_id id города
 * @property string $addr_city Город
 * @property string $addr_zipcode Индекс
 * @property string $addr_address Адрес
 * @property string $addr_street Улица
 * @property string $addr_house Дом
 * @property string $addr_block Корпус
 * @property string $addr_apartment Квартира
 * @property string $addr_entrance Подъезд
 * @property string $addr_entryphone Домофон
 * @property string $addr_floor Этаж
 * @property string $addr_subway Станция метро
 * @property array $addr_extra Дополнительные данные
 * @property array $userfields_arr Дополнительные сведения
 * @property integer $delivery Доставка
 * @property integer $courier_id Курьер
 * @property integer $warehouse Склад
 * @property integer $payment Тип оплаты
 * @property string $comments Комментарий
 * @property integer $substatus Причина отклонения заказа
 * @property string $user_fio Ф.И.О.
 * @property string $user_email E-mail
 * @property string $user_phone Телефон
 * @property string $user_login Логин
 * @property integer $user_autologin 
 * @property string $user_openpass Пароль
 * @property string $user_pass2 
 * @property integer $is_mobile_checkout Оформлен через мобильное приложение?
 * @property integer $register_user Зарегистрировать пользователя
 * @property array $regfields Дополнительные сведения
 * --\--
 */
class Order extends OrmObject
{
    const USER_TYPE_NOREGISTER = 'noregister';
    const USER_TYPE_USER = 'user';
    const USER_TYPE_COMPANY = 'company';
    const USER_TYPE_PERSON = 'person';
    const EXTRAINFOLINE_TYPE_DEFAULT = 'default';
    const EXTRAINFOLINE_TYPE_DELIVERY = 'delivery';
    const EXTRAKEYPAIR_DELIVERY_EXTRA = 'delivery_extra';
    const DOCUMENT_TYPE_ORDER = 'order';
    const ORDER_SESS_VAR = 'ORDER-ORMOBJECT';
    const SPECIAL_CHECKOUT_FORBID_VALIDATE = 'checkout_forbid_validate';
    const SPECIAL_DISABLE_CHECK_QUANTITY = 'checkout_disable_check_quantity';

    protected static $table = 'order';
        
    protected $use_generated_order_num = null; //Флаг использовать уникальный номер заказа
    protected $products_hash = null; //хэш от товаров в заказе
    protected $cache_weigth = []; // кэшированный вес
    protected $refresh_mode = false;
    protected $my_currency;

    /** @var Address $address */
    public $address;
    /** @var Cart $order_cart */
    public $order_cart;
    /** @var Cart $session_cart */
    public $session_cart;
    /** @var Order $this_before_write */
    public $this_before_write;
    
    function _init()
    {
        $properties = parent::_init()
            ->groupSet('condition', ['step' => 'init'])
            ->append([
                'site_id' => new Type\CurrentSite(),
                'order_num' => new Type\Varchar([
                    'maxLength' => '20',
                    'description' => t('Уникальный идентификатор номера заказа'),
                    'meVisible' => false,
                ]),
                'step' => new Type\MixedType([
                    'meVisible' => false,
                ]),
                'user_id' => new Type\User([
                    'allowEmpty' => false,
                    'maxLength' => '11',
                    'attr' => [[
                        'data-autocomplete-body' => '1'
                    ]],
                    'description' => t('ID покупателя'),
                    'meVisible' => false,
                ]),
                'basket' => new Type\MixedType([
                    'description' => t('Объект - корзина'),
                    'meVisible' => false,
                ]),
                'currency' => new Type\Varchar([
                    'maxLength' => '5',
                    'description' => t('Трехсимвольный идентификатор валюты на момент оформления заказа'),
                    'meVisible' => false,
                ]),
                'currency_ratio' => new Type\Real([
                    'description' => t('Курс относительно базовой валюты'),
                    'meVisible' => false,
                ]),
                'currency_stitle' => new Type\Varchar([
                    'description' => t('Символ валюты'),
                    'maxLength' => 10,
                    'meVisible' => false,
                ]),
                'ip' => new Type\Varchar([
                    'maxLength' => '15',
                    'description' => t('IP'),
                    'meVisible' => false,
                ]),
                'notify_user' => new Type\Integer([
                    'runtime' => true,
                    'description' => t('Уведомлять пользователя об изменении в заказе'),
                    'appVisible' => false,
                    'meVisible' => true,
                    'visible' => false,
                    'checkboxView' => [1,0],
                ]),
                'manager_user_id' => new Type\Integer([
                    'index' => true,
                    'description' => t('Менеджер заказа'),
                    'hint' => t('Заказу не назначен менеджер'),
                    'list' => [['\Shop\Model\OrderApi', 'getUsersManagersName'], [0 => t('Не задан')]],
                    'allowEmpty' => false,
                    'meVisible' => false,
                ]),
                //Поля, которые отображаются только в определенном статусе заказа
                'create_refund_receipt' => new Type\Integer([
                    'description' => t('Выбить чек возврата'),
                    'hint' => t('Будет отправлен запрос на выписку чека возврата провайдеру ККТ'),
                    'visible' => false,
                    'runtime' => false,
                    'dependVisible' => true,
                    'attr' => [[
                        //Через запятую указываем с каким статусом связано поле
                        'data-depend-status' => implode(',', [UserStatus::STATUS_CANCELLED])
                    ]],
                    'checkboxview' => [1, 0],
                    'meVisible' => false,
                ]),
        
            //Подтверждение
                'dateof' => new Type\Datetime([
                    'description' => t('Дата заказа'),
                    'meVisible' => false,
                ]),
                'dateofupdate' => new Type\Datetime([
                    'description' => t('Дата обновления'),
                    'meVisible' => false,
                ]),
                'totalcost' => new Type\Decimal([
                    'allowEmpty' => false,
                    'maxLength' => '15',
                    'decimal' => 2,
                    'description' => t('Общая стоимость'),
                    'meVisible' => false,
                ]),
                'profit' => new Type\Decimal([
                    'maxLength' => '15',
                    'decimal' => 2,
                    'description' => t('Доход'),
                    'meVisible' => false,
                ]),
                'user_delivery_cost' => new Type\Decimal([
                    'maxLength' => '15',
                    'decimal' => 2,
                    'description' => t('Стоимость доставки, определенная администратором'),
                    'meVisible' => false,
                ]),
                'is_payed' => new Type\Integer([
                    'maxLength' => '1',
                    'description' => t('Заказ полностью оплачен?'),
                    'CheckBoxView' => [1,0],
                    'meVisible' => false,
                ]),
                'status' => new Type\Integer([
                    'maxLength' => '11',
                    'description' => t('Статус'),
                    'index' => true,
                    'meVisible' =>true,
                    'tree' => [['\Shop\Model\UserStatusApi',"staticTreeList"]],
                ]),
                'admin_comments' => new Type\Text([
                    'description' => t('Комментарии администратора (не отображаются пользователю)'),
                    'Attr' => [['class' => 'fullwide']],
                    'meVisible' => false,
                ]),
                'user_text' => new Type\Richtext([
                    'description' => t('Текст для покупателя'),
                    'Attr' => [['class' => 'fullwide']],
                    'meVisible' => false,
                ]),
                '_serialized' => new Type\Text([
                    'description' => t('Дополнительные сведения'),
                    'appVisible' => false,
                    'meVisible' => false,
                ]),
                'userfields' => new Type\Text([
                    'description' => t('Дополнительные сведения'),
                    'condition' => ['step' => 'address'],
                    'visible' => false,
                    'meVisible' => false,
                ]),
                'extra' => new Type\ArrayList([
                    'visible' => false,
                    'meVisible' => false,
                ]),
                'hash' => new Type\Varchar([
                    'maxLength' => '32',
                    'meVisible' => false,
                ]),
                'expired' => new Type\MixedType(),
                'is_exported' => new Type\Integer([
                    'maxLength' => '1',
                    'default' => 0,
                    'description' => t('Выгружен ли заказ'),
                    'visible' => false,
                    'meVisible' => false,
                ]),
                'delivery_order_id' => new Type\Varchar([
                    'description' => t('Идентификатор заказа доставки'),
                    'visible' => false,
                    'meVisible' => false,
                ]),
                'delivery_shipment_id' => new Type\Varchar([
                    'description' => t('Идентификатор партии заказов доставки'),
                    'visible' => false,
                    'meVisible' => false,
                ]),
                'track_number' => new Type\Varchar([
                    'maxLength' => 30,
                    'description' => t('Трек-номер'),
                    'deliveryVisible' => true,
                    'meVisible' => false,
                ]),
                'saved_payment_method_id' => (new Type\Integer())
                    ->setDescription(t('Выбранный "сохранённый метод оплаты"'))
                    ->setMeVisible(false),
                'trigger_cart_change' => (new Type\Integer())
                    ->setDescription(t('Применить обработчики "изменений в корзине"'))
                    ->setCheckboxView(1, 0)
                    ->setDefault(0)
                    ->setVisible(true)
                    ->setRuntime(true)
                    ->setMeVisible(false),
                'special_params' => (new Type\ArrayList())
                    ->setDescription(t('Специальные параметры'))
                    ->setVisible(false),
            ])->cancelGroupSet();
        
        $this->addIndex(['site_id', 'order_num'],self::INDEX_UNIQUE);
        
        //поля для регистрации пользователя
        $chk_condition = [
            'user_type' => ['person', 'company'],
            'step' => 'address'
        ];
        
        $properties
            ->append([
                'user_type' => new Type\Varchar([
                    'maxLength' => '30',
                    'condition' => ['step' => 'address'],
                    'runtime' => true,
                    'appVisible' => false,
                    'meVisible' => false,
                ])
            ])
            ->groupSet('condition', $chk_condition)
            ->groupSet('runtime', true)
            ->groupSet('appVisible', false)
            ->append([
                'reg_fio' => new Type\Varchar([
                    'description' => t('Ф.И.О'),
                    'meVisible' => false,
                ]),
                'reg_name' => new Type\Varchar([
                    'description' => t('Имя'),
                    'maxLength' => '200',
                    'meVisible' => false,
                ]),
                'reg_surname' => new Type\Varchar([
                    'description' => t('Фамилия'),
                    'maxLength' => '200',
                    'meVisible' => false,
                ]),
                'reg_midname' => new Type\Varchar([
                    'description' => t('Отчество'),
                    'maxLength' => '200',
                    'meVisible' => false,
                ]),
                'reg_phone' => new UsersCustomType\VerifiedPhone([
                    'description' => t('Телефон'),
                    'maxLength' => '100',
                    'meVisible' => false,
                    'verificationAction' => new TwoStepRegisterCheckout(),
                    'enableVerification' => false
                ]),
                'reg_login' => new Type\Varchar([
                    'description' => t('Логин'),
                    'maxLength' => '64',
                    'meVisible' => false,
                    'trimString' => true,
                ]),
                'reg_e_mail' => new Type\Varchar([
                    'description' => t('E-mail'),
                    'maxLength' => '100',
                    'meVisible' => false,
                    'trimString' => true,
                ]),
                'reg_autologin' => new Type\Integer([
                    'maxLength' => '1',
                    'CheckBoxView' => [1, 0],
                    'default' => 1,
                    'meVisible' => false,
                ]),
                'reg_openpass' => new Type\Varchar([
                    'description' => t('Пароль'),
                    'maxLength' => '70',
                    'attr' => [['type' => 'password']],
                    'meVisible' => false,
                ]),
                'reg_pass2' => new Type\Varchar([
                    'maxLength' => '70',
                    'attr' => [['type' => 'password']],
                    'meVisible' => false,
                ]),
            ]);
            
            
        // поля для регистрации предприятия
        $chk_condition = [
            'user_type' => 'company',
            'step' => 'address'
        ];
        $properties
            ->groupSet('condition', $chk_condition)
            ->append([
                'reg_company' => new Type\Varchar([
                    'description' => t('Наименование компании'),
                    'maxLength' => '255',
                    'meVisible' => false,
                ]),
                'reg_company_inn' => new Type\Varchar([
                    'description' => t('ИНН'),
                    'maxLength' => '50',
                    'meVisible' => false,
                ]),
            ]);
            
        
        // поля для авторизации пользователя
        $chk_condition = [
            'user_type' => 'user',
            'step' => 'address'
        ];
        $properties
            ->groupSet('condition', $chk_condition)            
            ->append([
                'login' => new Type\Varchar([
                    'maxLength' => '100',
                    'meVisible' => false,
                ]),
                'password' => new Type\Varchar([
                    'maxLength' => '100',
                    'Attr' => [['type' => 'password']],
                    'meVisible' => false,
                ])
            ])
            ->cancelGroupSet();
        
        // Поля для адреса доставки
        $properties
            ->append([
                'contact_person' => new Type\Varchar([
                    'maxLength' => '255',
                    'description' => t('Контактное лицо'),
                    'condition' => ['step' => 'address'],
                    'meVisible' => false,
                ]),
                'use_addr' => new Type\Integer([
                    'maxLength' => '11',
                    'description' => t('ID адреса доставки'),
                    'condition' => ['step' => 'address'],
                    'meVisible' => false,
                ]),
                'only_pickup_points' => new Type\Integer([
                    'maxLength' => '1',
                    'description' => t('Использовать только самовывоз'),
                    'runtime' => true,
                    'meVisible' => false,
                ])
            ]);

        $chk_condition = [
            'step' => 'address',
        ];
        
        $properties
            ->groupSet('condition', $chk_condition)
            ->groupSet('runtime', true)
            ->groupSet('appVisible', false)
            ->groupSet('meVisible', false)
            ->append([
                'addr_country_id' => new Type\Varchar([
                    'maxLength' => '100',
                    'description' => t('Страна'),
                    'List' => [['\Shop\Model\RegionApi', 'countryList']],
                    'Attr' => [['size' => 1]],
                    'Checker' => [function ($object, $value) {
                        return $object->checkAddressField('addr_country_id', $object, $value);
                    }],
                ]),
                'addr_country' => new Type\Varchar([
                    'maxLength' => '100',
                    'description' => t('Страна'),
                    'Checker' => [function ($object, $value) {
                        return $object->checkAddressField('addr_country', $object, $value);
                    }],
                ]),
                'addr_region_id' => new Type\Varchar([
                    'maxLength' => '100',
                    'description' => t('Область/Край'),
                    'List' => [[__CLASS__, 'regionList']],
                    'Attr' => [['size' => 1]],
                    'Checker' => [function ($object, $value) {
                        return $object->checkAddressField('addr_region_id', $object, $value);
                    }],
                ]),
                'addr_region' => new Type\Varchar([
                    'maxLength' => '100',
                    'description' => t('Область/край'),
                    'Checker' => [function ($object, $value) {
                        return $object->checkAddressField('addr_region', $object, $value);
                    }],
                ]),
                'addr_city_id' => new Type\Varchar([
                    'description' => t('id города'),
                    'maxLength' => '100',
                    'Checker' => [function ($object, $value) {
                        return $object->checkAddressField('addr_city_id', $object, $value);
                    }],
                ]),
                'addr_city' => new Type\Varchar([
                    'maxLength' => '100',
                    'description' => t('Город'),
                    'Checker' => [function ($object, $value) {
                        return $object->checkAddressField('addr_city', $object, $value);
                    }],
                ]),
                'addr_zipcode' => new Type\Varchar([
                    'maxLength' => '20',
                    'description' => t('Индекс'),
                    'Checker' => [function ($object, $value) {
                        return $object->checkAddressField('addr_zipcode', $object, $value);
                    }],
                ]),
                'addr_address' => new Type\Varchar([
                    'maxLength' => '255',
                    'description' => t('Адрес'),
                    'Checker' => [function ($object, $value) {
                        return $object->checkAddressField('addr_address', $object, $value);
                    }],
                ]),
                'addr_street' => new Type\Varchar([
                    'maxLength' => '100',
                    'description' => t('Улица'),
                    'Checker' => [function ($object, $value) {
                        return $object->checkAddressField('addr_street', $object, $value);
                    }],
                ]),
                'addr_house' => new Type\Varchar([
                    'maxLength' => 20,
                    'description' => t('Дом'),
                    'Checker' => [function ($object, $value) {
                        return $object->checkAddressField('addr_house', $object, $value);
                    }],
                ]),
                'addr_block' => new Type\Varchar([
                    'maxLength' => 20,
                    'description' => t('Корпус'),
                    'Checker' => [function ($object, $value) {
                        return $object->checkAddressField('addr_block', $object, $value);
                    }],
                ]),
                'addr_apartment' => new Type\Varchar([
                    'maxLength' => 20,
                    'description' => t('Квартира'),
                    'Checker' => [function ($object, $value) {
                        return $object->checkAddressField('addr_apartment', $object, $value);
                    }],
                ]),
                'addr_entrance' => new Type\Varchar([
                    'maxLength' => 20,
                    'description' => t('Подъезд'),
                    'Checker' => [function ($object, $value) {
                        return $object->checkAddressField('addr_entrance', $object, $value);
                    }],
                ]),
                'addr_entryphone' => new Type\Varchar([
                    'maxLength' => 20,
                    'description' => t('Домофон'),
                    'Checker' => [function ($object, $value) {
                        return $object->checkAddressField('addr_entryphone', $object, $value);
                    }],
                ]),
                'addr_floor' => new Type\Varchar([
                    'maxLength' => 20,
                    'description' => t('Этаж'),
                    'Checker' => [function ($object, $value) {
                        return $object->checkAddressField('addr_floor', $object, $value);
                    }],
                ]),
                'addr_subway' => new Type\Varchar([
                    'maxLength' => 20,
                    'description' => t('Станция метро'),
                    'Checker' => [function ($object, $value) {
                        return $object->checkAddressField('addr_subway', $object, $value);
                    }],
                ]),
                'addr_extra' => new Type\ArrayList([
                    'description' => t('Дополнительные данные'),
                ]),
            ])->cancelGroupSet();

        $properties->append([
            'userfields_arr' => new Type\ArrayList([
                'description' => t('Дополнительные сведения'),
                'condition' => ['step' => 'address'],
                'meVisible' => false,
            ]),
            'code' => new Type\Captcha([
                'enable' => false,
                'condition' => ['step' => 'address'],
                'meVisible' => false,
            ]),
            
        //Шаг 2
            'delivery' => new Type\Integer([
                'maxLength' => '11',
                'description' => t('Доставка'),
                'condition' => ['step' => 'delivery'],
                'Checker' => [function($orm, $value, $error) {
                    /** @var Order $orm */
                    if (!ConfigLoader::byModule('shop')['hide_delivery']) {
                        if (!$value) {
                            return $error;
                        }
                        $list = DeliveryApi::getInstance()->clearFilter()->getCheckoutDeliveryList($orm->getUser(), $orm);
                        if (!isset($list[$value])) {
                            return t('Указан недопустимый тип доставки');
                        }
                    }
                    return true;
                }, t('Укажите тип доставки')],
                'List' => [['\Shop\Model\DeliveryApi', 'staticSelectList']],
                'meVisible' => false,
            ]),
            // todo поле deliverycost закомментировано по подозрению в неиспользуемости (20.10), если ошибока не появится - удалить
            /*'deliverycost' => new Type\Decimal(array(
                'maxLength' => '15',
                'decimal' => 2,
                'description' => t('Стоимость доставки'),
                'condition' => array('step' => 'delivery'),
                'meVisible' => false,
            )),*/
            'courier_id' => new Type\Integer([
                'description' => t('Курьер'),
                'allowEmpty' => false,
                'default' => 0,
                'list' => [['\Shop\Model\DeliveryApi', 'getCourierList']],
                'meVisible' => false,
            ]),
            
        //Шаг 2.2 - только при самовывозе
            'warehouse' => new Type\Integer([
                'maxLength' => '11',
                'description' => t('Склад'),
                'condition' => ['step' => 'warehouses'],
                'Checker' => ['chkEmpty', t('Укажите склад для забора товара')],
                'List' => [['\Catalog\Model\WarehouseApi', 'staticSelectList']],
                'meVisible' => false,
            ]),
            
        //Шаг 3
            'payment' => new Type\Integer([
                'description' => t('Тип оплаты'),
                'condition' => ['step' => 'pay'],
                'Checker' => [function($orm, $value, $error) {
                    /** @var Order $orm */
                    if (!ConfigLoader::byModule('shop')['hide_payment']) {
                        if (!$value) {
                            return $error;
                        }
                    }
                    return true;
                }, t('Укажите тип оплаты')],
                'List' => [['\Shop\Model\PaymentApi', 'staticSelectList']],
                'meVisible' => false,
            ]),
            
        //Шаг 4
            'comments' => new Type\Text([
                'description' => t('Комментарий'),
                'condition' => ['step' => 'confirm'],
                'meVisible' => false,
            ]),

            //Поля, которые отображаются только в определенном статусе заказа
            'substatus' => new Type\Integer([
                'description' => t('Причина отклонения заказа'),
                'hint' => t('Настраивается в настройках модуля Магазин'),
                'list' => [['Shop\Model\SubStatusApi', 'staticSelectList'], [0 => t('Не выбрано')]],
                'visible' => false,
                'dependVisible' => true,
                'attr' => [[
                    //Через запятую указываем с каким статусом связано поле
                    'data-depend-status' => implode(',', [UserStatus::STATUS_CANCELLED])
                ]]
            ])
        ]);
        
        //Поля для заказа без авторизации или регистрации
        $properties->append([
            'user_fio' => new Type\Varchar([
                'description' => t('Ф.И.О.'),
                'Attr' => [[
                    'size' => 40,
                ]],
                'maxLength' => 255,
                'meVisible' => false,
            ]),
            'user_email' => new Type\Varchar([
                'description' => t('E-mail'),
                'Attr' => [[
                    'size' => 40,
                ]],
                'maxLength' => 255,
                'meVisible' => false,
            ]),
            'user_phone' => new UsersCustomType\VerifiedPhone([
                'description' => t('Телефон'),
                'maxLength' => '100',
                'meVisible' => false,
                'verificationAction' => new TwoStepRegisterCheckout(),
                'enableVerification' => false,
            ]),
            'user_login' => new Type\Varchar([
                'description' => t('Логин'),
                'maxLength' => '64',
                'meVisible' => false,
                'trimString' => true,
                'runtime' => true,
            ]),
            'user_autologin' => new Type\Integer([
                'maxLength' => '1',
                'CheckBoxView' => [1, 0],
                'default' => 1,
                'meVisible' => false,
                'runtime' => true,
            ]),
            'user_openpass' => new Type\Varchar([
                'description' => t('Пароль'),
                'maxLength' => '70',
                'attr' => [['type' => 'password']],
                'meVisible' => false,
                'runtime' => true,
            ]),
            'user_pass2' => new Type\Varchar([
                'maxLength' => '70',
                'attr' => [['type' => 'password']],
                'meVisible' => false,
                'runtime' => true,
            ]),
            'is_mobile_checkout' => new Type\Integer([
                'maxLength' => 1,
                'description' => t('Оформлен через мобильное приложение?'),
                'checkboxView' => [1,0],
                'listenPost' => false,
                'meVisible' => false,
            ]),
            'register_user' => (new Type\Integer())
                ->setDescription(t('Зарегистрировать пользователя'))
                ->setRuntime(true)
                ->setVisible(false)
                ->setMeVisible(false),
            'regfields' => (new Type\ArrayList())
                ->setDescription(t('Дополнительные сведения'))
                ->setMeVisible(false),
        ]);
    }

    /**
     * Проверяет указанное поле адреса
     *
     * @param string $field_name - имя поверяемого поля
     * @param Order $order - объект заказа
     * @param mixed $value - значение
     * @return bool|string
     */
    protected function checkAddressField(string $field_name, Order $order, $value)
    {
        if ($order['use_addr'] == 0) {
            if ($order['delivery']) {
                $required_fields = $order->getDelivery()->getTypeObject()->getRequiredAddressFields();
            } else {
                /** @var ShopConfig $config */
                $config = ConfigLoader::byModule('shop');
                $required_fields = $config->getRequiredAddressFields();
            }

            $required_field_name = str_replace('addr_', '', $field_name);
            if (in_array($required_field_name, $required_fields) && empty($value)) {
                if (!in_array($required_field_name, ['country', 'region', 'city']) || empty($order["{$field_name}_id"])) {
                    return $order["__$field_name"]->getDescription() . ' - ' . t('обязательное поле');
                }
            }
        }
        return true;
    }

    /**
     * Фукнция срабатывает после записи объекта в БД
     *
     * @return string
     * @throws RSException
     */
    function getProductsHash()
    {
        if (!$this['id']) {
            $products = $this->getCart()->getProductItems();
            $arr = [];
            foreach($products as $uniq=>$item){
               $product  = $item['product'];
               $cartitem = $item['cartitem'];
               $arr[]    = $product['title']."_".$product['id']."_".$cartitem['offer']."_".$cartitem['amount'];
            } 
            sort($arr);
            return md5(serialize($arr));
        }else{
            $cart_data=$this->getCart()->getPriceItemsData();
            $cart_data['checkcount'] = count($cart_data['items']);
            return md5(serialize($cart_data));
        }
    }


    /**
     * Функция срабатывает перед записью заказа
     *
     * @param string $flag - insert или update
     * @return null|bool
     * @throws RSException
     */
    function beforeWrite($flag)
    {
        $this->this_before_write = new Order($this['id'], false);

        if ($this['delivery']) {
            $this->getDelivery()->getTypeObject()->beforeOrderWrite($this);
        }

        $cart = $this->getCart();
        if ($this->this_before_write->getCart())
            $this->this_before_write['old_items'] = $this->this_before_write->getCart()->getProductItems();

        if ($this['id'] < 0) {
            $this['_tmpid'] = $this['id'];
            unset($this['id']);
        }

        $config = ConfigLoader::byModule($this);

        //Проверка уникального идентификатора номера заказа
        if (empty($this['order_num']) && $config['use_generated_order_num']) {
            $api = new OrderApi();
            $this['order_num'] = $api->generateOrderNum($this);
        }

        if ($flag == self::INSERT_FLAG) {
            //Проверяем наличие необходимого количества товаров.
            if ($config['check_quantity'] && !$this->getSpecialParam(self::SPECIAL_DISABLE_CHECK_QUANTITY)) {
                $pnum_check = $this->checkProductsNum();
                if ($pnum_check !== true) {
                    return $this->addError($pnum_check);
                }
            }

            if (!$this['status']) {
                $this['status'] = $this->getStartStatus();
            }

            $this['ip']     = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
            $this['dateof'] = date('Y-m-d H:i:s');
            $this['hash']   = md5(uniqid(mt_rand(), true));
            if(!$this->isModified('is_exported')){
                $this['is_exported'] = 0;
            }

            //Создаем корзину к заказу
            if ($this['basket']) {
                //Подготавливаем корзину к переносу в заказ
                if ($cart && ($error = $cart->makeOrderCart()) !== true) {
                    return $this->addError($error);
                }
            }
            
            //Если есть массив дополнительных данных, то сохраним их
            if (!empty($this['order_extra'])){
                foreach ($this['order_extra'] as $order_key_step=>$order_step){
                    $m=0;
                    foreach ($order_step as $extra_title=>$extra_value){
                        $extra_uniq_key = $order_key_step."_".$m;
                        $this->addExtraInfoLine($extra_title, $extra_value, null, $extra_uniq_key); 
                        $m++;
                    }
                }
            }
            
            // Установим персонального менеджера пользователя или случайного менеджера (если такая опция включена)
            if (!$this['manager_user_id']) {
                $user = $this->getUser();
                if ($user['manager_user_id']) {
                    $this['manager_user_id'] = $user['manager_user_id'];
                } elseif ($config['set_random_manager']) {
                    $managers_ids = array_keys(OrderApi::getUsersManagers());
                    if ($managers_ids) {
                        $this['manager_user_id'] = $managers_ids[rand(0, count($managers_ids) - 1)];
                    }
                }
            }
        }
        
        if ($flag == self::UPDATE_FLAG) { //При обновлении заказа 
            //Подгрузим данные для дальнейшей сверки с новыми
            //Прежняя корзина
            $this->this_before_write['cart_md5'] = $this->this_before_write->getProductsHash();
        }

        if ($cart && $flag == self::INSERT_FLAG) {
            $this['profit'] = $cart->getOrderProfit();
        }
        
        $this['dateofupdate'] = date('c');
        $this['_serialized'] = serialize($this['extra']);
        $this['userfields']  = serialize($this['userfields_arr']);

        return null;
    }

    function afterWrite($flag)
    {
        $config = ConfigLoader::byModule($this);

        if ($flag == self::INSERT_FLAG) { //При вставке
            if ($this->session_cart) {
                $this->session_cart->saveOrderData(); //Создаем корзину к заказу
            }

            if ($this->session_cart) {
                //Отмечаем, что скидочный купон использован, если он был привязан к корзине
                $coupons = $this->session_cart->getCouponItems();
                foreach ($coupons as $coupon) {
                    /** @var Discount $coupon_obj */
                    $coupon_obj = $coupon['coupon'];
                    $coupon_obj->incrementUse();
                }
            }

            if (!$config['use_generated_order_num']) {
                //Устнавливаем номер заказа, равный ID заказа, в случае, если выключена опция генерации
                OrmRequest::make()
                    ->update($this)
                    ->set('order_num = id')
                    ->where(['id' => $this['id']])
                    ->exec();
                $this['order_num'] = $this['id'];
            }
        }

        if ($flag == self::UPDATE_FLAG) {

            if ($this['notify_user'] && $this->canUserNotify()) {
                //Отправляем уведомление пользователю об изменении заказа
                $notice = new Notice\OrderChange();
                $notice->init($this);
                AlertsManager::send($notice);
            }

            //Посмотрим, если появился трекномер, то отправим его по SMS
            if (empty($this->this_before_write['track_number']) && !empty($this['track_number']) ||
                (!empty($this['track_number']) && ($this->this_before_write['track_number'] != $this['track_number']))) {
                $notice = new Notice\TrackNumberToUser();
                $notice->init($this);
                AlertsManager::send($notice);
            }
        }

        //Выполняем действия с доставками и оплатами, если у этого типа доставок и оплат поддерживаются такие действия и включён флаг на разрешение
        if ($this['delivery_new_query']) { //Если доставке нужно делать запрос при создании или редактировании заказа
            $delivery_type = $this->getDelivery()->getTypeObject();
            $delivery_type->onOrderCreate($this, $this->getAddress());
        }

        $payment = $this->getPayment();

        if ($this['payment_new_query']) { //Если оплате нужно делать запрос при создании или редактировании заказа
            $payment_type = $payment->getTypeObject();
            $payment_type->onOrderCreate($this, $this->getAddress());
        }

        //Отправим уведомления
        if ($flag == self::INSERT_FLAG) { //При вставке
            if (empty($this['disable_checkout_notice'])) { //Если не стоит запрет на отправку уведомлений

                //Отправляем уведомление покупателю
                $notice = new Notice\CheckoutUser();
                $notice->init($this);
                AlertsManager::send($notice);

                //Отправляем уведомление администратору
                $notice = new Notice\CheckoutAdmin();
                $notice->init($this);
                AlertsManager::send($notice);
            }
        }

        //Создаем  или обновляем транзакцию на оплату, если необходимо
        if ($payment['id'] && !$payment->getTypeObject()->canOnlinePay() && $payment['create_order_transaction']) {

            $transactionApi = new TransactionApi();

            if ($flag == self::INSERT_FLAG || !$transactionApi->isExistsTransactionForOrder($this['id'])) {
                $transactionApi->createTransactionFromOrder($this['order_num']);
            } else {
                $transactionApi->updateTransactionFromOrder($this);
            }
        }

        //Посмотрим, есть ли адреса заказов, которые были не присвоины и новому идентификатору
        if ($this['_tmpid'] < 0) {
            $address_api = new AddressApi();
            $address_api->setFilter('order_id', $this['_tmpid']);
            $address_list = $address_api->getList();

            if ($address_list) {
                foreach ($address_list as $address) {
                    $address['order_id'] = $this['id'];
                    $address->update();
                }
            }
        }

        if ($this['courier_id'] && $this->this_before_write['courier_id'] != $this['courier_id']) {
            //Отправляем уведомление о назначении заказа курьеру
            $notice = new Notice\AssignOrderToCourier();
            $notice->init($this);
            AlertsManager::send($notice);
        }

        $cancelled = UserStatusApi::getStatusesIdByType(UserStatus::STATUS_CANCELLED);

        //Если нужно сделать возврат средств за заказы
        if ($config['cashregister_class'] && $this['create_refund_receipt'] && in_array($this['status'], $cancelled)) {
            //Проверим был ли воврат ранее
            $transaction = ReceiptApi::getTransactionForRefundReceiptByOrderId($this['id']);

            if (!$transaction['id']) { //Если такой успешной транзакции нет то сделать возврат
                //Если заказ уже оплачен
                $cashregister_api = new CashRegisterApi();
                $cashregister_api->makeOrderRefund($this);
            }
        }
        $catalog_config = ConfigLoader::byModule('catalog');

        if ($catalog_config['inventory_control_enable'] || $config['check_quantity']) {
            $stock_manager = StockManager::getInstance();
            $stock_manager->updateRemainsFromOrder($this, $flag, $this['back_warehouse']);
        }
        EventManager::fire('order.change', ['order_before' => $this->this_before_write, 'order' => $this]);
    }

    /**
     * Возвращает склад на котором должно происходить изменение остатков
     *
     * @return WareHouse
     */
    public function getStockWarehouse()
    {
        if ($this['warehouse']) {
            $warehouse = new WareHouse($this['warehouse']);
            if (!$warehouse['dont_change_stocks']) {
                return $warehouse;
            }
        }

        // Если включена опция "Ограничить остатки товара только остатками на складах выбранного филиала", то вернет склад филиала
        return ($this->getStockAffiliateWarehouse()) ?:  WareHouseApi::getDefaultWareHouse();
    }

    /**
     * Возвращает склад филиала на котором должно происходить изменение остатков
     *
     * @return WareHouse|bool
     */
    public function getStockAffiliateWarehouse()
    {
        $config = ConfigLoader::byModule('catalog');

        if (ModuleManager::staticModuleExists('affiliate') && $config['affiliate_stock_restriction']) {
            $warehouse_ids = WareHouseApi::getAvailableWarehouses(false,false,true);
            return reset($warehouse_ids);
        }
        return false;
    }

    /**
     * Удаляет объект из хранилища
     * @return boolean - true, в случае успеха
     */
    function delete()
    {
        $result = parent::delete();
        if ($result) {
            $link_manager = new DocumentLinkManager($this['id'], self::DOCUMENT_TYPE_ORDER);
            $link_manager->deleteLinkedDocuments();

            //Удалим позиции в заказе
            OrmRequest::make()->delete()
                ->from(new OrderItem())
                ->where(['order_id' => $this['id']])
                ->exec();
        }

        return $result;
    }
    
    /**
    * Функция срабатывает после загрузки объекта
    * 
    */
    function afterObjectLoad()
    {
        $this['extra'] = @unserialize($this['_serialized']);
        $this['userfields_arr'] = @unserialize($this['userfields']);
    }

    /**
     * Возвращает true, если в заказе произошли изменения, о которых следует сообщить пользователю
     *
     * @return bool
     * @throws RSException
     */
    function canUserNotify()
    {
        $changed = false;
        if ($this->this_before_write !== null) {
            $old_order = $this->this_before_write;
            $changed =  (float)$this['totalcost'] != (float)$old_order['totalcost']  //Проверяем сумму заказа
                        || ($old_order['status'] != $this['status'])                 //Проверяем статус
                        || ($old_order['cart_md5'] != $this->getProductsHash()) //Проверяем состав товаров
                        || (($this['before_address'] !== null) && ($this->before_address->getLineView() != $this->getAddress()->getLineView())) //Проверяем адрес доставки
                        || ($old_order['delivery'] != $this['delivery'])             //Проверяем способ доставки
                        || ($old_order['contact_person'] != $this['contact_person']) //Проверяем контактное лицо
                        || ($old_order['warehouse'] != $this['warehouse'])           //Проверяем склад
                        || ($old_order['payment'] != $this['payment'])               //Проверяем способ оплаты
                        || ($old_order['user_text'] != $this['user_text'])           //Проверяем текст для пользователя
                        || ($old_order['is_payed'] != $this['is_payed']);            //Проверяем флаг оплаты
        }
        
        return $changed;
    }
    
    /**
    * Привязывает корзину к заказу
    * 
    * @param Cart $cart - загруженный объект корзины в режиме PREORDER или EMPTY
    * @return Order
    */
    function linkSessionCart(Cart $cart)
    {
        $this['basket'] = serialize($cart);
        $this->session_cart = $cart;
        return $this;
    }
    
    /**
    * Сохраняет параметры валюты, в которой оформляется заказ
    * 
    * @param Currency $currency
    * @return Order
    */
    function setCurrency(Currency $currency)
    {
        if ($currency['ratio'] > 0) {
            $calculated_ratio = CurrencyApi::getBaseCurrency()->ratio / $currency['ratio'];
        } else {
            $calculated_ratio = 1;
        }
        
        $this['currency'] = $currency['title'];
        $this['currency_ratio'] = $calculated_ratio;
        $this['currency_stitle'] = $currency['stitle'];        
        return $this;
    }

    /**
     * Проверяет поля данных незарегистрированного пользователя
     *
     * @return void
     */
    public function checkUnregisteredUserFields(): void
    {
        $shop_config = ConfigLoader::byModule('shop');
        /** @var UsersConfig $users_config */
        $users_config = ConfigLoader::byModule('users');

        if (empty($this['user_fio'])) {
            $this->addError(t('Укажите, пожалуйста, Ф.И.О.'), 'user_fio');
        }
        if ($shop_config['require_email_in_noregister'] && !filter_var($this['user_email'], FILTER_VALIDATE_EMAIL)) {
            $this->addError(t('Укажите, пожалуйста, E-mail'), 'user_email');
        }

        if ($shop_config['require_phone_in_noregister']) {
            if (empty($this['user_phone'])) {
                $this->addError(t('Укажите, пожалуйста, Телефон'), 'user_phone');
            } elseif (!preg_match('/^[0-9()\-\s+,]+$/', $this['user_phone'])) {
                $this->addError(t('Неверно указан телефон'), 'user_phone');
            }
        }

        $payment_type = $this->getPayment()->getTypeObject();
        if ($payment_type instanceof InterfaceRecurringPayments && $payment_type->getRecurringPaymentsType() == InterfaceRecurringPayments::RECURRING_TYPE_ONLY_SAVE_METHOD) {
            $authorization_url = $users_config->getAuthorizationUrl();
            $error_text = t('Указанный способ оплаты доступен только зарегестрированным пользователям') . ", <a href=\"$authorization_url\" class=\"rs-in-dialog\">" . t('авторизоваться') . '</a>';
            $this->addError($error_text, 'register_user');
        }
    }

    /**
     * Проверяет наличие всех товаров в корзине
     *
     * @return bool|string
     * @throws RSException
     */
    function checkProductsNum()
    {
        $num_error = false;
        if (!$this['basket']) {
            return true;
        }
        $bitems = $this->getCart()->getProductItems(false);

        foreach ($bitems as $n => $item) {
            $real_num = $item['product']->getNum($item['cartitem']['offer']);
            if ($item['cartitem']['amount'] > $real_num) {
                $num_error = true;
                $bitems[$n]['product']['num'] = $real_num;
            }
        }
        if ($num_error) {
            return t('Извините, некоторых товаров уже нет в наличии');
        }
        return true;
    }

    /**
    * Возвращает экземпляр класса текущей корзины
    * @return Order
    */
    public static function currentOrder()
    {
        $order = new self();
        $order->getFromSession();
        return $order;
    }
    
    /**
    * Загружает объект данными из сессии. 
    * После вызова данного метода, любые изменения в объект будут сохраняться в сессию
    * 
    * @return void
    */
    function getFromSession() 
    {
        if (!isset($_SESSION[self::ORDER_SESS_VAR])) {
            $_SESSION[self::ORDER_SESS_VAR] = [];
        }
        $this->_values = &$_SESSION[self::ORDER_SESS_VAR];
    }

    /**
     * Удаляет чекеры, которые используются при оформлении заказа в клиентской части
     *
     * @return void
     */
    function removeConditionCheckers()
    {
        foreach($this->getProperties() as $key => $property) {
            if (isset($property->condition)) {
                $property->removeAllCheckers();
            }
        }
    }
    
    
    /**
    * Возвращает поля, которые удовлетворяют условиям condition.
    * Условия задают в каком случае поля должны запрашиваться и проверяться из POST
    * 
    * @return array
    */
    function useFields($post)
    {
        $result = [];
        foreach($this->getProperties() as $key=>$property) {
            $rule = isset($property->condition) ? $property->condition : null;
            $include = true;            
            if ($rule) {
                foreach($rule as $field => $need) {
                    if (!isset($post[$field])) {
                        $include = false;
                    } else {
                        if (!array_intersect((array)$post[$field], (array)$need)) {
                            $include = false;
                        }
                    }
                }
            }
            if ($include) $result[] = $key;
        }
        return $result;        
    }
    
    /**
    * Возвращает объект, управляющий дополнительными полями, заданными в настройках модуля
    * 
    * @return \RS\Config\UserFieldsManager
    */
    function getFieldsManager()
    {
        $order_fields_manager  = ConfigLoader::byModule($this)->getUserFieldsManager();
        $order_fields_manager->setErrorPrefix('orderfield_');
        $order_fields_manager->setArrayWrapper('userfields_arr');
        
        $data = @unserialize($this['userfields']);

        if (!empty($data)) $order_fields_manager->setValues($data);
        
        return $order_fields_manager;
    }    
    
    /**
    * Возвращает список регионов в стране
    * 
    */
    public static function regionList()
    {
        $_this = self::currentOrder();
        $parent = $_this['addr_country_id'];
        $api = new RegionApi();
        if ($parent < 1) {
            $countries = $api->countryList();
            $array_countries_keys = array_keys($countries);
            if (count($countries)) $parent = reset($array_countries_keys);
        }
        if ($parent>0) {
            $api->setFilter('parent_id', $parent);
            $regions = $api->getAssocList('id', 'title');
        } else {
            $regions = [];
        }
        return $regions;
    }

    /**
     * Устанавливает id адреса доставки
     *
     * @param int $address_id - id адреса доставки
     * @return self
     */
    public function setUseAddr(?int $address_id): self
    {
        $this['use_addr'] = $address_id;
        $this->clearAddressCache();
        return $this;
    }

    /**
     * Возвращает объект адреса доставки
     *
     * @param bool $cache - использовать кэш
     * @return Address
     */
    function getAddress($cache = true)
    {
        if ($this->address === null || !$cache) {
            if ($this['use_addr']) {
                $this->address = new Address($this['use_addr']);
            } else {
                $this->address = new Address();
                $this->address->getFromArray($this->getValues(), 'addr_');
                $this->address->updateAddressTitles();
                $this->address->updateCityId();
            }
        }
        return $this->address;
    }

    /**
     * Устанавливает объект адреса доставки, в случае если адрес доставки еще не существует в БД
     *
     * @param Address $address
     * @return Order
     */
    function setAddress(Address $address)
    {
        $this->address = $address;

        $properties = $this->getPropertyIterator();
        foreach ($address->getValues() as $key => $value) {
            $order_key = 'addr_' . $key;
            if (isset($properties[$order_key])) {
                $this[$order_key] = $value;
            }
        }
        return $this;
    }

    /**
     * Очищает кэшированный адрес
     *
     * @return void
     */
    public function clearAddressCache(): void
    {
        $this->address = null;
    }

    /**
     * Возращает вес заказа в граммах
     *
     * @param null|string $weight_unit - идентификатор единицы измерения, в которй нужно получить вес (соотношение к граммам)
     * @param bool $cache - использовать кэш
     * @return float
     * @throws RSException
     */
    function getWeight($weight_unit = null, $cache = true)
    {
        if (!$cache || !isset($this->cache_weigth[$weight_unit])) {
            $this->cache_weigth[$weight_unit] = $this->getCart()->getTotalWeight($weight_unit);
        }
        return $this->cache_weigth[$weight_unit];
    }

    /**
     * Возвращает объект способа доставки
     *
     * @return Delivery
     */
    function getDelivery()
    {
        return new Delivery($this['delivery']);
    }

    /**
     * Возвращает объект способа оплаты
     *
     * @return Payment
     */
    function getPayment()
    {
        return new Payment($this['payment'], true, $this);
    }

    /**
     * Возвращает объект сохранённого способа платежа
     *
     * @return SavedPaymentMethod|null
     */
    public function getSavedPaymentMethod(): ?SavedPaymentMethod
    {
        $saved_payment_method = new SavedPaymentMethod($this['saved_payment_method_id']);
        if ($saved_payment_method['id']) {
            return $saved_payment_method;
        }
        return null;
    }

    /**
     * Возвращает объект выбранного склада
     *
     * @return WareHouse
     */
    function getWarehouse()
    {
        return new WareHouse($this['warehouse']);
    }

    /**
     * Возвращает стоимсть доставки для текущего заказа и заданного типа доставки
     *
     * @param Delivery $delivery
     * @return string
     */
    function getDeliveryCostText(Delivery $delivery)
    {
        return $delivery->getDeliveryCostText($this, $this->getAddress());
    }

    function getDeliveryExtraText(Delivery $delivery)
    {
        return $delivery->getDeliveryExtraText($this, $this->getAddress());
    }

    /**
     * Применяет валюту заказа к заданной цене
     *
     * @param float $price
     * @return double
     */
    function applyMyCurrency($price)
    {
        return round($price * $this['currency_ratio'], 2);
    }

    /**
     * Возвращает валюту, в которой был оформлен заказ
     *
     * @return Currency
     */
    function getMyCurrency()
    {
        if ($this->my_currency === null) {
            $this->my_currency = CurrencyApi::getByUid($this['currency']);
            if (!$this->my_currency) {
                $this->my_currency = CurrencyApi::getBaseCurrency();
            }
        }
        return $this->my_currency;
    }

    /**
     * Возвращает пользователя, оформившего заказ
     *
     * @return User
     */
    function getUser()
    {
        if ($this['user_id'] > 0) {
            return new User($this['user_id']);
        }
        $user = new User();

        //Парсит строку так: первое слово - фамилия, второе - имя, все остальное - отчество
        //Необходимо для тюркских отчеств, например, для Мамедов Ильгар Натиг Оглы, где Натиг Оглы - отчество
        preg_match('/^([^\s]+)\s*([^\s]+)?\s*(.+)?$/u', trim($this['user_fio']), $match);
        $user['surname'] = isset($match[1]) ? $match[1] : '';
        $user['name'] = isset($match[2]) ? $match[2] : '';
        $user['midname'] = isset($match[3]) ? $match[3] : '';

        $user['e_mail'] = $this['user_email'];
        $user['phone'] = $this['user_phone'];
        return $user;
    }
    
    /**
    * Возвращает объект с позициями оформленного заказа
    * 
    * @return Cart
    */
    function getCart()
    {
        if ($this['id']) {
            if ($this->order_cart === null) {
                $this->order_cart = Cart::orderCart($this);
            }
            return $this->order_cart;
        } else {
            if ($this->session_cart === null) {
                $this->session_cart = @unserialize($this['basket']);
            }
            if ($this->session_cart){
               $this->session_cart->setOrder($this);
            }          
            return $this->session_cart;
        }
    }
    
    /**
    * Возможно ли редактирование заказа. 
    * Возвращает false если были удалены налоги либо скидки, идентфикаторы которых присутсвуют в этом заказе
    * 
    * @return bool
    */
    function canEdit()
    {
        // Проверям, не удалены ли налоги
        $items   = $this->getCart()->getCartItemsByType(Cart::TYPE_TAX);
        foreach($items as $one){
            $obj = new Tax();
            if(!$obj->exists($one['entity_id'])){
                return false;
            }
        }

        // Проверям, не удалены ли скидочные купоны
        $items   = $this->getCart()->getCartItemsByType(Cart::TYPE_COUPON);
        foreach($items as $one){
            $obj = new Discount();
            if(!$obj->exists($one['entity_id'])){
                return false;
            }
        }

        return true;
    }
    
    /**
    * Возвращает объект статуса заказа
    * 
    * @return UserStatus
    */
    function getStatus()
    {
        return new UserStatus($this['status']);
    }

    /**
     * Возвращает общую стоимость заказа
     *
     * @param bool $format - Если true, то стоимость будет отформатирована
     * @param bool $use_currency - Если true, то стоимость будет возвращена, в валюте в которой оформлялся заказ
     * @return float|string
     */
    function getTotalPrice($format = true, $use_currency = false)
    {
        $price = $this['totalcost'];
        if ($use_currency) {
            $price = $this->applyMyCurrency($price);
        }

        if ($format) {
            $currency = $use_currency ? $this['currency_stitle'] : CurrencyApi::getBaseCurrency()['stitle'];
            $price = CustomView::cost($price, $currency);
        }
        return $price;
    }

    /**
     * Возвращает все транзакции заказа
     *
     * @return Transaction[]
     */
    public function getTransactions()
    {
        $transactrions_api = new TransactionApi();
        $transactrions_api->setFilter('order_id', $this['id']);
        $transactrions_api->setOrder('dateof DESC');
        /** @var Transaction[] $transactrions */
        $transactrions = $transactrions_api->getList();

        return $transactrions;
    }

    /**
     * Возвращает успешно выполненные транзакции заказа
     *
     * @return Transaction[]
     */
    function getSuccessTransactions()
    {
        $transactrions_api = new TransactionApi();
        /** @var Transaction[] $transactrions */
        $transactrions = $transactrions_api->setFilter('order_id', $this['id'])
            ->setFilter('status', Transaction::STATUS_SUCCESS)
            ->setOrder('dateof DESC')
            ->getList();

        return $transactrions;
    }
    
    /**
    * Возвращает список из базовой валюты и валюты в которой оформлен заказ
    * 
    * @return array
    */
    function getAllowCurrencies()
    {
        $base_currency = CurrencyApi::getBaseCurrency();
        $result = [
            '0' => $base_currency->title
        ];
        
        if ($base_currency->title == $this['currency']) {
            $my = 0;
        } else {
            $result = ['1' => $this['currency']] + $result;
            $my = 1;
        }
        
        $result[$my] .= t(' (заказ оформлен в этой валюте)');
        return $result;
    }
    
    /**
    * Возвращает дополнительные пары ключ => значение для отображения в админ. панели в разделе "Информация о заказе"
    * 
    * @return array
    */
    function getExtraInfo()
    {
        $data = $this['extra'];
        if (isset($data['extrainfo']) && is_array($data['extrainfo'])) {  
            return $data['extrainfo'];
        }
        return [];
    }
    
    /**
    * Добавляет дополнительную информацию к заказу
    * 
    * @param string $title - Название информации
    * @param mixed $value - Значение
    * @param mixed $data - доп. сведения (если есть)
    * @param mixed $key - уникальный идентификатор информации
    * @param mixed $type - тип инфостроки
     *
    * @return Order
    */
    function addExtraInfoLine($title, $value, $data = null, $key = null, $type = self::EXTRAINFOLINE_TYPE_DEFAULT)
    {

        $extra = $this['extra'];
        $item = [
            'title' => $title,
            'value' => $value,
            'data' => $data,
            'type'=>$type
        ];
        
        if ($key === null) {
            $extra['extrainfo'][] = $item;
        } else {
            $extra['extrainfo'][$key] = $item;
        }



        $this['extra'] = $extra;
        $this['_serialized'] = serialize($extra);
        return $this;
    }

    /**
     * Возвращет данные из секции "extrainfo"
     *
     * @param string $key - ключ в секции extrainfo, если не указан, то возвращает всю секцию
     * @return mixed
     */
    function getExtraInfoLine($key = null)
    {
        $extra = $this['extra'];
        if (!isset($extra['extrainfo'])) {
            return [];
        }
        if ($key === null) {
            return $extra['extrainfo'];
        } else {
            return isset($extra['extrainfo'][$key]) ? $extra['extrainfo'][$key] : false;
        }
    }

    /**
     * Удаляет данные из секции "extrainfo"
     *
     * @param string $key - ключ в секции extrainfo
     * @param string $type - тип инфолинии
     * @return Order
     */
    function removeExtraInfoLine($key = null, $type = null)
    {
        $extra = $this['extra'];
        if (($key || $type) && isset($extra['extrainfo'])) {
            if (isset($extra['extrainfo'][$key])) {
                unset($extra['extrainfo'][$key]);
            }
            if (isset($extra['extrainfo'])) {
                foreach ($extra['extrainfo'] as $index => $one) {
                    if ($one['type'] == $type) {
                        if (!$key) {
                            unset($extra['extrainfo'][$index]);
                        } elseif ($key == $index) {
                            unset($extra['extrainfo'][$index]);
                        }
                    }
                }
            }
            $this['extra'] = $extra;
            $this['_serialized'] = serialize($extra);
        }
        return $this;
    }

    /**
     * Добавляет в скрытую(которая не будет выводится) секцию с данными
     * ваши данные по ключу
     *
     * @param string $key - ключ
     * @param mixed $value - значение для сохранения
     * @return Order
     */
    function addExtraKeyPair($key, $value)
    {
        $extra = $this['extra'];
        $extra['extrakeypair'][$key] = $value;

        $this['extra'] = $extra;
        $this['_serialized'] = serialize($extra);
        return $this;
    }

    /**
     * Возвращет данные из секции "extrakeypair"
     *
     * @param string $key - ключ в секции extrakeypair, если не указан, то возвращает всю секцию
     * @return mixed
     */
    function getExtraKeyPair($key = null)
    {
        $extra = $this['extra'];
        if (!isset($extra['extrakeypair'])) {
            return [];
        }
        if ($key === null) {
            return $extra['extrakeypair'];
        } else {
            return isset($extra['extrakeypair'][$key]) ? $extra['extrakeypair'][$key] : false;
        }
    }

    /**
     * Удаляет в скрытую секцию с данными по ключу
     *
     * @param string $key - ключ
     * @return Order
     */
    function removeExtraKeyPair($key)
    {
        $extra = $this['extra'];
        if (isset($extra['extrakeypair'][$key])) {
            unset($extra['extrakeypair'][$key]);

            $this['extra'] = $extra;
            $this['_serialized'] = serialize($extra);
        }
        return $this;
    }
    
    /**
    * Возвращает список объектов для печати текущего заказа
    * 
    * @return array
    */
    function getPrintForms()
    {
        return AbstractPrintForm::getList();
    }
    
    /**
    * Возвращает объект компании(с реквизитами), которая поставляет услуги для данного заказа
    * 
    * @return Company
    */
    function getShopCompany()
    {
        return $this->getPayment()->getTypeObject()->getCompany();
    }

    /**
     * Возвращает true если для этого заказа возможна online-оплата
     *
     * @return bool
     */
    function canOnlinePay(): bool
    {
        if (!$this->getPayment()->getTypeObject()->canOnlinePay()) {
            return false;
        }
        $available_statuses = array_merge(UserStatusApi::getStatusesIdByType(UserStatus::STATUS_WAITFORPAY), UserStatusApi::getStatusesIdByType(UserStatus::STATUS_PAYMENT_METHOD_SELECTED));
        if (!in_array($this['status'], $available_statuses)) {
            return false;
        }
        if ($this['is_payed']) {
            return false;
        }
        return true;
    }

    /**
     * Возвращает true если после сохранения этого заказа для него будет возможна online-оплата
     *
     * @return bool
     */
    public function checkoutCanOnlinePay(): bool
    {
        $payment_type = $this->getPayment()->getTypeObject();
        if (!$payment_type || !$payment_type->canOnlinePay()) {
            return false;
        }
        if (!in_array($this->getStartStatus(), UserStatusApi::getStatusesIdByType(UserStatus::STATUS_WAITFORPAY))) {
            return false;
        }
        return true;
    }

    /**
     * Возвращает стартовый статус заказа
     *
     * @return int
     */
    public function getStartStatus(): int
    {
        $config = ConfigLoader::byModule($this);
        if (!empty($config['first_order_status'])) {
            $status = $config['first_order_status'];
        } else {
            $status = UserStatusApi::getStatusIdByType(UserStatus::STATUS_NEW);
        }

        $payment = $this->getPayment();
        if ($payment['first_status'] > 0) {
            $status = $payment['first_status'];
        }

        $delivery = $this->getDelivery();
        if ($delivery['first_status'] > 0) {
            $status = $delivery['first_status'];
        }

        return $status;
    }

    /**
     * Проверяет можно ли сменить оплату в заказе
     *
     * @return bool
     */
    function canChangePayment()
    {
        return (!$this['is_payed'] && in_array($this['status'], UserStatusApi::getStatusesIdByType(UserStatus::STATUS_WAITFORPAY)));
    }
    
    /**
    * Возвращайет URL для оплаты заказа в случае выбора online способа оплаты
    * 
    * @param bool $absolute - Если true, то будет возвращен абсолютный URL
    * @return string
    */
    function getOnlinePayUrl($absolute = false)
    {
        $router = RouterManager::obj();
        $pay_params = [
            'type' => OnlinePayApi::TYPE_ORDER_PAY,
            'order_num' => (string)$this['order_num'],
        ];
        $params = [
            'Act' => 'pay',
            'params' => $pay_params,
            'sign' => OnlinePayApi::getPayParamsSign($pay_params)
        ];
        return $router->getUrl('shop-front-onlinepay', $params, $absolute);
    }
    
    /**
    * Возвращает список файлов, прикрепленных к заказу
    * 
    * @param string | array $access - уровень доступа
    * @param bool $include_product_files - если true, то в результатах будет выведены и список файлов товаров, 
    * доступных после оплаты
    * @return File[]
    */
    function getFiles($access = ['visible', 'afterpay'], $include_product_files = true)
    {
        $result = [];
        if ($this['id'] && ModuleManager::staticModuleExists('files')) {
            $file_api = new FileApi();
            if (!is_array($access)) {
                $access = (array)$access;
            }
            
            if (!$this['is_payed'] && in_array('afterpay', $access)) {
                $access = array_diff($access, ['afterpay']);
            }
            
            if ($access) {
                //Получаем файлы заказа
                $file_api->setFilter([
                    'link_type_class' => 'files-shoporder',
                    'link_id' => $this['id']
                ]);
                $file_api->setFilter('access', $access, 'in');
                $result = $file_api->getList();
            }
            
            if ($include_product_files && in_array('afterpay', $access)) {
                //Получаем файлы товаров
                $cartitems = $this->getCart()->getCartItemsByType(Cart::TYPE_PRODUCT);
                $ids = [];
                foreach($cartitems as $cartitem) {
                    $ids[] = $cartitem['entity_id'];
                }
                
                if ($ids) {
                    $file_api->clearFilter();
                    $file_api->setFilter([
                        'link_type_class' => 'files-catalogproduct',
                        'access' => 'afterpay'
                    ]);
                    $file_api->setFilter('link_id', $ids, 'in');
                    $result = array_merge($result, $file_api->getList());
                }
            }
        }
        return $result;
    }
    
    /**
    * Возвращает стоимость доставки, у существующего заказа
    * 
    * @return float
    */
    function getDeliveryCost()
    {
        return $this['user_delivery_cost'] ?: 0;
    }

    /**
     * Устанавливает адрес по умолчанию, если это возможно
     *
     * @return void
     */
    function setDefaultAddress()
    {
        $this->setUseAddr(0);
        /** @var ShopConfig $config */
        $config = ConfigLoader::byModule($this);
        if ($config['use_selected_address_in_checkout']) {
            $this->setAddressFromSelectedAddress();
        } else { //Если нужно простое указание адреса
            $address = new Address();

            if (!$config['require_city'] && $config->getDefaultCityId()) {
                $city = new Region($config->getDefaultCityId());
                $address['city']    = $city['title'];
                $address['city_id'] = $city['city_id'];
                $this['addr_city']  = $city['title'];
            }
            if (!$config['require_region'] && $config->getDefaultRegionId()) {
                $region = new Region($config->getDefaultRegionId());
                $this['addr_region_id'] = $address['region_id'] = $region['id'];
            }
            if (!$config['require_country'] && $config->getDefaultCountryId()) {
                $country = new Region($config->getDefaultCountryId());
                $this['addr_country_id'] = $address['country_id'] = $country['id'];
            }
            if (!$config['require_zipcode'] && $config['default_zipcode']){
                $this['addr_zipcode'] = $address['zipcode'] = $config['default_zipcode'];
            }
            $this->setAddress($address);
        }
        // todo описать событие в документации
        EventManager::fire('order.setdefaultaddress', [
            'order' => $this,
        ]);
    }

    /**
     * Пробует установить адрес по "выбранному адресу"
     */
    function setAddressFromSelectedAddress()
    {
        $address = SelectedAddress::getInstance()->getAddress();
        $city = $address->getCity();
        $region = $address->getRegion();
        $country = $address->getCountry();

        if (!empty($city['id'])) {
            $this['addr_city_id'] = $city['id'];
            $this['addr_city'] = $address['city'] = $city['title'];
            $this['addr_zipcode'] = $address['zipcode'] = $city['zipcode'];
        }
        if (!empty($region['id'])) {
            $this['addr_region_id'] = $region['id'];
            $this['addr_region'] = $address['region'] = $region['title'];
        }
        if (!empty($country['id'])) {
            $this['addr_country_id'] = $country['id'];
            $this['addr_country'] = $address['country'] = $country['title'];
        }

        $this->setAddress($address);
    }

    /**
     * Возвращает указанный в заказе ПВЗ
     *
     * @return Pvz|null
     */
    public function getSelectedPvz(): ?Pvz
    {
        if ($type_object = $this->getDelivery()->getTypeObject()) {
            return $type_object->getSelectedPvz($this);
        }
        return null;
    }
    
    /**
    * Возвращает адрес для отслеживания доставки заказа
    * 
    * @return string
    */
    function getTrackUrl()
    {
        return $this->getDelivery()->getTypeObject()->getTrackNumberUrl($this);
    }
    
    /**
    * Возвращает курьера, назначенного на данный заказ
    * 
    * @return User
    */
    function getCourierUser()
    {
        return new User($this['courier_id']);
    }

    /**
     * Возвращает документы складского учета, связанные с заказом
     *
     * @return array|bool
     */
    function getLinkedDocuments()
    {
        $manager = new DocumentLinkManager($this['id'], self::DOCUMENT_TYPE_ORDER);
        return $manager->getLinks();
    }

    /**
     * Возвращает API Складского учета
     *
     * @return InventoryDocumentApi
     */
    function getInventoryApi()
    {
        return new InventoryDocumentApi();
    }

    /**
     * Возвращает признак способа расчета по умолчанию для данного заказа
     *
     * @return string
     */
    function getDefaultPaymentMethod()
    {
        if ($this->getPayment()->payment_method) {
            return $this->getPayment()->payment_method;
        } else {
            $shop_config = Loader::byModule($this);
            return $shop_config['payment_method'];
        }
    }

    /**
     * Возвращает список отгрузок данного заказа
     *
     * @return Shipment[]
     */
    public function getShipments()
    {
        /** @var Shipment[] $shipments */
        $shipments = (new OrmRequest())
            ->from(new Shipment())
            ->where([
                'order_id' => $this['id']]
            )
            ->objects();

        return $shipments;
    }

    /**
     * Устанавливает id пользователя
     *
     * @param int $user_id
     * @return void
     */
    public function setUserId(int $user_id): void
    {
        $shop_config = ConfigLoader::byModule('shop');

        $this['user_id'] = $user_id;
        /** @var Type\Captcha $code_field */
        $code_field = $this['__code'];
        if ($this['user_id']) {
            $code_field->setEnable(false);
        } else {
            /** @var UsersCustomType\VerifiedPhone $reg_phone_field */
            $reg_phone_field = $this['__reg_phone'];
            /** @var UsersCustomType\VerifiedPhone $user_phone_field */
            $user_phone_field = $this['__user_phone'];

            if ($shop_config['checkout_type'] == ShopConfig::CHECKOUT_TYPE_FOUR_STEP && in_array($this['user_type'], ['person', 'company'])) {
                $reg_phone_field->setEnableVerification(true);
                $user_phone_field->setEnableVerification(false);
            } else {
                $reg_phone_field->setEnableVerification(false);
                $user_phone_field->setEnableVerification(true);
            }

            if (ConfigLoader::byModule('shop')['check_captcha'] && !$reg_phone_field->isEnabledVerification()) {
                $code_field->setEnable(true);
            }
        }
    }

    /**
     * Возвращает значение флага "пересчёт данных заказа без сохранения"
     *
     * @return bool
     */
    public function isRefreshMode(): bool
    {
        return $this->refresh_mode;
    }

    /**
     * Устанавливает значение флага "пересчёт данных заказа без сохранения"
     *
     * @param bool $refresh_mode
     */
    public function setRefreshMode(bool $refresh_mode): void
    {
        $this->refresh_mode = $refresh_mode;
    }

    /**
     * Возвращает специальный параметр, список возможных параметров находится в константах класса
     *
     * @param string $key - флаг
     * @param mixed $default - значение по умолчанию
     * @return bool
     */
    public function getSpecialParam(string $key, $default = null)
    {
        return $this['special_params'][$key] ?? $default;
    }

    /**
     * Устанавливает специальный параметр, список возможных параметров находится в константах класса
     *
     * @param string $key - флаг
     * @param bool $value - значение
     * @return void
     */
    public function setSpecialParam(string $key, $value): void
    {
        $special_params = $this['special_params'];
        $special_params[$key] = $value;
        $this['special_params'] = $special_params;
    }

    /**
     * Очищает поля в объекте заказа, которые должны быть
     * пустыми перед оформлением нового заказа.
     */
    public function resetOrderForCheckout()
    {
        $this->clearErrors();
        $this['id'] = null;
        $this['status'] = null;
        $this['ip'] = $_SERVER['REMOTE_ADDR'];
        $this['expired'] = false;
        $this['only_pickup_points'] = 0;
    }
}
