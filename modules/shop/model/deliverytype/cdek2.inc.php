<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/ declare(strict_types=1);

namespace Shop\Model\DeliveryType;

use Catalog\Model\Api as ProductApi;
use Catalog\Model\ProductDimensions;
use RS\Exception as RSException;
use RS\Html\Toolbar\Button as ToolbarButton;
use RS\Html\Toolbar\Element as ToolbarElement;
use RS\Http\Request as HttpRequest;
use RS\Module\Item as ModuleItem;
use RS\Orm\FormObject;
use RS\Orm\PropertyIterator;
use RS\Orm\Type;
use RS\Router\Manager as RouterManager;
use RS\View\Engine as ViewEngine;
use Shop\Model\Cart;
use Shop\Model\DeliveryType\Cdek\CdekApi;
use Shop\Model\DeliveryType\Helper\DeliveryPeriod;
use Shop\Model\DeliveryType\Helper\Pvz;
use Shop\Model\Exception as ShopException;
use Shop\Model\Orm\AbstractCartItem;
use Shop\Model\Orm\Address;
use Shop\Model\Orm\Delivery;
use Shop\Model\Orm\DeliveryOrder;
use Shop\Model\Orm\Order;

class Cdek2 extends AbstractType implements InterfaceDeliveryOrder, InterfaceIonicMobile
{
    use TraitInterfaceDeliveryOrder;

    /** @var CdekApi */
    public $api;

    /**
     * Возвращает название расчетного модуля (типа доставки)
     *
     * @return string
     */
    function getTitle(): string
    {
        return t('СДЭК');
    }

    /**
     * Возвращает описание типа доставки
     *
     * @return string
     */
    function getDescription()
    {
        // todo поправить описание
        $link = RouterManager::obj()->getAdminUrl('edit', ['mod' => 'catalog'], 'modcontrol-control');
        $link .= '#tab-7';
        $regions_link = RouterManager::obj()->getAdminUrl('index', null, 'shop-cdekregionctrl');
        $shop_link = RouterManager::obj()->getAdminUrl('edit', ['mod' => 'shop'], 'modcontrol-control');
        //$shop_link .= '#tab-10';

        $description = t('Доставка СДЭК <br/><br/>
        <div class="notice-box no-padd">
            <div class="notice-bg">
                Для работы доставки необходимо у товаров указать <b>вес</b> и <b>габариты</b>.<br/>
                Значения веса и габаритов по умолчанию можно указать в <u><a href="%link" target="_blank">настройках модуля "Каталог"</a></u>.<br/>
                Для идентификации населённых пунктов используется <u><a href="%regions_link" target="_blank">список регионов СДЭК</a></u><br>
                Подписаться на веб-хуки можно в <u><a href="%shop_link" target="_blank">настройках модуля "Магазин"</a></u>.
            </div>
        </div>', ['link' => $link, 'regions_link' => $regions_link, 'shop_link' => $shop_link]);

        return $description;
    }

    /**
     * Возвращает идентификатор данного типа доставки. (только англ. буквы)
     *
     * @return string
     */
    function getShortName(): string
    {
        return t('cdek_2_0');
    }

    /**
     * Возвращает какие поля адреса необходимы данной доставке
     *
     * @return string[]
     */
    public function getRequiredAddressFields(): array
    {
        $fields = ['country', 'region', 'city'];
        if (!$this->hasPvz()) {
            $fields[] = 'address';
        }
        return $fields;
    }

    /**
     * Возвращает true если стоимость доставки можно расчитать на основе адреса доставки
     *
     * @param Address $address - адрес
     * @return bool
     */
    public function canCalculateCostByDeliveryAddress(Address $address): bool
    {
        return !empty($address['city_id']) || !empty($address['city']);
    }

    /**
     * todo убрать лишние поля
     * Возвращает ORM объект для генерации формы или null
     *
     * @return FormObject
     */
    function getFormObject()
    {
        $properties = new PropertyIterator([
            'test_mode' => (new Type\Integer())
                ->setDescription(t('Тестовый режим'))
                ->setCheckboxView(1, 0)
                ->setDefault(0),
            'secret_login' => (new Type\Varchar())
                ->setDescription(t('Идентификатор клиента API'))
                ->setHint(t('Если не указан - будет взят из настроек модуля "Магазин"')),
            'secret_pass' => (new Type\Varchar())
                ->setDescription(t('Секретный ключ клиента API'))
                ->setHint(t('Если не указан - будет взят из настроек модуля "Магазин"')),
            'city_from' => (new Type\Integer())
                ->setDescription(t('Город отправки'))
                ->setTree('Shop\Model\RegionApi::staticTreeList', 0, [0 => t('- Верхний уровень -')]),
            'phone_from' => (new Type\Varchar())
                ->setDescription(t('Телефон отправителя')),
            'address_from' => (new Type\Varchar())
                ->setDescription(t('Адрес отправки'))
                ->setHint(t('Только для доставки от двери')),
            'pvz_from' => (new Type\Varchar())
                ->setDescription(('ПВЗ отправки'))
                ->setHint(t('Только для доставки от склада'))
                ->setTemplate('%shop%/form/delivery/cdek/pvz_from.tpl'),
            "tariff_priority" => (new Type\Varchar())
                ->setDescription(t('Приоритет тарифов'))
                ->setListFromArray([
                    CdekApi::TARIFF_PRIORITY_SORT => t('В указанном порядке'),
                    CdekApi::TARIFF_PRIORITY_PRICE => t('По стоимости доставки'),
                    CdekApi::TARIFF_PRIORITY_TIME => t('По сроку доставки'),
                ])
                ->setDefault(CdekApi::TARIFF_PRIORITY_SORT),
            'tariffTypeCode' => (new Type\Integer())
                ->setDescription(t('Список тарифов (техническое поле)'))
                ->setListFromArray(self::handbookTariffList())
                ->setChangeSizeForList(false)
                ->setAttr(['size' => 16])
                ->setVisible(false),
            'tariffTypeList' => (new Type\ArrayList())
                ->setDescription(t('Список используемых тарифов'))
                ->setMaxLength(1000)
                ->setRuntime(false)
                ->setAttr(['multiple' => true])
                ->setListFromArray([])
                ->setTemplate('%shop%/form/delivery/cdek/field_tariff_type_list.tpl'),
            'delivery_points_type' => (new Type\Varchar())
                ->setDescription(t('Отображаемые типы ПВЗ'))
                ->setListFromArray([
                    'ALL' => t('Все ПВЗ независимо от их типа'),
                    'PVZ' => t('Только склады СДЭК'),
                    'POSTAMAT' => t('Только постаматы СДЭК'),
                ]),
            'day_apply_delivery' => (new Type\Integer())
                ->setDescription(t('Количество дней, через сколько будет произведена планируемая отправка заказа'))
                ->setDefault(1),
            'auto_create_delivery_order' => (new Type\Integer())
                ->setDescription(t('Автоматически создавать заказ на доставку при оформлении заказа на сайте'))
                ->setCheckboxView(1, 0)
                ->setDefault(0),
            'default_cash_on_delivery' => (new Type\Integer())
                ->setDescription(t('Наложенный платёж (значение по умолчанию)'))
                ->setHint(t('Используется в случае если в заказе нет указан способ оплаты'))
                ->setCheckboxView(1, 0)
                ->setDefault(0),
            'decrease_declared_cost' => (new Type\Integer())
                ->setDescription(t('Снижать объявленную стоимость товаров до 0'))
                ->setHint(t('Объявленная стоимость влияет стоимость страховки'))
                ->setCheckboxView(1, 0)
                ->setDefault(0),
            'additional_services' => (new Type\ArrayList())
                ->setDescription(t('Дополнительные услуги'))
                ->setListFromArray(self::handbookServices())
                ->setAttr([
                    'size' => 7,
                    'multiple' => true,
                ])
                ->setRuntime(false),
            'forbid_delivery_for_volume_products' => (new Type\Integer())
                ->setDescription(t('Запретить доставку, если <a target="_blank" href="%0">объёмный вес</a> превышает реальный у товаров в заказе', ['https://global.cdek.ru/faq/vse-o-dostavke?faq_search%5Bsearch%5D=%D0%BE%D0%B1%D1%8A%D0%B5%D0%BC%D0%BD%D1%8B%D0%B9+%D0%B2%D0%B5%D1%81']))
                ->setHint(t("Объёмный вес = (Ширина * Длина * Высота) / 5000"))
                ->setCheckboxView(1, 0)
                ->setDefault(0),
            'write_log' => (new Type\Integer())
                ->setDescription(t('Вести лог запросов?'))
                ->setCheckboxView(1, 0)
                ->setDefault(0),
            'timeout' => (new Type\Integer())
                ->setDescription(t('Максимальное время ожидания ответа на запрос (сек)'))
                ->setHint(t('Если в течение указанного времени от сервера СДЭК не будет ответа, то процесс ожидания прервется'))
                ->setDefault(20),



            'delivery_recipient_vat_rate' => new Type\Varchar([
                'description' => t('Ставка НДС за доставку'),
                'default' => '',
                'listFromArray' => [[
                    '' => t('- Не указано -'),
                    'VATX' => 'Без НДС',
                    'VAT0' => '0%',
                    'VAT10' => '10%',
                    'VAT18' => '18%',
                    'VAT20' => '20%',
                ]],
            ]),
        ]);

        $form_object = new FormObject($properties);
        $form_object->setParentObject($this);
        $form_object->setParentParamMethod('Form');
        return $form_object;
    }

    /**
     * Возвращает HTML для приложения на Ionic
     *
     * @param Order $order - объект заказа
     * @param Delivery $delivery - объект доставки
     * @return string
     * @throws \Exception
     * @throws \SmartyException
     */
    function getIonicMobileAdditionalHTML(Order $order, Delivery $delivery)
    {
        $view = new ViewEngine();
        if (!$order) {
            $order = Order::currentOrder();
        }

        $pvz_list = $this->getPvzByAddress($order->getAddress());

        $this->getDeliveryCostText($order, null, $delivery);

        $view->assign([
                'errors' => $this->getErrors(),
                'order' => $order,
                'extra_info' => $order->getExtraKeyPair(),
                'delivery' => $delivery,
                'cdek' => $this,
                'pvz_list' => $pvz_list,
            ] + ModuleItem::getResourceFolders($this));

        return $view->fetch("%shop%/delivery/cdek/mobilesiteapp/pvz.tpl");
    }

    /**
     * Возвращает, поддерживает ли данный способ доставки ПВЗ
     *
     * @return bool
     */
    public function hasPvz(): bool
    {
        return (bool)array_intersect($this->getOption('tariffTypeList', []), self::handbookTariffListToPvz());
    }

    /**
     * Функция срабатывает после создания заказа
     *
     * @param Order $order - объект заказа
     * @param Address $address - Объект адреса
     * @return void
     * @throws RSException
     */
    function onOrderCreate(Order $order, Address $address = null)
    {
        if ($this->getOption('auto_create_delivery_order') && !RouterManager::obj()->isAdminZone()) {
            try {
                $this->createDeliveryOrder($order);
            } catch (ShopException $e) {}
        }
    }

    /**
     * Возвращает ошибки, мешающие выбрать доставку
     *
     * @param Order $order - заказ
     * @return string
     * @throws RSException
     */
    public function getSelectError(Order $order): string
    {
        try {
            if ($this->getOption('forbid_delivery_for_volume_products', false)) {
                $this->checkProductVolumeWeight($order);
            }

            $this->api->getPriorityTariff($order);
        } catch (ShopException $exception) {
            if (in_array($exception->getCode(), [ShopException::ERROR_LIST_DELIVERY_API])) {
                return t('Произошла внутренняя ошибка, свяжитесь с администратором');
            }
            return $exception->getMessage();
        }
        return '';
    }

    /**
     * Проверяет товары заказа на предмет превышения "объёмного веса"
     *
     * @param Order $order - заказ
     * @throws RSException
     */
    protected function checkProductVolumeWeight(Order $order)
    {
        $volume_products = [];
        foreach ($order->getCart()->getProductItems() as $item) {
            /** @var AbstractCartItem $cart_item */
            $cart_item = $item[Cart::CART_ITEM_KEY];
            $product = $cart_item->getEntity();
            $dimensions = $product->getDimensionsObject();

            $length = $dimensions->getLength(ProductDimensions::DIMENSION_UNIT_SM);
            $width = $dimensions->getWidth(ProductDimensions::DIMENSION_UNIT_SM);
            $height = $dimensions->getHeight(ProductDimensions::DIMENSION_UNIT_SM);
            $volume_weight = ($length * $width * $height) / 5000;
            if ($volume_weight > $product->getWeight($cart_item['offer'], ProductApi::WEIGHT_UNIT_KG)) {
                $volume_products[] = $product['barcode'];
            }
        }
        if ($volume_products) {
            throw new ShopException(t('Габариты товаров с арт. "%0" слишком велики для данной доставки', [implode('", "', $volume_products)]));
        }
    }

    /**
     * Возвращает список ПВЗ на основе адреса
     *
     * @param Address $address - адрес получателя
     * @return Pvz[]
     */
    public function getPvzByAddress(Address $address)
    {
        try {
            return $this->api->getPvzList($address);
        } catch (ShopException $e) {
            return [];
        }
    }

    /**
     * Возвращает стоимость доставки для заданного заказа. Только число.
     *
     * @param Order $order - объект заказа
     * @param Address $address - адрес доставки
     * @param Delivery $delivery - объект доставки
     * @param boolean $use_currency - использовать валюту?
     * @return double
     * @throws RSException
     */
    function getDeliveryCost(Order $order, Address $address = null, Delivery $delivery, $use_currency = true)
    {
        try {
            $calculation = $this->api->getPriorityTariff($order);
            return $calculation['delivery_sum'];
        } catch (ShopException $exception) {
            return 0;
        }
    }

    /**
     * Рассчитывает структурированную информацию по сроку, который требуется для доставки товара по заданному адресу
     *
     * @param Order $order - объект заказа
     * @param Address $address - объект адреса
     * @param Delivery $delivery - объект доставки
     * @return DeliveryPeriod|null
     * @throws RSException
     * @throws ShopException
     */
    protected function calcDeliveryPeriod(Order $order, Address $address = null, Delivery $delivery = null)
    {
        $calculate = $this->api->getPriorityTariff($order);
        if (!empty($calculate['period_min']) && !empty($calculate['period_max'])) {
            return new DeliveryPeriod($calculate['period_min'], $calculate['period_max'] + $this->getOption('day_apply_delivery'));
        }

        return null;
    }

    /**
     * Возвращает трек-номер указанного заказа на доставку
     *
     * @param DeliveryOrder $delivery_order - объект заказа на доставку
     * @return string|null
     */
    public function getDeliveryOrderTrackNumber(DeliveryOrder $delivery_order): ?string
    {
        return $delivery_order['data']['cdek_number'] ?? null;
    }

    /**
     * Возвращает список действий, доступных для заказа на доставку
     *
     * @param DeliveryOrder $delivery_order - объект заказка на доставку
     * @return array
     */
    public function getDeliveryOrderActions(DeliveryOrder $delivery_order): array
    {
        $actions = [];

        $actions[] = [
            'title' => t('Печать квитанции к заказу'),
            'class' => 'btn-primary btn-alt',
            'action' => 'print_order',
            'attributes' => [
                'target' => '_blank',
            ],
        ];
        $actions[] = [
            'title' => t('Печать ШК места к заказу'),
            'class' => 'btn-primary btn-alt',
            'action' => 'print_barcode',
            'attributes' => [
                'target' => '_blank',
            ],
        ];
        if (empty($delivery_order['extra']['call_courier_id'])) {
            $actions[] = [
                'title' => t('Вызов курьера'),
                'class' => 'btn-primary btn-alt crud-edit crud-sm-dialog',
                'action' => 'call_courier',
            ];
        } else {
            $actions[] = [
                'title' => t('Информация о вызове курьера'),
                'class' => 'btn-primary btn-alt crud-edit crud-sm-dialog',
                'action' => 'call_courier_info',
            ];
            $actions[] = [
                'title' => t('Отмена вызова курьера'),
                'class' => 'btn-danger btn-alt crud-get',
                'action' => 'call_courier_delete',
            ];
        }
        $actions[] = [
            'title' => t('Регистрация отказа'),
            'class' => 'btn-warning btn-alt crud-get',
            'action' => 'refusal',
            'confirm_text' => t('Вы действительно хотите зарегистрирвать отказ по данному заказу?'),
        ];

        return $actions;
    }

    /**
     * Исполняет действие с заказом на доставку
     * При успехе - возвращает инструкции для вывода, при неудаче - бросает исключение
     *
     * @param HttpRequest $http_request - объект запроса
     * @param Order $order - заказ
     * @param string $action - действие
     * @return array
     * @throws ShopException
     * @throws \SmartyException
     */
    public function executeInterfaceDeliveryOrderAction(HttpRequest $http_request, Order $order, string $action): array
    {
        switch ($action) {
            case 'print_order':
                $delivery_order = $this->getDeliveryOrderFromRequest($http_request, $order);
                $print_uuid = $this->api->createPrintOrder($delivery_order);
                $url = $this->api->getPrintOrder($print_uuid);
                return [
                    'view_type' => 'output',
                    'content_type' => 'application/pdf',
                    'content' => $this->api->getDocument($url),
                ];
            case 'print_barcode':
                $delivery_order = $this->getDeliveryOrderFromRequest($http_request, $order);
                $print_uuid = $this->api->createPrintBarcode($delivery_order);
                $url = $this->api->getPrintBarcode($print_uuid);
                return [
                    'view_type' => 'output',
                    'content_type' => 'application/pdf',
                    'content' => $this->api->getDocument($url),
                ];
            case 'call_courier':
                $delivery_order = $this->getDeliveryOrderFromRequest($http_request, $order);
                $save_url = RouterManager::obj()->getAdminUrl('interfaceDeliveryOrderAction', [
                    'action' => 'call_courier_create',
                    'order_id' => $order['id'],
                    'delivery_order_id' => $delivery_order['id'],
                ]);
                return [
                    'view_type' => 'form',
                    'title' => t('Создать заявку на вызов курьера'),
                    'bottom_toolbar' => new ToolbarElement([
                        'Items' => [
                            new ToolbarButton\Button($save_url, t('Создать заявку'), [
                                'attr' => [
                                    'class' => 'btn btn-sm btn-success crud-form-save',
                                    'data-update-container' => '.delivery-order-view',
                                ],
                            ]),
                        ],
                    ]),
                    'template' => '%shop%/form/delivery/cdek/admin_call_courier_form.tpl',
                    'assign' => [
                        'delivery_order' => $delivery_order,
                        'type_object' => $this,
                        'order' => $order,
                    ],
                ];
            case 'call_courier_create':
                $delivery_order = $this->getDeliveryOrderFromRequest($http_request, $order);
                $this->api->createCallCourier($delivery_order, HttpRequest::commonInstance());
                return [
                    'view_type' => 'message',
                    'message' => t('Заявка на вызов курьера создана'),
                ];
            case 'call_courier_info':
                $delivery_order = $this->getDeliveryOrderFromRequest($http_request, $order);
                $info = $this->api->getCallCourierInfo($delivery_order);
                return [
                    'view_type' => 'form',
                    'title' => t('Информация о заявке на вызов курьера'),
                    'template' => '%shop%/form/delivery/cdek/admin_call_courier_info.tpl',
                    'assign' => [
                        'delivery_order' => $delivery_order,
                        'type_object' => $this,
                        'order' => $order,
                        'info' => $info,
                    ],
                ];
            case 'call_courier_delete':
                $delivery_order = $this->getDeliveryOrderFromRequest($http_request, $order);
                $this->api->deleteCallCourier($delivery_order);
                return [
                    'view_type' => 'message',
                    'message' => t('Заявка на вызов курьера удалена'),
                ];
            case 'refusal':
                $this->api->refuseOrder($this->getDeliveryOrderFromRequest($http_request, $order));
                return [
                    'view_type' => 'message',
                    'message' => t('Отказ зарегистрирован'),
                    'no_update' => true,
                ];
                break;
            default:
                return $this->executeCommonDeliveryOrderAction($http_request, $order, $action);
        }
    }

    /**
     * Создаёт заказ доставки
     *
     * @param Order $order - объект заказа
     * @return DeliveryOrder
     * @throws RSException
     * @throws ShopException
     */
    public function createDeliveryOrder(Order $order): DeliveryOrder
    {
        $delivery_order = $this->api->createOrder($order);
        if (empty($order['track_number']) && $track_number = $this->getDeliveryOrderTrackNumber($delivery_order)) {
            $order['track_number'] = $track_number;
            $order->update();
        }
        return $delivery_order;
    }

    /**
     * Создаёт заказ доставки
     *
     * @param DeliveryOrder $delivery_order - объект заказа на доставку
     * @param Order $order - объект заказа
     * @return DeliveryOrder
     * @throws ShopException
     */
    public function changeDeliveryOrder(DeliveryOrder $delivery_order, Order $order)
    {
        return $this->api->changeOrder($delivery_order, $order);
    }

    /**
     * Удаляет заказ на доставку
     *
     * @param DeliveryOrder $delivery_order - объект заказа на доставку
     * @return void
     * @throws ShopException
     */
    public function deleteDeliveryOrder(DeliveryOrder $delivery_order): void
    {
        $this->api->deleteOrder($delivery_order);
    }

    /**
     * Обновляет данные заказа на доставку
     *
     * @param DeliveryOrder $delivery_order - объект заказа на доставку
     * @return void
     * @throws ShopException
     */
    public function refreshDeliveryOrder(DeliveryOrder $delivery_order): void
    {
        $this->api->refreshOrder($delivery_order);
    }

    /**
     * Возвращает список данных заказа на доставку
     *
     * @param DeliveryOrder $delivery_order - объект заказа на доставку
     * @return array
     */
    public function getDeliveryOrderDataLines(DeliveryOrder $delivery_order): array
    {
        $tariff_code = $delivery_order['data']['tariff_code'];
        $tariff = $tariff_code;
        foreach (self::handbookTariffList() as $list) {
            if (isset($list[$tariff_code])) {
                $tariff = $list[$tariff_code];
            }
        }

        $lines = [];
        $lines[] = [
            'title' => t('Тариф'),
            'value' => $tariff,
        ];
        if (isset($delivery_order['data']['cdek_number'])) {
            $lines[] = [
                'title' => t('Трек-номер'),
                'value' => $delivery_order['data']['cdek_number'],
            ];
        }
        if (isset($delivery_order['data']['comment'])) {
            $lines[] = [
                'title' => t('Комментарий'),
                'value' => $delivery_order['data']['comment'],
            ];
        }
        if (isset($delivery_order['data']['to_location'])) {
            $value = $delivery_order['data']['to_location']['city'];
            if (isset($delivery_order['data']['to_location']['address'])) {
                $value .= ', ' . $delivery_order['data']['to_location']['address'];
            }
            $lines[] = [
                'title' => t('Адрес доставки'),
                'value' => $value,
            ];
        }
        if (isset($delivery_order['data']['delivery_point'])) {
            $lines[] = [
                'title' => t('ПВЗ доставки'),
                'value' => $delivery_order['data']['delivery_point'],
            ];
        }
        if (!empty($delivery_order['data']['statuses'])) {
            $lines[] = [
                'title' => t('Статус'),
                'value' => reset($delivery_order['data']['statuses'])['name'],
            ];
            $status_data = [];
            foreach ($delivery_order['data']['statuses'] as $status) {
                $status_data[] = date('d.m.Y H:i', strtotime($status['date_time'])) . ' - ' . $status['name'] . ' - ' . $status['city'];
            }
            $lines[] = [
                'title' => t('История изменения статусов'),
                'value' => implode('<br>', $status_data),
            ];
        }

        return $lines;
    }

    /**
     * Обрабатывает запрос веб-хука
     *
     * @param HttpRequest $http_request
     * @return string
     * @throws ShopException
     */
    public function executeWebHook(HttpRequest $http_request)
    {
        $json = $http_request->getStreamInput();
        $data = json_decode($json, true);

        if (empty($data['uuid'])) {
            throw new ShopException(t('Запрос не содержит идентификатор заказа на доставку'));
        }

        $delivery_order = DeliveryOrder::loadByWhere(['external_id' => $data['uuid']]);
        $order = new Order($delivery_order['order_id']);

        if (empty($order['id'])) {
            throw new ShopException(t('Связанный заказ уже не существует'));
        }

        $actual_delivery_type = $order->getDelivery()->getTypeObject();

        if (!($actual_delivery_type instanceof Cdek2)) {
            throw new ShopException(t('У заказа сменился тип доставки'));
        }

        $delivery_order = $actual_delivery_type->api->refreshOrder($delivery_order);

        if (empty($order['track_number']) && $track_number = $this->getDeliveryOrderTrackNumber($delivery_order)) {
            $order['track_number'] = $track_number;
            $order->update();
        }

        return '';
    }

    /**
     * Устанавливает настройки типа доставки
     *
     * @param array|null $opt
     * @return void
     */
    public function loadOptions(array $opt = null)
    {
        parent::loadOptions($opt);

        $this->api = new CdekApi();
        $this->api->setTypeObject($this);
    }

    /**
     * Справочник тарифов
     *
     * @return array[]
     */
    public static function handbookTariffList(): array
    {
        return [
            t('Тарифы для интернет-магазина') => [
                7 => t('Международный экспресс документы дверь-дверь'),
                8 => t('Международный экспресс грузы дверь-дверь'),
                136 => t('Посылка склад-склад'),
                137 => t('Посылка склад-дверь'),
                138 => t('Посылка дверь-склад'),
                139 => t('Посылка дверь-дверь'),
                233 => t('Экономичная посылка склад-дверь'),
                234 => t('Экономичная посылка склад-склад'),
                291 => t('CDEK Express склад-склад'),
                293 => t('CDEK Express дверь-дверь'),
                294 => t('CDEK Express склад-дверь'),
                295 => t('CDEK Express дверь-склад'),
                366 => t('Посылка дверь-постамат'),
                368 => t('Посылка склад-постамат'),
                378 => t('Экономичная посылка склад-постамат'),
            ],
            t('Тарифы Китайский экспресс') => [
                243 => t('Китайский экспресс склад-склад'),
                245 => t('Китайский экспресс дверь-дверь'),
                246 => t('Китайский экспресс склад-дверь'),
                247 => t('Китайский экспресс дверь-склад'),
            ],
            t('Тарифы для обычной доставки') => [
                1 => t('Экспресс лайт дверь-дверь'),
                3 => t('Супер-экспресс до 18'),
                5 => t('Экономичный экспресс склад-склад'),
                10 => t('Экспресс лайт склад-склад'),
                11 => t('Экспресс лайт склад-дверь'),
                12 => t('Экспресс лайт дверь-склад'),
                15 => t('Экспресс тяжеловесы склад-склад'),
                16 => t('Экспресс тяжеловесы склад-дверь'),
                17 => t('Экспресс тяжеловесы дверь-склад'),
                18 => t('Экспресс тяжеловесы дверь-дверь'),
                57 => t('Супер-экспресс до 9'),
                58 => t('Супер-экспресс до 10'),
                59 => t('Супер-экспресс до 12'),
                60 => t('Супер-экспресс до 14'),
                61 => t('Супер-экспресс до 16'),
                62 => t('Магистральный экспресс склад-склад'),
                63 => t('Магистральный супер-экспресс склад-склад'),
                118 => t('Экономичный экспресс дверь-дверь'),
                119 => t('Экономичный экспресс склад-дверь'),
                120 => t('Экономичный экспресс дверь-склад'),
                121 => t('Магистральный экспресс дверь-дверь'),
                122 => t('Магистральный экспресс склад-дверь'),
                123 => t('Магистральный экспресс дверь-склад'),
                124 => t('Магистральный супер-экспресс дверь-дверь'),
                125 => t('Магистральный супер-экспресс склад-дверь'),
                126 => t('Магистральный супер-экспресс дверь-склад'),
                361 => t('Экспресс лайт дверь-постамат'),
                363	=> t('Экспресс лайт склад-постамат'),
            ],
        ];
    }

    /**
     * Справочник список тарифов с доставкой до ПВЗ
     *
     * @return int[]
     */
    protected static function handbookTariffListToPvz()
    {
        return [136, 138, 234, 291, 295, 366, 368, 378, 243, 247, 5, 10, 12, 15, 17, 62, 63, 120, 123, 126, 361, 363];
    }

    /**
     * Справочник дополнительные услуги
     *
     * @return string[]
     */
    protected static function handbookServices()
    {
        return [
            'DELIV_WEEKEND' => t('ДОСТАВКА В ВЫХОДНОЙ ДЕНЬ'),
            'TAKE_SENDER' => t('ЗАБОР В ГОРОДЕ ОТПРАВИТЕЛЕ'),
            'DELIV_RECEIVER' => t('ДОСТАВКА В ГОРОДЕ ПОЛУЧАТЕЛЕ'),
            'TRYING_ON' => t('ПРИМЕРКА'),
            'PART_DELIV' => t('ЧАСТИЧНАЯ ДОСТАВКА'),
            'INSPECTION_CARGO' => t('ОСМОТР ВЛОЖЕНИЯ'),
            'REVERSE' => t('РЕВЕРС'),
            'DANGER_CARGO' => t('ОПАСНЫЙ ГРУЗ'),
            'PACKAGE_1' => t('УПАКОВКА 1'),
        ];
    }
}
