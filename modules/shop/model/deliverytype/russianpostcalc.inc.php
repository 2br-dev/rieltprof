<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model\DeliveryType;

use Catalog\Model\CostApi;
use Main\Model\Requester\ExternalRequest;
use RS\Config\Loader as ConfigLoader;
use RS\Exception as RSException;
use RS\Orm\FormObject;
use RS\Orm\PropertyIterator;
use RS\Orm\Type;
use Shop\Model\Orm\Address;
use Shop\Model\Orm\Delivery;
use Shop\Model\Orm\Order;

class RussianPostCalc extends AbstractType
{
    const SHORT_NAME = 'russianpostcalc';
    const CALCULATE_URL =  'http://tariff.russianpost.ru';
    const DELIVERY_PERIOD_URL = 'https://delivery.pochta.ru';

    protected $config;

    public function __construct()
    {
        $this->config = ConfigLoader::byModule($this);
    }

    /**
    * Возвращает название расчетного модуля (типа доставки)
    * 
    * @return string
    */
    function getTitle()
    {
        return t('Почта России (калькуляция)');
    }
    
    /**
    * Возвращает описание типа доставки
    * 
    * @return string
    */
    function getDescription()
    {
        return t('Почта России (калькуляция)');
    }
    
    /**
    * Возвращает идентификатор данного типа доставки. (только англ. буквы)
    * 
    * @return string
    */
    function getShortName()
    {
        return self::SHORT_NAME;
    }

    /**
     * Возвращает какие поля адреса необходимы данной доставке
     *
     * @return string[]
     */
    public function getRequiredAddressFields(): array
    {
        return ['zipcode', 'city', 'address'];
    }

    /**
     * Возвращает true если стоимость доставки можно расчитать на основе адреса доставки
     *
     * @param Address $address - адрес
     * @return bool
     */
    public function canCalculateCostByDeliveryAddress(Address $address): bool
    {
        return !empty($address['zipcode']) || $address->getCity()['zipcode'];
    }

    /**
    * Возвращает ORM объект для генерации формы или null
    * 
    * @return \RS\Orm\FormObject | null
    */
    function getFormObject()
    {
        $properties = new PropertyIterator([
            'tariff_code' => new Type\Varchar([
                'description' => t('Код тарифа для калькуляции стоимости отправления' ),
                'list' => [['\Shop\Model\DeliveryType\RusPost\HandBook', 'valuesTariffCode']],
            ]),
            'postoffice_code' => new Type\Integer([
                'description' => t('Индекс места приема'),
            ]),
            'pack' => new Type\Integer([
                'description' => t('Упаковка (Только для тарифа "посылка стандарт")'),
                'listFromArray' => [[
                    '10'    => t('Коробка «S»'),
                    '11'    => t('Пакет полиэтиленовый «S»'),
                    '12'    => t('Конверт с воздушно-пузырчатой пленкой «S»'),
                    '20'    => t('Коробка «М»'),
                    '21'    => t('Пакет полиэтиленовый «М»'),
                    '22'    => t('Конверт с воздушно-пузырчатой пленкой «М»'),
                    '30'    => t('Коробка «L»'),
                    '31'    => t('Пакет полиэтиленовый «L»'),
                    '40'    => t('Коробка «ХL»'),
                    '41'    => t('Пакет полиэтиленовый «ХL»'),
                ]]
            ]),
            'mark_courier' => new Type\Integer([
                'description' => t('Отметка «Курьер»'),
                'checkboxView' => [1,0],
            ]),
            'mark_fragile' => new Type\Integer([
                'description' => t('Отметка «Осторожно/Хрупкая»"'),
                'checkboxView' => [1,0],
            ]),
            'decrease_declared_cost' => new Type\Integer([
                'description' => t('Снижать объявленную стоимость до 1 руб.'),
                'checkboxView' => [1,0],
                'default' => 0,
            ]),
            'write_log' => new Type\Integer([
                'description' => t('Вести лог запросов?'),
                'maxLength' => 1,
                'default' => 0,
                'CheckboxView' => [1, 0],
            ]),
        ]);

        $form_object = new FormObject($properties);
        $form_object->setParentObject($this);
        $form_object->setParentParamMethod('Form');
        return $form_object;
    }

    /**
     * Возвращает ошибки, мешающие выбрать доставку
     *
     * @param Order $order - заказ
     * @return string
     */
    public function getSelectError(Order $order): string
    {
        return '';
    }

    /**
     * Возвращает ошибки, мешающие оформить заказ
     *
     * @param Order $order - заказ
     * @return string
     * @throws RSException
     */
    public function getCheckoutError(Order $order): string
    {
        $this->getDeliveryFinalCost($order);
        if ($this->hasErrors()) {
            return $this->getErrorsStr();
        }

        return '';
    }

    /**
     * Возвращает стоимость доставки  -  отсутствует логика по валюте
     *
     * @param Order $order
     * @param Address|null $address
     * @param Delivery $delivery
     * @param bool $use_currency
     * @return mixed
     * @throws RSException
     */
    function getDeliveryCost(Order $order, Address $address = null, Delivery $delivery, $use_currency = true)
    {
        if ($address === null) {
            $address = $order->getAddress();
        }
        $total_without_delivery_unformatted = $order->getCart()->getTotalWithoutDelivery();
        $params = [
            'tariff_code' => $this->getOption('tariff_code'),
            'indexfrom' => $this->getOption('postoffice_code'),
            'indexto' => $address['zipcode'] ?: $address->getCity()['zipcode'],
            'mass' => (empty($order['true_weight'])) ? $order->getWeight(\Catalog\Model\Api::WEIGHT_UNIT_G) : $order['true_weight'],
            'declared_value' => $total_without_delivery_unformatted,
            'mark_courier' => $this->getOption('mark_courier'),
            'mark_fragile' => $this->getOption('mark_fragile'),
            'decrease_declared_cost' => $this->getOption('decrease_declared_cost'),
        ];
        
        $result = $this->calculateCost($params);

        if (isset($result['errors'])) {
            foreach ($result['errors'] as $error) {
                $this->addError($error['msg']);
            }
            return false;
        } elseif (isset($result['paynds'])) {
            return CostApi::roundCost($result['paynds'] / 100);
        } else {
            $this->addError(t('Недоступно при данных условиях'));
            return false;
        }
    }


    /**
     * Рассчитывает структурированную информацию по сроку, который требуется для доставки товара по заданному адресу
     *
     * @param Order $order объект заказа
     * @param Address $address объект адреса
     * @param Delivery $delivery объект доставки
     * @return Helper\DeliveryPeriod | null
     */
    protected function calcDeliveryPeriod(Order $order, Address $address = null, Delivery $delivery = null)
    {
        if ($address === null) {
            $address = $order->getAddress();
        }

        //Если есть индекс точки назначения
        $params = [
            'json' => 1,
            'object' => $this->getOption('tariff_code'),
            'from' => $this->getOption('postoffice_code'),
            'to' => $address['zipcode'],
            'date' => date('Ymd'),
            'time' => date('Hi')
        ];

        if ($params['from'] && $params['to']) {
            $res = $this->apiRequest('get', self::DELIVERY_PERIOD_URL . '/delivery/v1/calculate', $params);
            if (isset($res['response']['delivery'])) {
                return new Helper\DeliveryPeriod(
                    $res['response']['delivery']['min'],
                    $res['response']['delivery']['max']
                );
            }
        }

        //При невозможности рассчитать в автоматическом режиме используем стандартный механизм сроков через админ.панель
        return parent::calcDeliveryPeriod($order, $address, $delivery);
    }

    /**
     * Рассчитывает стоимость доставки заказа
     *
     * @param array $params - даннные для калькуляции, включает следующие поля:
     *     mail_category - вид РПО
     *     mail_type - тип РПО
     *     indexto - индекс получателя
     *     mass - вес заказа
     *     mark_courier - отметка "курьер"
     *     mark_fragile - отметка "хрупкое"
     *     declared_value - объявленная стоимость
     */
    function calculateCost($params)
    {
        $tariff_list = \Shop\Model\DeliveryType\RusPost\HandBook::valuesTariffCode();
        $tariff_text = $tariff_list[$params['tariff_code']];
        $request_params = [
            'json' => 1,
            'object' => $params['tariff_code'],
            'from' => $params['indexfrom'],
            'to' => $params['indexto'],
            'weight' => $params['mass'],
            'pack' => $this->getOption('pack'),
        ];
        $request_params['service'] = [];
        if ($params['mark_courier']) {
            $request_params['service'][] = 26;
        }
        if ($params['mark_fragile']) {
            $request_params['service'][] = 4;
        }
        if (strpos($tariff_text, 'объявленной ценностью')) {
            if (strpos($tariff_text, 'наложенным платежом')) {
                $request_params['sumoc'] = $params['declared_value'] * 100;
                $request_params['sumnp'] = $params['declared_value'] * 100;
            } else {
                $request_params['sumoc'] = ($params['decrease_declared_cost']) ? 100 : $params['declared_value'] * 100;
            }
        }

        $res = $this->apiRequest('get', self::CALCULATE_URL . '/tariff/v1/calculate', $request_params);
        return $res['response'];
    }
    /**
     * API запрос
     *
     * @param string $method - метод (get|post|put|delete)
     * @param string $request - адрес запроса
     * @param array $params - параметры запроса
     * @return array
     */
    protected function apiRequest($method, $request, $params = [])
    {
        $response = (new ExternalRequest('delivery_russianpost_calc', $request))
            ->setMethod($method)
            ->setAuthorization('AccessToken ' . $this->config['token'])
            ->addHeader('X-User-Authorization', 'Basic ' . $this->config['auth_key'])
            ->setContentType(ExternalRequest::CONTENT_TYPE_JSON)
            ->setParams($params)
            ->setEnableLog((bool)$this->getOption('write_log'))
            ->executeRequest();

        $response_code = $response->getStatus();

        $result = [];
        if ($response->getStatus() != 200) {
            $result['error'] = $response_code;
        }
        $result['response'] = $response->getResponseJson();

        return $result;
    }

    /**
     * Рекурсивно превращает объект в массив
     *
     * @param mixed $obj - объект, который нужно преобразовать
     * @return array
     */
    protected function objToArray($obj)
    {
        $result = [];
        foreach ($obj as $key=>$item) {
            if (in_array(gettype($item), ['array', 'object'])) {
                $result[$key] = $this->objToArray((array)$item);
            } else {
                $result[$key] = $item;
            }
        }
        return $result;
    }

    /**
     * Действие с запросами к заказу для получения дополнительной информации от доставки
     *
     * @param Order $order - объект заказа
     * @return string
     */
    function actionOrderQuery(Order $order)
    {
        $url = new \RS\Http\Request();
        $method = $url->request('method', TYPE_STRING, false);
        switch ($method){
            case "createOrder": //Получение статуса заказа
                return $this->actionCreateOrder($order); 
                break;
            case "deleteOrder": //Получение статуса заказа
                return $this->actionDeleteOrder($order); 
                break;
        }
    }

    /**
     * Возвращает текст, в случае если доставка невозможна. false - в случае если доставка возможна
     *
     * @param Order $order
     * @param Address $address - Адрес доставки
     * @return mixed
     * @throws RSException
     */
    public function somethingWrong(Order $order, Address $address = null)
    {
        $this->getDeliveryFinalCost($order);

        if ($this->hasErrors()) { //Если есть ошибки
            return $this->getErrorsStr();
        }
        return false;
    }

    /**
     * Возвращает трек номер для отслеживания
     *
     * @param Order $order - объект заказа
     * @return boolean
     */
    public function getTrackNumber(Order $order)
    {
        return !empty($order['track_number']) ? $order['track_number'] : false;
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
        $track_number = $this->getTrackNumber($order);
        if ($track_number) {
            return "https://www.pochta.ru/tracking#" . $track_number;
        }
        return false;
    }
}
