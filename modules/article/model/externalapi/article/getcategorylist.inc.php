<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Article\Model\ExternalApi\Article;
use ExternalApi\Model\Utils;
use \RS\Orm\Type;

/**
* Возвращает список из пунктов меню
*/
class GetCategoryList extends \ExternalApi\Model\AbstractMethods\AbstractGetTreeList
{
    const
        RIGHT_LOAD = 1;
    
    protected
        $token_require = false, //Токен не обязателен
        $view, //Объект движка шаблонизатора
        $site; //Текущий объект сайта
        
    function __construct()
    {
        parent::__construct();
        $this->view = new \RS\View\Engine();
        $this->site = \RS\Site\Manager::getSite();
    }
    
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
    * Возвращает объект, который позволит производить выборку категорий новостей
    * 
    * @return \Article\Model\CatApi
    */
    public function getDaoObject()
    {
        return new \Article\Model\CatApi();
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
                'title' => t('ID меню. Одно значение или массив значений'),
                'type' => 'integer[]',
                'func' => self::FILTER_TYPE_IN
            ],
            'title' => [
                'title' => t('Название категории, частичное совпадение'),
                'type' => 'string',
                'func' => self::FILTER_TYPE_LIKE
            ],
            'public' => [
                'title' => t('Только публичные категории? 1 или 0'),
                'type' => 'integer',
                'func' => self::FILTER_TYPE_EQ
            ],
            'mobile_public' => [
                'title' => t('Только пункты для мобильных приложений (Флаг в пункте меню.) 1 или 0'),
                'type' => 'integer',
                'func' => self::FILTER_TYPE_EQ
            ],
        ];
    }    
    
    /**
    * Возвращает возможные значения для сортировки
    * 
    * @return array
    */
    public function getAllowableOrderValues()
    {
        return ['id', 'id desc', 'title', 'title desc', 'sortn', 'sortn desc'];
    }

    /**
     * Возвращает список доступных категорий новостей.
     *
     * @param string $token Авторизационный токен
     * @param integer $parent_id id родительской категории
     * @param array $filter фильтр меню по параметрам. Возможные ключи: #filters-info
     * @param string $sort Сортировка товаров по параметрам. Возможные значения: #sort-info
     *
     * @example GET /api/methods/article.getcategorylist
     *
     * GET /api/methods/article.getcategorylist?parent_id=0&filter[mobile_public]=1
     *
     * Ответ:
     * <pre>
     * {
     *     "response": {
     *        "list": [
     *            {
     *                   "id": "2",
     *                   "title": "Новости",
     *                   "alias": "news",
     *                   "parent": "0",
     *                   "public": "1",
     *                   "use_in_sitemap": "0",
     *                   "meta_title": "",
     *                   "meta_keywords": "",
     *                   "meta_description": "",
     *                   "mobile_public": "1",
     *                   "mobile_image": "newsletter",
     *                   "child": []
     *             },
     *             {
     *                   "id": "4",
     *                   "title": "Заголовок",
     *                   "alias": "4",
     *                   "parent": "0",
     *                   "public": "1",
     *                   "use_in_sitemap": "1",
     *                   "meta_title": "",
     *                   "meta_keywords": "",
     *                   "meta_description": "",
     *                   "mobile_public": "1",
     *                   "mobile_image": "",
     *                   "child": [
     *                       {
     *                           "id": "13",
     *                           "title": "Подзаголовок",
     *                           "alias": null,
     *                           "parent": "4",
     *                           "public": "1",
     *                           "use_in_sitemap": "0",
     *                           "meta_title": "",
     *                           "meta_keywords": "",
     *                           "meta_description": "",
     *                           "mobile_public": "1",
     *                           "mobile_image": "",
     *                           "child": []
     *                       },
     *                      ...
     *                   ]
     *             },
     *              ...
     *  }
     * </pre>
     *  Возвращает список объектов и связанные с ним сведения.
     * @return array
     * @throws \RS\Exception
     */
    protected function process($token = null, 
                               $parent_id = 0, 
                               $filter = [],
                               $sort = 'sortn')
    {
        //Если запрос пришёл из мобильного приложения, то смотрим parent_id в модуле
        if (isset($filter['mobile_public'])){
            $mobile_config = \RS\Config\Loader::byModule('mobilesiteapp');
            $parent_id     = $mobile_config['article_root_category'] ? $mobile_config['article_root_category'] : 0;
        }
        return parent::process($token, $parent_id, $filter, $sort);
    }
}