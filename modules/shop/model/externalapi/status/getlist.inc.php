<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Status;
use \ExternalApi\Model\Exception as ApiException;

/**
* Возвращает список заказов
*/
class GetList extends \ExternalApi\Model\AbstractMethods\AbstractGetList
{
    /**
    * Возвращает возможный ключи для фильтров
    * 
    * @return [
    *   'поле' => [
    *       'type' => 'тип значения'
    *   ]
    * ]
    */
    public function getAllowableFilterKeys()
    {
        return [
            'title' => [
                'func' => self::FILTER_TYPE_EQ,
                'type' => 'string',
            ]
        ];
    }
    
    /**
    * Возвращает объект выборки объектов 
    * 
    * @return \RS\Module\AbstractModel\EntityList
    */
    public function getDaoObject()
    {
        return new \Shop\Model\UserStatusApi();
    }    
 
    /**
    * Выполняет запрос на выборку статусов заказа
    * 
    * @param string $token Авторизационный token
    * @param array  $filter Фильтр, поддерживает в ключах поля: #filters-info
    * @param string $sort Сортировка по полю, поддерживает значения: #sort-info
    * @param integer $page Номер страницы, начинается с 1
    * @param mixed $pageSize Размер страницы
    * 
    * @example GET /api/methods/status.getlist?token=b45d2bc3e7149959f3ed7e94c1bc56a2984e6a86
    * Ответ:
    * <pre>
    * {
    *     "response": {
    *         "summary": {
    *             "page": "1",
    *             "pageSize": "20",
    *             "total": "7"
    *         },
    *         "userstatus": [
    *             {
    *                 "id": "1",
    *                 "title": "Новый",
    *                 "bgcolor": "#83b7b3",
    *                 "type": "new"
    *             },
    *             {
    *                 "id": "2",
    *                 "title": "Ожидает оплату",
    *                 "bgcolor": "#687482",
    *                 "type": "waitforpay"
    *             },
    *             {
    *                 "id": "3",
    *                 "title": "В обработке",
    *                 "bgcolor": "#f2aa17",
    *                 "type": "inprogress"
    *             },
    *             {
    *                 "id": "4",
    *                 "title": "Выполнен и закрыт",
    *                 "bgcolor": "#5f8456",
    *                 "type": "success"
    *             },
    *             {
    *                 "id": "5",
    *                 "title": "Отменен",
    *                 "bgcolor": "#ef533a",
    *                 "type": "cancelled"
    *             },
    *             {
    *                 "id": "20",
    *                 "title": "Передан курьеру",
    *                 "bgcolor": "#941e1e",
    *                 "type": "custom"
    *             },
    *             {
    *                 "id": "21",
    *                 "title": "Второй произвольный статус",
    *                 "bgcolor": "#142da8",
    *                 "type": "sec"
    *             }
    *         ]
    *     }
    * }
    * </pre>
    * 
    * @return array Возвращает список статусов заказа
    */
    protected function process($token, 
                               $filter = [],
                               $sort = 'id', 
                               $page = "1", 
                               $pageSize = "20")
    {
        
        return parent::process($token, $filter, $sort, $page, $pageSize);
    }
}
