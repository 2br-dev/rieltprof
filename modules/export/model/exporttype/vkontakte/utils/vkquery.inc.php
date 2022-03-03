<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Export\Model\ExportType\Vkontakte\Utils;

use RS\Helper\Log;
use RS\Helper\Tools;
use RS\Module\AbstractModel\BaseModel;

/**
 * Класс для составления запросов к VK
 *
 * Class VkQuery
 * @package Export\Model\ExportType\Vkontakte
 */
class VkQuery extends BaseModel
{
    private static $request_counter = 0;

    public $request_per_second = 3;
    public $timeout = 10;
    private $version;
    private $last_error_code;
    /**
     * @var $log Log
     */
    private $log;

    public function __construct($version = null, Log $log = null)
    {
        $this->version = $version ?: 5.95;
        $this->log = $log;
    }

    /**
     * Составляет выражение для обращения к VKAPI и возвращает ответ от сервера Vkontakte
     *
     * @param array $params
     * @param $method
     * @param $profile_id
     *
     * @return array | bool(false)
     */
    public function query(array $request_params, $method, $profile_id = null)
    {
        $this->last_error_code = null;
        $request_params['v'] = $this->version;

        $context = ([
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/x-www-form-urlencoded' . PHP_EOL,
                'content' => http_build_query($request_params),
                'timeout' => $this->timeout
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ]
        ]);

        $context = stream_context_create($context);
        $url = "https://api.vk.com/method/".$method;

        if ($this->log) {
            $request_string = [];
            foreach($request_params as $key => $value) {
                $request_string[] = $key."=".(is_scalar($value) ? $value : print_r($value, true));
            }

            $this->log->append(t('--> Запрос на адрес %url с параметрами %params' , [
                'url' => $url,
                'params' => implode(', ', $request_string)
            ]));
        }

        $response = file_get_contents($url, true, $context);

        if ($this->log) {
            $this->log->append(t('<-- Ответ на запрос: %response', [
                'response' => $response
            ]));
        }

        self::$request_counter++;
        if (self::$request_counter == $this->request_per_second) {
            self::$request_counter = 0;
            sleep(1);
        }

        if ($response === false) {
            return $this->addError(t('Не удалось выполнить запрос к API VK'));
        }

        $result = @json_decode($response, true);
        if ($result === false) {
            return $this->addError(t('Не удалось распарсить JSON данные от API VK').' '.$response);
        }

        if (isset($result['error'])) {
            $this->last_error_code = $result['error']['error_code'];
            return $this->addError(t('Ошибка запроса к VK API: %0', [$result['error']['error_msg']]));
        }

        return $result['response'];
    }

    /**
     * Возвращает код ошибки последнего запроса к API
     *
     * @return integer | null
     */
    function getLastErrorCode()
    {
        return $this->last_error_code;
    }
}