<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace ExternalApi\Model;

use ExternalApi\Model\Orm\Log;

class LogApi extends \RS\Module\AbstractModel\EntityList
{
    function __construct()
    {
        parent::__construct(new Orm\Log());
    }
    
    /**
    * Сохраняет запись в журнале обращений к API
    * 
    * @param \RS\Http\Request $url
    * @param string $method имя метода API
    * @param array $params параметры API
    * @param array $result ответ сервера
    * @return Orm\Log|false
    */
    public static function writeToLog(\RS\Http\Request $url, $method, $params, $result)
    {
        if (\RS\Config\Loader::byModule(__CLASS__)->enable_request_log) {
            //Очистим старые запросы, которые старше двух месяцев
            \RS\Orm\Request::make()
                ->delete()
                ->from(new \ExternalApi\Model\Orm\Log())
                ->where("dateof < '".date('Y-m-d H:i:s', strtotime("-2 months"))."'")
                ->exec();

            $log = new Orm\Log();
            if (isset($params['token'])) {
                $token = new Orm\AuthorizationToken($params['token']);
                if ($token['token']) {
                    $log['user_id'] = $token['user_id'];
                    $log['token'] = $token['token'];
                    $log['client_id'] = $token['app_type'];
                }
            }
            
            $log['dateof'] = date('c');
            $log['ip'] = $_SERVER['REMOTE_ADDR'];
            $log['method'] = $method;
            $log['request_uri'] = $url->server('REQUEST_URI');
            $log['request_params'] = serialize($params);
            $log['response'] = serialize($result);
            if (is_array($result) && isset($result['error'])) {
                $log['error_code'] = $result['error']['code'];
            }
            $log->insert();
            return $log;
        }
        return false;
    }

    /**
     * Очистка лога
     */
    function clearLog()
    {
        $log = new Orm\Log();
        \RS\Db\Adapter::sqlExec('TRUNCATE '.$log->_getTable());
    }
}