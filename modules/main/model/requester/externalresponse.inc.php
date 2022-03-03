<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/ declare(strict_types=1);

namespace Main\Model\Requester;

/**
 * Ответ внешнего запроса
 */
class ExternalResponse
{
    /** @var int */
    protected $status;
    /** @var string[] */
    protected $headers;
    /** @var string */
    protected $response;

    public function __construct(int $status, array $headers, string $response)
    {
        $this->setStatus($status);
        $this->setHeaders($headers);
        $this->setResponse($response);
    }

    /**
     * Возвращает статус ответа
     *
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * Устанавливает статус ответа
     *
     * @param int $status - статус ответа
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    /**
     * Возвращает заголовки ответа
     *
     * @return string[]
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Устанавливает заголовки ответа
     *
     * @param string[] $headers - заголовки ответа
     */
    public function setHeaders($headers): void
    {
        $this->headers = $headers;
    }

    /**
     * Возвращает ответ в виде раскодированного JSON
     *
     * @return mixed
     */
    public function getResponseJson()
    {
        return json_decode($this->getRawResponse(), true);
    }

    /**
     * Возвращает ответ в виде раскодированного XML
     *
     * @return \SimpleXMLElement
     */
    public function getResponseXml()
    {
        return new \SimpleXMLElement($this->getRawResponse());
    }

    /**
     * Возвращает тело ответа в исходном виде
     *
     * @return string
     */
    public function getRawResponse(): string
    {
        return $this->response;
    }

    /**
     * Устанавливает тело ответа
     *
     * @param string $response - тело ответа
     */
    public function setResponse(string $response): void
    {
        $this->response = $response;
    }
}
