<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/ declare(strict_types=1);

namespace Shop\Controller\Block;

use Catalog\Model\CurrencyApi;
use Catalog\Model\WareHouseApi;
use Main\Model\DaDataApi;
use RS\Application\Application;
use RS\Application\Auth;
use RS\Config\Loader as ConfigLoader;
use RS\Controller\Result\Standard as ResultStandard;
use RS\Controller\StandartBlock;
use RS\Db\Exception as DbException;
use RS\Event\Manager as EventManager;
use RS\Exception as RSException;
use RS\Helper\Tools as HelperTools;
use RS\Orm\Type;
use RS\Router\Manager as RouterManager;
use RS\View\Engine as ViewEngine;
use Shop\Config\File as ShopConfig;
use Shop\Model\AddressApi;
use Shop\Model\Cart;
use Shop\Model\DeliveryApi;
use Shop\Model\OrderApi;
use Shop\Model\Orm\Address;
use Shop\Model\Orm\Order;
use Shop\Model\PaymentApi;
use Users\Model\Orm\User;
use Users\Model\OrmType as UsersOrmType;

/**
 * Блок контроллер оформления заказа
 */
class Checkout extends StandartBlock
{
    protected static $controller_title = 'Блок оформления заказа';
    protected static $controller_description = 'Позволяет оформить заказ';
    protected static $exclude_fields = ['id', 'site_id', 'order_num', 'step', 'basket', 'user_id', 'extra', 'special_params'];

    protected $action_var = 'action';
    /** @var Order */
    protected $order;
    /** @var OrderApi */
    protected $order_api;
    /** @var AddressApi */
    protected $address_api;
    /** @var DeliveryApi */
    protected $delivery_api;
    /** @var PaymentApi */
    protected $payment_api;
    /** @var ShopConfig */
    protected $shop_config;
    /** @var bool */
    protected $is_cart_checkout;
    /** @var array */
    protected $default_params = [
        'indexTemplate' => 'blocks/checkout/checkout.tpl',
    ];

    public function init()
    {
        $this->order = Order::currentOrder();
        $this->order_api = new OrderApi();
        $this->address_api = new AddressApi();
        $this->delivery_api = new DeliveryApi();
        $this->payment_api = new PaymentApi();
        $this->shop_config = ConfigLoader::byModule('shop');
        $this->is_cart_checkout = $this->shop_config->getCheckoutType() == ShopConfig::CHECKOUT_TYPE_CART_CHECKOUT;

        $this->order->clearErrors();
        $user_id = (Auth::isAuthorize()) ? (int)Auth::getCurrentUser()['id'] : 0;
        $this->order->setUserId($user_id);
    }

    /**
     * @return ResultStandard
     * @throws DbException
     * @throws RSException
     * @throws \SmartyException
     */
    public function actionIndex()
    {
        if ($this->is_cart_checkout) {
            $this->freezeSessionCart();
        }

        if ($this->hasCriticalErrors()) {
            Application::getInstance()->redirect();
        }

        unset($this->order['id']);
        unset($this->order['order_num']);
        unset($this->order['user_delivery_cost']);
        $this->order->clearErrors();
        $this->order->setSpecialParam(Order::SPECIAL_CHECKOUT_FORBID_VALIDATE, true);
        $this->correctOrderData();
        $this->fireConfirmEvent();

        $this->view->assign([
            'order' => $this->order,
            'cart' => $this->order->getCart(),
            'shop_config' => $this->shop_config,
            'blocks' => $this->getBlocks(true),
        ]);

        return $this->result->setTemplate($this->getParam('indexTemplate'));
    }

    /**
     * Перепривязывает корзину к заказу
     *
     * @return ResultStandard
     * @throws DbException
     * @throws RSException
     * @throws \SmartyException
     */
    public function actionRelinkCart()
    {
        $this->freezeSessionCart();

        if ($this->hasCriticalErrors()) {
            return $this->result->setSuccess(false)->addSection('redirect', RouterManager::obj()->getUrl('main.index'));
        }

        $this->correctAndCheckOrder();

        return $this->result->setSuccess(true)->addSection('blocks', $this->getBlocks());
    }

    /**
     * Обновление блоков оформления заказа
     *
     * @return ResultStandard
     * @throws \SmartyException
     * @throws RSException
     */
    public function actionUpdate()
    {
        if ($this->hasCriticalErrors()) {
            return $this->result->setSuccess(false)->addSection('redirect', RouterManager::obj()->getUrl('main.index'));
        }

        $step_condition = ['step' => ['address', 'delivery', 'pay', 'confirm']];
        $work_fields = $this->order->useFields($step_condition + $_POST);
        $this->order->setCheckFields($work_fields);

        $this->order->fillFromPost([], null, null, $work_fields);
        if ($delivery_extra = $this->request('delivery_extra', TYPE_ARRAY, false)) {
            $this->order->addExtraKeyPair('delivery_extra', $delivery_extra);
        }
        $this->order->clearAddressCache();
        $this->correctAndCheckOrder();

        return $this->result->setSuccess(true)->addSection('blocks', $this->getBlocks());
    }

    /**
     * Корректирует данные заказа и проверяет наличие ошибок
     *
     * @return void
     */
    protected function correctAndCheckOrder(): void
    {
        $this->correctOrderData();
        $this->fireConfirmEvent();
        $this->order->clearErrors();
        if (!$this->order->getSpecialParam(Order::SPECIAL_CHECKOUT_FORBID_VALIDATE)) {
            /** @var Type\Captcha $code_field */
            $code_field = $this->order['__code'];
            $code_field->setEnable(false);
            $this->validateOrder();
        }
    }

    /**
     * Создаёт заказ
     *
     * @return ResultStandard
     * @throws \SmartyException
     * @throws RSException
     */
    public function actionCreateOrder()
    {
        if ($this->hasCriticalErrors()) {
            return $this->result->setSuccess(false)->addSection('redirect', RouterManager::obj()->getUrl('main.index'));
        }

        $this->order->setSpecialParam(Order::SPECIAL_CHECKOUT_FORBID_VALIDATE, false);
        $this->order->fillFromPost([], null, null, null, self::$exclude_fields);
        if ($delivery_extra = $this->request('delivery_extra', TYPE_ARRAY, false)) {
            $this->order->addExtraKeyPair('delivery_extra', $delivery_extra);
        }
        $this->order->clearAddressCache();
        $this->correctOrderData();
        $this->fireConfirmEvent();
        $this->order->clearErrors();
        $this->validateOrder();

        if (!$this->order->hasError()) {
            $this->createOrderNewUserAndAddress();
        }

        if (!$this->order->hasError()) {
            $this->order['is_payed'] = 0;
            $this->order['delivery_new_query'] = 1;

            if ($this->order->insert()) {
                $this->order['expired'] = true;
                Cart::currentCart()->clean();

                $redirect = $this->router->getUrl('shop-front-checkout', ['Act' => 'finish']);
                return $this->result->setSuccess(true)->addSection('redirect', $redirect);
            }
        }

        return $this->result->setSuccess(false)->addSection('blocks', $this->getBlocks(true));
    }

    /**
     * Создаёт нового пользователя и адрес на основе данных заказа
     * При неудаче добавляет в заказ ошибки
     *
     * @return void
     */
    protected function createOrderNewUserAndAddress(): void
    {
        $new_user = ($this->order['user_id'] == 0 && $this->order['register_user']) ? $this->createOrderRegisterUser() : null;

        if ($this->order['use_addr'] == 0) {
            $new_address = $this->order->getAddress();
            $new_address['user_id'] = $this->order->getUser()['id'];
            if ($new_address->insert()) {
                $this->order->setUseAddr($new_address['id']);
            } else {
                $this->order->addErrors($new_address->getNonFormErrors(), 'addr');
                foreach ($new_address->getErrorsByForm() as $form => $errors) {
                    $this->order->addErrors($errors, "addr_$form");
                }
                if ($new_user) {
                    $new_user->delete();
                    $this->order->setUserId(0);
                }
            }
        }
    }

    /**
     * Регистриррует нового пользователя на основе данных заказа
     * При успехе возвращает нового пользователя, иначе добавляет в заказ ошибки
     *
     * @return User|null
     */
    protected function createOrderRegisterUser(): ?User
    {
        $new_user = new User();
        $new_user->getFromArray($this->order->getValues(), 'user_');
        $new_user['e_mail'] = $new_user['email'];
        $new_user['data'] = $this->order['regfields'];
        $new_user['changepass'] = 1;
        if ($this->order['user_autologin']) {
            $new_user['openpass'] = HelperTools::generatePassword(6);
        }

        if ($new_user->insert()) {
            $this->order->setUserId($new_user['id']);

            Auth::setCurrentUser($new_user);
            if (!Auth::onSuccessLogin($new_user, true)) {
                $this->order->addErrors(Auth::getError(), 'user');
            }

            return $new_user;
        } else {
            $this->order->addErrors($new_user->getNonFormErrors(), 'user');
            foreach ($new_user->getErrorsByForm() as $form => $errors) {
                $field_name = ($form == 'e_mail') ? 'user_email' : "user_$form";
                $this->order->addErrors($errors, $field_name);
            }

            return null;
        }
    }

    /**
     * Удаляет сохранённый адрес
     *
     * @return ResultStandard
     * @throws RSException
     * @throws \SmartyException
     */
    public function actionDeleteAddress()
    {
        if ($this->hasCriticalErrors()) {
            return $this->result->setSuccess(false)->addSection('redirect', RouterManager::obj()->getUrl('main.index'));
        }

        $id = $this->url->request('id', TYPE_INTEGER, 0);
        $is_success = false;
        if ($id) {
            $address = new Address($id);
            if ($address['user_id'] == $this->user['id']) {
                $address['deleted'] = 1;
                $address->update();
                $this->correctOrderData();
                $is_success = true;
            }
        }
        return $this->result->setSuccess($is_success)->addSection('blocks', $this->getBlocks());
    }

    /**
     * Возвращает подсказки для поля "адрес"
     *
     * @return string
     */
    public function actionAddressAutocomplete(): string
    {
        $term = $this->url->request('term', TYPE_STRING);
        $address = $this->order->getAddress();

        $result = [];
        if ($term) {
            $query = $address->getCountry()['title'] . ', ' . $address->getRegion()['title'] . ', ' . $address->getCity()['title'] . ', ' . $term;

            foreach (DaDataApi::getInstance()->getAddressSuggestion($query) as $item) {
                if (!empty($item['data']['street_with_type'])) {
                    $label_parts = [];
                    $data_parts = [];
                    if (!empty($item['data']['city_district_with_type'])) {
                        $label_parts[] = $item['data']['city_district_with_type'];
                    }
                    $label_parts[] = $item['data']['street_with_type'];
                    $data_parts['street'] = $item['data']['street_with_type'];
                    if (!empty($item['data']['house'])) {
                        $label_parts[] = "{$item['data']['house_type']} {$item['data']['house']}";
                        $data_parts['house'] = $item['data']['house'];
                    }
                    if (!empty($item['data']['block'])) {
                        $label_parts[] = "{$item['data']['block_type']} {$item['data']['block']}";
                        $data_parts['block'] = $item['data']['block'];
                    }
                    if (!empty($item['data']['flat'])) {
                        $label_parts[] = "{$item['data']['flat_type']} {$item['data']['flat']}";
                        $data_parts['flat'] = $item['data']['flat'];
                    }
                    if (!empty($item['data']['postal_code'])) {
                        $data_parts['zipcode'] = $item['data']['postal_code'];
                    }

                    $result[] = [
                        'label' => implode(', ', $label_parts),
                        'data' => $data_parts,
                    ];
                }
            }
        }
        return json_encode($result);
    }

    /**
     * Замораживает текущую корзину и привязывает её к заказу
     *
     * @return void
     * @throws DbException
     * @throws RSException
     */
    protected function freezeSessionCart(): void
    {
        $frozen_cart = Cart::preOrderCart();
        $frozen_cart->splitSubProducts();
        $frozen_cart->mergeEqual();

        $this->order->linkSessionCart($frozen_cart);
        $this->order->setCurrency(CurrencyApi::getCurrentCurrency());
        $this->order->resetOrderForCheckout();
    }

    /**
     * Проверяет заказ на наличие ошибок, при которых дальнейшее оформление заказа невозможно
     *
     * @return bool
     * @throws RSException
     */
    protected function hasCriticalErrors()
    {
        /** @var ShopConfig $shop_config */
        $shop_config = ConfigLoader::byModule('shop');
        $cart_data = $this->order['basket'] ? $this->order->getCart()->getCartData() : null;

        if ($cart_data === null || !count($cart_data['items']) || $this->order['expired']) {
            return true;
        }
        if ($shop_config->getCheckoutType() != ShopConfig::CHECKOUT_TYPE_CART_CHECKOUT && $cart_data['has_error']) {
            return true;
        }
        return false;
    }

    /**
     * Запускает событие для дополнительной проверки/корректировки заказа
     *
     * @return void
     */
    protected function fireConfirmEvent()
    {
        EventManager::fire('checkout.confirm', [
            'order' => $this->order,
            'cart' => $this->order->getCart(),
        ]);
    }

    /**
     * Возвращает шаблоны блоков
     *
     * @param bool $add_captcha - добавить блок капчи
     * @return array
     * @throws RSException
     * @throws \SmartyException
     */
    protected function getBlocks(bool $add_captcha = false)
    {
        $bloks = [
            'user' => $this->getUserBlock(),
            'city' => $this->getCityBlock(),
            'address' => $this->getAddressBlock(),
            'delivery' => $this->getDeliveryBlock(),
            'payment' => $this->getPaymentBlock(),
            'products' => $this->getProductsBlock(),
            'total' => $this->getTotalBlock(),
            'comment' => $this->getCommentBlock(),
        ];
        if ($add_captcha) {
            $bloks['captcha'] = $this->getCaptchaBlock();
        }

        return $bloks;
    }

    /**
     * Получает шаблон блока с комментарием, согласием на обработку персональных данных, отображением ошибок
     *
     * @return string
     * @throws RSException
     * @throws \SmartyException
     */
    protected function getCommentBlock()
    {
        $view = new ViewEngine();
        $view->assign($this->getBlocksViewData());
        return $view->fetch("%shop%/blocks/checkout/comment_block.tpl");
    }

    /**
     * Получает шаблон блок с пользователем
     *
     * @return string
     * @throws \SmartyException
     * @throws RSException
     */
    protected function getUserBlock()
    {
        $view = new ViewEngine();
        $view->assign($this->getBlocksViewData());
        return $view->fetch("%shop%/blocks/checkout/user_block.tpl");
    }

    /**
     * Получает шаблон блок с городом
     *
     * @return string
     * @throws \SmartyException
     * @throws RSException
     */
    protected function getCityBlock()
    {
        $view = new ViewEngine();
        $view->assign($this->getBlocksViewData());
        return $view->fetch('%shop%/blocks/checkout/city_block.tpl');
    }

    /**
     * Получает шаблон блок с адресами
     *
     * @return string
     * @throws \SmartyException
     * @throws RSException
     */
    protected function getAddressBlock()
    {
        $view = new ViewEngine();
        $view->assign($this->getBlocksViewData());
        return $view->fetch('%shop%/blocks/checkout/address_block.tpl');
    }

    /**
     * Получает шаблон блок с доставками
     *
     * @return string
     * @throws \SmartyException
     * @throws RSException
     */
    protected function getDeliveryBlock()
    {
        if (!$this->shop_config['hide_delivery']) {
            $view = new ViewEngine();
            $view->assign($this->getBlocksViewData());
            return $view->fetch("%shop%/blocks/checkout/delivery_block.tpl");
        }
        return '';
    }

    /**
     * Получает шаблон блок с оплатами
     *
     * @return string
     * @throws \SmartyException
     * @throws RSException
     */
    protected function getPaymentBlock()
    {
        if (!$this->shop_config['hide_payment']) {
            $view = new ViewEngine();
            $view->assign($this->getBlocksViewData());
            return $view->fetch("%shop%/blocks/checkout/payment_block.tpl");
        }
        return '';
    }

    /**
     * Получает шаблон блок с товарами
     *
     * @return string
     * @throws \SmartyException
     * @throws RSException
     */
    protected function getProductsBlock()
    {
        $view = new ViewEngine();
        $view->assign($this->getBlocksViewData());
        return $view->fetch("%shop%/blocks/checkout/products_block.tpl");
    }

    /**
     * Получает шаблон блок с итогом
     *
     * @return string
     * @throws \SmartyException
     * @throws RSException
     */
    protected function getTotalBlock()
    {
        $view = new ViewEngine();
        $view->assign($this->getBlocksViewData());
        return $view->fetch("%shop%/blocks/checkout/total_block.tpl");
    }

    /**
     * Получает шаблон блок с капчей
     *
     * @return string
     * @throws \SmartyException
     * @throws RSException
     */
    protected function getCaptchaBlock()
    {
        $view = new ViewEngine();
        $view->assign($this->getBlocksViewData());
        return $view->fetch("%shop%/blocks/checkout/captcha_block.tpl");
    }

    /**
     * Возвращает массив данных для шаблонов
     *
     * @return array
     * @throws RSException
     */
    protected function getBlocksViewData(): array
    {
        static $view_data;
        if ($view_data === null) {
            $user = $this->order->getUser();
            $cart = $this->order->getCart();

            $view_data = [
                'order' => $this->order,
                'cart' => $cart,
                'user' => $user,
                'address' => $this->order->getAddress(),
                'is_auth' => Auth::isAuthorize(),
                'shop_config' => $this->shop_config,
                'this_controller' => $this,
                'cart_data' => $cart->getCartData(),
                'address_list' => $this->address_api->getCheckoutUserAddresses($this->order, $user, true),
                'delivery_list' => $this->delivery_api->getCheckoutDeliveryList($user, $this->order),
                'warehouses' => WareHouseApi::getPickupWarehousesPoints(),
                'delivery_extra' => $this->order->getExtraKeyPair('delivery_extra'),
                'payment_list' => $this->payment_api->getCheckoutPaymentList($user, $this->order),
                'order_user_fields' => $this->order->getFieldsManager()->setValues($this->order['userfields_arr']),
                'user_user_fields' => $this->order->getUser()->getUserFieldsManager()->setArrayWrapper('regfields')->setValues($this->order['regfields'])->setErrorPrefix('regfields_'),
            ];
        }
        return $view_data;
    }

    /**
     * Проверяет текущий заказ на наличие ошибок
     *
     * @return void
     */
    protected function validateOrder(): void
    {
        $this->order->validate();

        if ($this->shop_config['require_license_agree'] && !$this->url->post('license_agree', TYPE_INTEGER)) {
            $this->order->addError(t('Подтвердите согласие с условиями предоставления услуг'));
        }

        $this->validateOrderDelivery();

        if ($this->order['use_addr'] == 0) {
            $new_address = $this->order->getAddress();
            if (!$new_address->validate()) {
                $this->order->addErrors($new_address->getErrors(), 'address');
            }
        }

        $this->validateOrderUser();

        $order_user_fields = $this->order->getFieldsManager();
        if (!$order_user_fields->check($this->order['userfields_arr'])) {
            foreach ($order_user_fields->getErrors() as $key => $error) {
                $this->order->addError($error, $key);
            }
        }
    }

    /**
     * Проверяет данные доставки на наличие ошибок
     *
     * @return void
     */
    protected function validateOrderDelivery(): void
    {
        $delivery_type_object = $this->order->getDelivery()->getTypeObject();
        $has_address_error = false;
        foreach ($delivery_type_object->getRequiredAddressFields() as $field_name) {
            if ($this->order->getErrorsByForm("addr_$field_name")) {
                $has_address_error = true;
                break;
            }
        }
        if (!$has_address_error) {
            if ($delivery_type_object->hasSelectError($this->order)) {
                $this->order->addErrors([$delivery_type_object->getSelectError($this->order)], 'delivery_checkout');
            }
            if ($delivery_type_object->hasCheckoutError($this->order)) {
                $this->order->addErrors([$delivery_type_object->getCheckoutError($this->order)], 'delivery_checkout');
            }
        }
    }

    /**
     * Проверяет данные пользователя на наличие ошибок
     *
     * @return void
     */
    protected function validateOrderUser(): void
    {
        if ($this->order['user_id'] == 0) {
            if ($this->order['register_user']) {
                $new_user = new User();
                $new_user->getFromArray($this->order->getValues(), 'user_');
                $new_user['e_mail'] = $new_user['email'];
                $new_user['data'] = $this->order['regfields'];
                if (!$this->order['user_autologin']) {
                    $new_user['changepass'] = 1;
                }
                $new_user->enableRegistrationCheckers();
                $new_user->addLinkToAuthInError(true);

                /** @var UsersOrmType\VerifiedPhone $phone_field */
                $phone_field = $new_user['__phone'];
                $phone_field->setEnableVerification(false);

                if (!$new_user->validate()) {
                    $this->order->addErrors($new_user->getNonFormErrors(), 'user');
                    foreach ($new_user->getErrorsByForm() as $form => $errors) {
                        if ($form == 'e_mail') {
                            $field_name = 'user_email';
                        } elseif (preg_match('/^userfield_/', $form)) {
                            $field_name = preg_replace('/^userfield_/', 'regfields_', $form);
                        } else {
                            $field_name = "user_$form";
                        }

                        $this->order->addErrors($errors, $field_name);
                    }
                }

                if (!$this->order['user_autologin'] && $this->order['user_openpass'] != $this->order['user_pass2']) {
                    $this->order->addError(t('Пароли не совпадают'), 'user_pass2');
                }
            } else {
                $this->order->checkUnregisteredUserFields();
            }
        }
    }

    /**
     * Исправляет параметры заказа
     */
    protected function correctOrderData()
    {
        $user = $this->order->getUser();

        $this->correctOrderCityData();

        $delivery_list = $this->delivery_api->getCheckoutDeliveryList($user, $this->order);
        if (empty($this->order['delivery']) || !isset($delivery_list[$this->order['delivery']])) {
            foreach ($delivery_list as $delivery) {
                if ($delivery['default']) {
                    $this->order['delivery'] = $delivery['id'];
                    break;
                }
            }
        }

        $address_list = $this->address_api->getCheckoutUserAddresses($this->order, $this->order->getUser(), true);
        if (!isset($this->order['use_addr']) || ($this->order['use_addr'] != 0 && !isset($address_list[$this->order['use_addr']]))) {
            $use_addr = (!empty($address_list)) ? (int)reset($address_list)['id'] : 0;
            $this->order->setUseAddr($use_addr);
        }

        $payment_list = $this->payment_api->getCheckoutPaymentList($user, $this->order);
        if (empty($this->order['payment']) || !isset($payment_list[$this->order['payment']])) {
            foreach ($payment_list as $payment) {
                if ($payment['default_payment']) {
                    $this->order['payment'] = $payment['id'];
                    break;
                }
            }
        }

        if (!$this->order['warehouse']) {
            $this->order['warehouse'] = ($this->order->getStockAffiliateWarehouse()) ? $this->order->getStockAffiliateWarehouse()['id'] : WareHouseApi::getDefaultWareHouse()['id'];
        }
    }

    /**
     * Первоначальная установка адреса
     * Сброс "use_addr" при смене города
     *
     * @return void
     */
    protected function correctOrderCityData(): void
    {
        if (!isset($this->order['use_addr'])) {
            $this->order->setDefaultAddress();
        }

        if ($this->order['use_addr']) {
            $address = $this->order->getAddress();
            foreach (['country_id', 'region_id', 'city_id', 'country', 'region', 'city'] as $field) {
                if ($address[$field] != $this->order["addr_$field"]) {
                    $this->order->setUseAddr(null);
                    break;
                }
            }
        }
    }
}
