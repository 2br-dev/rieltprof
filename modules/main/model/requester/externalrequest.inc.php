<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/ declare(strict_types=1);

namespace Main\Model\Requester;

use Main\Model\ExternalRequestCacheApi;
use Main\Model\Log\LogExternalRequest;
use RS\Log\AbstractLog;

/**
 * Конструктор внешнего запроса
 */
class ExternalRequest
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_PATCH = 'PATCH';
    const METHOD_DELETE = 'DELETE';
    const CONTENT_TYPE_FORM_DATA = 'application/x-www-form-urlencoded; charset=utf-8';
    const CONTENT_TYPE_JSON = 'application/json; charset=utf-8';
    const CONTENT_TYPE_XML = 'application/xml; charset=utf-8';
    const LOG_OPTION_DONT_WRITE_RESPONSE_BODY = 'dont_write_response_body';

    /** @var string */
    private $source_id = '';
    /** @var string */
    private $url = '';
    /** @var string */
    private $method = self::METHOD_GET;
    /** @var string[] */
    private $headers = [];
    /** @var string */
    private $authorization = '';
    /** @var string */
    private $content_type = '';
    private $params = [];
    /** @var float */
    private $timeout = 20;
    /** @var string */
    private $idempotence_key = '';
    /** @var bool */
    private $enable_cache = true;
    /** @var array */
    private $log_options = [];
    /** @var AbstractLog */
    private $log;
    /** @var string */
    private $log_level_request = LogExternalRequest::LEVEL_REQUEST;
    /** @var string */
    private $log_level_response = LogExternalRequest::LEVEL_RESPONSE;
    /** @var bool */
    private $ignore_errors = true;

    /**
     * @param string $source_id - идентификатор инициатора запроса
     * @param string $url - URL запроса
     * Исключение ExternalRequestException оставлено на ручной контроль
     */
    public function __construct(string $source_id, string $url)
    {
        $this->setSourceId($source_id);
        $this->setUrl($url);
        $this->selfCheck();
        $this->log = LogExternalRequest::getInstance();

    }

    /**
     * Проверяет корректность заполнения обязательных параметров
     *
     * @return void
     * @throws ExternalRequestException
     */
    protected function selfCheck()
    {
        if ($this->getSourceId() == '') {
            throw new ExternalRequestException(t('Не указан идентификатор инициатора запроса'));
        }
        if ($this->getUrl() == '') {
            throw new ExternalRequestException(t('Не указан URL запроса'));
        }
    }

    /**
     * Исполняет сформированный запрос
     *
     * @return ExternalResponse
     */
    public function executeRequest()
    {
        $this->logRequest();

        if ($this->isEnabledCache() && $response = ExternalRequestCacheApi::loadResponseByRequest($this)) {
            $this->logResponse($response, null, true);
        } else {
            $request_time = microtime(true);
            $response_body = (string)@file_get_contents($this->getRequestUrl(), false, $this->createRequestContext());
            $time_to_response = round(microtime(true) - $request_time, 3);
            $response_headers = $http_response_header ?? [];
            $response_status = $this->parseResponseStatusFromHeaders($response_headers);

            $response = new ExternalResponse($response_status, $response_headers, $response_body);
            if ($response_status) {
                ExternalRequestCacheApi::saveResponse($this, $response);
            }
            $this->logResponse($response, $time_to_response);
        }

        return $response;
    }

    /**
     * Извлекает статус ответа из заголовков
     *
     * @param string[] $headers - заголовки ответа
     * @return int
     */
    protected function parseResponseStatusFromHeaders($headers)
    {
        foreach (array_reverse($headers) as $header) {
            if (preg_match('/^HTTP.*?(\d{3})/', $header, $matches)) {
                return (int)$matches[1];
            }
        }
        return 0;
    }

    /**
     * Возвращает итоговый адрес запроса
     *
     * @return string
     */
    protected function getRequestUrl(): string
    {
        $url = $this->getUrl();
        if ($this->getMethod() == self::METHOD_GET && $this->getParams()) {
            $url .= (preg_match('/\?/', $url)) ? '&' : '?';
            $url .= http_build_query($this->getParams());
        }
        return $url;
    }

    /**
     * Возвращает контекст потока для запроса
     *
     * @return resource
     */
    protected function createRequestContext()
    {
        $context_data = [
            'http' => [
                'ignore_errors' => $this->getIgnoreErrors(),
                'method' => $this->getMethod(),
                'header' => $this->getPreparedHeaders(),
            ],
            "ssl" => [
                "verify_peer" => false,
                "verify_peer_name" => false,
            ],
        ];
        if ($this->getMethod() != self::METHOD_GET && $this->getParams()) {
            $context_data['http']['content'] = $this->getPreparedContent();
        }
        if ($this->getTimeout()) {
            $context_data['http']['timeout'] = $this->getTimeout();
        }

        return stream_context_create($context_data);
    }

    /**
     * Возвращает подготовленные заголовки запроса
     *
     * @return string[]
     */
    public function getPreparedHeaders()
    {
        $headers = [];
        foreach ($this->getHeaders() as $name => $value) {
            $headers[] = "$name: $value";
        }
        if ($this->getAuthorization()) {
            $headers[] = "Authorization: {$this->getAuthorization()}";
        }
        if ($this->getContentType()) {
            $headers[] = "Content-Type: {$this->getContentType()}";
        }

        return $headers;
    }

    /**
     * Возвращает параметры запроса, подготовленные с учётом текущего типа содержимого
     *
     * @return mixed
     */
    public function getPreparedContent()
    {
        $params = $this->getParams();
        switch ($this->getContentType()) {
            case self::CONTENT_TYPE_XML:
                if (gettype($params) == 'object' && $params instanceof \SimpleXMLElement) {
                    $params =  $params->asXML();
                }
                break;
            case self::CONTENT_TYPE_JSON:
                if (is_array($params)) {
                    $params = json_encode($params, JSON_UNESCAPED_UNICODE);
                }
                break;
        }
        if (gettype($params) != 'string') {
            $params = http_build_query($params);
        }
        return $params;
    }

    /**
     * Устанавливает базовую http-авторизацию
     *
     * @param string $username - имя пользователя
     * @param string $password - пароль
     * @return self
     */
    public function setBasicAuth(string $username, string $password): self
    {
        return $this->setAuthorization('Basic ' . base64_encode("$username:$password"));
    }

    /**
     * Логирует заголовки и параметры запроса к внешнему серверу
     */
    protected function logRequest()
    {
        if ($this->getLog()->isEnabled() && $this->getLog()->isEnabledLevel($this->log_level_request)) {
            $text = "[{$this->getSourceId()}] ";
            $text .= t('Запрос') . ' - ' . $this->getMethod() . ' - ' . $this->getUrl() . "\n";
            if ($prepared_headers = $this->getPreparedHeaders()) {
                $text .= t('Заголовки запроса') . ' - ' . var_export($prepared_headers, true) . "\n";
            }
            if ($this->getParams()) {
                $text .= t('Параметры запроса') . ' - ' . var_export($this->getParams(), true) . "\n";
            }

            $this->getLog()->write($text, $this->log_level_request);
        }
    }

    /**
     * Логирует заголовки и содержимое ответа от внешнего сервера
     *
     * @param ExternalResponse $response - содержимое ответа
     * @param float|null $time_to_response - время получения ответа
     * @param bool $from_cache - ответ взят из кэша
     */
    protected function logResponse(ExternalResponse $response, float $time_to_response = null, bool $from_cache = false)
    {
        if ($this->getLog()->isEnabled() && $this->getLog()->isEnabledLevel($this->log_level_response)) {
            $text = "[{$this->getSourceId()}] ";
            if ($from_cache) {
                $text .= t('Ответ взят из кэша. Статус ответа - %0', [$response->getStatus()]) . "\n";
            } else {
                $text .= t('Статус ответа - %0 (ответ получен за %1 секунд)', [$response->getStatus(), $time_to_response]) . "\n";
            }
            if ($response->getHeaders()) {
                $text .= t('Заголовки ответа') . ' - ' . var_export($response->getHeaders(), true) . "\n";
            }
            if ($response->getRawResponse()) {
                $text .= t('Тело ответа') . ' - ';
                if ($this->getLogOption(self::LOG_OPTION_DONT_WRITE_RESPONSE_BODY)) {
                    $text .= t('не записывается');
                } else {
                    $text .= var_export($response->getRawResponse(), true) . "\n";
                }
            }

            $this->getLog()->write($text, $this->log_level_response);
        }
    }

    /**
     * Возвращает объект логирования
     *
     * @return AbstractLog
     */
    protected function getLog(): AbstractLog
    {
        return $this->log;
    }

    /**
     * Устанавливает используемый объект логирования
     *
     * @param AbstractLog $log - экземпляр класса логирования
     * @param string $log_level_request - уровень логирования запросов
     * @param string $log_level_response - уровень логирования ответов, если не указан то считается равным $log_level_request
     * @return self
     */
    public function setLog(AbstractLog $log, string $log_level_request, string $log_level_response = null): self
    {
        $this->log = $log;
        $this->log_level_request = $log_level_request;
        $this->log_level_response = ($log_level_response === null) ? $this->log_level_request : $log_level_response;
        return $this;
    }

    /**
     * Возвращает идентификатор инициатора запроса
     *
     * @return string
     */
    public function getSourceId(): string
    {
        return $this->source_id;
    }

    /**
     * Устанавливает идентификатор инициатора запроса
     *
     * @param string $source_id - идентификатор инициатора запроса
     * @return self
     */
    public function setSourceId(string $source_id): self
    {
        $this->source_id = $source_id;
        return $this;
    }

    /**
     * Возвращает адрес запроса
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Устанавливает игнорировать ли не 2xx статус ответов на запросы.
     *
     * @param $bool
     * @return ExternalRequest
     */
    public function setIgnoreErrors($bool): self
    {
        $this->ignore_errors = $bool;
        return $this;
    }

    /**
     * Возвращает игнорировать ли не 2xx статус ответов на запросы.
     *
     * @return bool
     */
    public function getIgnoreErrors()
    {
        return $this->ignore_errors;
    }

    /**
     * Устанавливает адрес запроса
     *
     * @param string $url - адрес запроса
     * @return self
     */
    public function setUrl(string $url): self
    {
        $this->url = $url;
        return $this;
    }

    /**
     * Возвращает метод запроса
     *
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Устанавливает метод запроса
     *
     * @param string $method - метод запроса
     * @return self
     */
    public function setMethod(string $method): self
    {
        $this->method = strtoupper($method);
        return $this;
    }

    /**
     * Возвращает заголовки запроса
     *
     * @return string[]
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Устанавливает заголовки запроса
     *
     * @param string[] $headers - заголовки запроса
     * @return self
     */
    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * Добавляет заголовок к существующему списку заголовков запроса
     *
     * @param string $name - название заголовка
     * @param string $value - значение заголовка
     * @return self
     */
    public function addHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    /**
     * Возвращает тип содержимого в запросе
     *
     * @return string
     */
    public function getContentType(): string
    {
        return $this->content_type;
    }

    /**
     * Устанавливает тип содержимого
     *
     * @param string $content_type - тип содержимого
     * @return self
     */
    public function setContentType(string $content_type): self
    {
        $this->content_type = $content_type;
        return $this;
    }

    /**
     * Возвращает http-авторизацию
     *
     * @return string
     */
    public function getAuthorization(): string
    {
        return $this->authorization;
    }

    /**
     * Устанавливает http-авторизацию
     *
     * @param string $authorization - http-авторизация
     * @return self
     */
    public function setAuthorization(string $authorization): self
    {
        $this->authorization = $authorization;
        return $this;
    }

    /**
     * Возвращает параметры запроса
     *
     * @return mixed
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Устанавливает параметры запроса
     *
     * @param mixed $params
     * @return self
     */
    public function setParams($params): self
    {
        $this->params = $params;
        return $this;
    }

    /**
     * Устанавливает один параметр запроса
     *
     * @param string $key ключ параметра
     * @param mixed $value значение параметра
     * @return self
     */
    public function setParam($key, $value): self
    {
        $this->params[$key] = $value;
        return $this;
    }

    /**
     * Возвращает тайм-аут на чтение в секундах
     *
     * @return float
     */
    public function getTimeout(): float
    {
        return $this->timeout;
    }

    /**
     * Устанавливает тайм-аут на чтение в секундах
     *
     * @param float $timeout - тайм-аут
     * @return self
     */
    public function setTimeout(float $timeout): self
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * Возвращает ключ идемпотентности
     * @return string
     */
    public function getIdempotenceKey(): string
    {
        return $this->idempotence_key;
    }

    /**
     * Устанавливает ключ идемпотентности
     *
     * @param string $idempotence_key - ключ идемпотентности
     * @return self
     */
    public function setIdempotenceKey(string $idempotence_key): self
    {
        $this->idempotence_key = $idempotence_key;
        return $this;
    }

    /**
     * Возвращает флаг кэширования
     *
     * @return bool
     */
    public function isEnabledCache(): bool
    {
        return $this->enable_cache;
    }

    /**
     * Устанавливает флаг кэширования
     *
     * @param bool $enable_cache - значение
     * @return self
     */
    public function setEnableCache(bool $enable_cache = true): self
    {
        $this->enable_cache = $enable_cache;
        return $this;
    }

    /**
     * Возвращает значение опции логирования
     *
     * @param string $option_name - имя опции
     * @param mixed $default - значение по умолчанию
     * @return mixed
     */
    public function getLogOption(string $option_name, $default = null)
    {
        return $this->log_options[$option_name] ?? $default;
    }

    /**
     * Устанавливает значение опции логирования
     *
     * @param string $option_name - имя опции
     * @param mixed $value - знацение опции
     * @return self
     */
    public function setLogOption(string $option_name, $value): self
    {
        $this->log_options[$option_name] = $value;
        return $this;
    }

    /**
     * @deprecated (21.04) - Метод больше не используется в связи с созданием общей системы логирования
     * Устанавливает флаг логирования
     *
     * @param bool $enable_log - значение
     * @return self
     */
    public function setEnableLog(bool $enable_log = true): self
    {
        return $this;
    }
}
