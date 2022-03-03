<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\ExternalApi\Oneclick;

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
        return ['id', 'id desc', 'dateof', 'dateof desc', 'title', 'title desc'];
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
            'title' => [
                'title' => t('Номер сообщения, частичное совпадение'),
                'type' => 'string',
                'func' => self::FILTER_TYPE_LIKE
            ],
            'user_id' => [
                'title' => t('ID привязанного пользователя, частичное совпадение'),
                'type' => 'string',
                'func' => self::FILTER_TYPE_IN
            ],
            'user_phone' => [
                'title' => t('Номер телефона, частичное совпадение'),
                'type' => 'string',
                'func' => self::FILTER_TYPE_LIKE
            ],
            'user_fio' => [
                'title' => t('ФИО пользователя, частичное совпадение'),
                'type' => 'string',
                'func' => self::FILTER_TYPE_LIKE
            ]
        ];
    }

    /**
     * Возвращает объект, который позволит производить выборку покупок в один клик
     *
     * @return \Catalog\Model\OneClickItemApi
     */
    public function getDaoObject()
    {
        return new \Catalog\Model\OneClickItemApi();
    }


    /**
     * Возвращает список покупок в один клик
     *
     * @param string $token Авторизационный токен
     * @param array $filter фильтр категорий по параметрам. Возможные ключи: #filters-info
     * @param string $sort Сортировка категорий по параметрам. Возможные значения #sort-info
     * @param integer $page Номер страницы
     * @param integer $pageSize Количество элементов на страницу
     *
     * @example GET /api/methods/oneclick.getlist?token=2bcbf947f5fdcd0f77dc1e73e73034f5735de4868
     *
     * GET /api/methods/oneclick.getlist?token=2bcbf947f5fdcd0f77dc1e73e73034f5735de4868&filter[title]=
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
     *          {
     *               "id": "1",
     *               "user_fio": "Супервизор",
     *               "user_phone": "+79628678430",
     *               "title": "Покупка №1 Супервизор (+79628678430)",
     *               "dateof": "2019-12-04 11:55:18",
     *               "status": "new",
     *               "ip": "127.0.0.1",
     *               "currency": "RUB",
     *               "sext_fields": [],
     *               "stext": [
     *                   {
     *                       "id": "1",
     *                       "title": "Моноблок Acer Aspire Z5763",
     *                       "barcode": "PW.SFNE2.033",
     *                       "offer_fields": {
     *                           "offer": "",
     *                           "offer_id": null,
     *                           "multioffer": [],
     *                           "multioffer_val": [],
     *                           "amount": 1
     *                       }
     *                   }
     *               ],
     *               "partner_id": "0"
     *          }
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
            foreach ($response['response']['list'] as &$oneclick){
                if (!empty($oneclick['stext'])){
                    $oneclick['stext'] = @unserialize($oneclick['stext']);
                }
                if (!empty($oneclick['sext_fields'])){
                    $oneclick['sext_fields'] = @unserialize($oneclick['sext_fields']);
                }
            }
        }

        return $response;
    }
}