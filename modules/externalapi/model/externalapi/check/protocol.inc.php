<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace ExternalApi\Model\ExternalApi\Check;
use \ExternalApi\Model\Exception as ApiException;

/**
 * Возвращает протокол, по которому следует работать с API.
 */
class Protocol extends \ExternalApi\Model\AbstractMethods\AbstractMethod
{
    /**
     * Возвращает какими методами могут быть переданы параметры для данного метода API
     *
     * @return array
     */
    public function getAcceptRequestMethod()
    {
        return [GET, POST];
    }



    /**
     * Метод помогает определить протокол, по которому следует работать с API.
     * Используется в мобильных приложениях, если в web-клиенте невозможно выключить FollowLocation.
     *
     * @example GET /api/methods/check.protocol
     *
     * Ответ:
     * <pre>
     * {
     *      'response': {
     *            'protocol': 'https',
     *        }
     * }
     * </pre>
     *
     * @return array Возвращает протокол, с которым следует работать
     * <b>response.protocol</b> - может содержать http или https
     */
    protected function process()
    {
        $site = \RS\Site\Manager::getAdminCurrentSite();
        $http_request = \RS\Http\Request::commonInstance();

        $protocol = ($site['redirect_to_https'] || $http_request->getProtocol() == 'https') ? 'https' : 'http';

        return [
            'response' => [
                'protocol' => $protocol
            ]
        ];
    }
}