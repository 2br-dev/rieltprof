<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model\DeliveryType\Cdek;

use Main\Model\Requester\ExternalRequest;
use RS\Config\Loader as ConfigLoader;
use RS\File\Tools as FileTools;

/**
 * @deprecated (20.10) - устарел, будет удалён
 */
class Api
{
    const API_URL = "https://integration.cdek.ru/"; //Основной URL
    const API_URL_CALCULATE = "http://api.cdek.ru/calculator/calculate_price_by_json.php"; //URL для калькуляции доставки
    const API_CALCULATE_VERSION = "1.0"; //Версия API для подсчёта стоимости доставки
    const DEVELOPER_KEY = "522d9ea0ad70744c58fd8d9ffae01fc1";// СДЭК попросил добавить дополнительный атрибут к запросу  28.09.2017
    const DEFAULT_TIMEOUT = 20;

    static protected $inst = null;

    protected $config;
    protected $write_log;
    protected $timeout;

    static public function getInstance()
    {
        if (self::$inst === null) {
            self::$inst = new self(true, self::DEFAULT_TIMEOUT);
        }
        return self::$inst;
    }

    function __construct($write_log, $timeout = self::DEFAULT_TIMEOUT)
    {
        $this->config = ConfigLoader::byModule($this);
        $this->write_log = $write_log;
        $this->timeout = $timeout;
    }

    /**
     * Запрос к серверу рассчета стоимости доставки. Ответ сервера кешируется
     *
     * @param string $script - скрипт
     * @param array $params - массив параметров
     * @param string $method - POST или GET
     * @param bool $use_cache - кэшировать запрос
     * @return mixed
     */
    protected function apiRequest($script, $params = [], $method = "POST", $use_cache = true)
    {
        $is_pvz_request = ($script == 'pvzlist/v1/xml');
        $source_id = ($is_pvz_request) ? 'delivery_cdek_api_pvz' : 'delivery_cdek_api';
        if (!empty($params)) { //Если параметры переданы
            ksort($params);
        }

        $external_request = (new ExternalRequest($source_id, $this->getApiHost() . $script))
            ->setMethod($method)
            ->setParams($params)
            ->setTimeout($this->getTimeout())
            ->setEnableLog((bool)$this->write_log)
            ->setEnableCache($use_cache);

        if ($is_pvz_request) {
            $external_request->setLogOption(ExternalRequest::LOG_OPTION_DONT_WRITE_RESPONSE_BODY, true);
        }

        return $external_request->executeRequest()->getRawResponse();
    }

    /**
     * Получает хост для api
     */
    protected function getApiHost()
    {
        return self::API_URL;
    }

    function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    function getTimeout()
    {
        return ($this->timeout > 0) ? $this->timeout : self::DEFAULT_TIMEOUT;
    }

    function setWriteLog($write_log)
    {
        $this->write_log = $write_log;
    }

    function getWriteLog()
    {
        return $this->write_log;
    }

    //Получение списка ПВЗ	https://integration.cdek.ru/pvzlist/v1/xml
    function getPvzList()
    {
        $filename = \Setup::$PATH . \Setup::$STORAGE_DIR . '/delivery/cdek/pvz_list.txt';

        if (!file_exists($filename) || strtotime('-7 day') > filectime($filename)) {
            $result = $this->apiRequest('pvzlist/v1/xml', [], 'GET');
            FileTools::makePath($filename, true);
            file_put_contents($filename, serialize($result));
        } else {
            $result = @unserialize(file_get_contents($filename)) ?? [];
        }

        return $result;
    }

    //Заказ от ИМ https://integration.cdek.ru/new_orders.php
    function createNewOrder($xml)
    {
        return $this->apiRequest("new_orders.php", ['xml_request' => $xml], 'POST', false);
    }

    //Заказ на доставку https://integration.cdek.ru/addDelivery https://integration.cdek.ru/addDeliveryRaw (для передачи контента в теле запроса)
    function createAddDelivery()
    {

    }

    //Изменение заказа http://integration.cdek.ru/update http://integration.cdek.ru/updateRaw (для передачи контента в теле запроса)
    function updateOrder($xml)
    {

    }

    //Удаление заказа https://integration.cdek.ru/delete_orders.php
    function deleteOrder($xml)
    {
        return $this->apiRequest("delete_orders.php", ['xml_request' => $xml], 'POST', false);
    }

    //Печать квитанции к заказу	https://integration.cdek.ru/orders_print.php
    function getOrdersPrintDocument($xml)
    {

    }

    //Регистрация заявки на  вызова курьера https://integration.cdek.ru/call_courier.php
    function createOrderCallCourier($xml)
    {
        return $this->apiRequest("call_courier.php", ['xml_request' => $xml], 'POST', false);
    }

    //Регистрация информации о результате прозвона https://integration.cdek.ru/new_schedule.php


    //Печать ШК-мест https://integration.cdek.ru/ordersPackagesPrint https://integration.cdek.ru/ordersPackagesPrintRaw (для передачи контента в теле запроса)
    function createOrdersPackagesPrint($xml)
    {

    }

    //Создание преалерта https://integration.cdek.ru/addPreAlert https://integration.cdek.ru/addPreAlertRaw (для передачи контента в теле запроса)


    //Отчет "Статусы заказов"	https://integration.cdek.ru/status_report_h.php
    function getOrderStatusReport($xml)
    {
        return $this->apiRequest("status_report_h.php", ['xml_request' => $xml], 'POST', false);
    }

    //Отчет "Информация по заказам"	https://integration.cdek.ru/info_report.php
    function getOrderInfoRequest($xml)
    {
        return $this->apiRequest("info_report.php", ['xml_request' => $xml], 'POST', false);
    }

    //Список субъектов РФ integration.cdek.ru/v1/location/regions
    function getLocationRegions($params, $method = 'GET', $decode = true)
    {
        if ($decode) {
            return json_decode($this->apiRequest('v1/location/regions/json', $params, $method));
        } else {
            return $this->apiRequest('v1/location/regions/json', $params, $method);
        }

    }

    //Список городов integration.cdek.ru/v1/location/cities
    function getLocationsCities($params, $method = 'GET', $decode = true)
    {
        if ($decode) {
            return json_decode($this->apiRequest('v1/location/cities/json', $params, $method));
        } else {
            return $this->apiRequest('v1/location/cities/json', $params, $method);
        }
    }
}
