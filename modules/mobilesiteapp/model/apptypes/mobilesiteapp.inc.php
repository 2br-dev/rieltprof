<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace MobileSiteApp\Model\AppTypes;
use \PushSender\Model\App\InterfaceHasPush;

/**
* Приложение мобильный сайт
*/
class MobileSiteApp extends \ExternalApi\Model\App\AbstractAppType implements InterfaceHasPush
{
    /**
    * Возвращает строковый идентификатор приложения
    * 
    * @return string
    */
    public function getId()
    {
        return 'mobilesiteapp';
    }
    
    /**
    * Возвращает SHA1 от секретного ключа client_secret, который должен 
    * передаваться вместе с client_id в момент авторизации
    * 32eb1c5e9eadfd48d24967c0cbf80d3f69583786
    *
    * @param string $client_secret - секретное слово на клиентской стороне
    * @return string
    */
    public function checkSecret($client_secret)
    {
        return sha1( $client_secret ) == '32eb1c5e9eadfd48d24967c0cbf80d3f69583786';
    }
    
    /**
    * Метод возвращает название приложения
    * 
    * @return string
    */
    public function getTitle()
    {
        return t('Мобильный сайт');
    }
    
    /**
    * Метод возвращает массив, содержащий требуемые права доступа к json api для приложения
    * 
    * @return [
    *   [
    *       'method' => 'метод',
    *       'right_codes' => [код действия, код действия, ...]
    *   ],
    *   ...
    * ]
    */
    public function getAppRights()
    {
        return [
            'oauth.token'       => self::FULL_RIGHTS,
            
            'banner.get'         => self::FULL_RIGHTS,
            'banner.getList'     => self::FULL_RIGHTS,
            
            'brand.get'         => self::FULL_RIGHTS,
            'brand.getList'     => self::FULL_RIGHTS,
            
            'category.get'      => self::FULL_RIGHTS,
            'category.getList'  => self::FULL_RIGHTS,
            
            'favorite.add'      => self::FULL_RIGHTS,
            'favorite.remove'   => self::FULL_RIGHTS,
            'favorite.getList'  => self::FULL_RIGHTS,
            
            'product.get'           => self::FULL_RIGHTS,
            'product.getList'       => self::FULL_RIGHTS,
            'product.getOffersList' => self::FULL_RIGHTS,
            'product.getRecommendedList' => self::FULL_RIGHTS,
            'product.reserve'       => self::FULL_RIGHTS,
            
            'mobileSiteApp.config'  => self::FULL_RIGHTS,
            'mobileSiteApp.getExtendsJSON' => self::FULL_RIGHTS,
            
            'menu.getList'       => self::FULL_RIGHTS,
            'menu.get'           => self::FULL_RIGHTS,

            'article.get'           => self::FULL_RIGHTS,
            'article.getCategoryList'   => self::FULL_RIGHTS,
            'article.getList'           => self::FULL_RIGHTS,

            'payment.getList'    => self::FULL_RIGHTS,
            
            'delivery.getList'   => self::FULL_RIGHTS,
            
            'status.getList'   => self::FULL_RIGHTS,        
            
            'affiliate.getList'   => self::FULL_RIGHTS,
            'affiliate.set'   => self::FULL_RIGHTS,
            
            'push.registerToken' => \PushSender\Model\ExternalApi\Push\RegisterToken::ALLOW_ALL_METHOD,
            'push.getList'       => \PushSender\Model\ExternalApi\Push\getList::ALLOW_ALL_METHOD,
            'push.change'        => \PushSender\Model\ExternalApi\Push\Change::ALLOW_ALL_METHOD,
            
            'order.getList' => [\Shop\Model\ExternalApi\Order\GetList::RIGHT_LOAD],
            'order.get'     => [\Shop\Model\ExternalApi\Order\Get::RIGHT_LOAD],
            
            'user.get'          => self::FULL_RIGHTS,
            'user.getAddresses' => self::FULL_RIGHTS,
            'user.getcourierlist' => self::FULL_RIGHTS,
            'user.update' => self::FULL_RIGHTS,
            
            'cart.add'  => self::FULL_RIGHTS,
            'cart.clear'  => self::FULL_RIGHTS,
            'cart.getCartData'  => self::FULL_RIGHTS,
            'cart.oneclickcartfields'  => self::FULL_RIGHTS,
            'cart.oneclickcartsend'  => self::FULL_RIGHTS,
            'cart.repeatOrder'  => \Shop\Model\ExternalApi\Cart\RepeatOrder::RIGHT_LOAD,
            'cart.remove'  => self::FULL_RIGHTS,
            'cart.update'  => self::FULL_RIGHTS,
            
            'checkout.address'  => self::FULL_RIGHTS,
            'checkout.deliverypayment'  => self::FULL_RIGHTS,
            'checkout.getaddresslistsinfo'  => self::FULL_RIGHTS,
            'checkout.getcartdata'  => self::FULL_RIGHTS,
            'checkout.getorderpickuppoints'  => self::FULL_RIGHTS,
            'checkout.init'  => self::FULL_RIGHTS,
            'checkout.confirm'  => self::FULL_RIGHTS
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
            new \MobileSiteApp\Model\Push\OrderChangeToUser,
            new \MobileSiteApp\Model\Push\MessageToUsers
        ];
    }  
    
}