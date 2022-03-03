<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Controller\Front;

use Catalog\Model\CurrencyApi;
use Catalog\Model\WareHouseApi;
use Main\Model\StatisticEvents;
use RS\Application\Application;
use RS\Application\Auth as AppAuth;
use RS\Config\Loader as ConfigLoader;
use RS\Controller\Front;
use RS\Event\Manager as EventManager;
use RS\Exception;
use RS\Helper\Tools as HelperTools;
use Shop\Model\AddressApi;
use Shop\Model\Cart;
use Shop\Model\DeliveryApi;
use Shop\Model\OrderApi;
use Shop\Model\Orm\Address;
use Shop\Model\Orm\Order;
use Shop\Model\PaymentApi;
use Shop\Model\UserStatusApi;
use Users\Config\File as UserConfig;
use Users\Model\Orm\User;

/**
 * Старый контроллер "Оформления заказа".
 * Используется для совместимости со старыми темами оформления
 */
class Checkout extends Front
{
    /** @var \Shop\Model\OrderApi $order_api */
    public $order_api;
    /** @var \Shop\Model\Orm\Order $order */
    public $order;

    /**
     * Инициализация контроллера
     */
    public function init()
    {
        $this->app->title->addSection(t('Оформление заказа'));
        $this->order = Order::currentOrder();
        $this->order_api = new OrderApi();

        $this->order->clearErrors();
        $this->view->assign('order', $this->order);
    }

    public function actionIndex()
    {
        $config = $this->getModuleConfig();
        $this->order->clear();

        //Замораживаем объект "корзина" и привязываем его к заказу
        $frozen_cart = Cart::preOrderCart(null);
        $frozen_cart->splitSubProducts();
        $frozen_cart->mergeEqual();

        $this->order->linkSessionCart($frozen_cart);
        $this->order->setCurrency(CurrencyApi::getCurrentCurrency());

        $this->order['ip'] = $_SERVER['REMOTE_ADDR'];
        $this->order['warehouse'] = 0;
        $this->order['expired'] = false;

        if (AppAuth::isAuthorize()) {
            $this->order['user_id'] = AppAuth::getCurrentUser()['id'];
        }

        $this->app->redirect($this->router->getUrl('shop-front-checkout', ['Act' => 'address']));
    }

    /**
     * Шаг 1. Установка адреса и контактов
     *
     * @return \RS\Controller\Result\Standard
     * @throws \RS\Exception
     */
    public function actionAddress()
    {
        if (!$this->order->getCart()) $this->app->redirect();
        $this->app->title->addSection(t('Адрес и контакты'));
        $config = $this->getModuleConfig();
        $user_config = ConfigLoader::byModule('users');

        //Добавим хлебные крошки
        $this->app->breadcrumbs
            ->addBreadCrumb(t('Корзина'), $this->router->getUrl('shop-front-cartpage'))
            ->addBreadCrumb(t('Адрес и контакты'));

        $logout = $this->url->request('logout', TYPE_BOOLEAN);
        $login = $this->url->request('ologin', TYPE_BOOLEAN); //Предварительная авторизация
        $addr_list = [];

        if ($logout) {
            AppAuth::logout();
            $this->app->redirect($this->router->getUrl('shop-front-checkout', ['Act' => 'address']));
        }

        if (AppAuth::isAuthorize()) {
            $this->order['user_type'] = null;
        } else {
            $this->order['__reg_phone']->setEnableVerification(true);
            if ($config['check_captcha'] && !$this->order['__reg_phone']->isEnabledVerification()) {
                $this->order['__code']->setEnable(true);
            }

            if (empty($this->order['user_type'])) {
                $this->order['user_type'] = $config['default_checkout_tab'];
                $this->order['reg_autologin'] = 1;
            }
        }

        $cart_data = $this->order['basket'] ? $this->order->getCart()->getCartData() : null;
        if ($cart_data === null || !count($cart_data['items']) || $cart_data['has_error'] || $this->order['expired']) {
            //Если корзина пуста или заказ уже оформлен или имеются ошибки в корзине, то выполняем redirect на главную сайта
            $this->app->redirect();
        }

        //Запрашиваем дополнительные поля формы заказа, если они определены в конфиге модуля
        $order_fields_manager = $this->order->getFieldsManager();
        $order_fields_manager->setValues($this->order['userfields_arr']);

        //Запрашиваем дополнительные поля формы регистрации, если они определены
        /** @var \Users\Config\File $users_config */
        $users_config = ConfigLoader::byModule('users');

        $reg_fields_manager = $users_config->getUserFieldsManager();
        $reg_fields_manager->setErrorPrefix('regfield_');
        $reg_fields_manager->setArrayWrapper('regfields');
        if (!empty($this->order['regfields'])) {
            $reg_fields_manager->setValues($this->order['regfields']);
        }

        if ($this->url->isPost()) { //POST
            $this->order['only_pickup_points'] = $this->request('only_pickup_points', TYPE_INTEGER, 0); //Флаг использования только самовывоза
            $this->order_api->addOrderExtraDataByStep($this->order, 'address', $this->url->request('order_extra', TYPE_ARRAY, [])); //Заносим дополнительные данные
            $sysdata = ['step' => 'address'];
            $work_fields = $this->order->useFields($sysdata + $_POST);

            if ($this->order['only_pickup_points']) { //Если только самовывоз то исключим поля
                $work_fields = array_diff($work_fields, ['addr_country', 'addr_country_id', 'addr_region', 'addr_region_id',
                    'addr_city', 'addr_city_id', 'addr_zipcode', 'addr_address', 'addr_extra', 'use_addr']);
                $this->order->setUseAddr(0);
            }

            $this->order['addr_region_id'] = null;
            $this->order['addr_city_id'] = null;
            $this->order->setCheckFields($work_fields);
            $this->order->checkData($sysdata, null, null, $work_fields);
            $this->order['userfields'] = serialize($this->order['userfields_arr']);

            //Авторизовываемся
            //@deprecated Оставлено для совместимости со старыми шаблонами. В будущих версиях будет удалено
            //Авторизация будет работать только при стандартном типе авторизации в настройках модуля Users
            if ($this->order['user_type'] == 'user' && !$logout && $user_config['type_auth'] == UserConfig::TYPE_AUTH_STANDARD) {
                if (!AppAuth::login($this->order['login'], $this->order['password'])) {
                    $this->order->addError(t('Неверный логин или пароль'), 'login');
                } else {
                    $this->order['user_type'] = '';
                    $this->order['__code']->setEnable(false);
                }
            }

            if (!$logout && !$login) {

                //Регистрируем пользователя, если нет ошибок            
                if (in_array($this->order['user_type'], ['person', 'company'])) {

                    if (!$this->order['reg_autologin']) {
                        if (strcmp($this->order['reg_openpass'], $this->order['reg_pass2'])) {
                            $this->order->addError(t('Пароли не совпадают'), 'reg_openpass');
                        }
                    }

                    //Сохраняем дополнительные сведения о пользователе
                    $uf_err = $reg_fields_manager->check($this->order['regfields']);
                    if (!$uf_err) {
                        foreach ($reg_fields_manager->getErrors() as $form => $errortext) {
                            $this->order->addError($errortext, $form); //Переносим ошибки в объект order
                        }
                    }

                    $new_user = new User();
                    $new_user->addLinkToAuthInError(true);
                    $new_user->enableRegistrationCheckers();
                    $new_user['__phone']->setEnableVerification(false); //Проверка будет идти в поле reg_phone в объекте order
                    $new_user['__data']->removeAllCheckers();
                    $new_user['changepass'] = 1;

                    $allow_fields = ['reg_name', 'reg_surname', 'reg_midname', 'reg_fio',
                        'reg_phone', 'reg_login', 'reg_e_mail', 'reg_openpass',
                        'reg_company', 'reg_company_inn'];

                    $reg_fields = array_intersect_key($this->order->getValues(), array_flip($allow_fields));

                    $new_user->getFromArray($reg_fields, 'reg_');
                    $new_user['data'] = $this->order['regfields'];
                    $new_user['is_company'] = (int)($this->order['user_type'] == 'company');

                    if ($this->order['reg_autologin']) {
                        $new_user['openpass'] = HelperTools::generatePassword(6);
                    }

                    if (!$new_user->validate()) {
                        foreach ($new_user->getErrorsByForm() as $form => $errors) {
                            $this->order->addErrors($errors, 'reg_' . $form);
                        }
                    }

                    if (!$this->order->hasError()) {
                        if ($new_user->insert()) {
                            AppAuth::setCurrentUser($new_user);
                            if (AppAuth::onSuccessLogin($new_user, true)) {
                                $this->order['user_type'] = ''; //Тип регитрации - не актуален после авторизации
                                $this->order['__code']->setEnable(false);
                            } else {
                                throw new Exception(AppAuth::getError());
                            }
                        } else {
                            throw new Exception(t('Не удалось создать пользователя.').$new_user->getErrorsStr());
                        }
                    }
                }

                //Если заказ без регистрации пользователя
                if ($this->order['user_type'] == 'noregister') {
                    //Получим данные
                    $this->order['user_fio'] = trim($this->request('user_fio', TYPE_STRING));
                    $this->order['user_email'] = trim($this->request('user_email', TYPE_STRING));
                    $this->order['user_phone'] = trim($this->request('user_phone', TYPE_STRING));

                    //Проверим данные
                    if (empty($this->order['user_fio'])) {
                        $this->order->addError(t('Укажите, пожалуйста, Ф.И.О.'), 'user_fio');
                    }
                    if ($config['require_email_in_noregister'] && !filter_var($this->order['user_email'], FILTER_VALIDATE_EMAIL)) {
                        $this->order->addError(t('Укажите, пожалуйста, E-mail'), 'user_email');
                    }

                    if ($config['require_phone_in_noregister']) {
                        if (empty($this->order['user_phone'])) {
                            $this->order->addError(t('Укажите, пожалуйста, Телефон'), 'user_phone');
                        } elseif (!preg_match('/^[0-9()\-\s+,]+$/', $this->order['user_phone'])) {
                            $this->order->addError(t('Неверно указан телефон'), 'user_phone');
                        }
                    }
                }

                //Сохраняем дополнительные сведения
                $uf_err = $order_fields_manager->check($this->order['userfields_arr']);
                if (!$uf_err) {
                    //Переносим ошибки в объект order
                    foreach ($order_fields_manager->getErrors() as $form => $errortext) {
                        $this->order->addError($errortext, $form);
                    }
                }

                //Сохраняем адрес
                if (!$this->order->hasError() && $this->order['use_addr'] == 0 && !$this->order['only_pickup_points']) {
                    $address = new Address();
                    $address->getFromArray($this->order->getValues(), 'addr_');
                    $current_user = AppAuth::getCurrentUser();
                    $address['user_id'] = $current_user['id'];
                    if ($address->insert()) {
                        $this->order->setUseAddr($address['id']);
                    }
                }

                //Все успешно
                if (!$this->order->hasError()) {

                    // Фиксация события "Указание адреса" для статистики
                    $this->fireStatistic(StatisticEvents::TYPE_SALES_FILL_ADDRESS);

                    $current_user = AppAuth::getCurrentUser();
                    $this->order['user_id'] = $current_user['id'];
                    $this->app->redirect($this->router->getUrl('shop-front-checkout', ['Act' => 'delivery']));
                }
            } //!logout && !login
        } else {
            //Установим адрес по умолчанию
            $this->order->setDefaultAddress();
        }

        $user = AppAuth::getCurrentUser();
        if (AppAuth::isAuthorize()) {
            //Получаем список адресов пользователя
            $address_api = new AddressApi();
            $address_api->setFilter('user_id', $user['id']);
            $address_api->setFilter('deleted', 0);
            $addr_list = $address_api->getList();

            // TODO описать событие 'checkout.useraddress.list' в документации
            $event_result = EventManager::fire('checkout.useraddress.list', [
                'addr_list' => $addr_list,
                'order' => $this->order,
                'user' => $user,
            ]);
            list($addr_list) = $event_result->extract();

            if (count($addr_list) > 0 && $this->order['use_addr'] === null) {
                $this->order->setUseAddr(reset($addr_list)['id']);
            }
        }

        if ($logout) {
            $this->order->clearErrors();
        }

        if ($login) { //Покажем только ошибки авторизации, остальные скроем
            $login_err = $this->order->getErrorsByForm('login');
            $this->order->clearErrors();
            if (!empty($login_err)) $this->order->addErrors($login_err, 'login');
        }

        //Посмотрим есть ли варианты для доставки по адресу и для самовывоза
        $have_to_address_delivery = DeliveryApi::isHaveToAddressDelivery($this->order);
        $have_pickup_points = DeliveryApi::isHavePickUpPoints($this->order);
        $this->view->assign([
            'have_to_address_delivery' => $have_to_address_delivery,
            'have_pickup_points' => $have_pickup_points,
        ]);

        if (!$this->url->isPost()) {
            if ($have_pickup_points && ($config['myself_delivery_is_default'] || !$have_to_address_delivery)) {
                $this->order['only_pickup_points'] = true;
            } else {
                $this->order['only_pickup_points'] = false;
            }
        }

        $this->order['password'] =     '';
        $this->order['reg_openpass'] = '';
        $this->order['reg_pass2'] =    '';

        $this->view->assign([
            'address_list' =>    $addr_list,
            'is_auth' =>         AppAuth::isAuthorize(),
            'order' =>           $this->order,
            'order_extra' =>     !empty($this->order['order_extra']) ? $this->order['order_extra'] : [],
            'user' =>            $user,
            'conf_userfields' => $order_fields_manager,
            'reg_userfields' =>  $reg_fields_manager,
            'user_config' => $user_config
        ]);

        return $this->result->setTemplate('checkout/address.tpl');
    }

    /**
     * Шаг 2. Выбор доставки
     */
    public function actionDelivery()
    {
        $config = $this->getModuleConfig();
        $delivery_api = new DeliveryApi();
        $delivery_list = $delivery_api->getCheckoutDeliveryList($this->user, $this->order);

        // Если доставка ещё не выбрана - выбираем первую доставку по умолчанию
        if (empty($this->order['delivery'])) {
            foreach ($delivery_list as $delivery) {

                if ($delivery['default']) {
                    $this->order['delivery'] = $delivery['id'];
                    break;
                }
            }
        }

        if ($config['hide_delivery']) { //Если нужно проскочить шаг доставка
            // Фиксация события "Выбран способ доставки" для статистики
            $this->fireStatistic(StatisticEvents::TYPE_SALES_SELECT_DELIVERY);

            $this->app->redirect($this->router->getUrl('shop-front-checkout', ['Act' => 'payment']));
        }

        $this->app->title->addSection(t('Выбор доставки'));

        //Добавим хлебные крошки
        $this->app->breadcrumbs
            ->addBreadCrumb(t('Корзина'), $this->router->getUrl('shop-front-cartpage'))
            ->addBreadCrumb(t('Адрес и контакты'), $this->router->getUrl('shop-front-checkout', [
                'Act' => 'address'
            ]))
            ->addBreadCrumb(t('Выбор доставки'));

        if ($this->order['expired'] || !$this->order->getCart()) $this->app->redirect();

        //Если есть доставка, и она одна, и выбран только самовывоз, то перейдём на склады
        if (!empty($delivery_list) && count($delivery_list) == 1 && $this->order['only_pickup_points']) { //Если доставка всего одна и выбран только самовывоз
            // Фиксация события "Выбран способ доставки" для статистики
            $this->fireStatistic(StatisticEvents::TYPE_SALES_SELECT_DELIVERY);

            $orderdelivery = reset($delivery_list);
            $this->order['delivery'] = $orderdelivery['id'];
            $this->app->redirect($this->router->getUrl('shop-front-checkout', ['Act' => 'warehouses']));
        }

        $this->view->assign([
            'delivery_list' => $delivery_list
        ]);

        if ($this->url->isPost()) {
            $this->order_api->addOrderExtraDataByStep($this->order, 'delivery', $this->url->request('order_extra', TYPE_ARRAY, [])); //Заносим дополнительные данные

            //Проверим параметры выбора доставки
            $sysdata = ['step' => 'delivery'];
            $work_fields = $this->order->useFields($sysdata + $this->url->getSource(POST));
            $this->order->setCheckFields($work_fields);
            if ($this->order->checkData($sysdata, null, null, $work_fields)) {

                // Фиксация события "Выбран способ доставки" для статистики
                $this->fireStatistic(StatisticEvents::TYPE_SALES_SELECT_DELIVERY);

                $delivery = $this->order->getDelivery(); //Выбранная доставка
                $delivery_extra = $this->request('delivery_extra', TYPE_ARRAY, false);
                if ($delivery_extra) {
                    $this->order->addExtraKeyPair('delivery_extra', $delivery_extra);
                }

                if ($delivery->getTypeObject()->isMyselfDelivery()) { //Если самовывоз и складов больше одного
                    $this->app->redirect($this->router->getUrl('shop-front-checkout', ['Act' => 'warehouses']));
                } else {
                    $this->app->redirect($this->router->getUrl('shop-front-checkout', ['Act' => 'payment']));
                }
            }
        }

        $this->view->assign([
            'order_extra' => !empty($this->order['order_extra']) ? $this->order['order_extra'] : [],
        ]);

        return $this->result->setTemplate('checkout/delivery.tpl');
    }

    /**
     * Шаг 2.2 Страница выбора склада откуда забирать
     * Используется только когда складов более одного
     * и выбран способ доставки "Самовывоз"
     *
     */
    public function actionWarehouses()
    {
        $this->app->title->addSection(t('Выбор склада для забора товара'));

        $warehouses = WareHouseApi::getPickupWarehousesPoints(); //Получим пункты самовывоза

        if (count($warehouses) < 2) {
            if (count($warehouses) == 1) {
                //Если склад только один, то пропускаем выбор склада
                $this->order['warehouse'] = $warehouses[0]['id'];
            }
            $this->app->redirect($this->router->getUrl('shop-front-checkout', ['Act' => 'payment']));
        }

        //Добавим хлебные крошки
        $this->app->breadcrumbs
            ->addBreadCrumb(t('Корзина'), $this->router->getUrl('shop-front-cartpage'))
            ->addBreadCrumb(t('Адрес и контакты'), $this->router->getUrl('shop-front-checkout', [
                'Act' => 'address'
            ]))
            ->addBreadCrumb(t('Выбор доставки'), $this->router->getUrl('shop-front-checkout', [
                'Act' => 'delivery'
            ]))
            ->addBreadCrumb(t('Выбор склада'));

        if ($this->order['expired'] || !$this->order->getCart()) $this->app->redirect();

        $this->view->assign([
            'warehouses_list' => $warehouses
        ]);

        if ($this->url->isPost()) {
            $this->order_api->addOrderExtraDataByStep($this->order, 'warehouses', $this->url->request('order_extra', TYPE_ARRAY, [])); //Заносим дополнительные данные
            $sysdata = ['step' => 'warehouses'];
            $work_fields = $this->order->useFields($sysdata + $this->url->getSource(POST));
            $this->order->setCheckFields($work_fields);
            if ($this->order->checkData($sysdata, null, null, $work_fields)) {
                $this->app->redirect($this->router->getUrl('shop-front-checkout', ['Act' => 'payment']));
            }
        }

        $this->view->assign([
            'order_extra' => !empty($this->order['order_extra']) ? $this->order['order_extra'] : [],
        ]);

        return $this->result->setTemplate('checkout/warehouse.tpl');
    }

    /**
     * Шаг 3. Выбор оплаты
     */
    public function actionPayment()
    {
        $config = $this->getModuleConfig();
        $this->app->title->addSection(t('Выбор оплаты'));

        //Добавим хлебные крошки
        $this->app->breadcrumbs
            ->addBreadCrumb(t('Корзина'), $this->router->getUrl('shop-front-cartpage'))
            ->addBreadCrumb(t('Адрес и контакты'), $this->router->getUrl('shop-front-checkout', [
                'Act' => 'address'
            ]));
        if (!$config['hide_delivery']) {
            $this->app->breadcrumbs->addBreadCrumb(t('Выбор доставки'), $this->router->getUrl('shop-front-checkout', [
                'Act' => 'delivery'
            ]));
        }
        $this->app->breadcrumbs->addBreadCrumb(t('Выбор оплаты'));

        if ($this->order['expired'] || !$this->order->getCart()) $this->app->redirect();

        $pay_api = new PaymentApi();
        $payment_list = $pay_api->getCheckoutPaymentList($this->user, $this->order);

        $this->view->assign([
            'pay_list' => $payment_list
        ]);

        //Найдём оплату по умолчанию, если оплата не была задана раннее
        if (!$this->order['payment']) {
            $pay_api->setFilter('default_payment', 1);
            $default_payment = $pay_api->getFirst($this->order);
            if ($default_payment) {
                $this->order['payment'] = $default_payment['id'];
            }
        }

        if ($config['hide_payment']) { //Если нужно проскочить шаг оплата
            $this->app->redirect($this->router->getUrl('shop-front-checkout', ['Act' => 'confirm']));
        }

        if ($this->url->isPost()) {
            $this->order_api->addOrderExtraDataByStep($this->order, 'pay', $this->url->request('order_extra', TYPE_ARRAY, [])); //Заносим дополнительные данные
            $sysdata = ['step' => 'pay'];
            $work_fields = $this->order->useFields($sysdata + $_POST);
            $this->order->setCheckFields($work_fields);
            if ($this->order->checkData($sysdata, null, null, $work_fields)) {

                // Фиксация события "Выбран способ оплаты" для статистики
                $this->fireStatistic(StatisticEvents::TYPE_SALES_SELECT_PAYMENT_METHOD);

                $this->app->redirect($this->router->getUrl('shop-front-checkout', ['Act' => 'confirm']));
            }
        }

        $this->view->assign([
            'order_extra' => !empty($this->order['order_extra']) ? $this->order['order_extra'] : [],
        ]);

        return $this->result->setTemplate('checkout/payment.tpl');
    }

    /**
     * Шаг 4. Подтверждение заказа
     */
    public function actionConfirm()
    {
        $config = $this->getModuleConfig();
        $this->app->title->addSection(t('Подтверждение заказа'));

        if ($this->order['expired'] || !$this->order->getCart()) $this->app->redirect();

        $basket = $this->order->getCart();
        EventManager::fire('checkout.confirm', [
            'order' => $this->order,
            'cart' => $basket
        ]);

        //Добавим хлебные крошки
        $this->app->breadcrumbs
            ->addBreadCrumb(t('Корзина'), $this->router->getUrl('shop-front-cartpage'))
            ->addBreadCrumb(t('Адрес и контакты'), $this->router->getUrl('shop-front-checkout', [
                'Act' => 'address'
            ]));
        if (!$config['hide_delivery']) {
            $this->app->breadcrumbs->addBreadCrumb(t('Выбор доставки'), $this->router->getUrl('shop-front-checkout', [
                'Act' => 'delivery'
            ]));
        }
        if (!$config['hide_payment']) {
            $this->app->breadcrumbs->addBreadCrumb(t('Выбор оплаты'), $this->router->getUrl('shop-front-checkout', [
                'Act' => 'payment'
            ]));
        }
        $this->app->breadcrumbs->addBreadCrumb(t('Подтверждение заказа'));

        $this->view->assign([
            'cart' => $basket
        ]);

        if ($this->url->isPost()) {
            $this->order_api->addOrderExtraDataByStep($this->order, 'confirm', $this->url->request('order_extra', TYPE_ARRAY, [])); //Заносим дополнительные данные

            $this->order->clearErrors();
            if ($config['require_license_agree'] && !$this->url->post('iagree', TYPE_INTEGER)) {
                $this->order->addError(t('Подтвердите согласие с условиями предоставления услуг'));
            }

            $sysdata = ['step' => 'confirm'];
            $work_fields = $this->order->useFields($sysdata + $_POST);

            $this->order->setCheckFields($work_fields);
            if (!$this->order->hasError() && $this->order->checkData($sysdata, null, null, $work_fields)) {
                $this->order['is_payed'] = 0;
                $this->order['delivery_new_query'] = 1;
                $this->order['payment_new_query'] = 1;

                //Создаем заказ в БД
                if ($this->order->insert()) {
                    // Фиксация события "Подтверждение заказа" для статистики
                    $this->fireStatistic(StatisticEvents::TYPE_SALES_CONFIRM_ORDER);

                    $this->order['expired'] = true; //заказ уже оформлен. больше нельзя возвращаться к шагам.
                    Cart::currentCart()->clean(); //Очищаем корзиу
                    $this->app->redirect($this->router->getUrl('shop-front-checkout', ['Act' => 'finish']));
                }
            }
        }

        $this->view->assign([
            'order_extra' => !empty($this->order['order_extra']) ? $this->order['order_extra'] : [],
        ]);

        return $this->result->setTemplate('checkout/confirm.tpl');
    }

    /**
     * Шаг 5. Создание заказа
     */
    public function actionFinish()
    {
        $this->app->title->addSection(t('Заказ №%0 успешно оформлен', [$this->order['order_num']]));

        //Добавим хлебные крошки
        $this->app->breadcrumbs
            ->addBreadCrumb(t('Корзина'))
            ->addBreadCrumb(t('Адрес и контакты'))
            ->addBreadCrumb(t('Выбор доставки'))
            ->addBreadCrumb(t('Выбор оплаты'))
            ->addBreadCrumb(t('Завершение заказа'));

        $this->view->assign([
            'cart' => $this->order->getCart(),
            'alt' => 'alt',
            'statuses' => UserStatusApi::getStatusIdByType()
        ]);

        return $this->result->setTemplate('checkout/finish.tpl');
    }

    /**
     * Выполняет пользовательский статический метод у типа оплаты или доставки,
     * если таковой есть у типа доставки
     */
    public function actionUserAct()
    {
        $module   = $this->request('module',TYPE_STRING, 'Shop'); //Имя модуля
        $type_obj = $this->request('typeObj',TYPE_INTEGER,0);     //0 - доставка (DeliveryType), 1 - оплата (PaymentType)
        $type_id  = $this->request('typeId',TYPE_INTEGER,0);      //id доставки или оплаты
        $class    = $this->request('class',TYPE_STRING,false);    //Класс для обращения
        $act      = $this->request('userAct',TYPE_STRING,false);  //Статический метод который нужно вызвать 
        $params   = $this->request('params',TYPE_ARRAY, []);  //Дополнительные параметры для передачи в метод

        if ($module && $act && $class) {
            $typeobj = "DeliveryType";
            if ($type_obj == 1) {
                $typeobj = "PaymentType";
            }

            $delivery = '\\' . $module . '\Model\\' . $typeobj . '\\' . $class;
            $data = $delivery::$act($this->order, $type_id, $params);

            if (!$this->order->hasError()) {
                return $this->result->setSuccess(true)->addSection('data', $data);
            } else {
                return $this->result->setSuccess(false)->addEMessage($this->order->getErrorsStr());
            }
        } else {
            return $this->result->setSuccess(false)->addEMessage(t('Не установлен метод или объект доставки или оплаты'));
        }
    }

    /**
     * Удаление адреса при оформлении заказа
     */
    public function actionDeleteAddress()
    {
        $id = $this->url->request('id', TYPE_INTEGER, 0); //id адреса доставки
        if ($id) {
            $address = new Address($id);
            if ($address['user_id'] == $this->user['id']) {
                $address['deleted'] = 1;
                $address->update();
                return $this->result->setSuccess(true);
            }
        }
        return $this->result->setSuccess(false);
    }

    /**
     * Подбирает город по совпадению в переданной строке
     */
    public function actionSearchCity()
    {
        $query       = $this->request('term', TYPE_STRING, false);
        $region_id   = $this->request('region_id', TYPE_INTEGER, false);
        $country_id  = $this->request('country_id', TYPE_INTEGER, false);

        if ($query !== false && $this->url->isAjax()) { //Если задана поисковая фраза и это аякс
            $cities = $this->order_api->searchCityByRegionOrCountry($query, $region_id, $country_id);

            $result_json = [];
            if (!empty($cities)) {
                foreach ($cities as $city) {
                    $region = $city->getParent();
                    $country = $region->getParent();
                    $result_json[] = [
                        'value'      => $city['title'],
                        'label'      => preg_replace("%($query)%iu", '<b>$1</b>', $city['title']),
                        'id'         => $city['id'],
                        'zipcode'    => $city['zipcode'],
                        'region_id'  => $region['id'],
                        'country_id' => $country['id']
                    ];
                }
            }
            
            $this->wrapOutput(false);
            $this->app->headers->addHeader('content-type', 'application/json');
            return json_encode($result_json);
        }
        return null;
    }

    /**
     * Вызывает событие для воронки заказа только один раз в рамках оформления заказа
     * @param string $type
     */
    protected function fireStatistic($type)
    {
        if (!isset($this->order['cache_statistic'.$type])) {
            EventManager::fire('statistic', ['type' => $type]);
            $this->order['cache_statistic'.$type] = true;
        }
    }
}
