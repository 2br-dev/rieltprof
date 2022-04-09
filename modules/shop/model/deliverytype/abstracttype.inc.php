<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/ declare(strict_types=1);

namespace Shop\Model\DeliveryType;

use RS\Config\Loader as ConfigLoader;
use RS\Event\Manager as EventManager;
use RS\Exception as RSException;
use RS\Helper\CustomView;
use RS\Http\Request as HttpRequest;
use RS\Module\Item as ModuleItem;
use RS\Orm\FormObject;
use RS\Orm\Type;
use RS\View\Engine as ViewEngine;
use Shop\Config\File as ShopConfig;
use Shop\Model\AddressApi;
use Shop\Model\DeliveryType\Helper\Pvz;
use Shop\Model\Log\LogDelivery;
use Shop\Model\Orm\Address;
use Shop\Model\Orm\Delivery;
use Shop\Model\Orm\Order;
use Shop\Model\ZoneApi as ShopZoneApi;

/**
 * Абстрактный класс типа доставки. Тип доставки содержит в себе функции для расчета стоимости
 * в зависимости от различных параметров заказа.
 */
abstract class AbstractType
{
    const EXTRA_KEY_PVZ_DATA = 'pvz_data';

    protected $common_log;

    /** @var Delivery */
    protected $delivery;
    protected $opt = [];

    private $errors = [];  // Ошибки доставки

    /**
     * Устанавливает настройки типа доставки
     * Также, в данном методе могут быть инициализированы параметры связанных вспомогательных объектов
     *
     * @param array|null $opt
     * @return void
     */
    public function loadOptions(array $opt = null)
    {
        $this->opt = $opt;
    }

    /**
     * Возвращает название расчетного модуля (типа доставки)
     *
     * @return string
     */
    abstract public function getTitle();

    /**
     * Возвращает описание типа доставки
     *
     * @return string
     */
    abstract public function getDescription();

    /**
     * Возвращает идентификатор данного типа доставки. (только англ. буквы)
     *
     * @return string
     */
    abstract public function getShortName();

    /**
     * Возвращает необходимые поля адреса, исключая определяющие выбор города
     *
     * @return Type\AbstractType[]
     */
    public function getNonCityRequiredAddressFieldsObjects(): array
    {
        $result = [];
        foreach (array_diff($this->getRequiredAddressFields(), Address::$city_selection_stage_fields) as $field_name) {
            if (isset(AddressApi::getInstance()->getElement()["__$field_name"])) {
                $result[$field_name] = AddressApi::getInstance()->getElement()["__$field_name"];
            }
        }
        return $result;
    }

    /**
     * Возвращает список полей адреса, которые являются обязательными для заполнения при выборе данного типа доставки
     *
     * @return string[]
     */
    public function getRequiredAddressFields(): array
    {
        /** @var ShopConfig $config */
        $config = ConfigLoader::byModule('shop');
        return $config->getRequiredAddressFields();
    }

    /**
     * Возвращает true если при оформлении заказа стоимость доставки можно рассчитать на основе адреса доставки, без указания дополнительных параметров
     *
     * @param Address $address - адрес
     * @return bool
     */
    public function canCalculateCostByDeliveryAddress(Address $address): bool
    {
        return true;
    }

    /**
     * Возвращает дополнительную информацию о доставке (например сроки доставки, но можно возвращать еще что-то)
     *
     * @param Order $order - объект заказа
     * @param Address $address - Адрес доставки
     * @param Delivery $delivery - Объект доставки
     *
     * @return string
     */
    public function getDeliveryExtraText(Order $order, ?Address $address = null, ?Delivery $delivery = null)
    {
        if ($period = $this->getDeliveryPeriod($order, $address, $delivery)) {
            $deliveryclass = $delivery->getTypeObject();                         //для работы в других классах доставки необходимо добавить опции
            $to_block = $deliveryclass->getOption('day_apply_delivery_to_block');//	day_apply_delivery и day_apply_delivery_to_block (сейчас только у СДЭКа)

            if ($to_block == 1) {
                $days_max = $period->getDayMax();
                $day_apply_delivery = $deliveryclass->getOption('day_apply_delivery');
                $days_max = (int)$days_max + (int)$day_apply_delivery;
                $period->setDayMax($days_max);
            }

            return $period->getPeriodAsText();
        }
        return '';
    }

    /**
     * Возвращает наличие ошибки, мешающей выбрать доставку
     *
     * @param Order $order - заказ
     * @return bool
     */
    public function hasSelectError(Order $order): bool
    {
        return !empty($this->getSelectError($order));
    }

    /**
     * Возвращает ошибки, мешающие выбрать способ доставки в списке доставок
     *
     * @param Order $order - заказ
     * @return string
     */
    public function getSelectError(Order $order): string
    {
        return '';
    }

    /**
     * Возвращает наличие ошибки, мешающей оформить заказ
     *
     * @param Order $order - заказ
     * @return bool
     */
    public function hasCheckoutError(Order $order): bool
    {
         return !empty($this->getCheckoutError($order));
    }

    /**
     * Возвращает ошибки, мешающие оформить заказ с данным способом доставки
     *
     * @param Order $order - заказ
     * @return string
     */
    public function getCheckoutError(Order $order): string
    {
        return '';
    }

    /**
     * Функция срабатывает после создания заказа в БД
     *
     * @param Order $order - объект заказа
     * @param Address $address - Объект адреса
     * @return void
     */
    public function onOrderCreate(Order $order, Address $address = null)
    {}

    /**
     * Возвращает ORM объект для генерации формы или null
     *
     * @return FormObject | null
     */
    public function getFormObject()
    {
        return null;
    }

    /**
     * Возвращает дополнительный HTML для админ части в заказе
     *
     * @param Order $order - объект заказа
     * @return string
     */
    public function getAdminHTML(Order $order)
    {
        return "";
    }

    /**
     * Возвращает true, если тип доставки предполагает самовывоз
     *
     * @return bool
     */
    public function isMyselfDelivery()
    {
        return false;
    }

    /**
     * Действие с запросами к заказу для получения дополнительной информации от доставки
     *
     * @param Order $order - объект заказа
     */
    public function actionOrderQuery(Order $order)
    {}

    /**
     * Производит валидацию текущих данных в свойствах
     *
     * @param Delivery $delivery - объект способа доставки
     */
    public function validate(Delivery $delivery)
    {}

    /**
     * Возвращает значение доп. поля доставки
     *
     * @param string $key - ключ
     * @param mixed $default - значение по умолчанию
     * @return array|mixed|null
     */
    function getOption($key = null, $default = null)
    {
        if ($key == null) return $this->opt;
        return isset($this->opt[$key]) ? $this->opt[$key] : $default;
    }

    /**
     * Устанавливает значение доп. поля доставки
     *
     * @param string $key_or_array
     * @param mixed $value
     */
    function setOption($key_or_array = null, $value = null)
    {
        if (is_array($key_or_array)) {
            $this->opt = $key_or_array + $this->opt;
        } else {
            $this->opt[$key_or_array] = $value;
        }
    }

    /**
     * Возвращает HTML форму данного типа доставки
     *
     * @return string
     * @throws \SmartyException
     */
    function getFormHtml()
    {
        if ($params = $this->getFormObject()) {
            $params->getPropertyIterator()->arrayWrap('data');
            $params->getFromArray((array)$this->opt);
            $params->setFormTemplate(strtolower(str_replace('\\', '_', get_class($this))));
            $module = ModuleItem::nameByObject($this);
            $tpl_folder = \Setup::$PATH . \Setup::$MODULE_FOLDER . '/' . $module . \Setup::$MODULE_TPL_FOLDER;

            return $params->getForm(null, null, false, null, '%system%/coreobject/tr_form.tpl', $tpl_folder);
        }
        return '';
    }

    /**
     * Возвращает HTML для указазния параметров доставки
     *
     * @param Order $order - заказ
     * @return string
     * @throws \SmartyException
     */
    public function getDeliveryParamsHtml(Order $order): string
    {
        $html = '';

        if ($this->hasPvz()) {
            $view = new ViewEngine();
            $view->assign([
                'delivery_type' => $this,
                'order' => $order,
                'pvz_list' => $this->getPvzByAddress($order->getAddress()),
            ]);

            $html .= trim($view->fetch('%shop%/delivery/delivery_params_pvz.tpl'));
        }

        $html .= $this->getAdditionalHtml($order);

        return $html;
    }

    /**
     * Возвращает дополнительный HTML для публичной части
     *
     * @param Order $order - объект заказа
     * @return string
     */
    protected function getAdditionalHtml(Order $order): string
    {
        return '';
    }

    /**
     * Возвращает HTML для указания параметров доставки в админке
     *
     * @param Order $order - заказ
     * @return string
     * @throws \SmartyException
     */
    public function getAdminDeliveryParamsHtml(Order $order): string
    {
        $html = '';

        if ($this->hasPvz()) {
            $view = new ViewEngine();
            $view->assign([
                'delivery_type' => $this,
                'order' => $order,
                'pvz_list' => $this->getPvzByAddress($order->getAddress()),
            ]);

            $html .= $view->fetch('%shop%/form/order/delivery/delivery_params_pvz.tpl');
        }
        if ($this instanceof InterfaceDeliveryOrder) {
            $html .= $this->getDeliveryOrderAdminDeliveryParamsHtml($order);
        }

        return $html;
    }

    /**
     * Корректировка заказа перед его сохранением
     *
     * @param Order $order - объект заказа
     */
    public function beforeOrderWrite(Order $order)
    {}

    /**
     * Добавление админского комментария об ошибке работы с доставкой
     *
     * @param Order $order - объект заказа
     * @param string $text - текст для добавления
     */
    function addToOrderAdminComment($order, $text)
    {
        $order->addExtraInfoLine(t('Ошибки при работе с Доставкой'), $text, null, 'errors', Order::EXTRAINFOLINE_TYPE_DELIVERY);
    }

    /**
     * Удаление админского комментария об ошибках в доставке
     *
     * @param Order $order - объект заказа
     */
    function removeFromOrderDeliveryErrorInfoLine($order)
    {
        $order->removeExtraInfoLine('errors', Order::EXTRAINFOLINE_TYPE_DELIVERY);
    }

    /**
     * Возвращает итоговую стоимость доставки для заданного заказа.
     *
     * @param Order $order - объект заказа
     * @param Address $address - Адрес доставки
     * @param bool $use_currency - конвертировать стоимость в валюту заказа
     * @return float
     * @throws RSException
     */
    public function getDeliveryFinalCost(Order $order, ?Address $address = null, $use_currency = true)
    {
        if ($address === null) {
            $address = $order->getAddress();
        }

        $cost = $this->getDeliveryCost($order, $address, $this->getDelivery(), $use_currency);
        $cost = $this->applyExtraChangeDiscount($this->getDelivery(), $cost, $order);
        if ($use_currency) {
            $cost = $order->applyMyCurrency($cost);
        }

        return $cost;
    }

    /**
     * Возвращает стоимость доставки для заданного заказа.
     * Данный метод запрещено вызывать напрямую, для получения стоимости доставки используется прослойка getDeliveryFinalCost
     *
     * @param Order $order - объект заказа
     * @param Address $address - Адрес доставки
     * @param Delivery $delivery - объект доставки
     * @param bool $use_currency - конвертировать стоимость в валюту заказа
     * @return float
     */
    abstract public function getDeliveryCost(Order $order, Address $address, Delivery $delivery, $use_currency = true);

    /**
     * Возвращает цену в текстовом формате, т.е. здесь может быть и цена и надпись, например "Бесплатно"
     *
     * @param Order $order - объект заказа
     * @param Address $address - объект адреса
     * @param Delivery $delivery - объект доставки
     * @return string
     * @throws RSException
     */
    public function getDeliveryCostText(Order $order, Address $address, Delivery $delivery)
    {
        $cost = $this->getDeliveryFinalCost($order, $address, $delivery);
        $order_currency = $order->getMyCurrency();
        return ($cost > 0) ? CustomView::cost($cost) . ' ' . $order_currency['stitle'] : t('бесплатно');
    }

    /**
     * Рассчитывает структурированную информацию по сроку, который требуется для доставки товара по заданному адресу
     *
     * @param Order $order объект заказа
     * @param Address $address объект адреса
     * @param Delivery $delivery объект доставки
     * @return Helper\DeliveryPeriod | null
     */
    protected function calcDeliveryPeriod(Order $order, ?Address $address = null, ?Delivery $delivery = null)
    {
        if (!$delivery) {
            $delivery = $order->getDelivery();
        }
        //Здесь у потомков нужно либо реализовать собственный рассчет сроков доставки,
        //либо вернуть заданные у доставки сроки по-умолчанию

        if (!empty($delivery['delivery_periods'])) {
            //Получим все зоны
            $zone_api = new ShopZoneApi();
            $zones = $zone_api->getZonesByRegionId($address['region_id'], $address['country_id'], $address['city_id']);

            foreach ($delivery['delivery_periods'] as $delivery_period) {
                if ($delivery_period['zone'] == 0 || in_array($delivery_period['zone'], $zones)) {

                    return new Helper\DeliveryPeriod(isset($delivery_period['days_min']) ? $delivery_period['days_min'] : null,
                                                     isset($delivery_period['days_max']) ? $delivery_period['days_max'] : null,
                                                     isset($delivery_period['text']) ? $delivery_period['text'] : null);
                }
            }
        }

        return null;
    }

    /**
     * Возвращает структурированную информацию по сроку, который требуется для доставки товара по заданному адресу.
     * Сторонние модули могут откорректировать информацию о сроках доставки
     *
     * @param Order $order объект заказа
     * @param Address $address объект адреса
     * @param Delivery $delivery объект доставки
     * @return Helper\DeliveryPeriod | null
     */
    public function getDeliveryPeriod(Order $order, ?Address $address = null, ?Delivery $delivery = null)
    {
        $period = $this->calcDeliveryPeriod($order, $address, $delivery);

        $event_result = EventManager::fire('delivery.period.correct', [
            'delivery_type' => $this,
            'order' => $order,
            'delivery' => $delivery,
            'address' => $address,
            'period' => $period
        ]);

        $result = $event_result->getResult();
        return $result['period'];
    }

    /**
     * Применяет наценку или скидку к итоговой цене за доставку
     *
     * @param Delivery $delivery - объект доставки
     * @param float $cost - цена за доставку
     * @param Order $order - объект заказа
     * @return int|float
     * @throws RSException
     */
    public function applyExtraChangeDiscount($delivery, $cost, ?Order $order = null)
    {
        $plus_cost = 0;

        if ($order && $order->getCart() && $delivery['free_price'] && $order->getCart()->getTotalWithoutDelivery() >= $delivery['free_price']) {
            $cost = 0;
        } else {
            if (!empty($delivery['extrachange_discount'])) {

                switch ($delivery['extrachange_discount_type']) {
                    case 0: //Если в еденицах
                        $plus_cost = $delivery['extrachange_discount'];
                        break;
                    case 1: //Если в процентах
                        if ($delivery['extrachange_discount_implementation'] == 1) {
                            $plus_cost = ceil(($cost * $delivery['extrachange_discount']) / 100);
                            break;

                        } elseif (($delivery['extrachange_discount_implementation'] == 0) && $order) {
                            $cart = $order->getCart();
                            $itemscost = $cart->getTotalWithoutDelivery();

                            $plus_cost = ceil(($itemscost * $delivery['extrachange_discount']) / 100);
                            break;

                        } elseif (($delivery['extrachange_discount_implementation'] == 2) && $order) {
                            $cart = $order->getCart();
                            $itemscost = $cart->getTotalWithoutDelivery();
                            $total = $itemscost + $cost;
                            $plus_cost = ceil(($total * $delivery['extrachange_discount']) / 100);
                            break;
                        }
                }
            }
        }

        $cost = (float)$cost + $plus_cost;

        //Добавим событие в обработчик
        $eresult = EventManager::fire('deliverytype.applyextrachangediscount', [
            'cost' => $cost,
            'delivery' => $delivery,
            'order' => $order
        ]);
        $result = $eresult->getResult();
        $cost = $result['cost'];

        return ($cost < 0) ? 0 : $cost;
    }

    /**
     * Переводит строку XML в форматированный XML
     *
     * @param string $xml_string - строка XML
     * @return string
     */
    public function toFormatedXML($xml_string)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML("\xEF\xBB\xBF" . $xml_string);
        return html_entity_decode($dom->saveXML());
    }

    /**
     * Записывает в лог файл информацию
     *
     * @param string $key - ключевое слово или фраза для записи, будет записана перед информацией в логе
     * @param mixed $value - информация для записи
     */
    public function writeToLog($key, $value = '')
    {
        if ($this->common_log === null) {
            $this->common_log = LogDelivery::getInstance();
        }

        $text = t("Рассчётный класс:") . get_class($this) . "\r\n";
        $text .= $key . "\r\n";
        if (!is_string($value)) { //Если не строка, то покажем содержимое
            $text .= var_export($value, true) . "\r\n";
        } else {
            $text .= $value . "\r\n";
        }

        $this->common_log->write($text, LogDelivery::LEVEL_MESSAGE);
    }

    /**
     * Возвращает трек номер для отслеживания
     *
     * @param Order $order - объект заказа
     * @return boolean
     */
    public function getTrackNumber(Order $order)
    {
        return false;
    }

    /**
     * Возвращает ссылку на отслеживание заказа
     *
     * @param Order $order - объект заказа
     *
     * @return string
     */
    public function getTrackNumberUrl(Order $order)
    {
        return "";
    }

    /**
     * Возвращает ошибки в виде строки склеевая символами
     *
     * @param string $glue - символы для склеивания
     * @return string
     */
    public function getErrorsStr($glue = ", ")
    {
        return implode($glue, $this->errors);
    }

    /**
     * Получает массив ошибок
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Возвращает есть ошибки при работе метода или нет
     *
     * @return boolean
     */
    public function hasErrors()
    {
        return count($this->errors);
    }

    /**
     * Очищает ошибки доставки
     *
     */
    public function clearErrors()
    {
        $this->errors = [];
    }

    /**
     * Добавляет ошибку в массив ошибок
     *
     * @param string $error_text - текст ошибки
     */
    public function addError($error_text)
    {
        if (!in_array($error_text, $this->errors)) { //Исключим дублирование
            $this->errors[] = $error_text;
        }
    }

    /**
     * Возвращает указанный в заказе ПВЗ
     *
     * @param Order $order - заказ
     * @return Pvz|null
     */
    public function getSelectedPvz(Order $order): ?Pvz
    {
        if ($this->hasPvz()) {
            $delivery_extra = $order->getExtraKeyPair(Order::EXTRAKEYPAIR_DELIVERY_EXTRA);
            if (!empty($delivery_extra[self::EXTRA_KEY_PVZ_DATA])) {
                $pvz_data = json_decode(htmlspecialchars_decode($delivery_extra[self::EXTRA_KEY_PVZ_DATA]), true);
                if ($pvz_data) {
                    return Pvz::loadFromArray($pvz_data);
                }
            }
        }

        return null;
    }

    /**
     * Обрабатывает запрос веб-хука
     *
     * @param HttpRequest $http_request
     * @return string
     */
    public function executeWebHook(HttpRequest $http_request)
    {
        return '';
    }

    /**
     * Возвращает список доступных для указанного адреса ПВЗ
     *
     * @param Address $address - адрес получателя
     * @return Pvz[]
     */
    public function getPvzByAddress(Address $address)
    {
        return [];
    }

    /**
     * Возвращает, поддерживает ли данный способ доставки ПВЗ
     *
     * @return bool
     */
    public function hasPvz(): bool
    {
        return false;
    }

    /**
     * Возвращает объект доставки
     *
     * @return Delivery|null
     */
    public function getDelivery(): ?Delivery
    {
        return $this->delivery;
    }

    /**
     * Устанавливает объект доставки
     *
     * @param Delivery $delivery
     * @return self
     */
    public function setDelivery(Delivery $delivery): self
    {
        $this->delivery = $delivery;
        return $this;
    }

    /**
     * @deprecated (20.10) - устаревший метод для старых шаблонов
     * Возвращает массив фильтров для списка пунктов выдачи
     *
     * @param Order $order - объект заказа
     * @return \Shop\Model\DeliveryType\Helper\FilterType\AbstractFilter[]
     */
    public function getPvzListFilters(?Order $order = null)
    {
        return [];
    }

    /**
     * @deprecated (20.10) - устаревший метод для старых шаблонов
     * Возвращает дополнительный HTML для публичной части для вывода пунктов самовывоза
     *
     * @param Delivery $delivery - объект доставки
     * @param Order|null $order - объект заказа
     * @param array $pickpoints - массив пунктов самовывоза
     * @return string
     * @throws \Exception
     * @throws \SmartyException
     */
    function getAdditionalHtmlForPickPoints(Delivery $delivery, ?Order $order = null, $pickpoints = [])
    {
        $view = new ViewEngine();
        $view->assign([
                'order'         => $order,
                'extra_info'    => $order->getExtraKeyPair(),
                'delivery'      => $delivery,
                'delivery_type' => $this,
                'pickpoints'    => $pickpoints,
            ] + ModuleItem::getResourceFolders($this));

        return $this->wrapByWidjet($delivery, $order, $view->fetch("%shop%/delivery/pickpoints.tpl"));
    }

    /**
     * @deprecated (20.10) - устаревший метод для старых шаблонов
     * Оборачивает в виджет содержимое для вывода
     *
     * @param Delivery $delivery - объект текущей доставки
     * @param Order $order - объект текущего заказа
     * @param string $content - содержимое для обёрки
     * @return string
     * @throws \Exception
     * @throws \SmartyException
     */
    public function wrapByWidjet(Delivery $delivery, Order $order, $content)
    {
        $view = new ViewEngine();
        $view->assign([
                'errors'          => $this->getErrors(),
                'order'           => $order,
                'delivery'        => $delivery,
                'delivery_type'   => $this,
                'wrapped_content' => $content,
            ] + ModuleItem::getResourceFolders($this));

        return $view->fetch("%shop%/delivery/widjet_wrapper.tpl");
    }

    /**
     * @deprecated (20.10) - устаревший метод для старых шаблонов
     * Возвращает дополнительный HTML для публичной части
     *
     * @param Delivery $delivery - объект доставки
     * @param Order|null $order - объект заказа
     * @return string
     * @throws \SmartyException
     */
    public function getAddittionalHtml(Delivery $delivery, ?Order $order = null)
    {
        if ($order === null) {
            return '';
        }
        return $this->getDeliveryParamsHtml($order);
    }

    /**
     * @deprecated (20.10) - устаревший метод для старых шаблонов
     * Возвращает текст, в случае если доставка невозможна. false - в случае если доставка возможна
     *
     * @param Order $order - объект заказа
     * @param Address $address - Адрес доставки
     * @return mixed
     */
    public function somethingWrong(Order $order, ?Address $address = null)
    {
        if ($this->hasSelectError($order)) {
            return $this->getSelectError($order);
        }
        return false;
    }

    /**
     * @deprecated (20.05) - устаревший метод, вместо него используется getPvzByAddress()
     * Возвращает список доступных ПВЗ для переданного заказа
     *
     * @param Order $order - объект заказа
     * @param Address $address - объект адреса
     * @return array | bool - если ПВЗ поддерживаются, false - есть ПВЗ не поддерживаются
     */
    public function getPvzList(Order $order, ?Address $address = null)
    {
        return false;
    }
}
