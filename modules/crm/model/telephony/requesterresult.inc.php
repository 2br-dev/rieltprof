<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\Telephony;

/**
 * Результат запроса
 */
class RequesterResult
{
    private $raw;
    private $headers;
    private $parsed_headers = [];

    /**
     * Устанавливает ответ в исходном виде
     *
     * @param $response
     * @return self
     */
    public function setRawData($response)
    {
        $this->raw = $response;
        return $this;
    }

    /**
     * Возвращает ответ в исходном виде
     *
     * @return mixed
     */
    public function getRawData()
    {
        return $this->raw;
    }

    /**
     * Возвращает разобранные JSON данные в виде объекта
     *
     * @return mixed
     */
    public function getJsonData()
    {
        return @json_decode($this->raw);
    }

    /**
     * Устанавливает заголовки ответа
     *
     * @param $headers
     * @return self
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
        $this->parsed_headers = [];

        foreach ($this->headers as $header) {
            if (! preg_match('/^([^:]+):(.*)$/', $header, $output)) continue;
            $this->parsed_headers[strtolower($output[1])] = trim($output[2]);
        }

        return $this;
    }

    /**
     * Возвращает статус ответа на запрос
     *
     * @return int
     */
    public function getStatusCode()
    {
        $headers = $this->getHeaders();
        if (isset($headers[0]) && preg_match('|HTTP/(.*?)\s+(\d+)\s+.*|', $headers[0], $match)) {
            return $match[2];
        }

        return 0;
    }

    /**
     * Возвращает массив заголовков ответа на запрос
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Возвращает конкретный заголовок ответа на запрос
     *
     * @param string $key Заголовок
     * @param mixed $default Значение по умолчанию, если такого заголовка нет
     * @return mixed
     */
    public function getHeader($key, $default = null)
    {
        if (isset($this->parsed_headers[strtolower($key)])) {
            return $this->parsed_headers[strtolower($key)];
        }

        return $default;
    }
}