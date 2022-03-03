<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\ExternalApi\Warehouse;

/**
* Возвращает бренд по ID
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
        return 'warehouse';
    }
    
    /**
    * Возвращает объект с которым работает
    * 
    */
    public function getOrmObject()
    {
        return new \Catalog\Model\Orm\Warehouse();
    }

    /**
     * Возвращает склад по ID
     *
     * @param string $token Авторизационный токен
     * @param integer $id ID склада
     *
     *
     * @example GET api/methods/warehouse.get?token=2bcbf947f5fdcd0f77dc1e73e73034f5735de486&id=1
     * Ответ
     * <pre>
     * {
     *   "response": {
     *       "warehouse": {
     *           "id": "1",
     *           "title": "Основной склад",
     *           "alias": "sklad",
     *           "group_id": "0",
     *           "image": null,
     *           "description": "<p>Наш склад находится в центре города. Предусмотрена удобная парковка для автомобилей и велосипедов. </p>",
     *           "adress": "г. Краснодар, улица Красных Партизан, 246",
     *           "phone": "+7(123)456-78-90",
     *           "work_time": "с 9:00 до 18:00",
     *           "coor_x": "45.0483",
     *           "coor_y": "38.9745",
     *           "default_house": "1",
     *           "public": null,
     *           "checkout_public": null,
     *           "dont_change_stocks": "0",
     *           "use_in_sitemap": "0",
     *           "xml_id": null,
     *           "meta_title": null,
     *           "meta_keywords": null,
     *           "meta_description": null,
     *           "affiliate_id": "0",
     *           "yandex_market_point_id": null
     *       }
     *   }
     * }
     * </pre>
     * @return array
     * @throws \ExternalApi\Model\Exception
     */
    function process($token = null, $id)
    {
        return  parent::process($token, $id);
    }
}