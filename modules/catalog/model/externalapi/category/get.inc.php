<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\ExternalApi\Category;

/**
* Возвращает категорию по ID
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
        return 'category';
    }
    
    /**
    * Возвращает объект с которым работает
    * 
    */
    public function getOrmObject()
    {
        return new \Catalog\Model\Orm\Dir();
    }
    
    /**
    * Возвращает категорию по ID
    * 
    * @param string $token Авторизационный токен
    * @param integer $category_id ID товара
    * @param array $sections Секции с данными, которые следует включить в ответ. Возможные значения:
    * <b>image</b> - изображение категориии
    * 
    * 
    * @example GET api/methods/product.get?token=2bcbf947f5fdcd0f77dc1e73e73034f5735de486&category_id=1
    * Ответ
    * <pre>
    * {
    *     "response": {
    *         "category": {
    *             "id": "1",
    *             "name": "Категория 1",
    *             "alias": "category-1",
    *             "description": "Ноутбуки с оптимальным соотношением цены и возможностей. ...",
    *             "public": "1",
    *             "is_virtual": null,
    *             "parent" : "0",  
    *             "xml_id": null,
    *             "meta_title": "",
    *             "meta_keywords": "",
    *             "meta_description": "",
    *             "product_meta_title": "",
    *             "product_meta_keywords": "",
    *             "product_meta_description": "",
    *             "tax_ids": "all",
    *             "virtual_data": "null",
    *             "virtual_data_arr": "null",
    *             "weight": "0",
    *             "image": [
    *                 {
    *                     "original_url": "http://full.readyscript.local/storage/photo/original/a/46s7ye2cobjx5j6.jpg",
    *                     "big_url": "http://full.readyscript.local/storage/photo/resized/xy_1000x1000/a/46s7ye2cobjx5j6_ded27759.jpg",
    *                     "small_url": "http://full.readyscript.local/storage/photo/resized/xy_300x300/a/46s7ye2cobjx5j6_7aa365e2.jpg"
    *                 }
    *             ]
    *         }
    *     }
    * }
    * </pre>
    */
    function process($token = null, $category_id, $sections = ['image'])
    {
        $response = parent::process($token, $category_id);
        
        //Загружаем изображения категории
        if (in_array('image', $sections)) {
            /**
            * @var \RS\Orm\Type\Image $image
            */
            $image = $this->object->__image;
            $response['response']['category']['image'] = \Catalog\Model\ApiUtils::prepareImagesSection($image);
        }

        
        return $response;
    }
}