<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Catalog\Model;

use Alerts\Model\Manager as AlertsManager;
use Catalog\Model\Notice as CatalogNotice;
use Catalog\Model\Orm\Offer;
use Catalog\Model\Orm\OneClickItem;
use Catalog\Model\Orm\Product;
use RS\Application\Auth;
use RS\Captcha\Manager as CaptchaManager;
use RS\Config\Loader as ConfigLoader;
use RS\Config\UserFieldsManager;
use RS\Event\Manager as EventManager;
use RS\Helper\Tools as RSTools;
use RS\Http\Request as HttpRequest;
use RS\Module\AbstractModel\EntityList;
use RS\Module\Manager as ModuleManager;
use RS\Orm\Request as OrmRequest;
use Shop\Model\Cart;
use Shop\Model\Notice as ShopNotice;
use Shop\Model\Orm\Order;

/**
 * Класс содержит API функции для работы с формой купить в один клик
 */
class OneClickApi extends EntityList
{
    const EXTRA_FIELDS = 'oneclickfields';

    private $click_info = []; //Массив полей для отправки и сохранения

    function __construct()
    {
        parent::__construct(new Product());
    }

    /**
     * Проверяет поля для отправки и заполняет массив значений перед отправкой
     *
     * @param UserFieldsManager $click_fields_manager - менеджер дополнительных полей
     * @param boolean $use_captcha - Использовать каптчу
     * @return boolean
     */
    function checkFieldsFromPostToSend($click_fields_manager, $use_captcha = true)
    {
        $url = HttpRequest::commonInstance();

        //Проверим и получим обязательные переменные из запроса
        $this->click_info['name'] = $url->request('name', TYPE_STRING);       //Имя
        $this->click_info['phone'] = $url->request('phone', TYPE_STRING);      //Телефон
        $kaptcha_key = $url->request('kaptcha', TYPE_STRING);    //Каптча
        $click_fields = $url->request('clickfields', TYPE_ARRAY); //Доп. поля

        $config = ConfigLoader::byModule($this); //Конфиг модуля

        if ($config['oneclick_name_required'] && empty($this->click_info['name'])) {  //Если пустые поля
            $this->addError(t("Поле 'Имя' является обязательным."), 'name');
        }

        if (empty($this->click_info['phone'])) {  //Если пустые поля
            $this->addError(t("Поле 'Телефон' является обязательным."), 'phone');
        }

        //Проверим дополнительные поля
        if (!$click_fields_manager->check($click_fields)) {
            $this->addError(implode(', ', $click_fields_manager->getErrors()));
        }
        $this->click_info['ext_fields'] = $click_fields_manager->getStructure(); //Получим значения доп. полей

        //Проверим каптчу, если не залогинен
        if (!Auth::isAuthorize() && $use_captcha) {
            $captcha = CaptchaManager::currentCaptcha();
            $orm_object = new OneClickItem();
            $captcha_context = $orm_object->__kaptcha->getReadyContext($orm_object);
            if (!$captcha->check($kaptcha_key, $captcha_context)) {
                $this->addError($captcha->errorText(), 'kaptcha');
            }
        }

        return !$this->hasError();
    }

    /**
     * Отсылает форму купить в один клик и предварительно проверяет её. Неоходим вызов функции checkFieldsFromPostToSend
     *
     * @param array $products - массив объектов товаров со всеми сведениями. сведения о выбранной комплектации храняться в ключено offer_fields [\Catalog\Model\Orm\Product, ...]
     *
     * @return void
     */
    function send($products)
    {
        if (isset($this->click_info['name']) && empty($this->click_info['name'])) {
            $this->click_info['name'] = t('Не указано');
        }
        $this->click_info['products'] = $products;

        $notice = new CatalogNotice\OneClickUser();
        $notice->init($this->click_info);
        //Отсылаем sms пользователю
        AlertsManager::send($notice);

        //Добавим в БД
        $this->addOneClickInfo();
    }

    /**
     * Добавляет запись о покупке в один клик в БД
     */
    private function addOneClickInfo()
    {
        //Добавим в БД сведения
        $click_item = new OneClickItem();
        $click_item['user_fio']    = $this->click_info['name'];
        $click_item['user_phone']  = $this->click_info['phone'];
        $click_item['dateof']      = date("Y.m.d H:i:s");
        $click_item['products']    = $this->click_info['products'];
        $click_item['sext_fields'] = serialize($this->click_info['ext_fields']);
        $click_item['ip']          = $_SERVER['REMOTE_ADDR'] ? $_SERVER['REMOTE_ADDR'] : $_SERVER['HTTP_X_FORWARDED_FOR'];

        $click_item->insert();
    }

    /**
     * Возвращает подготовленные данные (товары) из корзины пользователя для отправки
     * @return array
     * @throws \RS\Event\Exception
     * @throws \RS\Exception
     */
    function getPreparedProductsFromCart()
    {
        $products = [];
        $cart = Cart::currentCart();
        $product_items = $cart->getProductItemsWithConcomitants(); //Получим товары из корзины

        foreach ($product_items as $item) {
            $product = $item['product'];  //Товар
            $cartitem = $item['cartitem']; //Объект в корзине

            //Предварительные данные
            $offer_fields = [
                'offer_id' => $cartitem['offer'],
                'multioffer' => [],
                'multioffer_val' => [],
                'amount' => $cartitem['amount'] //количество
            ];
            //Если есть комплектация
            if ($cartitem['offer'] !== null) {
                $offer = OrmRequest::make()
                    ->from(new Offer())
                    ->where([
                        'product_id' => $product['id'],
                        'id' => $cartitem['offer'],
                    ])->object();

                $offer_fields['offer'] = $offer['title'];
                $offer_fields['offer_id'] = $offer['id'];
                $offer_fields['barcode'] = $offer['barcode'];
            }

            //Соберём многомерные комплектации, если они есть
            $multioffers = @unserialize($cartitem['multioffers']);
            if (!empty($multioffers)) {
                foreach ($multioffers as $prop_id => $multioffer) {
                    $offer_fields['multioffer'][$prop_id] = $multioffer['value']; //
                    $offer_fields['multioffer_val'][$prop_id] = $multioffer['title'] . ": " . $multioffer['value']; //Текстовое представление
                }
            }

            $product['offer_fields'] = $offer_fields;

            $event_result = EventManager::fire('oneclick.addproduct.before', [
                'product' => $product,
                'cartitem' => $cartitem,
            ]);
            if ($event_result->getEvent()->isStopped()) {
                continue;
            }

            $products[] = $product;
        }

        return $products;
    }

    /**
     * Создаёт заказ из Купить в один клик
     *
     * @param Orm\OneClickItem $oneclick - объект купить в один клик
     * @return Order
     * @throws \RS\Db\Exception
     * @throws \RS\Event\Exception
     * @throws \RS\Exception
     * @throws \RS\Orm\Exception
     */
    function createOrderFromOneClick(Orm\OneClickItem $oneclick)
    {
        //Создадим заказ
        $order = new Order();

        //Сообщаем ID покупки в 1 клик для возможной обработки в сторонних модулях
        $order['oneclickitem_id'] = $oneclick['id'];

        //Данные пользователя
        if ($oneclick['user_id'] > 0) {
            $order['user_id'] = $oneclick['user_id'];
        } else {
            $order['user_fio'] = $oneclick['user_fio'];
            $order['user_phone'] = $oneclick['user_phone'];
        }

        //Данные валюты
        $order['currency'] = $oneclick['currency'];

        $currency_api = new CurrencyApi();
        $currency = $currency_api->setFilter('title', $order['currency'])->getFirst();
        if ($currency) {
            $order['currency_ratio'] = $currency['ratio'];
            $order['currency_stitle'] = $currency['stitle'];
        }
        //Отключение уведомлений
        $order['disable_checkout_notice'] = 1;

        $products_arr = [];

        //Получим информацию о товаре, чтобы создать корзину
        $products = $oneclick->tableDataUnserialized();
        if (!empty($products)) {
            $symb = array_merge(range('a', 'z'), range('0', '9')); //Символя для генерации уникального индекса
            foreach ($products as $product_info) {
                //Попробуем загрузить сам товар
                $product = new Product($product_info['id']);
                $offer_id = $product_info['offer_fields']['offer_id'];

                //Генерируем запист товара
                $uniq = RSTools::generatePassword(10, $symb); // Уникальный индекс товара
                $cost_id = CostApi::getUserCost($order->getUser()); // Цена пользователя
                $cartitem_arr = [
                    'uniq' => $uniq,
                    'type' => Cart::TYPE_PRODUCT,
                    'entity_id' => $product_info['id'],
                    'title' => $product_info['title'],
                    'barcode' => $product->getBarCode($offer_id),
                    'single_weight' => $product->getWeight($offer_id),
                    'amount' => $product_info['offer_fields']['amount'],
                    'offer' => $offer_id,
                    'single_cost' => $product->getCost($cost_id, $offer_id, false),
                ];

                if (isset($product_info['offer_fields']['multioffer'])) {
                    //Разберём многомерные комплектации из текта
                    $cartitem_arr['multioffers'] = $product_info['offer_fields']['multioffer'];
                }

                $event_result = EventManager::fire('oneclick.createorder.addproduct.before', [
                    'cartitem_arr' => $cartitem_arr, // array
                    'product_info' => $product_info,
                ]);
                if ($event_result->getEvent()->isStopped()) {
                    continue;
                }
                list($cartitem_arr) = $event_result->extract();

                $products_arr[$uniq] = $cartitem_arr;
            }
        }

        // Создаём корзину
        $cart = Cart::orderCart($order);
        $order->session_cart = $cart;
        $cart->updateOrderItems($products_arr); //Обновляем товары в корзине
        $cart->saveOrderData(); //Сохраняем данные товаров в БД
        $order->insert();

        //Отправляем уведомление покупателю
        $notice = new ShopNotice\CheckoutUser();
        $notice->init($order);
        AlertsManager::send($notice);
        $sext_fields = unserialize($oneclick['sext_fields']);
        $order->addExtraKeyPair(self::EXTRA_FIELDS, $sext_fields);
        //Отключим уведомления
        $order['notify_user'] = false;
        //Если включена статистика, то запишем источник, если он присутвует
        if (ModuleManager::staticModuleExists('statistic') && ModuleManager::staticModuleEnabled('statistic') && $oneclick['source_id']) {
            $order['source_id'] = $oneclick['source_id'];
        }
        $oneclick['status'] = Orm\OneClickItem::STATUS_VIEWED;
        $oneclick->update();
        return $order;
    }

    /**
     * Подготавливает сведения о многомерных комплектациях для отображения
     *
     * @param array $offer_fields - массив доп. сведения
     * @param Orm\Product $product - объект товар
     * @param array $multioffers - массив с сведениями для установки
     * @return array
     */
    function preparedMultiOffers($offer_fields, $product, $multioffers)
    {
        //Многомерные комплектации
        if (!empty($multioffers)) {
            if ($product->isMultiOffersUse() || $product->isVirtualMultiOffersUse()) {
                $product_multioffers = $product->fillMultiOffers();
            }

            //Переберём комплектации и запишем значения в виде строки, на случай если удалён товар
            $multioffers_val = [];
            foreach ($multioffers as $prop_id => $value) {
                //Проверим, если данные в старом формате, обрежем как надо
                if (mb_stripos($value, ":") !== false) {
                    strtok($value, ":");
                    $multioffers[$prop_id] = trim(strtok(":"));
                }

                //Запишем значения текстом   
                if (isset($product_multioffers['levels'][$prop_id])) {
                    $property_title = ($product_multioffers['levels'][$prop_id]['title']) ? $product_multioffers['levels'][$prop_id]['title'] : $product_multioffers['levels'][$prop_id]['prop_title'];
                    $multioffers_val[$prop_id] = $property_title . ": " . $multioffers[$prop_id];
                }
            }
            $offer_fields['multioffer'] = $multioffers;
            $offer_fields['multioffer_val'] = $multioffers_val;
        }
        return $offer_fields;
    }

    /**
     * Подготавливает
     *
     * @param Product $product - объект товара
     * @param integer|null $offer_id - id комплектации если есть
     * @param array $multioffers - массив многомерных комплектаций
     * @return array
     */
    function prepareProductOfferFields($product, $offer_id = null, $multioffers = [])
    {
        $offer_fields = [
            'offer' => null,
            'offer_id' => null,
            'multioffer' => [],
            'multioffer_val' => [],
            'amount' => 1 //Количество
        ];

        //Многомерные комплектации
        $offer_fields = $this->preparedMultiOffers($offer_fields, $product, $multioffers);
        if ($offer_id) {
            $offer = new Offer($offer_id);
            $offer_fields['offer_id'] = $offer_id;
            $offer_fields['offer'] = $offer['title'];
            $offer_fields['barcode'] = $offer['barcode'];
        }
        return $offer_fields;
    }
}
