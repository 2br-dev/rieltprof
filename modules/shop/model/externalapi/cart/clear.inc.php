<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Cart;

/**
* Очищает полностью корзину
*/
class Clear extends \ExternalApi\Model\AbstractMethods\AbstractMethod
{
    const
        RIGHT_LOAD = 1;
    
    protected
        $token_require = false, //Токен не обязателен
        $cart;
    
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
            self::RIGHT_LOAD => t('Удаление товаров')
        ];
    }
    


    /**
    * Очищает полностью корзину
    * 
    * @param string $token Авторизационный токен
    * 
    * @example GET /api/methods/cart.clear
    * Ответ:
    * <pre>
    * {
    *        "response": {
    *            "cartdata": {
    *                "total": "0 р.", //Сумма заказа
    *                "total_base": "0 р.", //Сумма заказа в базовой валюте
    *                "total_discount": "0 р.", //Скидка общая на заказ (особая опция)
    *                "items": [
    *                ],
    *                "items_count": 0, //Количество элементов в корзине
    *                "total_weight": 0,
    *                "checkcount": 1,
    *                "currency": "р.",
    *                "errors": [
    *                   "Общая ошибка"
    *                ],//Ошибки
    *                "has_error": false,
    *                "taxes": [], //Налоги
    *                "total_without_delivery": "0 р.",
    *                "total_without_delivery_unformatted": 0,
    *                "total_without_payment_commission": "0 р.",
    *                "total_without_payment_commission_unformatted": 0,
    *                "total_unformatted": 0,
    *                "total_bonuses": 0,
    *                "coupons": [ //Скидки
    *                   {
    *                       "id": "ghmuwcfg",
    *                       "code": "aaa"
    *                    }
    *                ]
    *            }
    *        }
    *    }
    * </pre>
    * 
    * @return array Возвращает список со сведения об элементах в корзине
    */
    protected function process($token = null)
    {
        \Shop\Model\Cart::currentCart()->clean();
        
        $api = new \Shop\Model\ApiUtils();
        $response['response']['cartdata'] = $api->fillProductItemsData();

        return $response;
    }
}