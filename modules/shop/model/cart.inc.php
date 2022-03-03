<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model;

use Catalog\Config\File as CatalogConfig;
use Catalog\Model\Api as CatalogApi;
use Catalog\Model\CurrencyApi;
use Catalog\Model\DirApi;
use Catalog\Model\MultiOfferLevelApi;
use Catalog\Model\Orm\Product;
use RS\Application\Auth;
use RS\Config\Loader as ConfigLoader;
use RS\Db\Adapter as DBAdapter;
use RS\Db\Exception as DbException;
use RS\Event\Manager as EventManager;
use RS\Exception as RSException;
use RS\Helper\CustomView;
use RS\Helper\Tools as HelperTools;
use RS\Orm\Request as OrmRequest;
use RS\Site\Manager as SiteManager;
use Shop\Controller\Front\CartPage;
use Shop\Model\Discounts\CartItemDiscount;
use Shop\Model\Discounts\DiscountManager;
use Shop\Model\Orm\AbstractCartItem;
use Shop\Model\Orm\Address;
use Shop\Model\Orm\CartItem;
use Shop\Model\Orm\ConcomitantCartItem;
use Shop\Model\Orm\Discount;
use Shop\Model\Orm\Order;
use Shop\Model\Orm\OrderItem;
use Shop\Model\Orm\Region;
use Shop\Model\Orm\Tax;

/**
 * Корзина пользователя.
 * Хранится в базе данных, привязана к пользователю по идентификатору сессии.
 * В сессии кэшируются общие данные о корзине. Данный класс также используется для работы с корзиной оформленного заказа.
 */
class Cart
{
    const CART_SESSION_VAR = 'cart';
    const MODE_SESSION = 'session'; // Режим обычной корзины. Корзина привязана к сессии
    const MODE_PREORDER = 'preorder'; // Режим оформления заказа.
    const MODE_ORDER = 'order'; // Режим привязки к заказу
    const MODE_EMPTY = 'order'; // Режим пустой корзины
    const CART_ITEM_KEY = 'cartitem';
    const TYPE_PRODUCT = 'product';
    const TYPE_COUPON = 'coupon';
    const TYPE_COMMISSION = 'commission';
    const TYPE_TAX = 'tax';
    const TYPE_ORDER_DISCOUNT = 'order_discount';
    const TYPE_SUBTOTAL = 'subtotal';
    const TYPE_DELIVERY = 'delivery';
    const ITEM_EXTRA_KEY_ADDITIONAL_UNIQUE = 'additional_uniq';
    const ITEM_EXTRA_KEY_SOURCE = 'source'; // источник добавления в корзину
    const CART_SOURCE_REPEAT_ORDER = 'repeat_order';
    const DISCOUNT_SOURCE_OLD_COST = 'old_cost';
    const SESSION_CART_PRODUCTS = 'cart_products';

    protected static $instance;

    protected $mode;
    protected $order;
    protected $cartitem;
    protected $user_errors = [];
    protected $select_expression = [];
    protected $order_expression;
    protected $session_id;
    protected $site_id;
    protected $cache_coupons;
    protected $cache_commission;
    protected $cache_products;
    protected $cache_products_with_concomitants;
    /** @var CartItem[]|OrderItem[] */
    protected $items = []; // Элементы корзины
    /** @var OrderItem[] */
    protected $order_items = []; // Элементы оформленного заказа
    protected $prevent_change_event = false;
    protected $first_cart_data_call = true;
    private $custom_amount_checker;

    /**
     * Получать экземпляр класса можно только через ::currentCart()
     *
     * @param string $mode режим работы корзины. см константы self::MODE_....
     * @param Orm\Order|null $order объект заказа, если корзина создается из заказа
     * @param integer|null $session_id ID сессии корзины
     * @param integer|null $site_id ID сайта
     */
    protected function __construct($mode = self::MODE_SESSION, Orm\Order $order = null, $session_id = null, $site_id = null)
    {
        $this->session_id = $session_id ?: Auth::getGuestId();
        $this->site_id = $site_id ?: SiteManager::getSiteId();

        $this->mode = $mode;
        $this->order = $order;
        switch($this->mode) {
            case self::MODE_ORDER:
                $this->cartitem = new Orm\OrderItem();
                $this->select_expression = [
                    'order_id' => $this->order['id']
                ];
                $this->order_expression = 'sortn';
                break;

            case self::MODE_EMPTY:
                $this->cartitem = new Orm\CartItem();
                break;
            default: {
                $this->cartitem = new Orm\CartItem();
                $this->select_expression = [
                    'site_id' => $this->site_id,
                ];
                if (Auth::isAuthorize() && $this->session_id == Auth::getGuestId()) {
                    $user = Auth::getCurrentUser();
                    $this->select_expression['user_id'] = $user['id'];
                } else {
                    $this->select_expression['session_id'] = $this->session_id;
                }
                $this->order_expression = 'dateof';
            }
        }

        $this->loadCart();
    }

    /**
     * Возвращает объект корзины текущего пользователя.
     *
     * @return Cart
     */
    public static function currentCart()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Возвращает корзину пользователя во время оформления заказа.
     * т.е. элементы корзины еще привязаны к сессии, но к ним уже добавляются сведения из заказа (налоги, доставка)
     *
     * @param Orm\Order $order
     * @return cart
     */
    public static function preOrderCart(Orm\Order $order = null)
    {
        $cart = new self(self::MODE_PREORDER, $order);
        //Вызовем событие
        EventManager::fire('cart.preorder', [
            'cart' => $cart,
        ]);
        return $cart;
    }

    /**
     * Возвращает пустую корзину
     *
     * @param Orm\Order $order
     * @return cart
     */
    public static function emptyCart(Orm\Order $order)
    {
        return new self(self::MODE_EMPTY, $order);
    }

    /**
     * Возвращает корзину оформленного заказа.
     *
     * @param Orm\Order $order
     * @return cart
     */
    public static function orderCart(Orm\Order $order)
    {
        return new self(self::MODE_ORDER, $order);
    }

    /**
     * Возвращает корзину конкретного пользователя по ID его сессии
     *
     * @param string $session_id - ID сессии корзины
     * @param int $site_id - ID сайта
     * @param string $cart_mode - режим корзины
     * @return Cart
     */
    public static function userCart($session_id, $site_id = null, $cart_mode = self::MODE_SESSION)
    {
        return new self($cart_mode, null, $session_id, $site_id);
    }

    /**
     * Уничтожает загруженный экземпляр корзины. Означает, что при следующем вызове ::currentCart()
     * будет произведена загрузка из базы заново
     *
     * @return void
     */
    public static function destroy()
    {
        self::$instance = null;
    }

    /**
     * Возвращает все элементы корзины
     *
     * @return AbstractCartItem[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Очистка всех истекших по времени корзин
     *
     * @return void
     */
    public static function deleteExpiredCartItems()
    {
        // Получаем список истекших сессий
        $sessions = OrmRequest::make()
            ->select("distinct session_id")
            ->from(new Orm\CartItem)
            ->where('dateof < DATE_SUB(NOW(), interval 60 day)')
            ->exec()->fetchSelected(null, 'session_id');

        // Удаляем все элементы корзин, относящиеся к этим истекшим сессиям
        if($sessions){
            OrmRequest::make()
                ->from(new Orm\CartItem)
                ->whereIn('session_id', $sessions)
                ->delete()->exec();
        }
    }

    /**
     * Устанавливает объект заказа в режиме PREORDER
     *
     * @param Orm\Order $order
     * @return Cart
     */
    function setOrder(Orm\Order $order = null)
    {
        $this->order = $order;
        return $this;
    }

    /**
     * Загружает корзину из базы данных
     *
     * @return void
     */
    function loadCart()
    {
        $q = OrmRequest::make()
            ->from($this->cartitem)
            ->where($this->select_expression)
            ->orderby($this->order_expression);

        $this->items = $q->objects(null, 'uniq');

        if ($this->mode == self::MODE_ORDER) {
            $this->order_items = $this->items;
        }
    }

    /**
     * Возвращает информацию по количеству товаров и стоимости товаров в корзине
     *
     * @param mixed $format - форматировать общую сумму заказа
     * @param mixed $use_currency - если true, то использовать текущую валюту, иначе используется базовая валюта
     * @return array
     * @throws RSException
     */
    public static function getCurrentInfo($format = true, $use_currency = true)
    {
        $current_currency = CurrencyApi::getCurrentCurrency();
        $options = (int)$format.(int)$use_currency.$current_currency['id'];

        if (!isset($_SESSION[self::CART_SESSION_VAR][$options])) {
            self::currentCart()->getCartData($format, $use_currency);
        }
        return $_SESSION[self::CART_SESSION_VAR][$options];
    }

    /**
     * Обновляет сведения об элементах заказа.
     *
     * @param Orm\OrderItem[] $items - массив элементов заказа
     * @return void
     * @throws RSException
     */
    function updateOrderItems(array $items)
    {
        foreach ($this->items as $key => $item) {

            if (isset($items[$key])) {
                //Если используются многомерные комплектации
                $mo_arr = [];
                if (isset($items[$key]['multioffers']) && !empty($items[$key]['multioffers'])) {
                    //Подгрузим сведения
                    $multioffers_values = $items[$key]['multioffers'];
                    $multioffer_api = new MultiOfferLevelApi();
                    $levels = $multioffer_api->getLevelsInfoByProductId($items[$key]['entity_id']);

                    foreach ($multioffers_values as $prop_id => $value) {
                        $info = $levels[$prop_id];
                        $mo_arr[$prop_id] = [
                            'title' => $info['title'] ? $info['title'] : $info['prop_title'],
                            'value' => $value,
                        ];
                    }
                    $items[$key]['offer'] = CatalogApi::getOfferByMultiofferValues($item['entity_id'], $mo_arr);
                }
                $items[$key]['multioffers'] = serialize($mo_arr);

                if (empty($items[$key]['offer'])) {
                    $product = new Product($item['entity_id']);
                    $items[$key]['offer'] = $product->getMainOffer()['id'];
                }

                if (isset($items[$key]['offer']) && $item['offer'] != $items[$key]['offer']) {
                    $item->removeAllDiscounts();
                }
                if (!empty($items[$key]['discount_from_old_cost'])) {
                    $item->removeDiscountsBySource(self::DISCOUNT_SOURCE_OLD_COST);
                    $item->addDiscount(new CartItemDiscount($items[$key]['discount_from_old_cost'], CartItemDiscount::UNIT_SINGLE_BASE_COST, self::DISCOUNT_SOURCE_OLD_COST));
                }
                $this->items[$key]->getFromArray((array)$items[$key]);
            } else {
                unset($this->items[$key]); //Удаляем исключенные элементы
            }
        }

        foreach ($items as $key => $item) {
            // Если такого товара еще нет
            if (!isset($this->items[$key])) {
                // Если это купон, то мы должны узнать его идентификатор по коду
                if (isset($item['type']) && $item['type'] == self::TYPE_COUPON) {
                    if (empty($item['entity_id']) && isset($item['code'])) {
                        $coupon = Orm\Discount::loadByWhere(['code' => $item['code']]);
                        if (!$coupon['id']) continue;
                        $item['entity_id'] = $coupon['id'];
                    }

                    $this->items[$key] = new Orm\OrderItem();
                    $this->items[$key]->getFromArray((array)$item);
                } elseif (isset($item['type']) && $item['type'] == self::TYPE_ORDER_DISCOUNT) {
                    if (empty($item['entity_id']) && isset($item['order_discount'])) {
                        $item['entity_id'] = 0;
                    }

                    $this->items[$key] = new Orm\OrderItem();
                    $this->items[$key]->getFromArray((array)$item);
                } else {
                    // Если передан идентификатор типа цены (в случае если это добавление товаров администратором)
                    $product = new Product($item['entity_id']);
                    if (isset($item['cost_id'])) {
                        $item['single_cost'] = $product->getCost($item['cost_id'], $item['offer'] ?? null, false, false);
                    }

                    if (!isset($item['title'])) {
                        $item['title'] = $product['title'];
                    }

                    $mo_arr = [];
                    //Если используются многомерные комплектации
                    if (isset($item['multioffers']) && !empty($item['multioffers'])) {
                        //Подгрузим сведения
                        $multioffers_values = $item['multioffers'];
                        $multioffer_api = new MultiOfferLevelApi();
                        $levels = $multioffer_api->getLevelsInfoByProductId($item['entity_id']);

                        foreach ($multioffers_values as $prop_id => $value) {
                            $info = $levels[$prop_id];
                            $mo_arr[$prop_id] = [
                                'title' => $info['title'] ? $info['title'] : $info['prop_title'],
                                'value' => isset($value['value']) ? $value['value'] : $value,
                            ];
                        }
                        $item['offer'] = CatalogApi::getOfferByMultiofferValues($item['entity_id'], $mo_arr);
                    }
                    $item['multioffers'] = serialize($mo_arr);

                    if (empty($item['offer'])) {
                        $product = new Product($item['entity_id']);
                        $item['offer'] = $product->getMainOffer()['id'];
                    }

                    $this->items[$key] = new Orm\OrderItem();

                    $this->items[$key]->getFromArray((array)$item);
                    if (!empty($item['discount_from_old_cost'])) {
                        $this->items[$key]->removeDiscountsBySource(self::DISCOUNT_SOURCE_OLD_COST);
                        $this->items[$key]->addDiscount(new CartItemDiscount($items[$key]['discount_from_old_cost'], CartItemDiscount::UNIT_SINGLE_BASE_COST, self::DISCOUNT_SOURCE_OLD_COST));
                    }
                }
            }
        }

        if ($this->getOrder()['trigger_cart_change']) {
            $this->triggerChangeEvent();
        } else {
            DiscountApi::applyCouponPercentDiscountsToCart($this);
            DiscountApi::applyCouponFixedDiscountsToCart($this);
        }

        EventManager::fire('cart.updateorderitems.after', ['cart' => $this]);

        $this->cleanCache();
        $this->makeOrderCart();
    }

    /**
     * Очищает кэш товаров корзины
     *
     */
    function cleanCache()
    {
        $this->cache_coupons = null;
        $this->cache_products = null;
    }

    /**
     * Очищает кэшированные сведения о сумме и количестве товаров в корзине
     *
     * @return void
     */
    function cleanInfoCache()
    {
        if (isset($_SESSION)) {
            unset($_SESSION[self::CART_SESSION_VAR]);
        }
    }


    /**
     * Возвращает элементы корзины по типу
     *
     * @param string|null $type Если Type - null, то возвращаются элементы всех типов
     * @return CartItem[]
     */
    function getCartItemsByType($type = null)
    {
        if ($type === null) {
            return $this->items;
        }

        $result = [];
        foreach($this->items as $uniq => $cartitem) {
            if ($cartitem['type'] == $type) {
                $result[$uniq] = $cartitem;
            }
        }
        return $result;
    }

    /**
     * Возвращает список товаров в корзине с учётом сопутствующих товаров
     *
     * @param bool $cache - исполльзовать кэш
     * @return array
     * @throws RSException
     */
    public function getProductItemsWithConcomitants($cache = true)
    {
        if ($this->cache_products_with_concomitants === null || !$cache) {
            if ($this->mode == self::MODE_ORDER) {
                $this->cache_products_with_concomitants = $this->getProductItems();
            } else {
                $product_items = $this->getProductItems();
                $concomitant_items = [];
                $concomitant_item_ids = [];
                foreach ($product_items as $item) {
                    /** @var AbstractCartItem $cartitem */
                    $cartitem = $item[self::CART_ITEM_KEY];
                    if ($cartitem instanceof CartItem) {
                        foreach ($cartitem->getConcomitants() as $uniq => $concomitant_item) {
                            $concomitant_items[$uniq][self::CART_ITEM_KEY] = $concomitant_item;
                            $concomitant_item_ids[] = $concomitant_item['entity_id'];
                        }
                    }
                }
                if ($concomitant_item_ids) {
                    $api = new CatalogApi();
                    $api->setAffiliateRestrictions();
                    $api->setFilter('id', $concomitant_item_ids, 'in');
                    $products = $api->getAssocList('id');
                    $products = $api->addProductsCost($products);
                    $products = $api->addProductsOffers($products);
                    $products = $api->addProductsMultiOffers($products);
                    $products = $api->addProductsDirs($products);
                    $products = $api->addProductsDynamicNum($products);

                    foreach ($concomitant_items as $uniq => $concomitant_item) {
                        $product = $products[$concomitant_item[self::CART_ITEM_KEY]['entity_id']];
                        $concomitant_items[$uniq][self::CART_ITEM_KEY]->setProduct($product);
                        $concomitant_items[$uniq][self::TYPE_PRODUCT] = $product;
                    }
                }

                $this->cache_products_with_concomitants = $concomitant_items + $product_items;
            }
        }
        return $this->cache_products_with_concomitants;
    }

    /**
     * Возвращает список товаров в корзине
     *
     * @param boolean $cache - использовать кэш?
     * @return array
     * @throws RSException
     */
    function getProductItems($cache = true)
    {
        if (!$cache || $this->cache_products === null) {
            $this->cache_products = [];
            $ids = [];
            $cartitem_product = $this->getCartItemsByType(self::TYPE_PRODUCT);
            $shop_config = ConfigLoader::byModule($this);

            foreach ($cartitem_product as $cartitem) {
                $ids[] = $cartitem['entity_id'];
            }
            if (!empty($ids)) {
                $api = new CatalogApi();
                $api->setAffiliateRestrictions();
                $api->setFilter('id', $ids, 'in');
                $products = $api->getAssocList('id');
                $products = $api->addProductsCost($products);
                $products = $api->addProductsOffers($products);
                $products = $api->addProductsMultiOffers($products);
                $products = $api->addProductsDirs($products);
                $products = $api->addProductsDynamicNum($products);

                foreach($cartitem_product as $key => $cartitem) {

                    if ($this->mode != self::MODE_SESSION && !isset($products[ $cartitem['entity_id'] ])) {
                        //В режиме корзины для заказа, Создаем пустой объект товара, если таковой уже был удален
                        $product = new Product();
                        $products[ $cartitem['entity_id'] ] = $product;
                    }

                    if (isset($products[$cartitem['entity_id']])) {

                        if ($this->mode == self::MODE_SESSION && $cartitem['offer'] > 0) {
                            //Если удалена дополнительная комплектация, то удаляем товар из корзины
                            $product = $products[$cartitem['entity_id']];
                            $offer_found = false;
                            foreach ($product["offers"]["items"] as $offer_key => $item) {
                                if ($cartitem['offer'] == $offer_key) {
                                    $offer_found = true;
                                }
                            }
                            if (!$offer_found) {
                                $this->removeItem($key);
                            }
                        }
                        if ($this->mode == self::MODE_SESSION && $shop_config['remove_nopublic_from_cart'] && !$products[$cartitem['entity_id']]['public']) {
                            $this->removeItem($key); //Если товар был скрыт, то удаляем его из корзины
                        } else {
                            $product = clone $products[$cartitem['entity_id']];
                            $cartitem->setEntity($product);
                            $this->cache_products[$key] = [
                                self::TYPE_PRODUCT => $product,
                                self::CART_ITEM_KEY => $cartitem,
                            ];
                        }

                    } else {
                        $this->removeItem($key); //Если товар не был найден, то удаляем его из корзины
                    }

                    //Заменяем стандартные свойства товаров, значениями, которые были во время оформления заказа
                    if ($this->mode == self::MODE_ORDER) {
                        /** @var Product $product */
                        $product = $this->cache_products[$key][self::TYPE_PRODUCT];

                        $tax_ids_arr = !empty($cartitem['data']['tax_ids']) ? ['tax_ids' => implode(',', (array)$cartitem['data']['tax_ids'])] : [];
                        $product->getFromArray([
                                'weight' => $cartitem['single_weight'],
                            ] + $tax_ids_arr);

                        if (empty($this->cache_products[$key][self::TYPE_PRODUCT]['title'])) {
                            $this->cache_products[$key][self::TYPE_PRODUCT]['title'] = $cartitem['title'];
                        }

                        $product->setUserCost($cartitem['single_cost']);
                        $this->cache_products[$key][self::TYPE_PRODUCT]['id'] = $cartitem['entity_id'];
                    }
                }
            }
        }
        return $this->cache_products;
    }

    /**
     * Добавляет товар в корзину
     *
     * @param integer $product_id - id товара
     * @param integer $amount - количество
     * @param integer $offer - комплектация
     * @param array $multioffers - многомерная комплектация
     * @param array $concomitants - сопутствующие товары
     * @param array $concomitants_amount - количество сопутствующих товаров
     * @param string $additional_uniq - дополнительный строковый идентификатор товара, исключающий группировку товаров
     * @param string $source - источник добавления в корзину
     * @param bool $manual - операция вызвана пользователем
     * @return string|false - возвращает уникальный идентификтор элемента корзины
     * @throws RSException
     */
    function addProduct($product_id, $amount = 0, $offer = null, $multioffers = [], $concomitants = [], $concomitants_amount = [], $additional_uniq = null, $source = null, $manual = false)
    {
        $api = new CatalogApi();
        /** @var Product $product */
        $product = $api->getOneItem($product_id);
        if (!$product) {
            throw new RSException(t('Невозможно добавить товар в корзину. Товар с ID %0 не найден', [$product_id]));
        }

        if (!$offer) {
            $offer = $product->getMainOffer()['id'];
        }

        //Приготовим мультикомплектации
        $multioffers = $this->prepareMultiOffersInfo($product_id, $multioffers);

        $eresult = EventManager::fire('cart.addproduct.before', [
            'product_id' => $product_id,
            'amount' => $amount,
            'offer' => $offer,
            'multioffers' => $multioffers,
            'concomitants' => $concomitants,
            'cart' => $this,
            'source' => $source,
            'manual' => $manual,
        ]);
        if ($eresult->getEvent()->isStopped()) {
            return false;
        }

        list($product_id, $amount, $offer, $multioffers, $concomitants) = $eresult->extract();

        if ($manual && $product['disallow_manually_add_to_cart']) {
            return false;
        }

        // Производим очистку устаревших корзин
        self::deleteExpiredCartItems();

        $offer  = (int)$offer;
        $amount = (float)$amount;
        $amount_step = $product->getAmountStep();
        if ($amount < $amount_step) {
            $amount = $amount_step;
        }
        $product_uniq = $this->exists($product_id, $offer, serialize($multioffers), $concomitants, self::TYPE_PRODUCT, $additional_uniq);

        if (!$product_uniq) {
            $product->fillCost();        //Загружаем цены
            $product->fillOffers();      //Загружаем комплектации
            $product->fillMultiOffers(); //Загружаем многомерные комлпектации
            $product->fillCategories();  //Загружаем сведения о категориях

            if ($product) {
                $item                  = clone $this->cartitem;
                $item['site_id']       = $this->site_id;
                $item['session_id']    = $this->session_id;
                $item['uniq']          = $this->generateId();
                $item['dateof']        = date('Y-m-d H:i:s');
                $item['user_id']       = (empty($this->getItems())) ? Auth::getCurrentUser()['id'] : $this->getCartUserId();
                $item['type']          = 'product';
                $item['entity_id']     = $product_id;
                $item['offer']         = $offer;
                $item['multioffers']   = serialize($multioffers);
                $item['amount']        = $this->correctAmount($product, $amount, $offer);
                $item['title']         = $product['title'];
                $item['single_weight'] = $product->getWeight($offer);
                $item->setExtraParam(self::ITEM_EXTRA_KEY_SOURCE, $source);

                if ($item instanceof CartItem) {
                    $item->fillConcomitantsFromPost($concomitants, $concomitants_amount);
                }

                if (!empty($additional_uniq)) {
                    $item->setExtraParam(Cart::ITEM_EXTRA_KEY_ADDITIONAL_UNIQUE, $additional_uniq);
                }

                if($this->mode != self::MODE_PREORDER){
                    $item->insert();
                }

                $this->items[$item['uniq']] = $item;

                if ($this->cache_products !== null) {
                    $this->cache_products[$item['uniq']] = [
                        self::TYPE_PRODUCT => $product,
                        self::CART_ITEM_KEY => $item,
                    ];
                }
                $product_uniq = $item['uniq'];
            }
        } else {
            $this->items[$product_uniq]['amount'] = $this->correctAmount($product, $this->items[$product_uniq]['amount'] + $amount, $offer);

            if ($this->mode != self::MODE_PREORDER) {
                if ($this->items[$product_uniq]->replace()) {
                    // обновляем id пользователя у всех элементов корзины
                    OrmRequest::make()
                        ->update(new CartItem())
                        ->set(['user_id' => $this->items[$product_uniq]['user_id']])
                        ->where([
                            'site_id' => $this->items[$product_uniq]['site_id'],
                            'session_id' => $this->items[$product_uniq]['session_id'],
                        ])
                        ->exec();
                }
            }
        }
        $this->cleanInfoCache();

        EventManager::fire('cart.addproduct.after', [
            'product_uniq' => $product_uniq,
            'product_id' => $product_id,
            'amount' => $amount,
            'offer' => $offer,
            'multioffers' => $multioffers,
            'concomitants' => $concomitants,
            'cart' => $this,
            'source' => $source,
            'manual' => $manual,
        ]);
        if ($this->getMode() != $this::MODE_ORDER ) {
            $this->triggerChangeEvent();
        }

        return $product_uniq;
    }

    /**
     * Возвращает id пользователя, которому принадлежит корзина
     *
     * @return int|null
     */
    public function getCartUserId(): ?int
    {
        $items = $this->getItems();
        if ($items) {
            $guest_id = reset($items)['session_id'];
            foreach ($items as $item) {
                if ($item['user_id'] > 0) {
                    return (int)$item['user_id'];
                    break;
                }
            }
            return (int)$guest_id;
        }
        return null;
    }

    /**
     * Подготавливает дополнительную информацию уточняя детали многомерных комплектаций
     * Возвращет подготовленный двумерный массив с ключами id,title для каждой комплектации
     *
     * @param integer $product_id - id товара
     * @param array $multioffers  - массив с мультикомплектациями полученными из запроса, ключи в нём id характеристики
     * @return array
     */
    function prepareMultiOffersInfo($product_id, $multioffers){
        //Подгрузим полную расширенную информацию о многомерных компл.
        if (!empty($multioffers)){
            $mo_arr      = $multioffers;
            $multioffers = [];

            //Получим уровни многомерной комлектации для обработки
            $levelsApi = new MultiOfferLevelApi();
            $levels    = $levelsApi->getLevelsInfoByProductId($product_id);

            if (!empty($levels)){
                foreach ($levels as $k=>$level){
                    if (isset($mo_arr[$level['prop_id']])){
                        $multioffers[$level['prop_id']]['id']    = $level['prop_id'];
                        $multioffers[$level['prop_id']]['value'] = $mo_arr[$level['prop_id']];
                        $multioffers[$level['prop_id']]['title'] = !empty($level['title']) ? $level['title'] : $level['prop_title'];
                    }
                }
            }

        }

        return $multioffers;
    }

    /**
     * Проверяет существование элемента в корзине.
     * Возвращает уникальный идентификатор позиции в корзине или false
     *
     * @param mixed $id
     * @param integer $offer - комплектация
     * @param array $multioffers - многомерная комплектация
     * @param array $concomitants - сопутствующие товары
     * @param string $type - тип позиции в корзине
     * @param string $additional_uniq - дополнительный идентификатор
     * @return string|bool
     * @throws RSException
     */
    function exists($id, $offer = null, $multioffers = null, $concomitants = null, $type = self::TYPE_PRODUCT, $additional_uniq = null)
    {
        foreach($this->items as $uniq => $cartitem) {
            $item_concomitant_ids = [];
            if ($cartitem instanceof CartItem) {
                $item_concomitants = $cartitem->getConcomitants();
                foreach ($item_concomitants as $item_concomitant) {
                    $item_concomitant_ids[] = $item_concomitant['entity_id'];
                }
            }
            $item_additional_uniq = $cartitem->getExtraParam(Cart::ITEM_EXTRA_KEY_ADDITIONAL_UNIQUE);

            if ($cartitem['type'] == $type
                && $cartitem['entity_id'] == $id
                && ($offer === null || $cartitem['offer'] == $offer)
                && ($multioffers === null || $cartitem['multioffers'] == $multioffers)
                && ($concomitants === null || ($item_concomitant_ids == $concomitants))
                && ($additional_uniq === null || $item_additional_uniq == $additional_uniq)) {
                return $uniq;
            }
        }
        return false;
    }

    /**
     * Генерирует уникальный в рамках пользователя id позиции в корзине
     *
     * @return string
     */
    protected function generateId()
    {
        $symb = array_merge(range('a', 'z'), range('0', '9'));
        return HelperTools::generatePassword(10, $symb);
    }

    /**
     * Обновляет информацию в корзине
     *
     * @param array $products - новые сведения по товарам
     * @param string $coupon - код купона на скидку
     * @param bool $safe - если true, то цена, скидка и наименование товара в $products будет игнорироваться
     * @param bool $manual - операция вызвана пользователем
     * @return bool | false
     * @throws DbException
     * @throws RSException
     */
    function update($products = [], $coupon = null, $safe = true, $manual = false)
    {
        $eresult = EventManager::fire('cart.update.before', [
            'products' => $products,
            'coupon' => $coupon,
            'cart' => $this,
            'safe' => $safe,
            'manual' => $manual,
        ]);

        if ($eresult->getEvent()->isStopped()) return false;
        list($products, $coupon) = $eresult->extract();
        $product_items = $this->getProductItems();
        $result = true;
        foreach($products as $uniq => $product) {
            //Обработка товара
            if (isset($this->items[$uniq]) && $this->items[$uniq]['type'] == self::TYPE_PRODUCT) {
                $cart_item = $this->items[$uniq];

                //Количество
                if (isset($product['amount'])) {
                    if (!$manual || !$cart_item->getForbidChangeAmount()) {
                        $cart_item['amount'] = $this->correctAmount($product_items[$uniq][self::TYPE_PRODUCT], abs((float)$product['amount']), (int)($product['offer'] ?? 0));
                    }
                }

                //Комплектация
                if (isset($product['offer'])) {
                    $cart_item['offer'] = (int)$product['offer'];
                }
                if (empty($cart_item['offer'])) {
                    $cart_item['offer'] = $cart_item->getEntity()->getMainOffer()['id'];
                }

                //Многомерные комплектации
                if (!empty($product['multioffers'])){
                    //Записываем сведения о выбранных параметрах многомерной комплектации
                    $multioffers = $this->prepareMultiOffersInfo($cart_item['entity_id'], $product['multioffers']);
                    $cart_item['multioffers'] = serialize($multioffers);
                }

                if (!$safe) { //Установка приватных параметров товара

                    //Принудительно назначенная цена
                    if (isset($product['price'])) {
                        if ($product['price'] !== false) {
                            $cart_item->setExtraParam('price', $product['price']);
                        } else {
                            $cart_item->unsetExtraParam('price');
                        }
                    }

                    //Произвольные данные, которые записываются к товару
                    if (isset($product['custom_extra'])) {
                        if ($product['custom_extra'] !== false) {
                            $cart_item->setExtraParam('custom_extra', $product['custom_extra']);
                        } else {
                            $cart_item->unsetExtraParam('custom_extra');
                        }
                    }

                    //Название товара
                    if (isset($product['title'])) {
                        $cart_item['title'] = $product['title'];
                    }
                }

                if ($cart_item instanceof CartItem) {
                    if ( !empty($product['concomitant']) ) {
                        $cart_item->fillConcomitantsFromPost($product['concomitant'], $product['concomitant_amount'] ?? []);
                    } else {
                        $cart_item->clearConcomitants();
                    }
                    if (! empty($product['discounts'])) {
                        foreach ($product['discounts'] as $discount) {
                            $cart_item->addDiscount($discount);
                        }
                    }
                }

                if (isset($this->cache_products)) {
                    $this->cache_products[$uniq][self::CART_ITEM_KEY] = $cart_item;
                }

                if ($this->mode == self::MODE_SESSION) {
                    $cart_item->update();
                }
            }
        }
        if (!empty($coupon)) {
            $result = $this->addCoupon($coupon);
        }
        $this->cleanInfoCache();

        EventManager::fire('cart.update.after', [
            'products' => $products,
            'coupon' => $coupon,
            'cart' => $this,
            'safe' => $safe,
            'manual' => $manual,
        ]);

        if ($this->getMode() != $this::MODE_ORDER ) {
            $this->triggerChangeEvent();
        }

        return $result;
    }

    /**
     * Удаляет позицию из корзины.
     *
     * @param string $uniq - Уникальный идентификатор элемента корзины
     * @param bool $manual - операция вызвана пользователем
     * @return bool
     * @throws RSException
     */
    function removeItem($uniq, $manual = false)
    {
        $eresult = EventManager::fire('cart.removeitem.before', [
            'cart' => $this,
            'uniq' => $uniq,
            'manual' => $manual,
        ]);
        if ($eresult->getEvent()->isStopped()) return false;

        if (isset($this->items[$uniq])) {
            if (!$manual || !$this->items[$uniq]->getForbidRemove()) {
                if ($this->mode == self::MODE_SESSION) {
                    OrmRequest::make()
                        ->delete()
                        ->from($this->cartitem)
                        ->where($this->select_expression)
                        ->where([
                            'uniq' => $uniq
                        ])->exec();
                }

                $deleted_item = $this->items[$uniq];
                unset($this->items[$uniq]);
                unset($this->order_items[$uniq]);
                unset($this->cache_products[$uniq]);
                unset($this->cache_coupons[$uniq]);

                EventManager::fire('cart.removeitem.after', [
                    'cart' => $this,
                    'uniq' => $uniq,
                    'item' => $deleted_item,
                    'manual' => $manual,
                ]);
                if ($this->getMode() != $this::MODE_ORDER ) {
                    $this->triggerChangeEvent();
                }
            }
        }
        return true;
    }

    /**
     * Очищает корзину
     *
     * @return bool
     */
    function clean()
    {
        $this->items = [];
        $this->order_items = [];
        $this->cache_coupons = null;
        $this->cache_products = null;

        OrmRequest::make()
            ->delete()
            ->from($this->cartitem)
            ->where([
                'site_id' => SiteManager::getSiteId(),
                'session_id' => $this->session_id,
            ])->exec();

        if (Auth::isAuthorize()) {
            $user = Auth::getCurrentUser();
            OrmRequest::make()
                ->delete()
                ->from($this->cartitem)
                ->where([
                    'site_id' => SiteManager::getSiteId(),
                    'user_id' => $user['id'],
                ])->exec();
        }

        $this->cleanInfoCache();
        return true;
    }

    /**
     * Объединяет одинаковые товары с одинаковой комплектацией, увеличивая количество.
     *
     * @return void
     * @throws DbException
     * @throws RSException
     */
    function mergeEqual()
    {
        $result = [];
        $items = $this->getCartItemsByType(self::TYPE_PRODUCT);
        foreach($items as $uniq => $cartitem) {
            $additional_uniq = $cartitem->getExtraParam(Cart::ITEM_EXTRA_KEY_ADDITIONAL_UNIQUE, '');
            $offerkey = $cartitem['entity_id'].'-'.((int)$cartitem['offer']).$cartitem['multioffers'].$cartitem['single_cost'].$additional_uniq;

            if (isset($result[$offerkey])) {
                //Обновляем количество другого товара, удаляем текущий товар
                $this->update([$result[$offerkey] => [
                    'amount' => $this->items[$result[$offerkey]]['amount'] + $cartitem['amount']
                ]]);
                $this->removeItem($uniq);
            } else {
                $result[$offerkey] = $uniq;
            }
        }
    }

    /**
     * Применяет к товарам корзины скидки от разницы между ценой и старой ценой
     *
     * @return void
     * @throws RSException
     */
    public function applyOldCostDiscount()
    {
        $shop_config = ConfigLoader::byModule($this);
        $all_items = $this->getProductItemsWithConcomitants();
        foreach ($all_items as $item) {
            /** @var AbstractCartItem $cart_item */
            $cart_item = $item[self::CART_ITEM_KEY];
            $cart_item->removeDiscountsBySource(self::DISCOUNT_SOURCE_OLD_COST);
            if ($shop_config['old_cost_delta_as_discount']) {
                /** @var Product $product */
                $product = $item[self::TYPE_PRODUCT];
                $current_cost = $product->getCost(null, $cart_item['offer'], false, true);
                $old_cost = $product->getOldCost($cart_item['offer'], false, true);
                if ($old_cost > $current_cost) {
                    $discount_sum = $old_cost - $current_cost;
                    $discount = new CartItemDiscount($discount_sum, CartItemDiscount::UNIT_SINGLE_BASE_COST, self::DISCOUNT_SOURCE_OLD_COST);
                    $cart_item->addDiscount($discount);
                }
            }
        }
    }

    /**
     * Возвращает данные по товарам, налогам и скидкам в корзине
     *
     * @param bool $use_currency - Если true, то использовать текущую валюту, иначе используется базовая валюта
     * @return array
     * @throws RSException
     */
    function getPriceItemsData($use_currency = true)
    {
        $result = [
            'checkcount' => count($this->items),
            'currency' => $use_currency ? CurrencyApi::getCurrecyLiter() : CurrencyApi::getBaseCurrency()['stitle'],
            'errors' => [],
            'has_error' => false
        ];

        $result = $this->addProductData($result, $use_currency);
        $result = $this->addDiscountData($result, $use_currency);
        $result = $this->addOrderDiscountData($result, $use_currency);
        $result = $this->addTaxData($result);

        return $result;
    }

    /**
     * Возвращает стоимость товаров в заказе
     *
     * @param bool $use_currency - Если true, то использовать текущую валюту, иначе используется базовая валюта
     * @return float
     * @throws RSException
     */
    function getTotalWithoutDelivery($use_currency = true)
    {
        $result = $this->getPriceItemsData($use_currency);
        return $result['total'];
    }

    /**
     * Возвращает калькулируемые данные, необходимые для отображения корзины пользователя.
     * Результат не содержит объекты товаров. Объекты товаров можно получить вызвав метод getProductItems
     *
     * @param bool $format - форматировать цены
     * @param bool $use_currency - Если true, то использовать текущую валюту, иначе используется базовая валюта
     * @return array
     * @throws RSException
     */
    function getCartData($format = true, $use_currency = true)
    {
        $shop_config = ConfigLoader::byModule($this);
        /** @var CatalogConfig $catalog_config */
        $catalog_config = ConfigLoader::byModule('catalog');

        if ($this->first_cart_data_call && ($this->getMode() != $this::MODE_ORDER)) {
            $this->triggerChangeEvent(false);
            $this->first_cart_data_call = false;
        }

        $result   = $this->getPriceItemsData($use_currency);
        $currency = $result['currency'];

        //Сумма без доставки
        $result['total_without_delivery'] = CustomView::cost($result['total'], $currency);
        $result['total_without_delivery_unformatted'] = $result['total'];

        //Добавим данные по доставке
        $result = $this->addDeliveryData($result, $use_currency);

        //Сумма без комисии способа оплаты
        $result['total_without_payment_commission'] = CustomView::cost($result['total'], $currency);
        $result['total_without_payment_commission_unformatted'] = $result['total'];

        //Добавим данные по комиссии за оплату
        $result = $this->addPaymentCommissionData($result);

        $min_weight_limit = $shop_config['basketminweightlimit'];
        $weight_unit = $catalog_config->getShortWeightUnit();

        if (($this->getTotalWeight()) < $min_weight_limit) {
            $result['errors'][] = t('Минимальный суммарный вес товаров должен составлять %0', [$min_weight_limit." ".$weight_unit]);
            $result['has_error'] = true;
        }
        $result['total_weight'] = $this->getTotalWeight();
        $result = $this->appendUserErrors($result);
        $result['total_base_unformatted'] = $result['total_base'];
        $result['total_unformatted'] = $result['total'];
        $result['total_discount_unformatted'] = $result['total_discount'];

        $min_limit = Auth::getCurrentUser()->getBasketMinLimit();
        if ($use_currency) {
            $min_limit = CurrencyApi::applyCurrency( $min_limit );
        }
        if ($result['total_base_unformatted'] < $min_limit) {
            $result['errors'][] = t('Минимальная стоимость заказа должна составлять %0', [CustomView::cost($min_limit, $result['currency'])]);
            $result['has_error'] = true;
        }

        if ($format) {

            foreach($result['items'] as &$product) {
                $product['single_cost'] = CustomView::cost($product['single_cost'], $currency);
                $product['cost']        = CustomView::cost($product['cost'], $currency);
                $product['base_cost']   = CustomView::cost($product['base_cost'], $currency);
                $product['discount']    = CustomView::cost($product['discount'], $currency);

                if (isset($product['sub_products'])){
                    foreach($product['sub_products'] as &$sub_product) {
                        $sub_product['cost']        = CustomView::cost($sub_product['cost'], $currency);
                        $sub_product['single_cost'] = CustomView::cost($sub_product['single_cost'], $currency);
                        if (isset($sub_product['discount'])){
                            $sub_product['discount']    = CustomView::cost($sub_product['discount'], $currency);
                        }
                    }
                }
            }

            //Если если налоги и их надо преобразовать в форматированный вид
            if (isset($result['taxes'])) {
                foreach($result['taxes'] as &$tax) {
                    $tax['cost'] = CustomView::cost($tax['cost'], $currency);
                }
            }

            //Если если доставка и её надо преобразовать в форматированный вид
            if (isset($result['delivery'])) {
                $result['delivery']['cost'] = CustomView::cost($result['delivery']['cost'], $currency);
            }

            //Если если комиссия и её надо преобразовать в форматированный вид
            if (isset($result['payment_commission'])) {
                $result['payment_commission']['cost'] = CustomView::cost($result['payment_commission']['cost'], $currency);
            }

            //Форматируем итоговые суммы
            $result['total']          = CustomView::cost($result['total'], $currency);
            $result['total_base']     = CustomView::cost($result['total_base'], $currency);
            $result['total_discount'] = CustomView::cost($result['total_discount'], $currency);
        }

        $options = (int)$format . (int)$use_currency . CurrencyApi::getCurrentCurrency()['id'];

        $eresult = EventManager::fire('cart.getcartdata', [
            'cart' => $this,
            'cart_result' => $result,
            'format' => $format,
            'use_currency' => $use_currency,
        ]);

        $new_result = $eresult->getResult();
        $result = $new_result['cart_result'];

        //Формируем кэш данные в сессии
        $products_id = [];
        foreach($this->getCartItemsByType(self::TYPE_PRODUCT) as $cartitem) {
            $id = $cartitem['entity_id'];
            $products_id[$id] = $id;
        }
        if ($this->mode == self::MODE_SESSION) {
            $_SESSION[self::CART_SESSION_VAR] = [
                $options => [
                    'total_unformatted' => $result['total_unformatted'],
                    'currency' => $currency,
                    'total' => $result['total'],
                    'items_count' => $result['items_count'],
                    'products_id' => $products_id,
                    'has_error' => $result['has_error']
                ]
            ];
        }

        return $result;
    }

    /**
     * Добавляет сведения о налогах к корзине
     *
     * @param mixed $result
     * @return mixed
     * @throws RSException
     */
    protected function addTaxData($result)
    {
        $result['taxes'] = [];

        if ($this->mode != self::MODE_SESSION && $this->order ) {
            $subtotal = 0;
            //Расчитываем налоги для каждого товара
            foreach($this->getProductItems() as $key => $product) {
                $product_subtotal = $result['items'][$key]['cost']; //стоимость товара без налогов
                $address = $this->order->getAddress();
                $delivery = $this->order->getDelivery();

                if (!isset($address->city)){  //если доставка самовывоз, и свой адрес пользователь не вводил
                    if ($delivery->getTypeObject()->isMyselfDelivery()) {  // для расчета ставки налога использовать регион(Город) из доставки
                        $city_id = $delivery->getTypeObject()->getOption('myself_addr');
                        $city_region = new Region($city_id);
                        if ($city_region['id']) {
                            $address = Address::createFromRegion($city_region);
                        }
                    }
                }

                $taxes = TaxApi::getProductTaxes($product[self::TYPE_PRODUCT], $this->order->getUser(), $address);

                // Событие для модификации списка применямых налогов
                $event_result = EventManager::fire('cart.getcartdata.producttaxlist', [
                    'taxes' => $taxes,
                    'product' => $product,
                    'cart' => $this,
                ]);
                /** @var Tax[] $taxes */
                list($taxes) = $event_result->extract();

                foreach($taxes as $tax) {
                    $tax_rate = (float)$tax->getRate($address);
                    $tax_part = ($tax['included']) ? ($tax_rate / (100 + $tax_rate)) : ($tax_rate / 100) ;
                    $tax_value = round($result['items'][$key]['cost'] * $tax_part, 2);
                    if (!isset($result['taxes'][$tax['id']])) {
                        $result['taxes'][$tax['id']] = [
                            'tax' => $tax,
                            'title' => $tax['title'],
                            'cost' => 0
                        ];
                    }
                    $result['taxes'][$tax['id']]['cost'] += $tax_value;

                    if (!$tax['included']) {
                        $result['total'] += $tax_value;
                    }

                    $result['items'][$key]['taxes'][$tax['id']] = $tax_value;
                }
                $subtotal += $product_subtotal;
            }

            if (!empty($result['taxes'])) {
                $result['subtotal'] = $subtotal;
            }
        }
        return $result;
    }

    /**
     * Возвращает наценку на цену $price
     *
     * @param float $price - сумма
     * @param integer $commission_percent - процент комиссии
     * @return float
     */
    protected function getPaymentCommissionValue($price, $commission_percent)
    {
        return ceil($price * ($commission_percent/100));
    }

    /**
     * Добавляет сведения о комиссии в корзине
     *
     * @param mixed $result
     * @param bool $use_currency - Если true, то использовать текущую валюту, иначе используется базовая валюта
     * @return mixed
     */
    protected function addPaymentCommissionData($result, $use_currency = true)
    {
        if ($this->mode != self::MODE_SESSION && isset($this->order) && $this->order['payment']>0 ) {
            $payment = $this->order->getPayment();
            if ($payment['commission']) { //Если комиссия за оплату назначена
                if ($payment['commission_as_product_discount']) {
                    foreach($result['items'] as $key=>&$item) {
                        $payment_commisson = ceil($item['cost'] * $payment['commission'] / 100);
                        $item['cost'] += $payment_commisson;
                        $item['discount'] -= $payment_commisson;
                        $result['total'] += $payment_commisson;
                        $result['total_discount'] -= $payment_commisson;
                    }
                } else {
                    $commission_source = $result['total_base'];
                    // Добавим к сумме на которрую накладывается комиссия стоимость доставки, если включена опция
                    if ($payment['commission_include_delivery'] && isset($result['delivery']['cost'])) {
                        $commission_source += $result['delivery']['cost'];
                    }
                    $result['payment_commission']['object'] = $payment;
                    $result['payment_commission']['cost']   = $this->getPaymentCommissionValue($commission_source, $payment['commission']);
                    $result['total']                        += $result['payment_commission']['cost'];
                }
            }
        }

        return $result;
    }

    /**
     * Добавляет сведения о доставке в корзину
     *
     * @param array $result
     * @param bool $use_currency
     * @return array
     */
    function addDeliveryData($result, $use_currency)
    {
        if ($this->mode != self::MODE_SESSION && isset($this->order) && $this->order['delivery'] > 0) {

            $delivery = $this->order->getDelivery();
            $address = $this->order->getAddress();

            if ($this->order['user_delivery_cost'] !== null) {
                $cost = $this->order['user_delivery_cost'];
            } else {
                $cost = $delivery->getDeliveryCost($this->order, $address, $use_currency);
            }

            $result['delivery']['object'] = $delivery;
            $result['delivery']['cost'] = $cost;
            $result['total'] += $cost;

            $taxes = TaxApi::getDeliveryTaxes($delivery, $this->order->getUser(), $address);

            foreach($taxes as $tax) {
                $tax_rate = (float)$tax->getRate($address);
                $tax_part = ($tax['included']) ? ($tax_rate / (100 + $tax_rate)) : ($tax_rate / 100) ;
                $tax_value = round($cost * $tax_part, 2);
                if (!isset($result['taxes'][$tax['id']])) {
                    $result['taxes'][$tax['id']] = [
                        'tax' => $tax,
                        'title' => $tax['title'],
                        'cost' => 0
                    ];
                }
                $result['taxes'][$tax['id']]['cost'] += $tax_value;
                $result['delivery']['taxes'][$tax['id']] = $tax_value;

                if (!$tax['included']) {
                    $result['total'] += $tax_value;
                }
            }
        }

        return $result;
    }

    /**
     * Переносит элементы из корзины в таблицу элементов заказа
     * Вызывается при подтверждении заказа
     *
     * @return bool
     * @throws RSException
     */
    function makeOrderCart()
    {
        $catalog_config = ConfigLoader::byModule('catalog');
        $session_items  = $this->getCartItemsByType();
        $products       = $this->getProductItems();
        $cartdata       = $this->getCartData(false, false);

        $this->order_items = [];

        $i = 0;
        foreach($session_items as $uniq => $item) {
            $new_item = new Orm\OrderItem();

            $new_item->getFromArray($item->getValues());
            $new_item['order_id'] = $this->order['id'];

            if ($item['type'] == self::TYPE_PRODUCT) {
                //Определимся с единицами измерения
                $unit = $products[$uniq][self::TYPE_PRODUCT]->getUnit()->stitle;
                if ($catalog_config['use_offer_unit']){
                    $offer = @$products[$uniq][self::TYPE_PRODUCT]['offers']['items'][(int)$item['offer']];
                    if ($offer) $unit = $offer->getUnit()->stitle;
                }

                $new_item['title']         = $products[$uniq][self::CART_ITEM_KEY]['title'];
                $new_item['model']         = $products[$uniq][self::TYPE_PRODUCT]->isOffersUse() ? $products[$uniq][self::TYPE_PRODUCT]->getOfferTitle($item['offer']) : '';
                $new_item['barcode']       = $products[$uniq][self::TYPE_PRODUCT]->getBarCode($item['offer']);
                $new_item['sku']           = $products[$uniq][self::TYPE_PRODUCT]->getSku($item['offer']);
                $new_item['single_weight'] = $cartdata['items'][$uniq]['single_weight'];
                $new_item['single_cost']   = $cartdata['items'][$uniq]['single_cost'];
                $new_item['price']         = $cartdata['items'][$uniq]['base_cost'];
                $new_item['discount']      = $cartdata['items'][$uniq]['discount'];
                $new_item['profit']        = $new_item->getProfit();

                if ($custom_extra = $item->getExtraParam('custom_extra')) {
                    $new_item->setExtraParam('custom_extra', $custom_extra);
                }
                $new_item->setExtraParam('tax_ids', TaxApi::getProductTaxIds($products[$uniq][self::TYPE_PRODUCT]));
                $new_item->setExtraParam('unit', $unit);
            }
            $new_item['sortn'] = $i++;
            $this->order_items[$uniq] = $new_item;
        }

        $saved_unique = [];
        foreach($this->order_items as $uniq => $item) {
            if (in_array($item['type'], [self::TYPE_SUBTOTAL, self::TYPE_TAX, self::TYPE_COMMISSION, self::TYPE_DELIVERY])) {
                $unique_key = "{$item['type']}#{$item['entity_id']}";
                $saved_unique[$unique_key] = $uniq;
                unset($this->order_items[$uniq]);
            }
        }

        if (isset($cartdata['subtotal'])) {
            $unique_key = self::TYPE_SUBTOTAL . '#';

            $new_item             = new Orm\OrderItem();
            $new_item['order_id'] = $this->order['id'];
            $new_item['uniq']     = $saved_unique[$unique_key] ?? $this->generateId();
            $new_item['type']     = self::TYPE_SUBTOTAL;
            $new_item['title']    = t('Товаров на сумму');
            $new_item['price']    = $cartdata['subtotal'];
            $new_item['discount'] = $cartdata['subtotal'];
            $new_item['sortn']    = $i++;

            $this->order_items[$new_item['uniq']] = $new_item;
        }

        foreach($cartdata['taxes'] as $taxdata) {
            $unique_key = self::TYPE_TAX . '#' . $taxdata['tax']['id'];

            $new_item              = new Orm\OrderItem();
            $new_item['order_id']  = $this->order['id'];
            $new_item['uniq']      = $saved_unique[$unique_key] ?? $this->generateId();
            $new_item['type']      = self::TYPE_TAX;
            $new_item['title']     = $taxdata['tax']->getTitle();
            $new_item['entity_id'] = $taxdata['tax']['id'];
            $new_item['price']     = $taxdata['cost'];

            if ($taxdata['tax']['included']) {
                $new_item['discount'] = $taxdata['cost'];
            }
            $new_item['sortn'] = $i++;
            $this->order_items[$new_item['uniq']] = $new_item;
        }
        if (isset($cartdata['delivery'])) {
            if ($cartdata['delivery']['cost']===false){
                return t('Не удалось получить цену доставки. Попробуйте оформить заказ позже.');
            }

            $unique_key = self::TYPE_DELIVERY . '#' . $cartdata['delivery']['object']['id'];

            $new_item              = new Orm\OrderItem();
            $new_item['order_id']  = $this->order['id'];
            $new_item['uniq']      = $saved_unique[$unique_key] ?? $this->generateId();
            $new_item['type']      = self::TYPE_DELIVERY;
            $new_item['title']     = t('Доставка: %0', [$cartdata['delivery']['object']['title']]);
            $new_item['entity_id'] = $cartdata['delivery']['object']['id'];

            $new_item['price']     = $cartdata['delivery']['cost'];
            $new_item['sortn']     = $i++;

            $this->order_items[$new_item['uniq']] = $new_item;
        }
        //Коммиссия на оплату
        if (isset($cartdata['payment_commission'])) {
            $unique_key = self::TYPE_COMMISSION . '#' . $cartdata['payment_commission']['object']['id'];

            $payment               = $cartdata['payment_commission']['object'];
            $new_item              = new Orm\OrderItem();
            $new_item['order_id']  = $this->order['id'];
            $new_item['uniq']      = $saved_unique[$unique_key] ?? $this->generateId();
            $new_item['type']      = self::TYPE_COMMISSION;
            $new_item['title']     = t('Комиссия при оплате через %0 %1%', [
                $payment['title'],
                $payment['commission']
            ]);
            $new_item['entity_id'] = $cartdata['payment_commission']['object']['id'];

            $new_item['price']     = $cartdata['payment_commission']['cost'];
            $new_item['sortn']     = $i++;

            $this->order_items[$new_item['uniq']] = $new_item;
        }

        //Скидка на заказ
        if (isset($cartdata['order_discount'])) {
            $new_item              = new Orm\OrderItem();
            $new_item['order_id']  = $this->order['id'];
            $new_item['uniq']      = $this->generateId();
            $new_item['type']      = self::TYPE_ORDER_DISCOUNT;
            $new_item['title']     = t('Скидка на заказ %0', [
                isset($cartdata['order_discount_extra']) ? $cartdata['order_discount_extra'] : ""
            ]);
            $new_item['entity_id'] = 0;

            $new_item['price']     = $cartdata['order_discount_unformatted'];
            $new_item['discount']  = $cartdata['order_discount_unformatted'];
            $new_item['sortn']     = $i++;

            $this->order_items[$new_item['uniq']] = $new_item;
        }

        $this->order['totalcost'] = $cartdata['total'];

        $this->order['user_delivery_cost'] = isset($cartdata['delivery']) ? $cartdata['delivery']['cost'] : 0;

        return true;
    }

    /**
     * Сохраняет сформированный заказ в базу данных
     *
     * @return bool
     */
    function saveOrderData()
    {
        //Удалим старые
        OrmRequest::make()->delete()
            ->from(new Orm\OrderItem())
            ->where(['order_id' => $this->order['id']])
            ->exec();

        foreach($this->order_items as $uniq => $order_item) {
            $order_item['order_id'] = $this->order['id'];
            $order_item->insert();
        }

        return true;
    }

    /**
     * Возвращает доходность товаров в заказе
     *
     * @return double
     */
    function getOrderProfit()
    {
        $profit = 0;
        foreach($this->order_items as $uniq => $order_item) {
            if ($order_item['type'] == Orm\OrderItem::TYPE_PRODUCT) {
                $profit += $order_item['profit'];
            }
        }
        return $profit;
    }


    /**
     * Возвращает сведения об элементах заказа.
     * Сведения не зависят от существования в магазине реальных элементов заказа.
     *
     * @param bool $format - если true, то форматировать вывод
     * @param bool $use_currency - если true, то конвертировать в валюту заказа
     * @param bool $write_currency_liter - если true, то отображать символ валюты после суммы
     * @return array
     */
    function getOrderData($format = true, $use_currency = true, $write_currency_liter = true)
    {
        $result = [
            'items' => [],
            'other' => [],
            'total_cost' => 0, //Общая стоимость заказа все равно пересчитывается, чтобы избежать копеечных отклонений при конвертировании валют.
            'total_weight' => 0 //Общий вес
        ];

        if ($use_currency) {
            $result['currency'] = $this->order['currency_stitle'];
        } else {
            $result['currency'] = CurrencyApi::getBaseCurrency()->stitle;
        }
        if (!$write_currency_liter) {
            $result['currency'] = null;
        }

        foreach($this->order_items as $key => $cartitem) {
            $cost = $cartitem['price'];
            $discount = $cartitem['discount'];
            $single_cost = $cartitem['single_cost'];

            if ($use_currency) {
                $cost = $this->order->applyMyCurrency($cost);
                $discount = $this->order->applyMyCurrency($discount);
                $single_cost = $this->order->applyMyCurrency($single_cost);
            }
            $result['total_cost'] += ($cost - $discount);
            $result['total_weight'] += $cartitem['single_weight'] * $cartitem['amount'];
            $category = ($cartitem['type'] == self::TYPE_PRODUCT) ? 'items' : 'other';

            $single_discount = $discount/($cartitem['amount']!=0 ? $cartitem['amount'] : 1);
            $result[$category][$key] = [
                'cost' => $cost,
                'cost_with_discount' => ($cost - $discount),
                'single_cost' => $single_cost,
                'single_cost_with_discount' => round($single_cost - $single_discount, 2),
                'single_cost_noformat' => $single_cost,
                'discount' => $discount,
                'cartitem' => $cartitem
            ];
            if ($cost == 0 && $discount == 0) {
                $item_total = '';
            } else {
                $item_total = ($cost == $discount) ? $cost : abs($cost-$discount);
            }

            $result[$category][$key]['total'] = $item_total;

            if ($format) {
                $result[$category][$key]['total'] = CustomView::cost($result[$category][$key]['total'], $result['currency']);
                $result[$category][$key]['discount'] = CustomView::cost($result[$category][$key]['discount']);
                $result[$category][$key]['single_cost'] = CustomView::cost($result[$category][$key]['single_cost'], $result['currency']);
                $result[$category][$key]['cost_with_discount'] = CustomView::cost($result[$category][$key]['cost_with_discount']);
                $result[$category][$key]['single_cost_with_discount'] = CustomView::cost($result[$category][$key]['single_cost_with_discount'], $result['currency']);
            }
        }
        $result['total_cost_noformat'] = $result['total_cost'];
        if ($format) {
            $result['total_cost'] = CustomView::cost($result['total_cost'], $result['currency']);
        }

        $eresult = EventManager::fire('cart.getorderdata', [
            'cart' => $this,
            'cart_result' => $result,
            'format' => $format,
            'use_currency' => $use_currency,
            'write_currency_liter' => $write_currency_liter
        ]);
        $new_result = $eresult->getResult();
        $result = $new_result['cart_result'];

        return $result;
    }

    /**
     * Возвращает общий вес элементов корзины
     *
     * @param null|string $weight_unit - идентификатор единицы измерения, в которй нужно получить вес (соотношение к граммам)
     * @return integer
     * @throws RSException
     */
    function getTotalWeight($weight_unit = null)
    {
        $in_basket    = $this->getProductItemsWithConcomitants();
        $total_weight = 0;
        foreach($in_basket as $n => $item) {
            $product = $item[self::TYPE_PRODUCT];
            $amount  = $item[self::CART_ITEM_KEY]['amount'];
            $total_weight += $product->getWeight($item[self::CART_ITEM_KEY]['offer'], $weight_unit) * ($amount > 0 ? $amount : 1); //Рассчитываем общий вес
        }
        return (float)$total_weight;
    }

    /**
     * Возвращает стоимость заказа
     *
     * @param array $excludeTypesOfItem - массив элементов из констант self::TYPE_...., с помощью которого можно исключить из расчета различные компоненты заказа
     * @param bool $use_currency
     * @return float
     * @throws RSException
     */
    function getCustomOrderPrice(array $excludeTypesOfItem = [], $use_currency = true)
    {
        //Если заказ еще не оформлен
        $result = [
            'checkcount' => count($this->items),
            'currency' => $use_currency ? CurrencyApi::getCurrecyLiter() : CurrencyApi::getBaseCurrency()->stitle,
            'errors' => [],
            'has_error' => false,
            'total' => 0
        ];
        if ($this->mode == self::MODE_ORDER) {
            //Если заказ уже оформлен
            foreach($this->order_items as $key => $cartitem) {
                if (!in_array($cartitem['type'], $excludeTypesOfItem)) {
                    $cost = $cartitem['price'];
                    $discount = $cartitem['discount'];
                    if ($use_currency) {
                        $cost = $this->order->applyMyCurrency($cost);
                        $discount = $this->order->applyMyCurrency($discount);
                    }
                    $result['total'] += ($cost - $discount);
                }
            }
        } else {
            if (!in_array(self::TYPE_PRODUCT, $excludeTypesOfItem)) {
                $result = $this->addProductData($result, $use_currency);
            }
            $result = $this->addDiscountData($result, $use_currency);

            if (!in_array(self::TYPE_TAX, $excludeTypesOfItem)) {
                $result = $this->addTaxData($result);
            }
            if (!in_array(self::TYPE_DELIVERY, $excludeTypesOfItem)) {
                $result = $this->addDeliveryData($result, $use_currency);
            }
        }
        return $result['total'];
    }

    /**
     * Разделение товаров
     *
     * @return void
     * @throws RSException
     */
    function splitSubProducts()
    {
        $in_basket = $this->getProductItems();

        foreach($in_basket as $n => $item) {
            /** @var Product $product */
            $product = $item[self::TYPE_PRODUCT];
            /** @var CartItem|OrderItem $cart_item */
            $cart_item = $item[self::CART_ITEM_KEY];
            $cart_source = $cart_item->getExtraParam(Cart::ITEM_EXTRA_KEY_SOURCE);

            //Переберём сопутствующие
            if ($cart_item instanceof CartItem) {
                foreach ($cart_item->getConcomitants() as $checked_concomitant) {
                    foreach($product->getConcomitant() as $sub_product) {
                        if ($sub_product['id'] == $checked_concomitant['entity_id']) {
                            $uniq = $this->addProduct($sub_product['id'], $checked_concomitant['amount'], 0, [], [], [], null, $cart_source);

                            // если у сопутствующего товара была установлена цена - добавляем её
                            if ($single_cost = $checked_concomitant->getExtraParam(ConcomitantCartItem::EXTRA_KEY_PRICE)) {
                                $extra = ['price' => $single_cost];
                                $this->update([$uniq => $extra], null, false);
                            }
                            // если у сопутствующего товара была скидка - переносим её
                            if ($discounts = $checked_concomitant->getDiscounts()) {
                                $extra = ['discounts' => $discounts];
                                $this->update([$uniq => $extra], null, false);
                            }
                            break;
                        }
                    }
                }
                $cart_item->clearConcomitants();
            }
        }
        $this->cache_products_with_concomitants = null;
        $this->triggerChangeEvent();
    }

    /**
     * Получаем массив из количества сопуствующего товара ключ - id товара, значение - количество доступное на складе
     *
     * @param array $concomitants        - массив сопутствующие товары
     * @param array $concomitant_amounts - массив уже подготовленных остатков
     * @return array
     */
    private function getConcomitantsAmounts($concomitants, $concomitant_amounts = [])
    {
        foreach ($concomitants as $sub_product){
            if (!isset($concomitant_amounts[$sub_product['id']])){
                $concomitant_amounts[$sub_product['id']] = $sub_product->getNum(0);
            }
        }
        return $concomitant_amounts;
    }

    /**
     * Пересчитывает стоимости товаров
     *
     * @param array $result - массив со сведениями о корзине
     * @param bool $use_currency - использовать ли текущую валюту?
     * @return array
     * @throws RSException
     */
    protected function addProductData($result, $use_currency)
    {
        $module_config = ConfigLoader::byModule($this);

        $base_data = [
            'total' => 0,
            'total_base' => 0,
            'total_discount' => 0,
            'items' => [],
            'items_count' => 0,
            'total_weight' => 0,
        ];
        $result = $base_data + $result;

        $concomitant_amounts  = [];
        $in_basket = $this->getProductItems();
        foreach($in_basket as $n => $item) {
            /** @var CartItem $cart_item */
            $cart_item = $item[self::CART_ITEM_KEY];
            /** @var Product $product */
            $product = $item[self::TYPE_PRODUCT];
            $amount = (float)$item[self::CART_ITEM_KEY]['amount'];

            //Если принудительно задана стоимость, то устанавливаем её
            $price = $cart_item->getExtraParam('price');
            if ( $price !== null ) {
                $product->setUserCost($price);
            }
            $cost = DiscountManager::instance()->getCartItemBaseCost($cart_item);
            if ($use_currency) {
                $cost = CurrencyApi::applyCurrency($cost);
            }
            $result['total'] +=  $cost; //Общая сумма товаров в корзине
            $result['items_count'] += $module_config['show_number_of_lines_in_cart'] ? 1 : $amount;
            $result['items'][$n] = [
                'id' => $n,
                'cost' => $cost, //Конечная цена для отображения, данная цена будет изменяться скидками далее
                'base_cost' => $cost, //Данная цена будет использоваться в качестве исходной для расчета скидки. изменяться не будет
                'amount' => $amount, // Количестово товара
                'single_cost' => $cost / $amount, //Цена за единицу товара
                'single_weight' => $product->getWeight($item[self::CART_ITEM_KEY]['offer']), //Вес единицы товара
                'discount' => 0,
                'sub_products' => []
            ];
            $result['items_count'] = (float)$result['items_count'];

            //Если задано, что заказать можно только определённое количество товара и количество не соотвествует
            if ($product->getMinOrderQuantity() > 0 && $amount < $product->getMinOrderQuantity()) {
                $product_stock = $product->getNum($cart_item['offer']);
                if ($module_config['allow_buy_num_less_min_order'] && $product_stock < $product->getMinOrderQuantity()) {
                    $allow_amount = ($module_config['allow_buy_all_stock_ignoring_amount_step']) ? $product_stock : $product_stock - ($product_stock % $product->getAmountStep());
                    if ($amount != $allow_amount) {
                        $result['items'][$n]['amount_error'] = t('Товар можно купить только в количестве %0', [$allow_amount.' '.$product->getUnit()['stitle']]);
                        $result['has_error'] = true;
                    }
                } else {
                    $result['items'][$n]['amount_error'] = t('Минимальное количество для заказа товара %0', [$product->getMinOrderQuantity().' '.$product->getUnit()['stitle']]);
                    $result['has_error'] = true;
                }

            }
            //Если задано, что заказать можно только определённое количество товара и количество не соотвествует
            if ($product->getMaxOrderQuantity() > 0 && $amount > $product->getMaxOrderQuantity()) {
                $result['items'][$n]['amount_error'] = t('Максимальное количество для заказа товара %0', [$product->getMaxOrderQuantity().' '.$product->getUnit()->stitle]);
                $result['has_error'] = true;
            }

            //Расчет "сопутствующих товаров"
            if ($cart_item instanceof CartItem) {
                $checked_concomitants = $cart_item->getConcomitants();
                $concomitants = $product->getConcomitant();

                //Подгрузим общее количество для каждого сопутствующего товара, если отмечен флаг
                if ($module_config['check_quantity']) {
                    $concomitant_amounts = $this->getConcomitantsAmounts($concomitants,$concomitant_amounts);
                }

                foreach ($concomitants as $sub_product) {
                    $sub_id   = $sub_product['id'];
                    $only_one = !empty($product['concomitant_arr']['onlyone'][$sub_id]);
                    $checked = false;
                    //Флаг 'Всегда в количестве одна штука' работает только при отключенной опции 'редактирование количества сопутствующих товаров в корзине'
                    $concomitant_amount = ($module_config['allow_concomitant_count_edit'] || $only_one) ? 1 : $amount;
                    $sub_product_single_cost = null;
                    foreach ($checked_concomitants as $checked_concomitant) {
                        if ($checked_concomitant['entity_id'] == $sub_id) {
                            $checked = true;
                            $concomitant_amount = $checked_concomitant['amount'];
                            $sub_product_single_cost = $checked_concomitant->getExtraParam(ConcomitantCartItem::EXTRA_KEY_PRICE, null);
                            break;
                        }
                    }

                    $discount = 0;
                    if ($sub_product_single_cost === null) {
                        $sub_product_single_cost = DiscountManager::instance()->getProductBaseCost($sub_product, 0);
                        if (! $checked) { // если сопутствующий не отмечен
                            $sub_product_cur_cost = $sub_product->getCost(null, null, false); // получим текущую стоимость товара
                            if ($sub_product_single_cost > $sub_product_cur_cost) { // если базовая цена большей текущей
                                $discount = $sub_product_single_cost - $sub_product_cur_cost; // рассчитаем скидку
                            }
                            $sub_product_single_cost = $sub_product_cur_cost; // укажем текущую цену с учётом скидки
                        }
                    }

                    $sub_product_cost = $sub_product_single_cost * $concomitant_amount;

                    $result['items'][$n]['sub_products'][$sub_id] = [
                        'amount' => $concomitant_amount,
                        'cost' => $sub_product_cost,
                        'base_cost' => $sub_product_cost,
                        'single_cost' => $sub_product_single_cost,
                        'discount' => $discount,
                        'checked' => $checked,
                    ];

                    //Проверяем количество сопутствующего у отмеченного товара, если стоит флаг проверки количества
                    if ($module_config['check_quantity'] && $checked) {
                        $check_amount = $result['items'][$n]['sub_products'][$sub_id]['amount'];
                        $concomitant_amounts[$sub_id] -= $check_amount; //Убавим количество

                        if ($concomitant_amounts[$sub_id]<0){
                            $result['items'][$n]['sub_products'][$sub_id]['amount_error'] = t('На складе нет требуемого количества товара. В наличии: %0', [(float)$sub_product->getNum(0).' '.$sub_product->getUnit()->stitle]);
                            $result['has_error'] = true;
                        }
                    }

                    if ($checked) {
                        $result['items_count'] += $module_config['show_number_of_lines_in_cart'] ? 1 : $concomitant_amount;
                        $result['total'] += $sub_product_cost;
                    }
                }
            }

            $result = $this->runAmountChecker($result, $amount, $item, $n);
            // Если нельзя покупать товары с 0 ценой
            if ($module_config['check_cost_for_zero'] && 0.0 === $cost && $cart_item->getExtraParam('source', '') === CartPage::CART_SOURCE_CART_PAGE) { // Проверяем, что товар добавлен "вручную"
                $result['items'][$n]['amount_error'] = t('Нельзя купить этот товар');
                $result['has_error'] = true;
            }
        }

        $result['total_weight'] = $this->getTotalWeight();
        $result['total_base'] = $result['total'];
        return $result;
    }

    /**
     * Устанавливает произвольный обработчик для контроля за остатком товара
     *
     * @param callback $callback
     */
    public function setCustomAmountChecker($callback)
    {
        $this->custom_amount_checker = $callback;
    }

    /**
     * Возвращает произвольный обработчик для контроля за остатком
     *
     * @return callback | null
     */
    public function getCustomAmountChecker()
    {
        return $this->custom_amount_checker;
    }

    /**
     * Выполняет проверку наличия товара с учетом всех параметров
     *
     * @param array $result Массив с результатом getCartData
     * @param integer $amount Количество товара
     * @param array $item Массив с товаром и элементом cartItem
     * @param string $n Уникальный идентификатор позиции в корзине
     * @return array Возвращает модифицированный $result
     */
    function runAmountChecker($result, $amount, $item, $n)
    {
        if ($callback = $this->getCustomAmountChecker()) {
            $result = call_user_func($callback, $result, $amount, $item, $n, $this);
        } else {
            $module_config = ConfigLoader::byModule($this);
            $product = $item[self::TYPE_PRODUCT];
            if ($amount < $product->getAmountStep() && (!$module_config['allow_buy_all_stock_ignoring_amount_step'] || $amount != $product->getNum())) {
                $result['items'][$n]['amount_error'] = t('Количество должно быть не менее %0', [$product->getAmountStep()]);
                $result['has_error'] = true;
            }
            elseif ($module_config['check_quantity'] && $amount > $product->getNum($item[self::CART_ITEM_KEY]['offer'])) {
                $result['items'][$n]['amount_error'] = t('На складе нет требуемого количества товара. В наличии: %0', [(float)$product->getNum($item[self::CART_ITEM_KEY]['offer']).' '.$product->getUnit()->stitle]);
                $result['has_error'] = true;
            }
        }
        return $result;
    }

    /**
     * Добавляет к результату информацию о скидках
     *
     * @param array $result - массив со сведениями о корзине
     * @param bool $use_currency - использовать ли текущую валюту?
     * @return array
     * @throws RSException
     */
    protected function addDiscountData($result, $use_currency)
    {
        $result['total_base_without_discount'] = $result['total_base'];

        $in_basket = $this->getProductItems();
        $discount_manager = DiscountManager::instance();
        foreach ($in_basket as $n => $item) {
            /** @var CartItem|OrderItem $cart_item */
            $cart_item = $item[self::CART_ITEM_KEY];
            $discount = $discount_manager->getCartItemFinalDiscount($cart_item);

            $result['items'][$n]['discount'] = $discount;
            $result['items'][$n]['cost'] -= $discount;
            $result['total'] -= $discount;
            $result['total_base'] -= $discount;
            $result['total_discount'] += $discount;

            if ($cart_item instanceof CartItem) {
                foreach ($cart_item->getConcomitants() as $concomitant_item) {
                    $product_id = $concomitant_item['entity_id'];
                    $discount = $discount_manager->getCartItemFinalDiscount($concomitant_item);

                    $result['items'][$n]['sub_products'][$product_id]['discount'] = $discount;
                    $result['items'][$n]['sub_products'][$product_id]['cost'] -= $discount;
                    $result['total'] -= $discount;
                    $result['total_base'] -= $discount;
                    $result['total_discount'] += $discount;
                }
            }
        }

        return $result;
    }

    /**
     * Добавляет сведения по скидкам
     *
     * @param array $result - массив со сведениями о корзине
     * @param float $discount - скидка общая в единицах
     * @param float $max_percent - максимальная доля заказа в процентах, которую может покрыть скидка
     * @param float $max_item_percent - максимальная доля заказа в процентах, которую может покрыть скидка
     * @param array $discounted_items - список позиций к которым применяется скидка, если не передан - скидка применяется ко всем позициям
     * @return array
     */
    private function addFixedDiscountData($result, $discount, $max_percent = 100., $max_item_percent = 100., $discounted_items = [])
    {
        if (!empty($result['items'])) {
            list($discount, $percent) = $this->correctFixedDiscount($result, $discount, $max_percent, $max_item_percent, $discounted_items);
            if ($discount) {
                $count_discount = 0; //Общая сумма скидки
                foreach($result['items'] as $uniq => &$data) {
                    if ($data['cost'] > 0 && (!$discounted_items || isset($discounted_items['products'][$uniq]))) {
                        $one_item_discount = ceil($data['cost'] * $percent); //Скидка одного элемента

                        if ($discount < $count_discount + $one_item_discount){ //Если последняя скидка больше по цене, то приведём её к нормальному виду
                            $one_item_discount = $discount - $count_discount;
                        }
                        $count_discount += $one_item_discount; //Общий плюсованный размер скидки

                        $data['cost']     = $data['cost'] - $one_item_discount;
                        $data['discount'] = ($one_item_discount + $data['discount']);
                        $result['total'] -= $one_item_discount;
                        $result['total_base'] -= $one_item_discount;
                        $result['total_discount'] += $one_item_discount;

                        //Пройдёмся по сопутствующим товарам
                        if (isset($data['sub_products']) && !empty($data['sub_products'])){
                            foreach ($data['sub_products'] as $sub_uniq=>&$subdata){
                                if ($subdata['checked'] && (!$discounted_items || isset($discounted_items['products'][$uniq]))){ //Если есть выбранные
                                    $one_item_discount = ceil($subdata['cost'] * $percent); //Скидка одного элемента

                                    if ($discount < $count_discount + $one_item_discount){ //Если последняя скидка больше по цене, то приведём её к нормальному виду
                                        $one_item_discount = $discount - $count_discount;
                                    }
                                    $count_discount += $one_item_discount; //Общий плюсованный размер скидки

                                    $subdata['cost']     = $subdata['cost'] - $one_item_discount;
                                    $subdata['discount'] = (isset($subdata['discount'])) ? $one_item_discount + $subdata['discount'] : $one_item_discount;
                                    $result['total'] -= $one_item_discount;
                                    $result['total_base'] -= $one_item_discount;
                                    $result['total_discount'] += $one_item_discount;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $result;
    }

    /**
     * Корректирует фиксированную скидку в соответствии с граничениями на процент заказа/позиции.
     * Возвращает итоговую сумму и долю скидки.
     *
     * @param array $result - массив со сведениями о корзине
     * @param float $discount - скидка общая в единицах
     * @param float $max_percent - максимальная доля заказа в процентах, которую может покрыть скидка
     * @param float $max_item_percent - максимальная доля заказа в процентах, которую может покрыть скидка
     * @param array $discounted_items - список позиций к которым применяется скидка, если не передан - скидка применяется ко всем позициям
     * @return array
     */
    private function correctFixedDiscount($result, $discount, $max_percent = 100., $max_item_percent = 100., $discounted_items = [])
    {
        $max_order_part = ($max_percent > 100) ? 1 : $max_percent / 100;
        $max_order_item_part = ($max_item_percent > 100) ? 1 : $max_item_percent / 100;
        if ($discount > $result['total'] * $max_order_part) {
            $discount = $result['total'] * $max_order_part;
        }
        if (!empty($discounted_items['products'])) {
            $discounted_items_base = 0;
            foreach ($result['items'] as $uniq=>$item) {
                if (isset($discounted_items['products'][$uniq])) {
                    $discounted_items_base += $item['cost'];

                    if (!empty($item['sub_products'])) {
                        foreach ($item['sub_products'] as $sub_id=>$sub_item) {
                            if ($sub_item['checked'] && isset($discounted_items['products'][$sub_id])) {
                                $discounted_items_base += $sub_item['cost'];
                            }
                        }
                    }
                }
            }
        } else {
            $discounted_items_base = $result['total'];
        }
        if ($discount > $discounted_items_base * $max_order_item_part) {
            $discount = $discounted_items_base * $max_order_item_part;
        }

        //Получим долю скидки
        $percent = ($discounted_items_base) ? $discount / $discounted_items_base : 0;
        return [$discount, $percent];
    }

    /**
     * Добавляет к результату информацию о скидках на заказ
     *
     * @param array $result - массив со сведениями о корзине
     * @param bool $use_currency - использовать ли текущую валюту?
     * @return array|void
     */
    protected function addOrderDiscountData($result, $use_currency)
    {
        //Вызовем событие для модулей
        $eresult = EventManager::fire('cart.before.addorderdata', [
            'cart_result' => $result,
            'cart' => $this,
            'use_currency' => $use_currency
        ]);
        if($eresult->getEvent()->isStopped()){
            return;
        }
        list($result) = $eresult->extract();
        if (isset($result['order_discount']) || ($order_discounts = $this->getCartItemsByType(self::TYPE_ORDER_DISCOUNT))){
//            if (!($coupons = $this->getCouponItems()) || (isset($result['order_discount_can_use_coupon']) && $result['order_discount_can_use_coupon'])){ //Если небыло применено ранее купонов, то можем общую скидку добавить
            //Если элемент с общей скидкой уже записан, то возьмём скидку оттуда
            if (!empty($order_discounts)){
                $order_discount_item = current($order_discounts);
                reset($order_discounts);
                $order_discount = $order_discount_item['price'];
            }else{
                $order_discount = $result['order_discount'];
            }

            //Добавим записи до применения скидки на заказ
            $result['total_without_order_discount'] = $result['total'];

            //Добавим сведения по скидкам в каждый товар

            $result = $this->addFixedDiscountData($result, $order_discount);

            if ($use_currency){
                $result['total_without_order_discount'] = CustomView::cost($result['total'], $result['currency']);
            }
            $result['total_without_order_discount_unformatted'] = $result['total'];
//            }
        }

        return $result;
    }

    /**
     * Возвращает товары, на которые данный купон предоставляет скидку
     *
     * @param array $coupons_items - результат выполнения getCouponItems()
     * @return array
     * @throws RSException
     */
    protected function getCouponsUsage($coupons_items)
    {
        $result = [];
        $products = $this->getProductItems();

        foreach($coupons_items as $key => $coupon_item) {
            /** @var Discount $coupon */
            $coupon = $coupon_item[self::TYPE_COUPON];
            // Получение всех категорий, на которые действунт купон
            $coupon_products = $coupon['products'];
            if (!empty($coupon_products['group'])) {
                $coupon_products['group'] = DirApi::getChildsId($coupon_products['group']);
            }

            $result[$key] = [
                'products' => []
            ];
            foreach($products as $uniq => $product_item) {
                $product = $product_item[self::TYPE_PRODUCT];
                //Сопутствующие
                if (isset($product['concomitant_arr'][self::TYPE_PRODUCT])){
                    $concomitants = $product['concomitant_arr'][self::TYPE_PRODUCT];
                }

                if ($coupon->isForAll()) {
                    $result[$key]['products'][$uniq] = $product['id'];

                    //Если есть сопутствующие, то и их добавим
                    if (!empty($concomitants)){
                        $result[$key]['products'] = $result[$key]['products'] + array_combine((array)$concomitants, (array)$concomitants);
                    }

                } else { //Если у купона скидка на определенные товары и группы
                    $in_group = false;
                    $groups = array_merge($product['xdir'], $product['xspec']);
                    foreach($groups as $dir) {
                        if (@in_array($dir, (array)$coupon_products['group'])) {
                            $in_group = true;
                            break;
                        }
                    }
                    //Посмотрим по отдельным товарам
                    if (@in_array($product['id'], (array)$coupon_products[self::TYPE_PRODUCT]) || $in_group) {
                        $result[$key]['products'][$uniq] = $product['id'];
                    }
                    //Если есть сопутствующие, то и их добавим
                    if (!empty($concomitants)){
                        foreach ((array)$concomitants as $concomitant){

                            $in_group = false;
                            $product  = new Product($concomitant);
                            $product->fillCategories();
                            $groups = array_merge($product['xdir'], $product['xspec']);
                            foreach($groups as $dir) {
                                if (@in_array($dir, (array)$coupon_products['group'])) {
                                    $in_group = true;
                                    break;
                                }
                            }
                            if (@in_array($concomitant, (array)$coupon_products[self::TYPE_PRODUCT]) || $in_group) {
                                $result[$key]['products'][$concomitant] = $concomitant;
                            }
                        }
                    }
                }
            }
        }

        $event_result = EventManager::fire('cart.getcouponsusage.after', [
            'result' => $result,
            'coupons' => $coupons_items,
        ]);
        list($result) = $event_result->extract();

        return $result;
    }


    /**
     * Возвращает объекты купонов на скидку
     *
     * @return array
     * @throws RSException
     */
    function getCouponItems()
    {
        if ($this->cache_coupons === null) {
            $this->cache_coupons = [];
            $ids = [];
            $cartitem_coupon = $this->getCartItemsByType(self::TYPE_COUPON);
            foreach ($cartitem_coupon as $cartitem) {
                $ids[] = $cartitem['entity_id'];
            }
            if (!empty($ids)) {
                $api = new DiscountApi();
                $api->setFilter('id', $ids, 'in');
                $coupons = $api->getAssocList('id');

                foreach($cartitem_coupon as $key => $cartitem) {

                    if (!isset($coupons[$cartitem['entity_id']]) || ($coupons[$cartitem['entity_id']]->isActive() !== true && $this->mode != self::MODE_ORDER)) {
                        $this->removeItem($key); //Если купон стал неактивным, то исключаем его
                    } else {
                        $this->cache_coupons[$key] = [
                            self::TYPE_COUPON => $coupons[ $cartitem['entity_id'] ],
                            self::CART_ITEM_KEY => $cartitem
                        ];
                    }
                }
            }
        }
        return $this->cache_coupons;
    }

    /**
     * Получает сколько раз был использован купон, текущим авторизованным пользователем
     *
     * @param Discount $coupon - объект купона
     * @return mixed
     * @throws DbException
     * @throws RSException
     */
    function getCouponUsedTimesByCurrentUser(Discount $coupon)
    {
        $user = Auth::getCurrentUser();

        $cnt = DBAdapter::sqlExec('SELECT COUNT(*)as cnt FROM('.OrmRequest::make()
                ->select('I.*,O.user_id')
                ->from(new OrderItem(),'I')
                ->join(new Order(),'O.id=I.order_id','O')
                ->where([
                    'I.entity_id' => $coupon['id'],
                    'I.type' => self::TYPE_COUPON,
                ])
                ->where([
                    'O.user_id' => $user['id'],
                ])
                ->toSql().')as AA')
            ->getOneField('cnt',0);

        return $cnt;
    }

    /**
     * Добавляет купон на скидку
     *
     * @param string $code - код купона
     * @param array $extra - массив дополнительных данных
     * @return bool | string возвращает true или текст ошибки
     * @throws DbException
     * @throws RSException
     */
    function addCoupon($code, array $extra = null)
    {
        $discount_api = new DiscountApi();
        /** @var Discount $coupon */
        $coupon = $discount_api->setFilter('code', $code)->getFirst();

        if (!$coupon){
            return t('Купон с таким кодом не найден');
        }
        if (count($this->getCartItemsByType(self::TYPE_COUPON)) >0){
            return t('Нельзя использовать больше одного скидочного купона');
        }
        if ($coupon->isActive() !== true) return $coupon->isActive();

        $min_order_price = $coupon->getMinOrderPrice();
        $cart_data = $this->getCartData(false, false);
        $cart_total = $cart_data['total_base'] + $cart_data['total_discount'];
        if ($min_order_price > $cart_total) {
            return t('Минимальная сумма заказа для применения купона') . " $min_order_price";
        }
        if (($coupon['period'] == 'timelimit') && (date('Y-m-d H:i:s') > $coupon['endtime'])) {
            return t('Срок действия купона истёк');
        }

        //Если действует лимит на использование одним пользователем
        if ($coupon['oneuserlimit'] > 0) {
            //Проверим авторизован ли пользователь
            if (!Auth::isAuthorize()) {
                return t('Для активации купона необходимо авторизоватся');
            } elseif ($this->getCouponUsedTimesByCurrentUser($coupon) >= $coupon['oneuserlimit']) {
                return t('Превышено число использования купона');
            }
        }

        $linked_products = $coupon['products'][self::TYPE_PRODUCT] ?? [];
        $linked_groups = $coupon['products']['group'] ?? [];
        $linked_all = in_array(0, $linked_groups) || (empty($linked_groups) && empty($linked_products));
        if (!$linked_all) {
            $linked_groups = DirApi::getChildsId($linked_groups);
        }
        $remaining_discount_limit = $this->getItemsRemainingDiscountLimit();
        $linked_remaining_discount_limit = [];
        foreach ($this->getProductItemsWithConcomitants() as $uniq => $basket_item) {
            /** @var AbstractCartItem $cart_item */
            $cart_item = $basket_item[self::CART_ITEM_KEY];
            /** @var Product $product */
            $product = $basket_item[self::TYPE_PRODUCT];

            if (!$cart_item->getForbidDiscounts() && ($linked_all || array_intersect($product['xdir'], $linked_groups) || in_array($product['id'], $linked_products))) {
                $linked_remaining_discount_limit[$uniq] = $remaining_discount_limit[$uniq];
            }
        }
        if (empty($linked_remaining_discount_limit)) {
            return t('Нет товаров, к которым можно применить скидочный купон %0', [$coupon['code']]);
        }

        //Добавляем событие перед  добавлением купона
        $eresult = EventManager::fire('cart.addcoupon.before', [
            'coupon' => $coupon
        ]);
        if ($eresult->getEvent()->isStopped()) {
            return implode(',', $eresult->getEvent()->getErrors());
        }

        //Добавляем купон в базу
        $item = clone $this->cartitem;
        $item['site_id'] = $this->site_id;
        $item['session_id'] = $this->session_id;
        $item['uniq'] = $this->generateId();
        $item['dateof'] = date('Y-m-d H:i:s');
        $item['user_id'] = Auth::getCurrentUser()['id'];
        $item['type'] = self::TYPE_COUPON;
        $item['entity_id'] = $coupon['id'];
        $item['title'] = t('Купон на скидку %0', [$coupon['code']]);
        if ($extra) {
            foreach ($extra as $key => $value) {
                $item->setExtraParam($key, $value);
            }
        }
        if ($this->mode != self::MODE_PREORDER) {
            $item->insert();
        }

        $this->items[$item['uniq']] = $item;

        if (is_array($this->cache_coupons)) {
            $this->cache_coupons[$item['uniq']] = [
                self::TYPE_COUPON => $coupon,
                self::CART_ITEM_KEY => $item
            ];
        }
        $this->cleanInfoCache();

        //Добавляем событие перед  добавление купона
        EventManager::fire('cart.addcoupon.after', [
            'coupon' => $coupon
        ]);

        return true;
    }

    /**
     * Добавляет ошибки к корзине
     *
     * @param string $message
     * @param bool $can_checkout
     * @param mixed $key
     * @return Cart
     */
    function addUserError($message, $can_checkout = true, $key = null)
    {
        $error = [
            'message' => $message,
            'can_checkout' => $can_checkout,
        ];

        if ($key === null) {
            $this->user_errors[] = $error;
        } else {
            $this->user_errors[$key] = $error;
        }

        return $this;
    }

    /**
     * Возвращает массив с ошибкой по ключу
     *
     * @param string $key - ключ в массиве ошибок
     * @return boolean
     */
    function getUserError($key)
    {
        return isset($this->user_errors[$key]) ? $this->user_errors[$key] : false;
    }

    /**
     * Удаляет одну по ключу или все пользовательские ошибки
     *
     * @param mixed $key
     * @return Cart
     */
    function cleanUserError($key = null)
    {
        if ($key === null) {
            $this->user_errors = [];
        } else {
            unset($this->user_errors[$key]);
        }
        return $this;
    }

    /**
     * Добавляет пользовательские ошибки к результату $result
     *
     * @param array $result - массив со сведениями
     * @return array
     */
    protected function appendUserErrors($result)
    {
        foreach($this->user_errors as $error) {
            $result['errors'][] = $error['message'];
            $result['has_error'] = $result['has_error'] || (!$error['can_checkout']);
        }

        return $result;
    }

    /**
     * Возвращает объект заказа, которому принадлежит корзина
     *
     * @return Order
     */
    function getOrder()
    {
        return $this->order;
    }

    /**
     * Повторяет корзину из определённого заказа, добавляя товары к текущей корзине пользователя
     *
     * @param string $order_num - номер заказа
     * @return boolean
     * @throws RSException
     */
    function repeatCartFromOrder($order_num)
    {
        //Подгрузим заказ
        $order_api = new OrderApi();
        /** @var Order $order */
        $order = $order_api->getById($order_num);

        if ($order){ //Если заказ нашли, то получим его корзину и попрбуем добавить теже товары в текущую корзину

            //Проверим принадлежность именно к данному пользователю
            $current_user = Auth::getCurrentUser();
            if (Auth::isAuthorize() && ($current_user['id'] != $order['user_id'])){
                $this->addUserError(t('Не указан пользователь для заказа №%0', [$order_num]));
                return false;
            }

            $order_cart = $order->getCart()->getOrderData();

            foreach ($order_cart['items'] as $uniq_id => $item) {
                /** @var OrderItem $item */
                $cartitem = $item[self::CART_ITEM_KEY];
                //Получим есть ли такой товар, то добавим его
                /** @var Product $product */
                $product = OrmRequest::make()
                    ->from(new Product())
                    ->where([
                        'id' => $cartitem['entity_id'],
                        'public' => 1
                    ])->object();
                if ($product){ //Добавим товар в корзину
                    $mo_array = [];
                    $multioffers = (array)@unserialize($cartitem['multioffers']);
                    if (!empty($multioffers)){

                        foreach($multioffers as $prop_id=>$multioffer){
                            $mo_array[$prop_id] = $multioffer['value'];
                        }
                    }
                    $this->addProduct($product['id'], $cartitem['amount'], $cartitem['offer'], $mo_array, [], [], null, self::CART_SOURCE_REPEAT_ORDER);
                }
            }
            return true;
        }

        return false;
    }

    /**
     * Возвращает какой размер скидки можно добавить к каждому товару в корзине
     *
     * @return float[]
     * @throws RSException
     */
    public function getItemsRemainingDiscountLimit()
    {
        $shop_config = ConfigLoader::byModule($this);
        $max_item_discount_percent = $shop_config['cart_item_max_discount'];
        $items = $this->getProductItemsWithConcomitants();
        $discount_manager = DiscountManager::instance();

        $result = [];
        foreach ($items as $key => $item) {
            /** @var AbstractCartItem $cart_item */
            $cart_item = $item[self::CART_ITEM_KEY];
            if (!$cart_item->getForbidDiscounts()) {
                $base_cost = $discount_manager->getCartItemBaseCost($cart_item);
                $final_discount = $discount_manager->getCartItemFinalDiscount($cart_item);
                $discount_limit = ($max_item_discount_percent / 100) * $base_cost;

                $result[$key] = ($final_discount < $discount_limit) ? $discount_limit - $final_discount : 0;
            }
        }

        return $result;
    }

    /**
     * Равномерно распределяет указанную сумму между элементами
     *
     * @param float $sum
     * @param float[] $items
     * @return float[]
     */
    public static function evenlyAllocateTheSum(float $sum, array $items): array
    {
        $total = 0;
        foreach ($items as $item) {
            $total += $item;
        }
        if ($sum > $total) {
            return $items;
        }
        $total_share = ($total > 0) ? $sum / $total : 0;

        $result = [];
        $applied_sum = 0;
        foreach ($items as $key => $item) {
            $allocate_part = ceil($item * $total_share);
            if ($allocate_part > $item) {
                $allocate_part = $item;
            }
            if ($applied_sum + $allocate_part > $sum) {
                $allocate_part = $sum - $applied_sum;
            }
            $applied_sum += $allocate_part;
            $result[$key] = $allocate_part;
        }

        return $result;
    }

    /**
     * Вызывает событие "изменения в корзине", обновляет содержимое корзины при изменении, предотвращает циклический вызов себя
     *
     * @param bool $update_items - обновить товарные позиции в БД
     * @return void
     * @throws RSException
     */
    public function triggerChangeEvent(bool $update_items = true): void
    {
        if (!$this->getFlagPreventChangeEvent()) {
            $order = $this->getOrder();
            if ($order && $order->isRefreshMode()) {
                $update_items = false;
            }
            $this->setFlagPreventChangeEvent();
            $item_hashes = [];
            if ($update_items) {
                foreach ($this->getItems() as $item) {
                    $item->beforeWrite($item::UPDATE_FLAG);
                    $item_hashes[$item['uniq']] = md5(serialize($item));
                }
            }

            if ($this->getMode() == CART::MODE_SESSION) {
                $this->applyOldCostDiscount();

                $_SESSION[self::SESSION_CART_PRODUCTS] = [];
                foreach ($this->getCartItemsByType(CartItem::TYPE_PRODUCT) as $cart_item) {
                    $_SESSION[self::SESSION_CART_PRODUCTS][$cart_item['entity_id']][$cart_item['offer']] = $cart_item['amount'];
                }
            }
            DiscountApi::applyCouponPercentDiscountsToCart($this);
            DiscountApi::applyCouponFixedDiscountsToCart($this);

            // TODO описать событие 'cart.change' в документации
            EventManager::fire('cart.change', [
                'cart' => $this,
            ]);

            if ($update_items) {
                foreach ($this->getItems() as $item) {
                    $item->beforeWrite($item::UPDATE_FLAG);
                    if (!isset($item_hashes[$item['uniq']]) || md5(serialize($item)) != $item_hashes[$item['uniq']]) {
                        $item->update();
                    }
                }
            }

            $this->setFlagPreventChangeEvent(false);
        }
    }

    /**
     * Возвращает откорректированное количество товара
     *
     * @param Product $product - объект товара
     * @param int $offer - комплектация
     * @param float $amount - исходное количество
     * @return float
     */
    protected function correctAmount(Product $product, float $amount, ?int $offer): float
    {
        $config = ConfigLoader::byModule($this);
        $amount_step = $product->getAmountStep();

        if ($amount == 0) {
            $amount = ($product->getNum($offer) < $amount_step && $config['allow_buy_all_stock_ignoring_amount_step']) ? $product->getNum($offer) : $amount_step;
        }
        if (fmod($amount, $amount_step) != 0) {
            if (!$config['allow_buy_all_stock_ignoring_amount_step'] || $amount != $product->getNum($offer)) {
                $amount = (ceil($amount / $amount_step) * $amount_step);
            }
        }

        return $amount;
    }

    /**
     * Возвращает флаг, запрещающий вызов события "изменения в корзине"
     *
     * @return bool
     */
    protected function getFlagPreventChangeEvent()
    {
        return $this->prevent_change_event;
    }

    /**
     * Устанавливает флаг, запрещающий вызов события "изменения в корзине"
     *
     * @param bool $value
     * @return void
     */
    protected function setFlagPreventChangeEvent($value = true)
    {
        $this->prevent_change_event = $value;
    }

    /**
     * Возвращает режим работы корзины
     *
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }
}
