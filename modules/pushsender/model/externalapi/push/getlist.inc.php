<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace PushSender\Model\ExternalApi\Push;
use \ExternalApi\Model\Exception as ApiException,
    \PushSender\Model\Orm\PushLock;

/**
* Возвращает список уведомлений, принадлежащих приложению, от которого идет запрос
*/
class getList extends \ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod
{
    const
        RIGHT_LOAD = 1;
        
    /**
    * Возвращает комментарии к кодам прав доступа
    * 
    * @return [
    *     КОД => КОММЕНТАРИЙ,
    *     КОД => КОММЕНТАРИЙ,
    *     ...
    * ]
    */
    public function getRightTitles()
    {
        return [
            self::RIGHT_LOAD => t('Поучение списка push уведомлений')
        ];
    }    
    
    /**
    * Возвращает список имеющихся у данного приложения Push уведомлений
    * 
    * @param string $token Авторизационный токен
    * 
    * @example GET /api/methods/push.getList?token=f49d5fcd051aa917e8d3b37e112a6226d0bec863
    * Ответ
    * <pre>{
    *     "response": {
    *         "pushes": {
    *             "all": {
    *                 "id": "all",
    *                 "title": "Все",
    *                 "enable": false
    *             },
    *             "mobilemanagerapp-newordertocourier": {
    *                 "id": "mobilemanagerapp-newordertocourier",
    *                 "title": "Новый заказ для курьера",
    *                 "enable": true
    *             },
    *             "mobilemanagerapp-newordertoadmin": {
    *                 "id": "mobilemanagerapp-newordertoadmin",
    *                 "title": "Новый заказ(администратору)",
    *                 "enable": true
    *             }
    *         }
    *     }
    * }
    * </pre>
    */
    protected function process($token)
    {
        $app = \RS\RemoteApp\Manager::getAppByType($this->token->app_type);
        
        if (!$app) {
            throw new ApiException(t('Приложение с таким ID не найдено'), ApiException::ERROR_WRONG_PARAM_VALUE);
        }
        
        if (!($app instanceof \PushSender\Model\App\InterfaceHasPush)) {
            throw new ApiException(t('Приложение не поддерживает push уведомления'), ApiException::ERROR_WRONG_PARAM_VALUE);
        }
        
        $locked = \RS\Orm\Request::make()
            ->from(new \PushSender\Model\Orm\PushLock())
            ->where([
                'site_id' => \RS\Site\Manager::getSiteId(),
                'user_id' => $this->token->user_id,
                'app' => $this->token->app_type
            ])
            ->exec()->fetchSelected('push_class', 'push_class');
        
        $result = [];
        
        $result[PushLock::PUSH_CLASS_ALL] = [
            'id' => PushLock::PUSH_CLASS_ALL,
            'title' => t('Все'),
            'enable' => !isset($locked[PushLock::PUSH_CLASS_ALL])
        ];
        
        foreach($app->getPushNotices() as $push) {
            $result[$push->getId()] = [
                'id' => $push->getId(),
                'title' => $push->getTitle(),
                'enable' => !isset($locked[$push->getId()])
            ];
        }
        
        return [
            'response' => [
                'pushes' => $result
            ]
        ];
    }
}