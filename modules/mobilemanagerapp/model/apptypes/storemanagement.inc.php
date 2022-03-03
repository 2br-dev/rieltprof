<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace MobileManagerApp\Model\AppTypes;
use \Users\Model\ExternalApi\User;
use \Shop\Model\ExternalApi\Order;
use \ExternalApi\Model\App\AbstractAppType;
use \ExternalApi\Model\Orm\AuthorizationToken;
use RS\Config\Loader as ConfigLoader;
use \PushSender\Model\App\InterfaceHasPush;

/**
* Приложение - управление магазином
*/
class StoreManagement extends AbstractAppType implements InterfaceHasPush
{    
    /**
    * Возвращает  строковый идентификатор приложения
    * 
    * @return string
    */
    public function getId()
    {
        return 'store-management';
    }
    
    /**
    * Возвращает SHA1 от секретного ключа client_secret, который должен 
    * передаваться вместе с client_id в момент авторизации
    * 
    * @return string
    */
    public function checkSecret($client_secret)
    {
        return sha1( $client_secret ) == 'fdb6e4f2df8f561c773d363fe40f7bbb052c736a';
    }
    
    /**
    * Метод возвращает название приложения
    * 
    * @return string
    */
    public function getTitle()
    {
        return t('Управление магазином(администратору/курьеру)');
    }
    
    /**
    * Метод возвращает массив, содержащий требуемые права доступа к json api для приложения
    * 
    * @return [
    *   [
    *       'method' => 'oauth/authorize',
    *       'right_codes' => [код действия, код действия, ...]
    *   ],
    *   ...
    * ]
    */
    public function getAppRights()
    {
        $courier_rights = [];
        if ($this->getToken()) {
            if (in_array(ConfigLoader::byModule('shop')->courier_user_group, $this->getToken()->getUser()->getUserGroups())) {
                $courier_rights[] = Order\Get::RIGHT_COURIER;
            }
        }
        
        return [
            'user.get' => [User\Get::RIGHT_LOAD_SELF],
            'user.getCourierList' => self::FULL_RIGHTS,
            
            'order.get' => array_merge([Order\Get::RIGHT_LOAD], $courier_rights),
            'order.getList' => array_merge([Order\GetList::RIGHT_LOAD], $courier_rights),
            'order.sellStatistic' => [Order\SellStatistic::RIGHT_LOAD],
            'order.sellStatisticMonth' => [Order\SellStatistic::RIGHT_LOAD],
            'order.statisticAvgOrderSum' => self::FULL_RIGHTS,
            'order.sellStatisticYears' => self::FULL_RIGHTS,
            'order.getReceiptList' => array_merge([Order\GetReceiptList::RIGHT_LOAD], $courier_rights),
            'order.update' => array_merge([Order\Update::RIGHT_UPDATE], $courier_rights),
            
            'warehouse.getList' => self::FULL_RIGHTS,
            'status.getList' => self::FULL_RIGHTS,
            'payment.getList' => self::FULL_RIGHTS,
            
            'product.get' => self::FULL_RIGHTS,
            'product.getList' => self::FULL_RIGHTS,
            'product.getOfferList' => self::FULL_RIGHTS,
            
            'push.getList' => self::FULL_RIGHTS,
            'push.registerToken' => self::FULL_RIGHTS,
            'push.change' => self::FULL_RIGHTS,

            'actionTemplate.getList' => self::FULL_RIGHTS,
            'actionTemplate.runAction' => self::FULL_RIGHTS
        ];
    }

    /**
    * Возвращает группы пользователей, которым доступно данное приложение.
    * Сведения загружаются из настроек текущего модуля
    * 
    * @return ["group_id_1", "group_id_2", ...]
    */    
    public function getAllowUserGroup()
    {
        return \RS\Config\Loader::byModule($this)->allow_user_groups;
    }
    
    /**
    * Возвращает массив объектов Push уведомлений
    * 
    * @return \PushSender\Model\AbstractPushNotice[]
    */
    public function getPushNotices()
    {
        return [
            new \MobileManagerApp\Model\Push\NewOrderToCourier(),
            new \MobileManagerApp\Model\Push\NewOrderToAdmin(),
            new \MobileManagerApp\Model\Push\NewOneClickToAdmin(),
            new \MobileManagerApp\Model\Push\NewReservationToAdmin(),
            new \MobileManagerApp\Model\Push\AddBalanceToAdmin(),
            new \MobileManagerApp\Model\Push\OrderPayedToAdmin()
        ];
    }    
}
