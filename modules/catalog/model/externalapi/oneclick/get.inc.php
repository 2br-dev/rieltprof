<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\ExternalApi\Oneclick;

/**
* Возвращает покупку в один клик по ID
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
        return 'oneclick';
    }
    
    /**
    * Возвращает объект с которым работает
    * 
    */
    public function getOrmObject()
    {
        return new \Catalog\Model\Orm\OneClickItem();
    }

    /**
     * Возвращает покупку в один клик по ID
     *
     * @param string $token Авторизационный токен
     * @param integer $id ID покупки в один клик
     * @param array $sections Секции с данными, которые следует включить в ответ. Возможные значения:
     *
     *
     * @example GET api/methods/oneclick.get?token=2bcbf947f5fdcd0f77dc1e73e73034f5735de486&id=1
     * Ответ
     * <pre>
     * {
     *     "response": {
     *         "oneclick": {
     *             "id": "1",
     *             "user_fio": "Супервизор",
     *             "user_phone": "+79628678430",
     *             "title": "Покупка №1 Супервизор (+79628678430)",
     *             "dateof": "2019-12-04 11:55:18",
     *             "status": "new",
     *             "ip": "127.0.0.1",
     *             "currency": "RUB",
     *             "sext_fields": [],
     *             "stext": [
     *                  {
     *                      "id": "1",
     *                      "title": "Моноблок Acer Aspire Z5763",
     *                      "barcode": "PW.SFNE2.033",
     *                      "offer_fields": {
     *                          "offer": "",
     *                          "offer_id": null,
     *                          "multioffer": [],
     *                          "multioffer_val": [],
     *                          "amount": 1
     *                      }
     *                  }
     *              ],
     *          "partner_id": "0"
     *       }
     *     }
     * }
     * </pre>
     * @return array
     * @throws \ExternalApi\Model\Exception
     */
    function process($token = null, $id)
    {
        $response = parent::process($token, $id);

        //Рассериализуем данные
        if (!empty($response['response']['oneclick']['stext'])){
            $response['response']['oneclick']['stext'] = @unserialize($response['response']['oneclick']['stext']);
        }
        if (!empty($response['response']['oneclick']['sext_fields'])){
            $response['response']['oneclick']['sext_fields'] = @unserialize($response['response']['oneclick']['sext_fields']);
        }

        return $response;
    }
}