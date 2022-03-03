<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Checkout;
use \ExternalApi\Model\Exception as ApiException;

/**
* Возвращает список пунктов самовывоза для текущего заказа пользователя
*/
class GetOrderPickUpPoints extends \ExternalApi\Model\AbstractMethods\AbstractMethod
{
    const
        RIGHT_LOAD = 1;
        
    protected
        $token_require = false;
        
    function __construct()
    {
        parent::__construct();
        $this->order     = \Shop\Model\Orm\Order::currentOrder();
        $this->order_api = new \Shop\Model\OrderApi();
        $this->order->clearErrors(); //Очистим ошибки предварительно    
        $this->shop_config = \RS\Config\Loader::byModule('shop'); //Конфиг магазина
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
    * Возвращает список пунктов самовывоза для текущего заказа пользователя. Заказ создаётся в рамках текущей сессии подключённого пользователя.
    * 
    * @param string $token Авторизационный токен
    * 
    * @example POST /api/methods/checkout.getorderpickuppoints
    * 
    * Ответ:
    * <pre>
    * {
    *        "response": {
    *            [
    *                {
    *                    "id": "1",
    *                    "title": "Самовывоз",
    *                    "description": "Пункты выдачи товаров см. в разделе контакты",
    *                    "picture": null,
    *                    "xzone": null,
    *                    "min_price": "0",
    *                    "max_price": "0",
    *                    "min_cnt": "0",
    *                    "first_status": null,
    *                    "user_type": "all",
    *                    "extrachange_discount": "0",
    *                    "public": "1",
    *                    "class": "myself",
    *                    "delivery_periods": false
    *                },
    *                ...
    *            ]
    *        }
    *    }
    * </pre>
    * 
    * @return array Возращает, либо массив пунктов самовывоза, либо пустой список
    */
    protected function process($token = null)
    {   
        $pick_up_points = \Shop\Model\DeliveryApi::getPickUpPoints($this->order);
        $response['response']['list'] = [];
        if (!empty($pick_up_points)){
            $response['response']['list'] = \ExternalApi\Model\Utils::extractOrmList($pick_up_points); 
        }
        
        return $response;
    }
}