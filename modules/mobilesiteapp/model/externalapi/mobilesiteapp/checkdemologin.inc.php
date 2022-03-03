<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace MobileSiteApp\Model\ExternalApi\MobileSiteApp;

/**
* Проверяет апи ключ и доступ к демо приложению
*/
class CheckDemoLogin extends \ExternalApi\Model\AbstractMethods\AbstractMethod
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
            self::RIGHT_LOAD => t('Получение данных приложением')
        ];
    }


    /**
     * Возвращает конфигурационные сведения для демо мобильного приложения сайта, передаёт адреса для запросов, которые нужно применить
     *
     * @example GET /api-нужный ключ/methods/mobilesiteapp.checkdemologin
     *
     * Ответ:
     * <pre>
     * {
     *  "response": {
     *    "data": {
     *        "multi_request": "/api/methods/multirequest.run",
     *        "slideshow_url": "/api/methods/banner.getlist",
     *        "brands_list": "/api/methods/brand.getlist",
     *        "brand": "/api/methods/brand.get",
     *        "category": "/api/methods/category.getlist",
     *        "category_products": "/api/methods/product.getlist",
     *        "category_list_default_order": "dateof",
     *        "category_list_default_order_direction": "desc",
     *        "product": "/api/methods/product.get",
     *        "offers": "/api/methods/product.getofferslist",
     *        "recommended": "/api/methods/product.getrecommendedlist",
     *        "reserve": "/api/methods/product.reserve",
     *        "favorite": "/api/methods/favorite.getList",
     *        "favorite_add": "/api/methods/favorite.add",
     *        "favorite_remove": "/api/methods/favorite.remove",
     *        "cart": "/api/methods/cart.getcartdata",
     *        "cart_add": "/api/methods/cart.add",
     *        "cart_remove": "/api/methods/cart.remove",
     *        "cart_update": "/api/methods/cart.update",
     *        "cart_clear": "/api/methods/cart.clear",
     *        "oneclick_url_send_cart": "/api/methods/cart.oneclickcartsend",
     *        "oneclick_url_send_product": "/api/methods/product.oneclicksend",
     *        "oneclick_url_fields": "/api/methods/cart.oneclickcartfields",
     *        "checkout_address": "/api/methods/checkout.address",
     *        "checkout_delivery_payment": "/api/methods/checkout.deliverypayment",
     *        "checkout_confirm": "/api/methods/checkout.confirm",
     *        "checkout_init": "/api/methods/checkout.init",
     *        "checkout_cartdata": "/api/methods/checkout.getcartdata",
     *        "checkout_address_lists_info": "/api/methods/checkout.getaddresslistsinfo",
     *        "checkout_online_pay_url": "/onlinepay/doPay/",
     *        "delivery_getlist": "/api/methods/delivery.getlist",
     *        "payment_getlist": "/api/methods/payment.getlist",
     *        "order_pickup_points": "/api/methods/checkout.getorderpickuppoints",
     *        "menu_list_url": "/api/methods/menu.getlist",
     *        "menu_url": "/api/methods/menu.get",
     *        "mobilesiteapp_config": "/api/methods/mobilesiteapp.config",
     *        "mobilesiteapp_products_pagesize": 20,
     *        "auth_url": "/api/methods/oauth.token",
     *        "push_change_url": "/api/methods/push.change",
     *        "push_list_url": "/api/methods/push.getlist",
     *        "push_get_token_url": "/api/methods/push.getregistertoken",
     *        "order_get_list": "/api/methods/order.getlist",
     *        "order_get": "/api/methods/order.get",
     *        "user_get_addresses": "/api/methods/user.getaddresses",
     *        "user_update_url": "/api/methods/user.update",
     *        "user_get_url": "/api/methods/user.get"
     *       }
     *    }
     *}
     * </pre>
     * Возращает, пустой массив ошибок, если всё успешно
     * @return array
     * @throws \RS\Exception
     */
    protected function process()
    {
        $config               = \RS\Config\Loader::byModule('externalapi');
        $shop_config          = \RS\Config\Loader::byModule('shop');
        $catalog_config       = \RS\Config\Loader::byModule('catalog');
        $mobilesiteapp_config = \RS\Config\Loader::byModule('mobilesiteapp');
        
        $router = \RS\Router\Manager::obj();
        
        //Передадим адреса для запросов для последующей подмены
        //Мультизапрос
        $params['data']['multi_request'] = $router->getUrl('externalapi-front-apigate', ['method'=>'multirequest.run']);

        //Баннеры
        $params['data']['slideshow_url'] = $router->getUrl('externalapi-front-apigate', ['method'=>'banner.getlist']);

        //Бренды
        $params['data']['brands_list'] = $router->getUrl('externalapi-front-apigate', ['method'=>'brand.getlist']);
        $params['data']['brand']       = $router->getUrl('externalapi-front-apigate', ['method'=>'brand.get']);

        //Категории
        $params['data']['category']          = $router->getUrl('externalapi-front-apigate', ['method'=>'category.getlist']);
        $params['data']['category_products'] = $router->getUrl('externalapi-front-apigate', ['method'=>'product.getlist']);
        
        $params['data']['category_list_default_order']           = $catalog_config['list_default_order'];
        $params['data']['category_list_default_order_direction'] = $catalog_config['list_default_order_direction'];

        //Товар                   
        $params['data']['product']     = $router->getUrl('externalapi-front-apigate', ['method'=>'product.get']);
        $params['data']['offers']      = $router->getUrl('externalapi-front-apigate', ['method'=>'product.getofferslist']);
        $params['data']['recommended'] = $router->getUrl('externalapi-front-apigate', ['method'=>'product.getrecommendedlist']);
        $params['data']['reserve']     = $router->getUrl('externalapi-front-apigate', ['method'=>'product.reserve']);

        //Избранное
        $params['data']['favorite']        = $router->getUrl('externalapi-front-apigate', ['method'=>'favorite.getList']);
        $params['data']['favorite_add']    = $router->getUrl('externalapi-front-apigate', ['method'=>'favorite.add']);
        $params['data']['favorite_remove'] = $router->getUrl('externalapi-front-apigate', ['method'=>'favorite.remove']);

        //Корзина
        $params['data']['cart']        = $router->getUrl('externalapi-front-apigate', ['method'=>'cart.getcartdata']);
        $params['data']['cart_add']    = $router->getUrl('externalapi-front-apigate', ['method'=>'cart.add']);
        $params['data']['cart_remove'] = $router->getUrl('externalapi-front-apigate', ['method'=>'cart.remove']);
        $params['data']['cart_update'] = $router->getUrl('externalapi-front-apigate', ['method'=>'cart.update']);
        $params['data']['cart_clear']  = $router->getUrl('externalapi-front-apigate', ['method'=>'cart.clear']);
        $params['data']['repeat_order']                = $router->getUrl('externalapi-front-apigate', ['method'=>'cart.repeatorder']);

        //Купить в один клик в корзине
        $params['data']['oneclick_url_send_cart']     = $router->getUrl('externalapi-front-apigate', ['method'=>'cart.oneclickcartsend']);
        $params['data']['oneclick_url_send_product']  = $router->getUrl('externalapi-front-apigate', ['method'=>'product.oneclicksend']);
        $params['data']['oneclick_url_fields']        = $router->getUrl('externalapi-front-apigate', ['method'=>'cart.oneclickcartfields']);

        //Оформление заказа
        $params['data']['checkout_address']            = $router->getUrl('externalapi-front-apigate', ['method'=>'checkout.address']);
        $params['data']['checkout_delivery_payment']   = $router->getUrl('externalapi-front-apigate', ['method'=>'checkout.deliverypayment']);
        $params['data']['checkout_confirm']            = $router->getUrl('externalapi-front-apigate', ['method'=>'checkout.confirm']);
        $params['data']['checkout_init']               = $router->getUrl('externalapi-front-apigate', ['method'=>'checkout.init']);
        $params['data']['checkout_cartdata']           = $router->getUrl('externalapi-front-apigate', ['method'=>'checkout.getcartdata']);
        $params['data']['checkout_address_lists_info'] = $router->getUrl('externalapi-front-apigate', ['method'=>'checkout.getaddresslistsinfo']);
        $params['data']['checkout_online_pay_url']     = $router->getUrl('shop-front-onlinepay', ['Act'=>'doPay']);
        $params['data']['delivery_getlist']            = $router->getUrl('externalapi-front-apigate', ['method'=>'delivery.getlist']);
        $params['data']['payment_getlist']             = $router->getUrl('externalapi-front-apigate', ['method'=>'payment.getlist']);
        $params['data']['order_pickup_points']         = $router->getUrl('externalapi-front-apigate', ['method'=>'checkout.getorderpickuppoints']);

        //Список пунктов меню
        $params['data']['menu_list_url'] = $router->getUrl('externalapi-front-apigate', ['method'=>'menu.getlist']);
        $params['data']['menu_url']      = $router->getUrl('externalapi-front-apigate', ['method'=>'menu.get']);

        //Список новостей
        $params['data']['article_category_list_url'] = $router->getUrl('externalapi-front-apigate', ['method'=>'article.getcategorylist']);
        $params['data']['article_list_url']          = $router->getUrl('externalapi-front-apigate', ['method'=>'article.getlist']);
        $params['data']['article_url']               = $router->getUrl('externalapi-front-apigate', ['method'=>'article.get']);

        //Конфиг мобильного приложения
        $params['data']['mobilesiteapp_config']            = $router->getUrl('externalapi-front-apigate', ['method'=>'mobilesiteapp.config']);
        $params['data']['mobilesiteapp_products_pagesize'] = $mobilesiteapp_config['products_pagesize'];

        //Филиалы в городах
        $params['data']['set_affiliate_url'] = $router->getUrl('externalapi-front-apigate', ['method'=>'affiliate.set']);

        //Аутентификация
        $params['data']['auth_url'] = $router->getUrl('externalapi-front-apigate', ['method'=>'oauth.token']);

        //Настройки PUSH
        $params['data']['push_change_url']    = $router->getUrl('externalapi-front-apigate', ['method'=>'push.change']);
        $params['data']['push_list_url']      = $router->getUrl('externalapi-front-apigate', ['method'=>'push.getlist']);
        $params['data']['push_get_token_url'] = $router->getUrl('externalapi-front-apigate', ['method'=>'push.registerToken']);

        //Адреса для апи расширения
        $params['data']['extends_url'] = $router->getUrl('externalapi-front-apigate', ['method'=>'mobilesiteapp.getextendsjson']);

        //Заказы
        $params['data']['order_get_list'] = $router->getUrl('externalapi-front-apigate', ['method'=>'order.getlist']);
        $params['data']['order_get']      = $router->getUrl('externalapi-front-apigate', ['method'=>'order.get']);

        //Пользователь
        $params['data']['user_get_addresses']  = $router->getUrl('externalapi-front-apigate', ['method'=>'user.getaddresses']);
        $params['data']['user_update_url']     = $router->getUrl('externalapi-front-apigate', ['method'=>'user.update']);
        $params['data']['user_get_url']        = $router->getUrl('externalapi-front-apigate', ['method'=>'user.get']);
        $params['data']['user_email_recovery'] = $router->getUrl('externalapi-front-apigate', ['method'=>'user.emailrecovery']);

        $params['data']['mobile_phone']       = $mobilesiteapp_config['mobile_phone'];

        $response['response'] = $params;
        return $response;
    }
}