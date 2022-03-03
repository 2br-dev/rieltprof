<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Article\Model\ExternalApi\Article;
use Article\Model\Orm\Article;

/**
* Возвращает статью по ID
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
        return 'article';
    }

    /**
     * Возвращает объект с которым работает
     *
     * @return \Article\Model\Orm\Article
     */
    public function getOrmObject()
    {
        return new \Article\Model\Orm\Article();
    }

    /**
     * Возвращает статью по ID
     *
     * @param string $token Авторизационный токен
     * @param integer $article_id ID статьи
     *
     *
     * @example GET api/methods/article.get?token=2bcbf947f5fdcd0f77dc1e73e73034f5735de486&article_id=1
     * Ответ
     * <pre>
     * {
     *     "response": {
     *         "brand": {
     *           "id": "3",
     *           "title": "Asus",
     *           "alias": "asus",
     *           "public": "1",
     *           "image": { //Может не быть
     *               "original_url": "http://full.readyscript.local/storage/system/original/c16e07c8cab021dd39d8a6e43c2e06d4.png",
     *               "big_url": "http://full.readyscript.local/storage/system/resized/xy_1000x1000/c16e07c8cab021dd39d8a6e43c2e06d4_81089c1d.png",
     *               "middle_url": "http://full.readyscript.local/storage/system/resized/xy_600x600/c16e07c8cab021dd39d8a6e43c2e06d4_1890920.png",
     *               "small_url": "http://full.readyscript.local/storage/system/resized/xy_300x300/c16e07c8cab021dd39d8a6e43c2e06d4_6850efe2.png",
     *               "micro_url": "http://full.readyscript.local/storage/system/resized/xy_100x100/c16e07c8cab021dd39d8a6e43c2e06d4_8940851c.png"
     *               "nano_url": "http://full.readyscript.local/storage/system/resized/xy_100x100/c16e07c8cab021dd39d8a6e43c2e06d4_8940851c.png"
     *           },
     *           "description": "<p>Описание категории</p>",
     *           "xml_id": null,
     *           "meta_title": "",
     *           "meta_keywords": "",
     *           "meta_description": "",
     *           "products_count": "1"
     *         }
     *     }
     * }
     * </pre>
     * @return array
     * @throws \ExternalApi\Model\Exception
     */
    function process($token = null, $article_id, $sections = ['images'])
    {
        $result = parent::process($token, $article_id);

        $this->object->getPropertyIterator()->append([
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
        ]);

        if ($this->object['image']){
            $result['response']['article']['image'] = \Catalog\Model\ApiUtils::prepareImagesSection($this->object->__image);
        }
        $result['response']['article']['preview_text'] = $this->object->getPreview(100, false);
        $result['response']['article']['date'] = date('d.m.Y', strtotime($this->object['dateof']));

        if (!empty($this->object['content'])) {
            $result['response']['article']['content'] = \ExternalApi\Model\Utils::prepareHTML($this->object['content']);
        }

        if (!empty($this->object['short_content'])){
            $result['response']['article']['short_content'] = \ExternalApi\Model\Utils::prepareHTML($this->object['short_content']);
        }

        //Если нужно добавить секцию с картинками
        if (in_array('images', $this->method_params['sections'])) {

            $photo_api = new \Photo\Model\PhotoApi();
            $images = [];
            $photos = $photo_api->getLinkedImages($this->object['id'], Article::IMAGES_TYPE);
            if (!empty($photos)) { //Массив товаров прикпренных к статье
                foreach ($photos as $photo) {
                    $images[] = \Catalog\Model\ApiUtils::prepareImagesSection($photo);
                }
            }
            $result['response']['article']['photos'] = $images;
        }

        return $result;
    }
}