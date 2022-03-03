<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Article\Model\ExternalApi\Article;

/**
* Возвращает список статей
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
        return ['id', 'id desc', 'title', 'title desc', 'dateof', 'dateof desc'];
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
                'title' => t('Название статьи, частичное совпадение'),
                'type' => 'string',
                'func' => self::FILTER_TYPE_LIKE
            ],
            'public' => [
                'title' => t('Только публичные статьи? 1 или 0'),
                'type' => 'integer',
                'func' => self::FILTER_TYPE_EQ
            ],
            'parent' => [
                'title' => t('Категория статьи'),
                'type' => 'integer',
                'func' => self::FILTER_TYPE_EQ
            ],
        ];
    }

    /**
    * Возвращает объект, который позволит производить выборку товаров
    * 
    * @return \Article\Model\Api
    */
    public function getDaoObject()
    {
        $dao = new \Article\Model\Api();
        $dao->setFilter('public', 1);
        return $dao;
    }

    /**
     * Возвращает список объектов
     *
     * @param \Article\Model\Api $dao - API стетей
     * @param integer $page - номер страницы
     * @param integer $pageSize - количество элементов
     * @return array
     */
    public function getResultList($dao, $page, $pageSize)
    {
        $list = $dao->getList($page, $pageSize);

        if (!empty($list)){
            if (in_array('images', $this->method_params['sections'])){//Если нужно добавить секцию с картинками
                $dao->getElement()->getPropertyIterator()->append([
                    'preview_text' => new \RS\Orm\Type\Varchar([
                        'description' => t('Текст анонса'),
                        'appVisible' => true,
                    ]),
                    'date' => new \RS\Orm\Type\Varchar([
                        'description' => t('Дата'),
                        'appVisible' => true,
                    ]),
                    'photos' => new \RS\Orm\Type\ArrayList([
                        'description' => t('Фото статьи'),
                        'appVisible' => true,
                    ]),
                    'attached_products_arr' => new \RS\Orm\Type\ArrayList([
                        'description' => t('Массив прикреплённых товаров'),
                        'appVisible' => true,
                    ]),
                ]);

                $photo_api = new \Photo\Model\PhotoApi();
            }

            /**
             * @var \Article\Model\Orm\Article $article
             */
            foreach ($list as $article){
                if ($article['image']){
                    \Catalog\Model\ApiUtils::prepareImagesSection($article->__image);
                }

                $article['preview_text'] = $article->getPreview(100, false);
                $article['date'] = date('d.m.Y', strtotime($article['dateof']));

                if (!empty($article['content'])){
                    $article['content'] = \ExternalApi\Model\Utils::prepareHTML($article['content']);
                }

                if (!empty($article['short_content'])){
                    $article['short_content'] = \ExternalApi\Model\Utils::prepareHTML($article['short_content']);
                }

                //Если нужно добавить секцию с картинками
                if (in_array('images', $this->method_params['sections'])){
                    $images = [];
                    $photos = $photo_api->getLinkedImages($article['id'], $article::IMAGES_TYPE);
                    if (!empty($photos)){ //Массив товаров прикпренных к статье
                        foreach ($photos as $photo){
                            $images[] = \Catalog\Model\ApiUtils::prepareImagesSection($photo);
                        }
                    }
                    $article['photos'] = $images;

                    if (!empty($article['attached_products_arr'])){ //Массив товаров прикпренных к статье

                    }
                }
            }
        }

        return \ExternalApi\Model\Utils::extractOrmList( $list );
    }


    /**
     * Возвращает список статей
     *
     * @param string $token Авторизационный токен
     * @param array $filter фильтр категорий по параметрам. Возможные ключи: #filters-info
     * @param string $sort Сортировка статей. Возможные значения #sort-info
     * @param integer $page Номер страницы
     * @param integer $pageSize Количество элементов на страницу
     * @param array $sections Количество элементов на страницу
     *
     * sections:
     * images - набор фото которые присутвуют у статьи
     *
     * @example GET /api/methods/article.getlist?token=2bcbf947f5fdcd0f77dc1e73e73034f5735de4868
     *
     * GET /api/methods/article.getlist?token=2bcbf947f5fdcd0f77dc1e73e73034f5735de4868&filter[parent]=1
     *
     * Ответ:
     * <pre>
     * {
     *     "response": {
     *         "summary": {
     *               "page": 1,
     *               "pageSize": 10,
     *               "total": "1"
     *           }, 
     *         "list": [
     *             {
     *                   "id": "90",
     *                   "title": "Ортопедические стельки купить",
     *                   "alias": "alias",
     *                   "content": "HTML страницы",
     *                   "parent": "13",
     *                   "dateof": "2017-07-05 08:27:52",
     *                   "image": {
     *                       "original_url": "http://readyscript.ru/storage/system/original/be9f29bedcbdd33e95b23b0f1a7c83dc.jpg",
     *                       "big_url": "http://readyscript.ru/storage/system/resized/xy_1000x1000/be9f29bedcbdd33e95b23b0f1a7c83dc_2a6888d5.jpg",
     *                       "middle_url": "http://readyscript.ru/storage/system/resized/xy_600x600/be9f29bedcbdd33e95b23b0f1a7c83dc_23f1021b.jpg",
     *                       "small_url": "http://readyscript.ru/storage/system/resized/xy_300x300/be9f29bedcbdd33e95b23b0f1a7c83dc_4a28e4d9.jpg",
     *                       "micro_url": "http://readyscript.ru/storage/system/resized/xy_100x100/be9f29bedcbdd33e95b23b0f1a7c83dc_ab388e27.jpg",
     *                       "nano_url": "http://readyscript.ru/storage/system/resized/xy_50x50/be9f29bedcbdd33e95b23b0f1a7c83dc_6e3c7b78.jpg"
     *                   },
     *                   "user_id": "1",
     *                   "public": "1",
     *                   "short_content": "Краткое содержимое",
     *                   "meta_title": "",
     *                   "meta_keywords": "",
     *                   "meta_description": "",
     *                   "photos": [
     *                       {
     *                           "id": "11409",
     *                           "title": "",
     *                           "original_url": "http://readyscript.ru/storage/photo/original/b/u0o50i27l7yd0mh.png",
     *                           "big_url": "http://readyscript.ru/storage/photo/resized/xy_1000x1000/b/u0o50i27l7yd0mh_59385d53.png",
     *                           "middle_url": "http://readyscript.ru/storage/photo/resized/xy_600x600/b/u0o50i27l7yd0mh_33887645.png",
     *                           "small_url": "http://readyscript.ru/storage/photo/resized/xy_300x300/b/u0o50i27l7yd0mh_5a519087.png",
     *                           "micro_url": "http://readyscript.ru/storage/photo/resized/xy_100x100/b/u0o50i27l7yd0mh_bb41fa79.png",
     *                           "nano_url": "http://readyscript.ru/storage/photo/resized/xy_50x50/b/u0o50i27l7yd0mh_dc38951a.png"
     *                       },
     *                       ...
     *                   ]
     *             }
     *            , ...
     *         ],
     *     }
     * }
     * </pre>
     * Возвращает список объектов и связанные с ним сведения.
     * @return array
     * @throws \ExternalApi\Model\Exception
     */
    protected function process($token = null, 
                               $filter = [],
                               $sort = "dateof desc",
                               $page = 1,
                               $pageSize = 20,
                               $sections = ['images'])
    {
        $response = parent::process($token, $filter, $sort, $page, $pageSize);

        return $response;
    }
}