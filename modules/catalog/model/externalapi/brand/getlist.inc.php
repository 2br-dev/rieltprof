<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\ExternalApi\Brand;

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
                'title' => t('ID бренда. Одно значение или массив значений'),
                'type' => 'integer[]',
                'func' => self::FILTER_TYPE_IN
            ],
            'title' => [
                'title' => t('Название бренда, частичное совпадение'),
                'type' => 'string',
                'func' => self::FILTER_TYPE_LIKE
            ],
            'public' => [
                'title' => t('Только публичные бренды? 1 или 0'),
                'type' => 'integer',
                'func' => self::FILTER_TYPE_EQ
            ],
        ];
    }

    /**
     * Возвращает объект, который позволит производить выборку товаров
     *
     * @return \Catalog\Model\BrandApi
     */
    public function getDaoObject()
    {
        $dao = new \Catalog\Model\BrandApi();
        $dao->setFilter('public', 1);
        return $dao;
    }


    /**
     * Возвращает список брендов
     *
     * @param string $token Авторизационный токен
     * @param array $filter фильтр категорий по параметрам. Возможные ключи: #filters-info
     * @param string $sort Сортировка категорий по параметрам. Возможные значения #sort-info
     * @param integer $page Номер страницы
     * @param integer $pageSize Количество элементов на страницу
     *
     * @example GET /api/methods/brand.getlist?token=2bcbf947f5fdcd0f77dc1e73e73034f5735de4868
     *
     * GET /api/methods/brand.getlist?token=2bcbf947f5fdcd0f77dc1e73e73034f5735de4868&filter[title]=
     *
     * Ответ:
     * <pre>
     * {
     *     "response": {
     *         "list": [
     *             {
     *              "id": "1",
     *              "title": "Acer",
     *              "alias": "acer",
     *              "public": "1",
     *              "description": "<p>Описание бренда</p>",
     *              "xml_id": null,
     *              "meta_title": "",
     *              "meta_keywords": "",
     *              "meta_description": ""
     *              "images": [ //Картинка
     *                   {
     *                     "original_url": "http://full.readyscript.local/storage/photo/original/a/46s7ye2cobjx5j6.jpg",
     *                      "big_url": "http://full.readyscript.local/storage/photo/resized/xy_1000x1000/a/46s7ye2cobjx5j6_ded27759.jpg",
     *                      "small_url": "http://full.readyscript.local/storage/photo/resized/xy_300x300/a/46s7ye2cobjx5j6_7aa365e2.jpg"
     *                  }
     *              ]
     *             }
     *             , ...
     *         ],
     *     }
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
        $response = parent::process($token, $filter, $sort, $page, $pageSize);

        return $response;
    }
}