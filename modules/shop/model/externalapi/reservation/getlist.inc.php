<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Reservation;

/**
* Возвращает список покупок в один клик
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
        return ['id', 'id desc', 'dateof', 'dateof desc', 'product_title', 'product_title desc'];
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
                'title' => t('ID покупки в один клик. Одно значение или массив значений'),
                'type' => 'integer[]',
                'func' => self::FILTER_TYPE_IN
            ],
            'user_id' => [
                'title' => t('ID привязанного пользователя. Одно значение или массив значений'),
                'type' => 'integer[]',
                'func' => self::FILTER_TYPE_IN
            ],
            'product_id' => [
                'title' => t('ID товара, частичное совпадение'),
                'type' => 'string',
                'func' => self::FILTER_TYPE_IN
            ],
            'product_barcode' => [
                'title' => t('Артикул товара, частичное совпадение'),
                'type' => 'string',
                'func' => self::FILTER_TYPE_LIKE
            ],
            'product_title' => [
                'title' => t('Наименование товара, частичное совпадение'),
                'type' => 'string',
                'func' => self::FILTER_TYPE_LIKE
            ],
            'offer' => [
                'title' => t('Наименование комплектации, частичное совпадение'),
                'type' => 'string',
                'func' => self::FILTER_TYPE_LIKE
            ],
            'phone' => [
                'title' => t('Номер телефона, частичное совпадение'),
                'type' => 'string',
                'func' => self::FILTER_TYPE_LIKE
            ],
            'email' => [
                'title' => t('E-mail пользователя, частичное совпадение'),
                'type' => 'string',
                'func' => self::FILTER_TYPE_LIKE
            ],
            'status' => [
                'title' => t('Статус, массив значений'),
                'type' => 'string[]',
                'func' => self::FILTER_TYPE_EQ,
                'values' => [\Shop\Model\Orm\Reservation::STATUS_OPEN, \Shop\Model\Orm\Reservation::STATUS_CLOSE]
            ]
        ];
    }

    /**
     * Возвращает объект, который позволит производить выборку предварительных заказов
     *
     * @return \Shop\Model\ReservationApi
     */
    public function getDaoObject()
    {
        return new \Shop\Model\ReservationApi();
    }


    /**
     * Возвращает список предварительных заказов
     *
     * @param string $token Авторизационный токен
     * @param array $filter фильтр категорий по параметрам. Возможные ключи: #filters-info
     * @param string $sort Сортировка категорий по параметрам. Возможные значения #sort-info
     * @param integer $page Номер страницы
     * @param integer $pageSize Количество элементов на страницу
     *
     * @example GET /api/methods/reservation.getlist?token=2bcbf947f5fdcd0f77dc1e73e73034f5735de4868
     *
     * GET /api/methods/reservation.getlist?token=2bcbf947f5fdcd0f77dc1e73e73034f5735de4868&filter[title]=
     *
     * Ответ:
     * <pre>
     * {
     *   "response": {
     *       "summary": {
     *           "page": 1,
     *           "pageSize": 1000,
     *           "total": "1"
     *       },
     *       "list": [
     *           {
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
     *       ]
     *   }
     * }
     * </pre>
     *
     * @return array Возвращает список объектов и связанные с ним сведения.
     * @throws \ExternalApi\Model\Exception
     */
    protected function process($token = null, 
                               $filter = [],
                               $sort = "dateof DESC",
                               $page = 1,
                               $pageSize = 1000)
    {
        $response = parent::process($token, $filter, $sort, $page, $pageSize);

        if (!empty($response['response']['list'])){
            foreach ($response['response']['list'] as &$reservation){
                if (!empty($reservation['multioffer'])){
                    $reservation['multioffer'] = @unserialize($reservation['multioffer']);
                }
            }
        }

        return $response;
    }
}