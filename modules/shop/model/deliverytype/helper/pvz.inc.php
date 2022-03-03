<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/ declare(strict_types=1);

namespace Shop\Model\DeliveryType\Helper;

use Catalog\Model\CurrencyApi;
use RS\Helper\CustomView;

/**
 * Класс отвечает за работу с Пунктами выдачи заказа
 */
class Pvz
{
    protected $code = ''; //Код пункта
    protected $title = ''; //Наименование пункта
    protected $country = ''; //Страна
    protected $region = ''; //Регион
    protected $city = ''; //Город
    protected $address = ''; //Адрес
    protected $phone = ''; //Телефон
    protected $worktime = ''; //Время работы
    protected $coord_x = 0; //Долгота
    protected $coord_y = 0; //Широта
    protected $note = ''; //Дополнительные заметки как пройти в пункт
    protected $cost = 0; //Цена доставки в пункт самовывоза
    protected $payment_by_cards = false; //Оплата картой
    protected $preset = 'islands#redIcon'; //Стиль отображения точки на карте
    protected $extra = [];

    /**
     * Возвращает данные ПВЗ в виде массива
     *
     * @return array
     */
    public function asArray()
    {
        $result = [
            'code' => $this->getCode(),
            'title' => $this->getTitle(),
            'country' => $this->getCountry(),
            'region' => $this->getRegion(),
            'city' => $this->getCity(),
            'address' => $this->getAddress(),
            'phone' => $this->getPhone(),
            'worktime' => $this->getWorktime(),
            'coord_x' => $this->getCoordX(),
            'coord_y' => $this->getCoordY(),
            'note' => $this->getNote(),
            'cost' => $this->getCost(),
            'payment_by_cards' => $this->getPaymentByCards(),
            'preset' => $this->getPreset(),
            'extra' => $this->getExtra(),
        ];

        return $result;
    }

    /**
     * Создаёт объект ПВЗ из массива данных
     *
     * @param array $data - данные ПВЗ
     * @return static
     */
    public static function loadFromArray(array $data)
    {
        $pvz = new static();
        if (isset($data['code'])) {$pvz->setCode((string)$data['code']);}
        if (isset($data['title'])) {$pvz->setTitle((string)$data['title']);}
        if (isset($data['country'])) {$pvz->setCountry((string)$data['country']);}
        if (isset($data['region'])) {$pvz->setRegion((string)$data['region']);}
        if (isset($data['city'])) {$pvz->setCity((string)$data['city']);}
        if (isset($data['address'])) {$pvz->setAddress((string)$data['address']);}
        if (isset($data['phone'])) {$pvz->setPhone((string)$data['phone']);}
        if (isset($data['worktime'])) {$pvz->setWorktime((string)$data['worktime']);}
        if (isset($data['coord_x'])) {$pvz->setCoordX((float)$data['coord_x']);}
        if (isset($data['coord_y'])) {$pvz->setCoordY((float)$data['coord_y']);}
        if (isset($data['note'])) {$pvz->setNote((string)$data['note']);}
        if (isset($data['cost'])) {$pvz->setCost((float)$data['cost']);}
        if (isset($data['payment_by_cards'])) {$pvz->setPaymentByCards((bool)$data['payment_by_cards']);}
        if (isset($data['preset'])) {$pvz->setPreset((string)$data['preset']);}
        if (isset($data['extra'])) {$pvz->setExtra((array)$data['extra']);}

        return $pvz;
    }

    /**
     * Возвращает дополнительный HTML для показа при выборе пункта выдачи заказа
     *
     * @return string
     */
    function getAdditionalHTML()
    {
        return "";
    }

    /**
     * Возвращает наименование пункта доставки
     *
     * @return string
     */
    function getPickPointTitle()
    {
        return $this->getAddress();
    }

    /**
     * Возвращает полный адрес пункта
     *
     * @return string
     */
    function getFullAddress()
    {
        $full_address = [$this->getCountry(), $this->getRegion(), $this->getCity(), $this->getAddress()];
        $full_address = array_diff($full_address, ['']);
        return implode(', ', $full_address);
    }

    /**
     * Возвращает цену доставки в данный пункт самовывоза с учетом валюты
     *
     * @return string
     */
    function getCostText()
    {
        return CustomView::cost($this->getCost(), CurrencyApi::getDefaultCurrency()['stitle']);
    }

    /**
     * Возвращает данные по ПВЗ, которые необходимы для оформления заказа
     *
     * @return string|false
     */
    function getDeliveryExtraJson()
    {
        return $this->jsonEncodeParams($this->asArray());
    }

    /**
     * Кодирование массива в JSON в нужном формате
     *
     * @param array $params - массив параметров
     * @return string|false
     */
    protected function jsonEncodeParams(array $params)
    {
        $flags = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
        return json_encode($params, $flags);
    }

    /**
     * Возвращает код пункта выдачи заказа
     *
     * @return string
     */
    function getCode()
    {
        return $this->code;
    }

    /**
     * Установка кода пункта выдачи заказа
     *
     * @param string $code
     * @return static
     */
    function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Возвращает название пункта выдачи
     *
     * @return string
     */
    function getTitle()
    {
        return $this->title;
    }

    /**
     * Установка названия пункта выдачи
     *
     * @param string $title
     * @return static
     */
    function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Возвращает страну
     *
     * @return string
     */
    function getCountry()
    {
        return $this->country;
    }

    /**
     * Устанавливает страну
     *
     * @param string $country
     * @return static
     */
    function setCountry($country)
    {
        $this->country = $country;
        return $this;
    }

    /**
     * Возвращает регион
     *
     * @return string
     */
    function getRegion()
    {
        return $this->region;
    }

    /**
     * Установка региона
     *
     * @param string $region
     * @return static
     */
    function setRegion($region)
    {
        $this->region = $region;
        return $this;
    }

    /**
     * Возвращает город
     *
     * @return string
     */
    function getCity()
    {
        return $this->city;
    }

    /**
     * Устанавливает город
     *
     * @param string $city
     * @return static
     */
    function setCity($city)
    {
        $this->city = $city;
        return $this;
    }

    /**
     * Возвращает адрес
     *
     * @return string
     */
    function getAddress()
    {
        return $this->address;
    }

    /**
     * Установка адреса
     *
     * @param string $address
     * @return static
     */
    function setAddress($address)
    {
        $this->address = $address;
        return $this;
    }

    /**
     * Вовзращает телефон
     *
     * @return string
     */
    function getPhone()
    {
        return $this->phone;
    }

    /**
     * Устанавливает телефон
     *
     * @param string $phone
     * @return static
     */
    function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * Вовзращает время работы
     *
     * @return string
     */
    function getWorktime()
    {
        return $this->worktime;
    }

    /**
     * Устанавливает время работы
     *
     * @param string $worktime
     * @return static
     */
    function setWorktime($worktime)
    {
        $this->worktime = $worktime;
        return $this;
    }

    /**
     * Возвращает координату долготы
     *
     * @return float
     */
    function getCoordX()
    {
        return $this->coord_x;
    }

    /**
     * Устанавливает координату долготы
     *
     * @param float $coord_x
     * @return static
     */
    function setCoordX($coord_x)
    {
        $this->coord_x = $coord_x;
        return $this;
    }

    /**
     * Возвращает координату широты
     *
     * @return float
     */
    function getCoordY()
    {
        return $this->coord_y;
    }

    /**
     * Устанавливает координату широты
     *
     * @param mixed $coord_y
     * @return static
     */
    function setCoordY($coord_y)
    {
        $this->coord_y = $coord_y;
        return $this;
    }

    /**
     * Возвращает заметки
     *
     * @return string
     */
    function getNote()
    {
        return $this->note;
    }

    /**
     * Устанавливает заметки
     *
     * @param string $note
     * @return static
     */
    function setNote($note)
    {
        $this->note = $note;
        return $this;
    }

    /**
     * Возвращает цену доставки в данный пункт самовывоза
     *
     * @return float
     */
    function getCost()
    {
        return $this->cost;
    }

    /**
     * Устанавливает цену доставки в данный пункт самовывоза
     *
     * @param float $cost - цена доставки
     * @return static
     */
    function setCost($cost)
    {
        $this->cost = $cost;
        return $this;
    }

    /**
     * Возвращает есть оплата картой?
     *
     * @return bool
     */
    function getPaymentByCards()
    {
        return $this->payment_by_cards;
    }

    /**
     * Устанавливает есть оплата картой
     *
     * @param bool $payment_by_cards - Есть оплата картой?
     * @return static
     */
    function setPaymentByCards($payment_by_cards)
    {
        $this->payment_by_cards = $payment_by_cards;
        return $this;
    }

    /**
     * Возвращает стиль отображения точки на карте
     *
     * @return string
     */
    function getPreset()
    {
        return $this->preset;
    }

    /**
     * Устанавливает стиль отображения точки на карте
     *
     * @param string $preset
     * @return static
     */
    function setPreset($preset)
    {
        $this->preset = $preset;
        return $this;
    }

    /**
     * Возвращает дополнительные данные
     *
     * @return array
     */
    function getExtra()
    {
        return $this->extra;
    }

    /**
     * Устанавливает дополнительные данные
     *
     * @param array $extra
     * @return static
     */
    function setExtra($extra)
    {
        $this->extra = $extra;
        return $this;
    }
}
