<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/ declare(strict_types=1);

namespace Main\Model;

use Main\Model\Requester\ExternalRequest;
use RS\Config\Loader as ConfigLoader;

class DaDataApi
{
    protected const URL_SUGGEST_ADDRESS = 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address';

    protected $config;

    public function __construct()
    {
        $this->config = ConfigLoader::byModule($this);
    }

    /**
     * Возвращает экземпляр api
     *
     * @return static
     */
    static function getInstance()
    {
        static $instance;
        if ($instance === null) {
            $instance = new static();
        }
        return $instance;
    }

    /**
     * Запрос подсказок адреса
     *
     * @param string $query
     * @return array
     */
    public function getAddressSuggestion(string $query)
    {
        $response = $this->apiRequest(ExternalRequest::METHOD_POST, self::URL_SUGGEST_ADDRESS, [
            'query' => $query,
        ]);

        return $response['suggestions'];
    }

    /**
     * Запрос к api сервиса DaData
     *
     * @param string $method - метод запроса
     * @param string $url - адрес запроса
     * @param array $params - параметры запроса
     * @return mixed
     */
    protected function apiRequest(string $method, string $url, array $params)
    {
        $request = (new ExternalRequest('dadata', $url))
            ->setMethod($method)
            ->addHeader('Accept', 'application/json')
            ->setAuthorization("Token {$this->config['dadata_api_key']}")
            ->setParams($params)
            ->setEnableLog((bool)$this->config['dadata_enable_log']);

        if ($method == ExternalRequest::METHOD_POST) {
            $request->setContentType(ExternalRequest::CONTENT_TYPE_JSON);
        }

        return $request->executeRequest()->getResponseJson();
    }
}
