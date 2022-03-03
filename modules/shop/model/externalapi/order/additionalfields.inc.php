<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Order;

/**
* Возвращает поля добавленные к заказу в мадуле магазин
*/
class AdditionalFields extends \ExternalApi\Model\AbstractMethods\AbstractMethod
{
    const
        RIGHT_LOAD = 1;
        
    protected
        $token_require = false;
    
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
            self::RIGHT_LOAD => t('Получение данных по полям')
        ];
    }
    


    /**
    * Возвращает поля добавленные к заказу в мадуле магазин
    * 
    * @param string $token Авторизационный token
    * 
    * @example GET /api/methods/order.additionalfields
    * 
    * Ответ:
    * <pre>
    * {
    *        "response": {
    *           "fields": [
    *               {
    *                   "alias": "pole",
    *                   "maxlength": "",
    *                   "necessary": "1",
    *                   "title": "Поле",
    *                   "type": "string",
    *                   "values": "",
    *                   "val": "",
    *                   "current_val": ""
    *               },
    *               {
    *                    "alias": "text",
    *                    "maxlength": "",
    *                    "necessary": "",
    *                    "title": "Текст",
    *                    "type": "text",
    *                    "values": "",
    *                    "val": "",
    *                    "current_val": ""
    *                },
    *                {
    *                    "alias": "spisok",
    *                    "maxlength": "",
    *                    "necessary": "",
    *                    "title": "Список",
    *                    "type": "list",
    *                    "values": "10 лет, 20 лет, 30 лет",
    *                    "val": "20 лет",
    *                    "current_val": "20 лет"
    *                },
    *                {
    *                    "alias": "yesno",
    *                    "maxlength": "",
    *                    "necessary": "",
    *                    "title": "Да нет",
    *                    "type": "bool",
    *                    "values": "",
    *                    "val": false,
    *                    "current_val": "Нет"
    *                }
    *           ]
    * }
    * </pre>
    * 
    * @return array Возращает, пустой массив ошибок, если всё успешно
    */
    protected function process($token = null)
    {
        $response['response']['fields'] = \Shop\Model\ApiUtils::getAdditionalOrderFieldsSection();
                  
        return $response;
    }
}