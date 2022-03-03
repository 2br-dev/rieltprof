<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Application;

/**
* Класс отвечает за управление заголовками для страницы
*/
class Headers
{
    private
        $cookies = [],
        $headers = [];
    
    /**
    * Добавляет заголовок для последующей отправки в браузер
    * 
    * @param string $key_value - параметр или вся строка заголовка, отправляемая в браузер, например: Content-type или Content-type:text/html; charset=utf-8
    * @param string $value - значение, используется, если задан $key_value,  например: text/html; charset=utf-8
    * @return Headers
    */
    public function addHeader($key_value, $value = null)
    {
        $item = [
            'key' => $key_value,
            'value' => $value
        ];
        
        if ($value !== null) {
            $this->headers[strtolower($key_value)] = $item;
        } else {
            $this->headers[] = $item;
        }
        
        return $this;
    }
    
    /**
    * Добавляет список заголовков к отправке
    * 
    * @param array $key_value - массив с заголовками
    * @return Headers
    */
    public function addHeaders(array $key_value)
    {
        foreach($key_value as $key => $value) {
            if (is_numeric($key)) {
                $this->addHeader($value);
            } else {
                $this->addHeader($key, $value);
            }
        }
        return $this;
    }
    
    /**
    * Удаляет заголовок по ключу
    * 
    * @param string $key - ключ заголовка, например: content-type
    * @return Headers
    */
    public function removeHeader($key)
    {
        unset($this->headers[strtolower($key)]);
        return $this;
    }
    
    /**
    * Удаляет все заголовки, находящиеся в очереди на отправку
    * @return Headers
    */
    public function cleanHeaders()
    {
        $this->headers = [];
        return $this;
    }
    
    /**
    * Возвращает заголовки, находящиеся в очереди на отправку
    * @return array
    */
    public function getHeaders()
    {
        return $this->headers;
    }
    
    /**
    * Добавляет заголовок, отправляющий статус открытия страницы в браузер
    * 
    * @param integer $status_code - номер статуса
    * @param string | null $phrase - поясняющая фраза
    * @return Headers
    */
    public function setStatusCode($status_code, $phrase = null)
    {
        if ($phrase === null) {
            $phrase = \RS\Http\CodeReason::getPhrase($status_code);
        }
        $this->addHeader("HTTP/1.0 $status_code $phrase");
        return $this;
    }
    
    
    /**
    * Отправляет заголовки в браузер и очищает очередь
    * @return Headers
    */
    public function sendHeaders()
    {
        //При запуске из коммандной строки, не отправляем заголовки
        $is_cli = php_sapi_name() == "cli";

        if (!$is_cli) {
            foreach ($this->headers as $item) {
                if ($item['value'] === null) {
                    $header_str = $item['key'];
                } else {
                    $header_str = $item['key'] . ': ' . $item['value'];
                }

                $header_str = str_replace(["\n", "\r"], '', $header_str); //Защита от CRLF атак
                header($header_str);
            }

            foreach ($this->cookies as $cookie) {
                setcookie($cookie['name'], $cookie['value'], $cookie['expire'], $cookie['path'], $cookie['domain'], $cookie['secure'], $cookie['httponly']);
            }
        }

        $this->cleanHeaders();
        $this->cleanCookies();
        
        return $this;
    }
    
    /**
    * Добавляет cookie в очередь для последующей отдачи браузеру
    * 
    * @param string $name
    * @param string | null $value
    * @param string | null $expire
    * @param string | null $path
    * @param string | null $domain
    * @param string | null $secure
    * @param string | null $httponly
    * @param string | null $other_key
    * @return Headers
    */
    public function addCookie($name, $value = null, $expire = null, $path = null, $domain = null, $secure = false, $httponly = false, $other_key = null)
    {
        $key = isset($other_key) ? $other_key : $name;
        $this->cookies[$key] = [
            'name' => $name,
            'value' => $value,
            'expire' => $expire,
            'path' => $path,
            'domain' => $domain,
            'secure' => $secure,
            'httponly' => $httponly
        ];
        return $this;
    }
    
    /**
    * Возвращает массив с очередью cookie
    * @return array
    */
    public function getCookies()
    {
        return $this->cookies;
    }
    
    /**
    * Удаляет cookie из очереди на отправку
    * 
    * @param string $name
    * @return Headers
    */
    public function removeCookie($name)
    {
        unset($this->cookies[$name]);
        return $this;
    }
    
    /**
    * Очищает очередь с cookie
    * @return Headers
    */
    public function cleanCookies()
    {
        $this->cookies = [];
        return $this;
    }
    
}

