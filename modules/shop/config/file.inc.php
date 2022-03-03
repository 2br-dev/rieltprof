<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Config;

use RS\Cache\Cleaner as CacheCleaner;
use RS\Config\UserFieldsManager;
use RS\Module\AbstractModel\TreeList\AbstractTreeListIterator;
use RS\Module\Exception as ModuleException;
use RS\Module\Manager as ModuleManager;
use RS\Orm\ConfigObject;
use RS\Orm\Type;
use RS\Router\Manager as RouterManager;
use Shop\Model\DeliveryApi;
use Shop\Model\DeliveryType\Cdek2;
use Shop\Model\Discounts\DiscountManager;
use Shop\Model\Orm\Delivery;
use Shop\Model\Orm\Region;
use Shop\Model\TaxApi;

class File extends ConfigObject
{
    const CHECKOUT_TYPE_FOUR_STEP = 'four_step';
    const CHECKOUT_TYPE_ONE_PAGE = 'one_page';
    const CHECKOUT_TYPE_CART_CHECKOUT = 'cart_checkout';
    const CHECKOUT_REGISTER_OPTION_USER_CHOOSES = 'user_chooses';
    const CHECKOUT_REGISTER_OPTION_ONLY_REGISTER = 'only_register';
    const CHECKOUT_REGISTER_OPTION_ONLY_NO_REGISTER = 'only_no_register';

    function _init()
    {
        parent::_init()->append([
            t('Основные'),
                'basketminlimit' => new Type\Decimal([
                    'description' => t('Минимальная сумма заказа (в базовой валюте)'),
                    'maxLength' => 20,
                    'decimal' => 2
                ]),
                'basketminweightlimit' => new Type\Decimal([
                    'description' => t('Минимальный суммарный вес товаров заказа '),
                    'maxLength' => 20,
                    'decimal' => 2
                ]),
                'check_quantity' => new Type\Integer([
                    'description' => t('Запретить оформление заказа, если товаров недостаточно на складе'),
                    'hint' => 'Включает учет остатков товаров (начинает списывать остатки за заказы и возвращать их за отмены заказов)',
                    'checkboxView' => [1, 0]
                ]),
                'allow_buy_num_less_min_order' => new Type\Integer([
                    'description' => t('Разрешить покупать товар, если его остаток меньше "минимального количества для заказа"'),
                    'checkboxView' => [1, 0]
                ]),
                'allow_buy_all_stock_ignoring_amount_step' => new Type\Integer([
                    'description' => t('Игнорировать "шаг изменения количества" если выкупается весь остаток'),
                    'checkboxView' => [1, 0]
                ]),
                'check_cost_for_zero' => new Type\Integer([
                    'description' => t('Запретить оформление заказа, если в корзину добавлен товар с нулевой ценой'),
                    'checkboxView' => [1, 0]
                ]),
                'first_order_status' => new Type\Integer([
                    'description' => t('Стартовый статус заказа (по-умолчанию)'),
                    'tree' => [['\Shop\Model\UserStatusApi', 'staticTreeList']],
                    'hint' => t('Данная настройка перекрывается настройкой способа оплаты, а затем настройкой способа доставки.<br>' .
                        'Важно: система ожидает прием on-line платежей и предоставляет ссылку на оплату только в статусе - Ожидает оплату', null, 'подсказка опции first_order_status')
                ]),
                'user_orders_page_size' => new Type\Integer([
                    'description' => t('Количество заказов в истории на одной странице')
                ]),
                'reservation' => new Type\Integer([
                    'description' => t('Разрешить предварительный заказ товаров с нулевым остатком'),
                    'hint' => t('Актуально только при включенной опции `Запретить оформление заказа, если товаров недостаточно на складе`'),
                    'listFromArray' => [[
                        0 => t('Нет'),
                        1 => t('Да')
                    ]]
                ]),
                'reservation_required_fields' => new Type\Varchar([
                    'description' => t('Запрашиваемые поля при предзаказе'),
                    'listFromArray' => [[
                        'phone_email' => t('Телефон и e-mail'),
                        'phone' => t('Телефон'),
                        'email' => t('E-mail'),
                    ]],
                ]),
                'allow_concomitant_count_edit' => new Type\Integer([
                    'description' => t('Разрешить редактирование количества сопутствующих товаров в корзине.'),
                    'checkboxView' => [1, 0]
                ]),
                'source_cost' => new Type\Integer([
                    'description' => t('Закупочная цена товаров'),
                    'hint' => t('Цена должна отражать ваши расходы на приобретение товара. Данная цена будет использована для расчета дохода, полученного при продаже товара. Расчет будет по форуме ЦЕНА ПРОДАЖИ - ЗАКУПОЧНАЯ ЦЕНА.'),
                    'list' => [['\Catalog\Model\Costapi', 'staticSelectList'], true]
                ]),
                'auto_change_status' => new Type\Integer([
                    'maxLength' => 1,
                    'checkboxView' => [1, 0],
                    'description' => t('Автоматически изменять статус заказа, который находится в статусе L более N дней'),
                    'hint' => t('Опция требует, чтобы в системе был настроен внутренний планировщик. С помощью данной опции удобно автоматически отменять неоплаченные заказы. Проверка статусов заказов происходит один раз в сутки.'),
                    'template' => '%shop%/form/config/auto_change_status.tpl'
                ]),
                'auto_change_timeout_days' => new Type\Integer([
                    'description' => t('Кол-во дней(N), после которых нужно автоматически менять статус заказа'),
                    'visible' => false
                ]),
                'auto_change_from_status' => new Type\ArrayList([
                    'runtime' => false,
                    'description' => t('Список статусов (L), в которых должен находиться заказ для автосмены'),
                    'hint' => t('Опция требует, чтобы в системе был настроен внутренний планировщик'),
                    'tree' => [['\Shop\Model\UserStatusApi', 'staticTreeList']],
                    'attr' => [[
                        AbstractTreeListIterator::ATTRIBUTE_MULTIPLE => true,
                    ]],
                    'visible' => false
                ]),
                'auto_change_to_status' => new Type\Integer([
                    'description' => t('Статус, на который следует переключать заказ, если он находится в статусе L более N дней'),
                    'tree' => [['\Shop\Model\UserStatusApi', 'staticTreeList'], 0, [0 => t('- Не выбрано -')]],
                    'visible' => false
                ]),
                'auto_send_supply_notice' => new Type\Integer([
                    'description' => t('Автоматически отправлять сообщения о поступлении товара'),
                    'hint' => t('Для работы опции требуется настроенный внутренний планировщик'),
                    'checkboxView' => [1, 0]
                ]),
                'courier_user_group' => new Type\Varchar([
                    'description' => t('Группа, пользователи которой считаются курьерами'),
                    'list' => [['\Users\Model\GroupApi', 'staticSelectList'], [0 => t('Не выбрано')]],
                ]),
                'ban_courier_del' => new Type\Integer([
                    'description' => t('Запретить курьерам удалять товары из заказа'),
                    'default' => 0,
                    'checkboxView' => [1, 0]
                ]),
                'remove_nopublic_from_cart' => new Type\Integer([
                    'description' => t('Удалять товары из корзины, которые были скрыты'),
                    'checkboxView' => [1, 0]
                ]),
                'show_number_of_lines_in_cart' => new Type\Integer([
                    'description' => t('Показывать количество товарных строк возле ярлыка корзины вместо общего количества товаров'),
                    'hint' => t('В случае, если вы продаете товары в разных единицах измерения лучше показывать возле корзны количество товарных строк'),
                    'checkboxView' => [1, 0]
                ]),
            t('Регион по умолчанию'),
                'default_region_id' => new Type\Integer([
                    'description' => t('Регион по умолчанию'),
                    'hint' => t('Данный регион может использоваться как выбранный регион по умолчанию при оформлении заказа и в различных блоках расчета стоимости доставки по сайту'),
                    'tree' => ['\Shop\Model\RegionApi::staticTreeList']
                ]),
                'use_geolocation_address' => new Type\Integer([
                    'description' => t('Определять регион используя геолокацию?'),
                    'hint' => t('Если город будет определен через геолокацию, то он будет перекрывать город по умолчанию'),
                    'maxLength' => 1,
                    'checkboxView' => [1, 0],
                ]),
                'use_selected_address_in_checkout' => new Type\Integer([
                    'description' => t('Заполнять адрес при оформлении заказа на основе выбранного региона'),
                    'maxLength' => 1,
                    'checkboxView' => [1, 0],
                ]),
                'regions_marked_when_change_selected_address' => new Type\ArrayList([
                    'description' => t('Регионы выделенные при изменении "выбранного адреса"'),
                    'tree' => ['\Shop\Model\RegionApi::staticTreeList'],
                    'runtime' => false,
                    'attr' => [[
                        AbstractTreeListIterator::ATTRIBUTE_MULTIPLE => true,
                    ]],
                ]),
            t('Лицевой счет'),
                'use_personal_account' => new Type\Integer([
                    'description' => t('Использовать лицевой счет'),
                    'checkboxView' => [1, 0]
                ]),
                'nds_personal_account' => new Type\Varchar([
                    'description' => t('Налог, при пополнении лицевого счета'),
                    'listFromArray' => [[
                        TaxApi::TAX_NDS_NONE => t('Без НДС'),
                        TaxApi::TAX_NDS_0 => t('НДС 0'),
                        TaxApi::TAX_NDS_110 => t('НДС 10% (включено в стоимость)'),
                        TaxApi::TAX_NDS_120 => t('НДС 20% (включено в стоимость)'),
                    ]],
                ]),
                'personal_account_payment_method' => new Type\Varchar([
                    'description' => t('Признак способа расчета для чека при пополнении баланса лицевого счета'),
                    'list' => [['\Shop\Model\CashRegisterApi', 'getStaticPaymentMethods']],
                ]),
                'personal_account_payment_subject' => new Type\Varchar([
                    'description' => t('Признак предмета расчета для чека при пополнении баланса лицевого счета'),
                    'list' => [['\Shop\Model\CashRegisterApi', 'getStaticPaymentSubjects']],
                ]),
            t('Дополнительные поля'),
                '__userfields__' => new Type\UserTemplate('%shop%/form/config/userfield.tpl'),
                'userfields' => new Type\ArrayList([
                    'description' => t('Дополнительные поля'),
                    'runtime' => false,
                    'visible' => false
                ]),
            t('Оформление заказа'),
                'checkout_type' => (new Type\Varchar())
                    ->setDescription(t('Тип оформления заказа'))
                    ->setListFromArray([
                        self::CHECKOUT_TYPE_FOUR_STEP => [
                            'title' => t('Оформление в 4 шага'),
                            'description' => t('Корзина и оформление заказа находятся на разных страницах. Оформление заказа разделено на 4 шага.<br>* Поддерживается во всех старых темах, выпущеных до октября 2020 г.'),
                        ],
                        self::CHECKOUT_TYPE_ONE_PAGE => [
                            'title' => t('Оформление на одной странице'),
                            'description' => t('Корзина и оформление заказа находятся на разных страницах. Все шаги оформления заказа представлены на одной странице.'),
                        ],
                        self::CHECKOUT_TYPE_CART_CHECKOUT => [
                            'title' => t('Оформление в корзине'),
                            'description' => t('Оформление заказа происходит прямо на странице корзины'),
                        ],
                    ])
                    ->setTemplate('%shop%/form/config/field_checkout_type.tpl'),
                'checkout_register_option' => (new Type\Varchar())
                    ->setDescription(t('Что делать с незарегистрированными пользователями?'))
                    ->setListFromArray([
                        self::CHECKOUT_REGISTER_OPTION_USER_CHOOSES => t('Пользователь выбирает регистрирроваться или нет'),
                        self::CHECKOUT_REGISTER_OPTION_ONLY_REGISTER => t('Всегда регистрировать пользователя'),
                        self::CHECKOUT_REGISTER_OPTION_ONLY_NO_REGISTER => t('Всегда оформлять заказы без регистрации'),
                    ])
                    ->setAttr([
                        'data-order-type' => self::CHECKOUT_TYPE_ONE_PAGE.';'.self::CHECKOUT_TYPE_CART_CHECKOUT
                    ]),
                'require_choose_address' => (new Type\Integer())
                    ->setDescription(t('Требовать выбирать адрес'))
                    ->setHint(t('Работает только если укзан API-ключ DaData в настройках модуля "Системный модуль"'))
                    ->setCheckboxView(1, 0)
                    ->setDefault(0)
                    ->setAttr([
                        'data-order-type' => self::CHECKOUT_TYPE_ONE_PAGE.';'.self::CHECKOUT_TYPE_CART_CHECKOUT
                    ]),
                'register_user_default_checked' => (new Type\Integer())
                    ->setDescription(t('По умолчанию включать флаг "Создать личный кабинет"'))
                    ->setMaxLength(1)
                    ->setCheckboxView(1, 0)
                    ->setAttr([
                        'data-order-type' => self::CHECKOUT_TYPE_ONE_PAGE.';'.self::CHECKOUT_TYPE_CART_CHECKOUT
                    ]),
                'default_checkout_tab' => new Type\Varchar([
                    'description' => t('Вкладка по умолчанию'),
                    'hint' => t('Отвечает за то, какая вкладка будет отображена на этапе оформления заказа первой<br>* '),
                    'listFromArray' => [[
                        'person' => t('Частное лицо'),
                        'company' => t('Компания'),
                        'noregister' => t('Без регистрации')
                    ]],
                    'attr' => [[
                        'data-order-type' => self::CHECKOUT_TYPE_FOUR_STEP
                    ]]
                ]),
                'default_zipcode' => new Type\Varchar([
                    'description' => t('Индекс по умолчанию'),
                    'hint' => t('Используется, если поле Индекс не отображается'),
                ]),
                'require_country' => new Type\Integer([
                    'maxLength' => 1,
                    'description' => t('"Страна доставки" обязательное поле?'),
                    'hint' => t('Данная настройка не используется, если выбран способ доставки, который сам определяет обязательные поля'),
                    'checkboxview' => [1, 0],
                ]),
                'require_region' => new Type\Integer([
                    'maxLength' => 1,
                    'description' => t('"Регион доставки" обязательное поле?'),
                    'hint' => t('Данная настройка не используется, если выбран способ доставки, который сам определяет обязательные поля'),
                    'checkboxview' => [1, 0],
                ]),
                'require_city' => new Type\Integer([
                    'maxLength' => 1,
                    'description' => t('"Город доставки" обязательное поле?'),
                    'hint' => t('Данная настройка не используется, если выбран способ доставки, который сам определяет обязательные поля'),
                    'checkboxview' => [1, 0],
                ]),
                'require_zipcode' => new Type\Integer([
                    'maxLength' => 1,
                    'default' => 0,
                    'checkboxview' => [1, 0],
                    'description' => t('"Индекс доставки" обязательное поле?'),
                    'hint' => t('Данная настройка не используется, если выбран способ доставки, который сам определяет обязательные поля'),
                ]),
                'require_address' => new Type\Integer([
                    'maxLength' => 1,
                    'description' => t('"Адрес доставки" обязательное поле?'),
                    'hint' => t('Данная настройка не используется, если выбран способ доставки, который сам определяет обязательные поля'),
                    'checkboxview' => [1, 0],
                ]),
                'check_captcha' => new Type\Integer([
                    'maxLength' => 1,
                    'description' => t('Запрашивать проверочный код у неавторизованных пользователей?'),
                    'checkboxView' => [1, 0],
                ]),
                'show_contact_person' => new Type\Integer([
                    'maxLength' => 1,
                    'description' => t('Показывать поле "контактное лицо"?'),
                    'checkboxview' => [1, 0],
                    'attr' => [[
                        'data-order-type' => self::CHECKOUT_TYPE_FOUR_STEP
                    ]]
                ]),
                'require_email_in_noregister' => new Type\Integer([
                    'maxLength' => 1,
                    'description' => t('Поле E-mail является обязательным?<br/>(Этап без регистрации)', null, 'название опции require_email_in_noregister'),
                    'checkboxView' => [1, 0]
                ]),
                'require_phone_in_noregister' => new Type\Integer([
                    'maxLength' => 1,
                    'description' => t('Поле телефон является обязательным?<br/>(Этап без регистрации)', null, 'название опции require_phone_in_noregister'),
                    'checkboxView' => [1, 0]
                ]),
                'myself_delivery_is_default' => new Type\Integer([
                    'maxLength' => 1,
                    'description' => t('Выбирать "самовывоз" по умолчанию'),
                    'checkboxView' => [1, 0],
                    'default' => 0,
                ]),
                'require_license_agree' => new Type\Integer([
                    'maxLength' => 1,
                    'description' => t('Отображать условия продаж?'),
                    'checkboxView' => [1, 0]
                ]),
                'license_agreement' => new Type\Richtext([
                    'description' => t('Условия продаж'),
                ]),
                'use_generated_order_num' => new Type\Integer([
                    'maxLength' => 1,
                    'default' => 0,
                    'description' => t('Использовать генерируемый идентификатор заказа?'),
                    'hint' => t('Этот уникальный номер будет использоваться вместо порядкового номера заказа'),
                    'checkboxView' => [1, 0]
                ]),
                'generated_ordernum_mask' => new Type\Varchar([
                    'maxLength' => 20,
                    'description' => t('Маска генерируемого номера'),
                    'hint' => t('Маска по которой формируется, уникальный номер заказа.<br/> {n} - обязательный тег означающий уникальный номер.', null, 'подсказка опции generated_ordernum_mask'),
                    'default' => '{n}'
                ]),
                'generated_ordernum_numbers' => new Type\Integer([
                    'maxLength' => 11,
                    'default' => 6,
                    'description' => t('Количество символов-цифр генерируемого уникального номера заказа')
                ]),
                'hide_delivery' => new Type\Integer([
                    'maxLength' => 1,
                    'default' => 0,
                    'checkboxview' => [1, 0],
                    'description' => t('Не показывать шаг оформления заказа - доставка?')
                ]),
                'hide_payment' => new Type\Integer([
                    'maxLength' => 1,
                    'default' => 0,
                    'checkboxview' => [1, 0],
                    'description' => t('Не показывать шаг оформления заказа - оплата?')
                ]),
                'manager_group' => new Type\Varchar([
                    'description' => t('Группа, пользователи которой считаются менеджерами заказов'),
                    'hint' => t('Пользователей данной группы можно назначать на ведение заказов'),
                    'default' => 0,
                    'list' => [['\Users\Model\GroupApi', 'staticSelectList'], [0 => t('Не задано')]]
                ]),
                'set_random_manager' => new Type\Integer([
                    'description' => t('Устанавливать случайного менеджера при создании заказа'),
                    'hint' => t('Для данной опции должна быть задана группа пользователей-менеджеров.'),
                    'checkboxView' => [1, 0]
                ]),
                'cashregister_class' => new Type\Varchar([
                    'description' => t('Класс для обмена информацией с кассами'),
                    'list' => [['\Shop\Model\CashRegisterApi', 'getStaticTypes']]
                ]),
                'cashregister_enable_auto_check' => new Type\Integer([
                    'description' => t('Включить автоматический запрос на проверку состояния чека?'),
                    'hint' => t('Будет проверяться раз в минуту'),
                    'checkboxView' => [1, 0]
                ]),
                'ofd' => new Type\Varchar([
                    'description' => t('Платформа ОФД'),
                    'hint' => t('Отвечает за формирование правильной ссылки на чек.'),
                    'list' => [['\Shop\Model\CashRegisterApi', 'getStaticOFDList']],
                ]),
                'payment_method' => new Type\Varchar([
                    'description' => t('Признак способа расчета'),
                    'hint' => t('Перекрывается настройками способа оплаты, а затем настройками товара'),
                    'list' => [['\Shop\Model\CashRegisterApi', 'getStaticPaymentMethods']]
                ]),
            t('Оформление возврата товара'),
                'return_enable' => new Type\Integer([
                    'description' => t('Включить функциональность возвратов'),
                    'hint' => t('Влияет на отображение пункта `Мои возвраты` в меню личного кабинета'),
                    'checkboxView' => [1, 0]
                ]),
                'return_rules' => new Type\Richtext([
                    'description' => t('Правила возврата товаров'),
                ]),
                'return_print_form_tpl' => new Type\Template([
                    'description' => t('Шаблон заявления на возврат товаров'),
                    'only_themes' => false
                ]),
            t('Купоны на скидку'),
                'discount_code_len' => (new Type\Integer())
                    ->setDescription(t('Длина кода купона на скидку'))
                    ->setHint(t('Такая длина будет использована при автоматической генерации номера купона')),
                'fixed_discount_max_order_percent' => (new Type\Decimal)
                    ->setDescription(t('Максимальная доля заказа в процентах, которую можно оплатить купоном на фиксированную сумму'))
                    ->setHint(t('Может принимать значения от 0 до 100'))
                    ->setChecker('chkMinmax', t('"Максимальная скидка на товарную позицию" должна иметь значение от 0 до 100'), 0, 100),
            t('Скидки'),
                'discount_combination' => (new Type\Enum(array_keys(DiscountManager::handbookDiscountCombination())))
                    ->setDescription(t('Правило сочетания скидок'))
                    ->setListFromArray(DiscountManager::handbookDiscountCombination()),
                'old_cost_delta_as_discount' => (new Type\Integer())
                    ->setDescription(t('Считать разницу от старой цены как скидку на товар'))
                    ->setCheckboxView(1, 0),
                'cart_item_max_discount' => (new Type\Decimal())
                    ->setDescription(t('Максимальная скидка на товарную позицию (в процентах)'))
                    ->setHint(t('Может принимать значения от 0 до 100'))
                    ->setChecker('chkMinmax', t('"Максимальная скидка на товарную позицию" должна иметь значение от 0 до 100'), 0, 100),
            t('Отгрузка заказов'),
                'check_conformity_uit_to_barcode' => (new Type\Integer())
                    ->setDescription('Проверять соответствие кода маркировки штрихкоду товара')
                    ->setCheckboxView(1, 0),
                'create_receipt_upon_shipment' => (new Type\Integer())
                    ->setDescription(t('Отправлять чек при создании отгрузки'))
                    ->setCheckboxView(1, 0),
            t('Рекуррентные платежи'),
                'recurring_show_methods_menu' => (new Type\Integer())
                    ->setDescription(t('Показывать пункт меню "Мои привязанные карты"'))
                    ->setCheckboxView(1, 0),
            t('Доставка СДЭК'),
                'cdek_default_delivery' => (new Type\Integer())
                    ->setDescription(t('Профиль доставки СДЭК по-умолчанию'))
                    ->setHint(t('Доступы из данного профиля будут использованы для загрузки справочника адресов'))
                    ->setListFromArray($this->getCdekDefaultDeliveryList()),
                'cdek_webhook_uuid' => (new Type\Varchar())
                    ->setDescription(t('Действия'))
                    ->setTemplate('%shop%/form/config/field_cdek_webhook_uuid.tpl'),
        ]);
    }

    /**
     * Возвращает список
     *
     * @return string[]
     */
    protected function getCdekDefaultDeliveryList(): array
    {
        $result = [
            0 => t('Использовать первый в списке профиль СДЭК'),
        ];
        $delivery_api = new DeliveryApi();
        foreach ($delivery_api->getList() as $delivery) {
            /** @var Delivery $delivery */
            if ($delivery->getTypeObject() instanceof Cdek2) {
                $result[$delivery['id']] = $delivery['title'];
            }
        }

        return $result;
    }

    /**
     * Возвращает url страницы корзины
     *
     * @return string
     */
    public function getCartUrl(): string
    {
        switch ($this->getCheckoutType()) {
            case self::CHECKOUT_TYPE_CART_CHECKOUT:
                $route_id = 'shop-front-checkout';
                break;
            default:
                $route_id = 'shop-front-cartpage';
        }
        return RouterManager::obj()->getUrl($route_id);
    }

    /**
     * Возвращает текущий тип офорления заказа
     *
     * @return string
     */
    public function getCheckoutType(): string
    {
        if (ModuleManager::staticModuleExists('onepageorder') && ModuleManager::staticModuleEnabled('onepageorder')) {
            return self::CHECKOUT_TYPE_FOUR_STEP;
        }
        return $this['checkout_type'];
    }

    /**
     * Возвращает список обязательных по умолчанию полей адреса
     *
     * @return string[]
     */
    public function getRequiredAddressFields(): array
    {
        $fields = [];
        if ($this['require_country']) {
            $fields[] = 'country';
        }
        if ($this['require_region']) {
            $fields[] = 'region';
        }
        if ($this['require_city']) {
            $fields[] = 'city';
        }
        if ($this['require_zipcode']) {
            $fields[] = 'zipcode';
        }
        if ($this['require_address']) {
            $fields[] = 'address';
        }
        return $fields;
    }

    /**
     * Показывать поля адреса?
     *
     * @return boolean
     */
    function isCanShowAddress()
    {
        return ($this['require_country'] || $this['require_region'] || $this['require_city'] || $this['require_zipcode'] || $this['require_address'] || $this['show_contact_person']);
    }

    /**
     * Возвращает id страны по умолчанию
     *
     * @return int
     */
    public function getDefaultCountryId(): int
    {
        static $default_country_id;

        if ($default_country_id === null) {
            $region = new Region($this['default_region_id']);
            if (empty($region['id'])) {
                $default_country_id = 0;
            } else {
                while ($region['parent_id'] != 0) {
                    $region = $region->getParent();
                }
                $default_country_id = $region['id'];
            }
        }

        return $default_country_id;
    }

    /**
     * Возвращает id региона по умолчанию
     *
     * @return int
     */
    public function getDefaultRegionId(): int
    {
        static $default_region_id;

        if ($default_region_id === null) {
            $region = new Region($this['default_region_id']);
            if (empty($region['id']) || $region['parent_id'] == 0) {
                $default_region_id = 0;
            } else {
                if ($region['is_city']) {
                    $region = $region->getParent();
                }
                $default_region_id = $region['id'];
            }
        }

        return $default_region_id;
    }

    /**
     * Возвращает id города по умолчанию
     *
     * @return int
     */
    public function getDefaultCityId(): int
    {
        static $default_city_id;

        if ($default_city_id === null) {
            $region = new Region($this['default_region_id']);
            $default_city_id = ($region['id'] && $region['is_city']) ? $region['id'] : 0;
        }

        return $default_city_id;
    }

    /**
     * Функция срабатывает перед записью конфига
     *
     * @param string $flag - insert или update
     * @return void
     */
    function beforeWrite($flag)
    {
        if ($flag == self::UPDATE_FLAG) {
            //Проверим на соотвествие конструкции
            if (empty($this['generated_ordernum_mask']) || (mb_stripos($this['generated_ordernum_mask'], '{n}') === false)) {
                $this['generated_ordernum_mask'] = '{n}';
            }


        }
    }

    function afterWrite($flag)
    {
        if ($flag == self::UPDATE_FLAG) {
            if ($this->isModified('checkout_type')) {
                CacheCleaner::obj()->clean();
            }
        }
    }

    /**
     * Возвращает объект, отвечающий за работу с пользовательскими полями.
     *
     * @return UserFieldsManager
     */
    function getUserFieldsManager()
    {
        return new UserFieldsManager($this['userfields'], null, 'userfields');
    }

    /**
     * Возвращает значения свойств по-умолчанию
     *
     * @return array
     * @throws ModuleException
     */
    public static function getDefaultValues()
    {
        return parent::getDefaultValues() + [
            'tools' => [
                [
                    'url' => RouterManager::obj()->getAdminUrl('ajaxCalcProfit', [], 'shop-tools'),
                    'title' => t('Пересчитать доходность заказов'),
                    'description' => t('Рассчитывает доходность заказов на основе Закупочной цены товара. Показатель доходности может использоваться другими модулями.'),
                    'confirm' => t('Вы действительно хотите пересчитать доходность заказов?')
                ],
                [
                    'url' => RouterManager::obj()->getAdminUrl('showCashRegisterLog', [], 'shop-tools'),
                    'title' => t('Просмотреть лог запросов обмена информацией с кассами'),
                    'description' => t('Открывает в новом окне журнал обмена данными с кассами'),
                    'target' => '_blank',
                    'class' => ' ',
                ],
                [
                    'url' => RouterManager::obj()->getAdminUrl('deleteCashRegisterLog', [], 'shop-tools'),
                    'title' => t('Очистить лог запросов обмена информацией с кассами'),
                    'description' => t('Удаляет лог файл обмена информацией с кассами'),
                ],
                [
                    'url' => RouterManager::obj()->getAdminUrl(false, [], 'shop-substatusctrl'),
                    'title' => t('Настроить причины отмены заказа'),
                    'description' => t('Здесь вы сможете создать, изменить, удалить причину отмены заказа'),
                    'class' => ' '
                ],
                [
                    'url' => RouterManager::obj()->getAdminUrl(false, [], 'shop-actiontemplatesctrl'),
                    'title' => t('Перейти к списку шаблонов действий курьера'),
                    'description' => t('Позволяет настроить быстрые кнопки для отправки SMS сообщений в курьерском приложении'),
                    'target' => '_blank',
                    'class' => ' ',
                ],
            ]
            ];
    }
}
