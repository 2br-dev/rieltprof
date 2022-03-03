<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Cart;

use ExternalApi\Model\AbstractMethods\AbstractMethod;
use RS\Exception as RSException;
use RS\Orm\Exception as OrmException;
use Shop\Model\Cart;

/**
* Добавляет товар в корзину
*/
class Add extends AbstractMethod
{
    const RIGHT_LOAD = 1;
    const CART_SOURCE_API_SHOP_CART_ADD = 'api_shop_cart_add';
    
    protected $token_require = false; //Токен не обязателен
    protected $cart;
    
    /**
    * Возвращает комментарии к кодам прав доступа
    * 
    * @return array [
    *     КОД => КОММЕНТАРИЙ,
    *     КОД => КОММЕНТАРИЙ,
    *     ...
    * ]
    */
    public function getRightTitles()
    {
        return [
            self::RIGHT_LOAD => t('Добавление товара в корзину')
        ];
    }


    /**
     * Добавляет товар в корзину и возвращает корзину пользователя со всеми сведениями
     *
     * @param string $token Авторизационный токен
     * @param string $id id товара для добавления
     *
     * @param int $amount
     * @param int $offer
     * @param array $multioffers
     * @param array $concomitants
     * @param array $concomitants_amount
     * @return array Возвращает список со сведения об элементах в корзине
     * @throws RSException
     * @throws OrmException
     *
     * @example GET /api/methods/cart.add?id=1
     * Ответ:
     * <pre>
     * {
     *        "response": {
     *            "cartdata": {
     *                "total": "21 110 р.", //Сумма заказа
     *                "total_base": "21 110 р.", //Сумма заказа в базовой валюте
     *                "total_discount": "0 р.", //Скидка общая на заказ (особая опция)
     *                "items": [
     *                    {
     *                        "id": "xzrbcvmpyz",
     *                        "cost": "21 110 р.", //Сумма
     *                        "base_cost": "21 110 р.", //Сумма в базовой валюте
     *                        "single_cost": "21 110 р.", //Цена одного товара в валюте
     *                        "single_weight": 0, //Вес
     *                        "discount": "0 р.", //Скидка на товар
     *                        "title": "Ноутбук HP Compaq Mini 5103 XM602AA",
     *                        "sub_products": [ //Сопутствующие товары
     *                            {
     *                                "amount": "1",
     *                                "cost": "43 440 р.",
     *                                "single_cost": "43 440 р.",
     *                                "checked": false,
     *                                "discount": "0 р.",
     *                                "title": "Планшет Fujitsu STYLISTIC Q550",
     *                                "product_id": "xzrbcvmpyz",
     *                                "image": {
     *                                    "id": "2340",
     *                                    "title": null,
     *                                    "original_url": "http://full.readyscript.ru/storage/photo/original/e/ubg07um3qxseroq.jpg",
     *                                    "big_url": "http://full.readyscript.ru/storage/photo/resized/xy_1000x1000/e/ubg07um3qxseroq_444ae71f.jpg",
     *                                    "middle_url": "http://full.readyscript.ru/storage/photo/resized/xy_600x600/e/ubg07um3qxseroq_239bd8fd.jpg",
     *                                    "small_url": "http://full.readyscript.ru/storage/photo/resized/xy_300x300/e/ubg07um3qxseroq_4a423e3f.jpg",
     *                                    "micro_url": "http://full.readyscript.ru/storage/photo/resized/xy_100x100/e/ubg07um3qxseroq_ab5254c1.jpg"
     *                                    "nano_url": "http://full.readyscript.ru/storage/photo/resized/xy_100x100/e/ubg07um3qxseroq_ab5254c1.jpg"
     *                                },
     *                                "id": "575",
     *                                "unit": null,
     *                                "allow_concomitant_count_edit": false //Включено ли редактирование сопутствующи= товаров в корзине
     *                            }
     *                        ],
     *                        "image": {
     *                            "id": "2361",
     *                            "title": null,
     *                            "original_url": "http://full.readyscript.ru/storage/photo/original/i/06eq2uxurfz9l3n.jpg",
     *                            "big_url": "http://full.readyscript.ru/storage/photo/resized/xy_1000x1000/i/06eq2uxurfz9l3n_c2e6b23d.jpg",
     *                            "middle_url": "http://full.readyscript.ru/storage/photo/resized/xy_600x600/i/06eq2uxurfz9l3n_ce60b3b1.jpg",
     *                            "small_url": "http://full.readyscript.ru/storage/photo/resized/xy_300x300/i/06eq2uxurfz9l3n_a7b95573.jpg",
     *                            "micro_url": "http://full.readyscript.ru/storage/photo/resized/xy_100x100/i/06eq2uxurfz9l3n_46a93f8d.jpg"
     *                            "nano_url": "http://full.readyscript.ru/storage/photo/resized/xy_100x100/i/06eq2uxurfz9l3n_46a93f8d.jpg"
     *                        },
     *                        "entity_id": "578",
     *                        "amount": "1",
     *                        "offer": "0",
     *                        "model": "красный, Savage", //Может быть null
     *                        "multioffers": [ //Может быть null
     *                            {
     *                                "title": "Цвет",
     *                                "value": "красный"
     *                            },
     *                            {
     *                                "title": "Бренд",
     *                                "value": "Savage"
     *                            }
     *                        ],
     *                        "multioffers_string": "Цвет: красный, Бренд: Savage",
     *                        "unit": null
     *                    }
     *                ],
     *                "items_count": 1, //Количество элементов в корзине
     *                "total_weight": 0,
     *                "checkcount": 1,
     *                "currency": "р.",
     *                "errors": [
     *                   "Общая ошибка"
     *                ],//Ошибки
     *                "has_error": false,
     *                "taxes": [], //Налоги
     *                "total_without_delivery": "21 110 р.",
     *                "total_without_delivery_unformatted": 21110,
     *                "total_without_payment_commission": "21 110 р.",
     *                "total_without_payment_commission_unformatted": 21110,
     *                "total_unformatted": 21110,
     *                "total_bonuses": 0,
     *                "coupons": [ //Скидки
     *                   {
     *                       "id": "ghmuwcfg",
     *                       "code": "aaa"
     *                   }
     *                ]
     *            }
     *        }
     *    }
     * </pre>
     *
     */
    protected function process($token = null, $id, $amount = 1, $offer = 0, $multioffers = [], $concomitants = [], $concomitants_amount = [])
    {
        Cart::currentCart()->addProduct($id, $amount, $offer, $multioffers, $concomitants, $concomitants_amount, null, self::CART_SOURCE_API_SHOP_CART_ADD);
        
        $api = new \Shop\Model\ApiUtils();
        $response['response']['cartdata'] = $api->fillProductItemsData();

        return $response;
    }
}
