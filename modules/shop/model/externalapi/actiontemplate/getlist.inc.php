<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\ActionTemplate;
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
     * Устанавливает фильтр для выборки
     *
     * @param \RS\Module\AbstractModel\EntityList $dao
     * @param array $filter
     *
     * @throws ApiException
     */
    public function setFilter($dao, $filter)
    {
        $dao->setFilter('public', 1);
        return parent::setFilter($dao, $filter);
    }

    /**
     * Возвращает объект выборки объектов
     *
     * @return \RS\Module\AbstractModel\EntityList
     */
    public function getDaoObject()
    {
        return new \Shop\Model\ActionTemplatesApi();
    }

    /**
     * Выполняет запрос на выборку возможных шаблонов действий курьеров
     *
     * @param string $token Авторизационный token
     * @param array  $filter Фильтр, поддерживает в ключах поля: #filters-info
     * @param string $sort Сортировка по полю, поддерживает значения: #sort-info
     * @param integer $page Номер страницы, начинается с 1
     * @param mixed $pageSize Размер страницы, если 0 - то все элементы
     *
     * @example GET /api/methods/actionTemplate.getList?token=894b9df5ebf40531d560235d7379a8cff50f930f
     * Ответ:
     * <pre>
     * {
     *       "response": {
     *           "summary": {
     *               "page": "1",
     *               "pageSize": "20",
     *               "total": "1"
     *           },
     *           "list": [
     *               {
     *                   "id": "1",
     *                   "title": "Не дозвонился",
     *                   "client_sms_message": "Извините, до вас не удалось дозвониться. Мы приедем позже",
     *                   "public": "1"
     *               }
     *           ]
     *       }
     *   }
     * </pre>
     *
     * @return array Возвращает список возможных действий курьера
     */
    protected function process($token,
                               $filter = [],
                               $sort = 'id',
                               $page = "1",
                               $pageSize = "0")
    {

        return parent::process($token, $filter, $sort, $page, $pageSize);
    }
}
