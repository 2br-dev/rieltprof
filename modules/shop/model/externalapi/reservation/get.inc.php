<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Reservation;

/**
* Возвращает предварительного заказа по ID
*/
class Get extends \ExternalApi\Model\AbstractMethods\AbstractGet
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
            self::RIGHT_LOAD => t('Загрузка объекта')
        ];
    }
    
    /**
    * Возвращает название секции ответа, в которой должен вернуться список объектов
    * 
    * @return string
    */
    public function getObjectSectionName()
    {
        return 'reservation';
    }
    
    /**
    * Возвращает объект с которым работает
    * 
    */
    public function getOrmObject()
    {
        return new \Shop\Model\Orm\Reservation();
    }

    /**
     * Возвращает предзаказ по ID
     *
     * @param string $token Авторизационный токен
     * @param integer $id ID покупки в один клик
     * @param array $sections Секции с данными, которые следует включить в ответ. Возможные значения:
     *
     *
     * @example GET api/methods/reservation.get?token=2bcbf947f5fdcd0f77dc1e73e73034f5735de486&id=1
     * Ответ
     * <pre>
     * {
     *     "response": {
     *         "reservation": {
     *               "id": "1",
     *               "product_id": "1",
     *               "product_barcode": null,
     *               "product_title": "Моноблок Acer Aspire Z5763",
     *               "offer_id": "104",
     *               "currency": "RUB",
     *               "multioffer": [],
     *               "amount": 2,
     *               "phone": "+79628678430",
     *               "email": "admin@admin.ru",
     *               "is_notify": "1",
     *               "dateof": "2019-12-04 12:47:22",
     *               "user_id": "1",
     *               "status": "open",
     *               "comment": null,
     *               "partner_id": "0"
     *           }
     *     }
     * }
     * </pre>
     * @return array
     * @throws \ExternalApi\Model\Exception
     */
    function process($token = null, $id)
    {
        $response = parent::process($token, $id);

        if (!empty($response['response']['reservation']['multioffer'])){
            $response['response']['reservation']['multioffer'] = @unserialize($response['response']['reservation']['multioffer']);
        }

        return $response;
    }
}