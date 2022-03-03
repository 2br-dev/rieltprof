<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Alerts\Model\ExternalApi\Notice;

/**
* Возвращает список типов уведомлений, предназначенных для клиента
*/
class getTypeList extends \ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod
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
            self::RIGHT_LOAD => t('Загрузка списка возможных типов уведомлений')
        ];
    }
    
    /**
    * Возвращает список возможных типов уведомлений, которые могут быть переданы в Desktop приложение для уведомлений
    * 
    * @param string $token Авторизационный токен
    * @example GET /api/methods/notice.getTypeList?token=2bcbf947f5fdcd0f77dc1e73e73034f5735de486
    * Ответ:
    * <pre>
    * {
    *     "response": {
    *         "type": [
    *             {
    *                 "title": "Купить в один клик (администратору)",
    *                 "type": "catalog-oneclickadmin"
    *             },
    *             {
    *                 "title": "Заказ оформлен (администратору)",
    *                 "type": "shop-checkoutadmin"
    *             }
    *         ]
    *     }
    * }
    * </pre>
    * 
    * @return Возвращает массив с названием и идентификатором типа уведомлений
    */
    function process($token)
    {
        $notice_api = new \Alerts\Model\Api();
        $values = $notice_api->excludeLockedUserNotices( $notice_api->getDesktopNoticeTypes(), $this->token['user_id'] );

        return [
            'response' => [
                'type' => array_values($values)
            ]
        ];
    }
}
