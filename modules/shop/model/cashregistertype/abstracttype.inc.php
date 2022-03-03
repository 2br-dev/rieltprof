<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model\CashRegisterType;

use Catalog\Model\Orm\Product;
use RS\Config\Loader as ConfigLoader;
use RS\Event\Manager as EventManager;
use RS\Exception as RSException;
use RS\Helper\Log;
use RS\Http\Request as HttpRequest;
use RS\Module\Manager as ModuleManager;
use RS\Orm\FormObject;
use RS\Site\Manager as SiteManager;
use Shop\Model\Cart;
use Shop\Model\CashRegisterApi;
use Shop\Model\Log\LogCashRegister;
use Shop\Model\Marking\MarkingApi;
use Shop\Model\Orm\Address;
use Shop\Model\Orm\Delivery;
use Shop\Model\Orm\Order;
use Shop\Model\Orm\OrderItem;
use Shop\Model\Orm\ProductsReturn;
use Shop\Model\Orm\ProductsReturnOrderItem;
use Shop\Model\Orm\Receipt;
use Shop\Model\Orm\Shipment;
use Shop\Model\Orm\ShipmentItem;
use Shop\Model\Orm\Tax;
use Shop\Model\Orm\Transaction;
use Shop\Model\ReceiptInfo;
use Shop\Model\TaxApi;

/**
 * Класс абстрактного типа онлайн касс
 */
abstract class AbstractType
{
    const OPERATION_SELL = "sell"; //Приход
    const OPERATION_SELL_REFUND = "sell_refund"; //Возврат прихода
    const OPERATION_SELL_CORRECTION = "sell_correction"; //Коррекция прихода

    //Лог файл с запросами и пр. информацией
    const LOG_FILE = '/logs/cash_register.log'; //storage/log/....

    const TAX_NONE = 'none';
    const TAX_VAT0 = 'vat0';
    const TAX_VAT10 = 'vat10';
    const TAX_VAT18 = 'vat18';
    const TAX_VAT110 = 'vat110';
    const TAX_VAT118 = 'vat118';
    const TAX_VAT20 = 'vat20';
    const TAX_VAT120 = 'vat120';

    const PAYMENT_TYPE_FROM_ADVANCE = 'from_advance'; //предварительная оплата (зачет аванса и (или) предыдущих платежей)
    const PAYMENT_TYPE_CACHLESS = 'cashless'; //Безналичный платеж

    protected $timeout = 30; //Таймаут на запрос
    protected $errors = [];

    protected $log;
    protected $log_file;
    protected $config;
    /** @var Transaction */
    protected $transaction;

    /**
     * Конструктор класса
     */
    function __construct()
    {
        $this->config = ConfigLoader::byModule("shop");
        $this->log = LogCashRegister::getInstance();
    }

    /**
     * Возвращает поддерживаемый список налогов
     *
     * @return array
     */
    public static function getTaxesList()
    {
        return [];
    }

    /**
     * Возвращает название расчетного модуля (онлайн кассы)
     *
     * @return string
     */
    abstract function getTitle();

    /**
     * Возвращает идентификатор данного типа онлайн кассы. (только англ. буквы)
     *
     * @return string
     */
    abstract function getShortName();

    /**
     * Отправляет запрос на создание чека по транзакции
     *
     * @param Transaction $transaction - объект транзакции
     * @param string $operation_type - тип чека, приход или возврат
     * @return bool
     * @throws RSException
     */
    function createReceipt(Transaction $transaction, $operation_type = self::OPERATION_SELL)
    {
        $this->transaction = $transaction;
        if ($this->makeAuth()) {
            //Подготавливает запрос для чека
            if ($transaction['order_id']) { //Если это оплата заказа
                switch ($transaction['entity']) {
                    case Transaction::ENTITY_SHIPMENT:
                        $shipment = new Shipment($transaction['entity_id']);
                        $receipts = $this->getReceiptsFromShipment($shipment, $operation_type);
                        break;
                    case Transaction::ENTITY_PRODUCTS_RETURN:
                        $products_return = new ProductsReturn($transaction['entity_id']);
                        $receipts = $this->getReceiptsFromProductsReturn($products_return, $operation_type);
                        break;
                    default:
                        $receipts = $this->getReceiptsFromOrder($operation_type);
                }
            } else { //Если это просто пополнение счёта
                $receipts = $this->getReceiptsForPersonalAccount($operation_type);
            }

            foreach ($receipts as $k => $receipt) {
                $this->createReceiptRequest($receipt, $operation_type);
            }
            if (!$this->hasError()) {
                $transaction->no_need_check_sign = true;
                $transaction['receipt'] = Transaction::RECEIPT_IN_PROGRESS;
                $transaction->update();
            }
        }

        return !$this->hasError();
    }

    /**
     * Перегружается у потомка, если для отправки чека необходима авторизация
     *
     * @return boolean
     */
    function makeAuth()
    {
        return true;
    }

    /**
     * Возвращает двумерный массив из товаров на основе возврата. Ключи это порции товаров. Значения, это список товаров.
     *
     * @param ProductsReturn $products_return - возврат
     * @param string $operation_type - тип чека, приход или возврат
     * @return array
     */
    protected function getReceiptsFromProductsReturn(ProductsReturn $products_return, $operation_type)
    {
        $receipts = [];
        $items = $products_return->getReturnItems();
        foreach (array_chunk($items, $this->getMaxReceiptLength()) as $n => $one_receipt) {
            $receipt = [];

            $receipt = $this->addReceiptItemsData($receipt, $one_receipt);
            $receipt = $this->addReceiptOtherData($receipt, $operation_type, $n);

            $receipts[] = $receipt;
        }

        return $receipts;
    }

    /**
     * Возвращает двумерный массив из товаров на основе отрузки. Ключи это порции товаров. Значения, это список товаров.
     *
     * @param Shipment $shipment - ортгрузка
     * @param string $operation_type - тип чека, приход или возврат
     * @return array
     */
    protected function getReceiptsFromShipment(Shipment $shipment, $operation_type)
    {
        $shipment->fillShipmentItems();

        $receipts = [];
        $items = $shipment->getShipmentItems();
        foreach (array_chunk($items, $this->getMaxReceiptLength()) as $n => $one_receipt) {
            $receipt = [];

            $receipt = $this->addReceiptItemsData($receipt, $one_receipt);
            $receipt = $this->addReceiptOtherData($receipt, $operation_type, $n);

            $receipts[] = $receipt;
        }

        return $receipts;
    }

    /**
     * Возвращает двумерный массив из товаров на основе заказа. Ключи это порции товаров. Значения, это список товаров.
     *
     * @param string $operation_type - тип чека, приход или возврат
     * @return array
     */
    protected function getReceiptsFromOrder($operation_type)
    {
        $order = $this->transaction->getOrder();
        $cart = $order->getCart();

        if (!$cart) {  //Если заказ был в это время удалён.
            $this->addError(t('Заказ был удалён'));
            return [];
        }

        $receipts = [];
        $items = array_merge($cart->getCartItemsByType(Cart::TYPE_PRODUCT), $cart->getCartItemsByType(Cart::TYPE_DELIVERY));
        foreach (array_chunk($items, $this->getMaxReceiptLength()) as $n => $one_receipt) {
            $receipt = [];

            $receipt = $this->addReceiptItemsData($receipt, $one_receipt);
            $receipt = $this->addReceiptOtherData($receipt, $operation_type, $n);

            $receipts[] = $receipt;
        }

        return $receipts;
    }

    /**
     * Возвращает чек для пополнения/списания средств с лицевого счета
     *
     * @param string $operation_type - тип чека, приход или возврат
     * @return array
     */
    protected function getReceiptsForPersonalAccount($operation_type)
    {
        $transaction = $this->transaction;
        $sum = abs($transaction['cost']);

        if ($sum < 0) {
            $payment_method = CashRegisterApi::PAYMENT_METHOD_FULL_PAYMENT;
            $payment_object = $transaction['receipt_payment_subject'] ?? 'service';
        } else {
            $payment_method = $this->config['personal_account_payment_method'];
            $payment_object = $this->config['personal_account_payment_subject'];
        }

        $item_data = [
            'name' => $transaction['reason'],
            'price' => (float)abs($sum),
            'quantity' => 1,
            'sum' => (float)abs($sum),
            'payment_method' => $payment_method,
            'payment_object' => $payment_object,
        ];
        $item_data += $this->getItemTaxData(static::TAX_NONE);

        $this->modifyReceiptItemData($item_data);

        $receipt['items'][] = $item_data;
        $receipt['total'] = $sum;

        $receipt = $this->addReceiptOtherData($receipt, $operation_type, 0);

        $receipts = [$receipt];
        return $receipts;
    }

    /**
     * Выполняет запрос на создание чека
     *
     * @param array $receipt - объект чека
     * @param string $operation_type - тип чека
     * @throws RSException
     */
    abstract protected function createReceiptRequest($receipt, $operation_type);

    /**
     * Дополняет чек списком позиций
     *
     * @param array $receipt - данные чека
     * @param array $items - позиции в чеке
     * @return array
     */
    protected function addReceiptItemsData(array $receipt, array $items)
    {
        $receipt_total_sum = 0;

        foreach ($items as $item) {
            if ($item instanceof OrderItem) {
                $item_data = $this->getItemDataFromOrderItem($item);
            }
            if ($item instanceof ShipmentItem) {
                $item_data = $this->getItemDataFromShipmentItem($item);
            }
            if ($item instanceof ProductsReturnOrderItem) {
                $item_data = $this->getItemDataFromProductReturnItem($item);
            }
            $receipt_total_sum += $item_data['sum'] ?? 0;

            $this->modifyReceiptItemData($item_data);

            $result = EventManager::fire('receipt.addreceiptitems', [
                'receipt' => $receipt,
                'item_data' => $item_data,
                'receipt_total_sum' => $receipt_total_sum
            ]);
            $result_array = $result->getResult();
            $receipt   = $result_array['receipt'];
            $item_data = $result_array['item_data'];
            $receipt_total_sum = $result_array['receipt_total_sum'];
            $receipt['items'][] = $item_data;
        }
        $receipt['total'] = $receipt_total_sum;

        return $receipt;
    }

    /**
     * Добавляет в чек дополнительные данные
     *
     * @param array $receipt - уже имеющиеся данные
     * @param string $operation_type - тип операции
     * @param int $receipt_number - порядковый номер чека в группе
     * @return array
     */
    abstract protected function addReceiptOtherData(array $receipt, string $operation_type, int $receipt_number);

    /**
     * Возвращает данные для одной позиции в чеке на основе позиции отгрузки
     *
     * @param ProductsReturnOrderItem $product_return_item
     * @return array
     */
    protected function getItemDataFromProductReturnItem(ProductsReturnOrderItem $product_return_item)
    {
        $product = new Product($product_return_item['entity_id']);

        $result['name'] = $product_return_item['title'];
        if (!empty($product_return_item['model'])) {
            $result['name'] .= " ({$product_return_item['model']})";
        }
        $result['price'] = (float)$product_return_item['cost'];
        $result['quantity'] = (float)$product_return_item['amount'];
        $result['sum'] = (float)($product_return_item['cost'] * $product_return_item['amount']);
        $result['payment_method'] = CashRegisterApi::PAYMENT_METHOD_FULL_PAYMENT;
        $result['payment_object'] = $product['payment_subject'];
        if ($product->getUnit()['stitle']) {
            $result['measurement_unit'] = $product->getUnit()['stitle'];
        }
        $result += $this->getItemTaxData($this->getRightTaxForProduct($this->transaction->getOrder(), $product));

        return $result;
    }

    /**
     * Возвращает данные для одной позиции в чеке на основе позиции отгрузки
     *
     * @param ShipmentItem $shipment_item
     * @return array
     */
    protected function getItemDataFromShipmentItem(ShipmentItem $shipment_item)
    {
        $order_item = $shipment_item->getOrderItem();
        $item_entity = $order_item->getEntity();
        $marked_classes = MarkingApi::getMarkedClasses();

        $result = $this->getItemDataFromOrderItem($order_item);
        $result['price'] = round($shipment_item['cost'] / $shipment_item['amount'], 2);
        $result['quantity'] = (float)$shipment_item['amount'];
        $result['sum'] = (float)$shipment_item['cost'];
        $result['payment_method'] = CashRegisterApi::PAYMENT_METHOD_FULL_PAYMENT;
        if ($item_entity instanceof Product && $item_entity['marked_class'] && isset($marked_classes[$item_entity['marked_class']])) {
            $result['nomenclature_code'] = $marked_classes[$item_entity['marked_class']]->getNomenclatureCode($shipment_item->getUit());
        }

        return $result;
    }

    /**
     * Возвращает данные для одной позиции в чеке на основе товарной позиции
     *
     * @param OrderItem $order_item - товарная позиция
     * @return array
     */
    protected function getItemDataFromOrderItem(OrderItem $order_item)
    {
        $result = [];
        $payment = $this->transaction->getOrder()->getPayment();
        $shop_config = ConfigLoader::byModule('shop');

        $result['name'] = $order_item['title'];
        if (!empty($order_item['model'])) {
            $result['name'] .= " ({$order_item['model']})";
        }
        $result['price'] = round(($order_item['price'] - $order_item['discount']) / $order_item['amount'], 2);
        $result['quantity'] = (float)$order_item['amount'];
        $result['sum'] = (float)($order_item['price'] - $order_item['discount']);
        $result['payment_method'] = $payment['payment_method'] ?: $shop_config['payment_method'];

        switch ($order_item['type']) {
            case OrderItem::TYPE_PRODUCT:
                /** @var Product $product */
                $product = $order_item->getEntity();
                $result['payment_object'] = $product['payment_subject'];
                if ($product->getUnit()['stitle']) {
                    $result['measurement_unit'] = $product->getUnit()['stitle'];
                }
                $result += $this->getItemTaxData($this->getRightTaxForProduct($this->transaction->getOrder(), $product));
                break;
            case OrderItem::TYPE_DELIVERY:
                $delivery = $order_item->getEntity();
                $result['payment_method'] = $delivery['payment_method'] ? $delivery['payment_method'] : $this->transaction->getOrder()->getDefaultPaymentMethod();
                $result['payment_object'] = 'service';
                $result += $this->getItemTaxData($this->getRightTaxForDelivery($this->transaction->getOrder(), $this->transaction->getOrder()->getDelivery()));
                break;
        }

        return $result;
    }

    /**
     * Возвращает данные налогов для позиции в чеке
     *
     * @param string $tax_id - идентификатор налога
     * @return array
     */
    abstract protected function getItemTaxData(string $tax_id);

    /**
     * Возвращает правильный идентификатор налога у товара
     *
     * @param Order $order - объект заказа
     * @param Product $product - объект товара
     * @return string
     */
    protected function getRightTaxForProduct(Order $order, Product $product)
    {
        $address = $order->getAddress();
        $tax_api = new TaxApi();
        $taxes = $tax_api->getProductTaxes($product, $this->transaction->getUser(), $address);

        return $this->fetchVatTax($taxes, $address);
    }

    /**
     * Возвращает налог, который присутствует у доставки
     *
     * @param Order $order
     * @param Delivery $delivery
     * @return string
     */
    protected function getRightTaxForDelivery(Order $order, Delivery $delivery)
    {
        $address = $order->getAddress();

        $tax_api = new TaxApi();
        $taxes = $tax_api->getDeliveryTaxes($delivery, $this->transaction->getUser(), $address);

        return $this->fetchVatTax($taxes, $address);
    }

    /**
     * Находит среди налогов НДС и возвращает его в виде идентификатора АТОЛ
     *
     * @param Tax[] $taxes
     * @param Address $address
     * @return string
     */
    protected function fetchVatTax($taxes, Address $address)
    {
        $tax = new Tax();
        foreach ($taxes as $item) {
            if ($item['is_nds']) {
                $tax = $item;
                break;
            }
        }

        //Получим ставку
        $tax_id = static::TAX_NONE;

        if ($tax['is_nds']) {
            $tax_rate = $tax->getRate($address);
            switch (floatval($tax_rate)) {
                case "10":
                    $tax_id = ($tax['included']) ? static::TAX_VAT110 : static::TAX_VAT10;
                    break;
                case "18":
                    $tax_id = ($tax['included']) ? static::TAX_VAT118 : static::TAX_VAT18;
                    break;
                case "20":
                    $tax_id = ($tax['included']) ? static::TAX_VAT120 : static::TAX_VAT20;
                    break;
                case "0":
                    $tax_id = static::TAX_VAT0;
                    break;
            }
        }
        return $tax_id;
    }

    /**
     * Позволяет модифицировать данные по умолчанию для позиции в чеке
     *
     * @param array $item_data - данные позиции в чеке
     * @return void
     */
    abstract protected function modifyReceiptItemData(array &$item_data);

    /**
     * Отправляет запрос на создание чека корректировки
     *
     * @param $transaction_id - id транзакции
     * @param $form_object - объект с заполненными данными формы, возвращенной методом getCorrectionReceiptFormObject
     */
    abstract function createCorrectionReceipt($transaction_id, $form_object);

    /**
     * Делает запрос на запрос статуса чека и возвращаетданные записывая их в чек, если произошли изменения
     *
     * @param Receipt $receipt - объект чека
     */
    abstract function getReceiptStatus(Receipt $receipt);

    /**
     * Функция обработки запроса продажи от провайдера чека продажи
     *
     * @param HttpRequest $request - объект запроса
     */
    abstract function onResultSell(HttpRequest $request);

    /**
     * Функция обработки запроса продажи от провайдера чека возврата
     *
     * @param HttpRequest $request - объект запроса
     */
    abstract function onResultRefund(HttpRequest $request);

    /**
     * Функция обработки запроса продажи от провайдера чека коррекции
     *
     * @param HttpRequest $request - объект запроса
     */
    abstract function onResultCorrection(HttpRequest $request);

    /**
     * Возвращает объект формы чека коррекции
     *
     * @return FormObject|false Если false, то это означает, что кассовый модуль не поддерживает чеки коррекции
     */
    function getCorrectionReceiptFormObject()
    {
        return false;
    }

    /**
     * Возвраает максимальное количество позиций в чеке
     *
     * @return int
     */
    public function getMaxReceiptLength()
    {
        return 100;
    }

    /**
     * Устанавливает таймаут на запрос
     *
     * @param integer $seconds - количество секунд для таймаутов
     */
    function setTimeout($seconds)
    {
        $this->timeout = $seconds;
    }

    /**
     * Возвращает таймаут для запроса
     *
     */
    function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * Получает значение опции онлайн кассы из модуля конфига модуля
     *
     * @param string $key - ключ опции
     * @param mixed $default - значение по умолчанию
     * @return mixed
     */
    function getOption($key = null, $default = null)
    {
        $config = ConfigLoader::byModule($this);
        if ($key == null) return $config;
        return isset($config[$key]) ? $config[$key] : $default;
    }


    /**
     * Отправляет запрос к АПИ провайдера обмена данными и возвращает результат в нужном типе.
     * В ответ получает ответ либо false, если не удалось сделать запрос, либо результат в том
     * типе, который указан в параметре
     *
     * @param string $url - адрес на который отправить запрос
     * @param mixed $params - дополнительные параметры запроса
     * @param array $headers - массив дополнительных заголовков для запроса
     * @param boolean $ssl - Запрос по SSL защищённому соединению
     * @param string $method - метод отправки GET|POST
     * @param string $post_type - тип отправляемого ответа json|text|xml через POST
     * @param string $answer_type - тип принимаемого ответа json|text|xml
     *
     * @return mixed|false
     */
    function createRequest($url, $params = [], $headers = [], $ssl = true, $method = 'GET', $post_type = 'json', $answer_type = 'json')
    {
        //Создадим запрос
        $opts['http']['method'] = $method;
        $opts['http']['timeout'] = $this->getTimeout(); //Таймаут 30 секунд
        $opts['http']['ignore_errors'] = true;

        $append_headers = [];
        //Заполним параметры
        if (!empty($params)) {
            switch (mb_strtoupper($method)) {
                case "POST": // POST запрос
                    $content = http_build_query($params);
                    $content_type = 'Content-Type: application/x-www-form-urlencoded';
                    switch (mb_strtolower($post_type)) {
                        case "json":
                            $content = json_encode($params, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                            $append_headers[] = "Content-Length: " . strlen($content);
                            $content_type = 'Content-type:application/json; charset=utf-8';
                            break;
                        case "xml":
                            $content = $params;
                            $content_type = 'Content-Type: application/xml';
                            break;
                    }

                    $append_headers[] = $content_type; //Заголовки
                    $opts['http']['content'] = $content;
                    break;
                case "GET": // GET запрос
                default:
                    $params = http_build_query($params);
                    $url = (mb_stripos($url, "?") !== false) ? $url . "&" . $params : $url . "?" . $params;
                    break;
            }
        }

        $headers += $append_headers;

        //Заполним заголовки
        if (!empty($headers)) {
            $opts['http']['header'] = implode("\r\n", $headers);
        }

        if ($ssl) { //Если запрос по SSL
            $opts['ssl']['verify_peer'] = false;
            $opts['ssl']['verify_peer_name'] = false;
        }

        $log_text = 'url: ' . $url . "\n";
        $log_text .= 'params: ' . var_export($params, true) . "\n";
        $log_text .= 'context options ' . var_export($opts, true);
        $this->log->write($log_text, $this->log::LEVEL_OUT);

        $context = stream_context_create($opts);

        //Отправляем запрос
        $response = @file_get_contents($url, false, $context);
        $this->log->write('response: ' . var_export($response, true), $this->log::LEVEL_OUT);

        if ($response !== false) {
            switch (mb_strtolower($answer_type)) {
                case "text":
                    return $response;
                    break;
                case "xml":
                    return @simplexml_load_string($response);
                    break;
                case "json":
                default:
                    return @json_decode($response, true);
                    break;
            }
        } else {
            $err = error_get_last();
            //$this->addError($err['message']);
        }
        return false;
    }

    /**
     * Возвращает url текушего домена
     *
     * @return string
     */
    function getCurrentDomainUrl()
    {
        $current_site = SiteManager::getSite();
        if (ModuleManager::staticModuleExists('partnership') && \RS\Module\Manager::staticModuleEnabled('partnership') && ($partner = \Partnership\Model\Api::getCurrentPartner())) {
            $current_site = $partner;
        }
        $domain = $current_site->getMainDomain();
        return $domain;
    }

    /**
     * Добавляет ошибку в список
     *
     * @param string $message - сообщение об ошибке
     * @param string $fieldname - название поля
     * @param string $form - техническое имя поля (например, атрибут name у input)
     *
     */
    function addError($message, $fieldname = null, $form = null)
    {
        $this->errors[] = $message;
    }

    /**
     * Возвращает true, если имеются ошибки
     *
     * @return bool
     */
    function hasError()
    {
        return !empty($this->errors);
    }

    /**
     * Возвращает полный список ошибок
     * @return array
     */
    function getErrors()
    {
        return $this->errors;
    }

    /**
     * Возвращает строку с ошибками
     * @return string
     */
    function getErrorsStr()
    {
        return implode(", ", $this->errors);
    }

    /**
     * Очищает ошибки
     * @return void
     */
    function cleanErrors()
    {
        $this->errors = [];
    }

    /**
     * Возвращает путь к логу для записи сообщений
     *
     */
    public static function getLogFilename()
    {
        return \Setup::$PATH . \Setup::$STORAGE_DIR . self::LOG_FILE;
    }

    /**
     * Возвращает настройки модуля Касс
     *
     * @return object
     */
    public function getCashRegisterTypeConfig()
    {
        return ConfigLoader::byModule($this);
    }

    /**
     * Возвращает стандартизированный объект информации о чеке
     *
     * @param Receipt $receipt Объект чека
     *
     * @return ReceiptInfo
     */
    public function getReceiptInfo(Receipt $receipt)
    {
        $receipt_info = new ReceiptInfo($receipt);
        return $receipt_info;
    }

    /**
     * Возвращает максимальное количество позиций в чеке
     *
     * @return int
     */
    public static function getMaxReceiptSize()
    {
        return 100;
    }
}
