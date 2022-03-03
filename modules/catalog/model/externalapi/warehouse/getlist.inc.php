<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\ExternalApi\Warehouse;

/**
* Возвращает список брендов
*/
class GetList extends \ExternalApi\Model\AbstractMethods\AbstractGetList
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
            self::RIGHT_LOAD => t('Загрузка списка объектов')
        ];
    }
    
    /**
    * Возвращает возможные значения для сортировки
    * 
    * @return array
    */
    public function getAllowableOrderValues()
    {
        return ['id', 'id desc', 'title', 'title desc'];
    }

    /**
     * Возвращает возможный ключи для фильтров
     *
     * @return [
     *   'поле' => [
     *       'title' => 'Описание поля. Если не указано, будет загружено описание из ORM Объекта'
     *       'type' => 'тип значения',
     *       'func' => 'постфикс для функции makeFilter в текущем классе, которая будет готовить фильтр, например eq',
     *       'values' => [возможное значение1, возможное значение2]
     *   ]
     * ]
     */
    public function getAllowableFilterKeys()
    {
        return [
            'id' => [
                'title' => t('ID склада. Одно значение или массив значений'),
                'type' => 'integer[]',
                'func' => self::FILTER_TYPE_IN
            ],
            'title' => [
                'title' => t('Название склада, частичное совпадение'),
                'type' => 'string',
                'func' => self::FILTER_TYPE_LIKE
            ],
            'public' => [
                'title' => t('Только публичные склады? 1 или 0'),
                'type' => 'integer',
                'func' => self::FILTER_TYPE_EQ,
                'values' => [0, 1]
            ],
        ];
    }

    /**
     * Возвращает объект, который позволит производить выборку товаров
     *
     * @return \Catalog\Model\WarehouseApi
     */
    public function getDaoObject()
    {
        $dao = new \Catalog\Model\WarehouseApi();
        return $dao;
    }


    /**
     * Возвращает список складов
     *
     * @param string $token Авторизационный токен
     * @param array $filter фильтр категорий по параметрам. Возможные ключи: #filters-info
     * @param string $sort Сортировка категорий по параметрам. Возможные значения #sort-info
     * @param integer $page Номер страницы
     * @param integer $pageSize Количество элементов на страницу
     *
     * @example GET /api/methods/warehouse.getlist?token=2bcbf947f5fdcd0f77dc1e73e73034f5735de4868
     *
     * GET /api/methods/warehouse.getlist?token=2bcbf947f5fdcd0f77dc1e73e73034f5735de4868&filter[title]=
     *
     * Ответ:
     * <pre>
     * {
     * "response": {
     *   "summary": {
     *       "page": 1,
     *       "pageSize": 1000,
     *       "total": "1"
     *   },
     *   "list": [
     *       {
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
     *   ]
     *  }
     * }
     * </pre>
     *
     * @return array Возвращает список объектов и связанные с ним сведения.
     * @throws \ExternalApi\Model\Exception
     */
    protected function process($token = null, 
                               $filter = [],
                               $sort = "title", 
                               $page = 1,
                               $pageSize = 1000)
    {
        return parent::process($token, $filter, $sort, $page, $pageSize);
    }
}