<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Checkout;

/**
* Реализует первоначальную инициализацию перед началом оформления заказа. 
*/
class Init extends \ExternalApi\Model\AbstractMethods\AbstractMethod
{
    const
        RIGHT_LOAD = 1;
        
    protected
        $token_require = false;
        
        
    public
        /**
        * @var \Shop\Model\Orm\OrderApi
        */
        $order_api,
        /**
        * @var \Shop\Model\Orm\Order
        */
        $order;
        
    function __construct()
    {
        parent::__construct();
        $this->order     = \Shop\Model\Orm\Order::currentOrder();
        $this->order_api = new \Shop\Model\OrderApi();
        $this->order->clearErrors(); //Очистим ошибки предварительно    
    }
    
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
            self::RIGHT_LOAD => t('Отправка данных')
        ];
    }
    


    /**
    * Реализует первоначальную инициализацию перед началом оформления заказа. 
    * Обязательный метод перед checkout.address. 
    * 
    * @param string $token Авторизационный токен
    * 
    * @example POST /api/methods/checkout.init
    * 
    * Ответ:
    * <pre>
    * {
    *        "response": {
    *            "success" : false,
    *            "errors" : ['Ошибка']
    *        }
    *    }
    * </pre>
    * 
    * @return array Возращает, либо пустой массив ошибок, если инициализация успешно пройдена, либо массив ошибок и success false
    */
    protected function process($token = null)
    {          
        $response['response']['success'] = false;  
        
        $this->order->clear();
                 
        //Замораживаем объект "корзина" и привязываем его к заказу
        $frozen_cart = \Shop\Model\Cart::preOrderCart(null);
        $frozen_cart->splitSubProducts();
        $frozen_cart->mergeEqual();
        
        $this->order->linkSessionCart($frozen_cart);
        $this->order->setCurrency( \Catalog\Model\CurrencyApi::getCurrentCurrency() );
        
        $this->order['ip']        = $_SERVER['REMOTE_ADDR'];
        $this->order['warehouse'] = 0;
        
        $this->order['expired'] = false;
        
        $errors = [];
        $response['response']['success'] = true;  
        $response['response']['errors']  = $errors;

        return $response;
    }
}