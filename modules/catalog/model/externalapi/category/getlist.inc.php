<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\ExternalApi\Category;

/**
* Возвращает список категорий по ID родителя
*/
class GetList extends \ExternalApi\Model\AbstractMethods\AbstractGetTreeList
{
    const
        RIGHT_LOAD = 1;
    
    protected
        $token_require = false,
        $category_list;
    
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
    * Возвращает объект, который позволит производить выборку товаров
    * 
    * @return \Catalog\Model\DirApi
    */
    public function getDaoObject()
    {
        $dao = new \Catalog\Model\DirApi();
        $dao->setFilter('public', 1);
        return $dao;
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
        return [];
    }    
    
    /**
    * Возвращает возможные значения для сортировки
    * 
    * @return array
    */
    public function getAllowableOrderValues()
    {
        return ['id', 'id desc', 'name', 'name desc', 'sortn', 'sortn desc'];
    }
    
    /**
    * Возвращает список объектов
    * 
    * @param \Catalog\Model\DirApi $dao - объект API
    * @param integer $parent_id - объект API
    * @return array
    */
    public function getResultList($dao, $parent_id)
    {
        $this->category_list = $dao->getTreeList($parent_id);
        $this->category_list = $this->addImageData($this->category_list);
        return \ExternalApi\Model\Utils::extractOrmTreeList( $this->category_list );
    }   
       
    
    /**
    * Добавляет секцию с изображениями к категориям
    * 
    * @param array $treelist - массив из объектов категорий
    * @return array
    */
    protected function addImageData($treelist)
    {
        //Загружаем изображения
        if (in_array('image', $this->method_params['sections'])) {
            if (!empty($treelist)){
                foreach ($treelist as $key=>$orm_object){
                    
                    if (!empty($orm_object['fields']['image'])){
                        /**
                        * @var \RS\Orm\Type\Image $image
                        */
                        $image = $orm_object['fields']->__image;
                        \Catalog\Model\ApiUtils::prepareImagesSection($image);
                    }
                    
                    //Если есть дети, то и им добавим изображения
                    if (isset($orm_object['child']) && !empty($orm_object['child'])){
                        $treelist[$key]['child'] = $this->addImageData($treelist[$key]['child']);
                    }
                }
            }
        }
        return $treelist;
    }
    
    /**
    * Возвращает список категорий
    * 
    * @param string $token Авторизационный токен
    * @param integer $parent_id идентификатор родительской категории. 
    * @param array $filter фильтр категорий по параметрам. Возможные ключи: #filters-info
    * @param string $sort Сортировка категорий по параметрам. Возможные значения #sort-info
    * @param array $sections - Дополнительные секции, которые должны быть представлены в результате.
    * Возможные значения:
    * <b>image</b> - изображение категории
    * 
    * @example GET /api/methods/category.getlist?token=2bcbf947f5fdcd0f77dc1e73e73034f5735de486&parent_id=0
    * Ответ:
    * <pre>
    * {
    *     "response": {
    *         "list": [
    *             {
    *                 "id": "1",
    *                 "name": "Категория 1",
    *                 "alias": "category-1",
    *                 "description": "Ноутбуки с оптимальным соотношением цены и возможностей. ...",
    *                 "public": "1",
    *                 "is_virtual": null,
    *                 "parent" : "0",  
    *                 "xml_id": null,
    *                 "meta_title": "",
    *                 "meta_keywords": "",
    *                 "meta_description": "",
    *                 "product_meta_title": "",
    *                 "product_meta_keywords": "",
    *                 "product_meta_description": "",
    *                 "tax_ids": "all",
    *                 "virtual_data": "null",
    *                 "virtual_data_arr": "null",
    *                 "weight": "0",
    *                 "image": [ //Может быть пустым
    *                    {
    *                        "original_url": "http://full.readyscript.local/storage/photo/original/a/46s7ye2cobjx5j6.jpg",
    *                        "big_url": "http://full.readyscript.local/storage/photo/resized/xy_1000x1000/a/46s7ye2cobjx5j6_ded27759.jpg",
     *                       "small_url": "http://full.readyscript.local/storage/photo/resized/xy_300x300/a/46s7ye2cobjx5j6_7aa365e2.jpg"
     *                       "micro_url": "http://full.readyscript.local/storage/photo/resized/xy_100x100/a/46s7ye2cobjx5j6_7aa365e2.jpg"
     *                       "nano_url": "http://full.readyscript.local/storage/photo/resized/xy_50x50/a/46s7ye2cobjx5j6_7aa365e2.jpg"
    *                    }
    *                 ],
    *                 "child": [ //Может быть пустым
    *                     "id": "2,
    *                     "name": "Категория 2,
    *                     "alias": "category-2,
    *                     "description": "Ноутбуки с оптимальным соотношением цены и возможностей. ...",
    *                     "public": "1",
    *                     "is_virtual": null,
    *                     "parent" : "1",  
    *                     "xml_id": null,
    *                     "meta_title": "",
    *                     "meta_keywords": "",
    *                     "meta_description": "",
    *                     "product_meta_title": "",
    *                     "product_meta_keywords": "",
    *                     "product_meta_description": "",
    *                     "tax_ids": "all",
    *                     "virtual_data": "null",
    *                     "virtual_data_arr": "null",
    *                     "weight": "0",
    *                     "image": [ //Может быть пустым
    *                     {
    *                        "original_url": "http://full.readyscript.local/storage/photo/original/a/46s7ye2cobjx5j6.jpg",
    *                        "big_url": "http://full.readyscript.local/storage/photo/resized/xy_1000x1000/a/46s7ye2cobjx5j6_ded27759.jpg",
    *                        "small_url": "http://full.readyscript.local/storage/photo/resized/xy_300x300/a/46s7ye2cobjx5j6_7aa365e2.jpg"
    *                        "micro_url": "http://full.readyscript.local/storage/photo/resized/xy_100x100/a/46s7ye2cobjx5j6_7aa365e2.jpg"
    *                        "nano_url": "http://full.readyscript.local/storage/photo/resized/xy_50x50/a/46s7ye2cobjx5j6_7aa365e2.jpg"
    *                     }
    *                     "child" ....
    *                 ],
    *                   ......
    *                 ]  
    *                   
    *             }
    *         ],
    *         "root_category": { //Корневая категория
    *            "id": "187",
    *            "name": "Электроника",
    *            "alias": "elektronika",
    *            "parent": "0",
    *            "public": "1",
    *            "image": null,
    *            "weight": "0",
    *            "description": "",
    *            "meta_title": "",
    *            "meta_keywords": "",
    *            "meta_description": "",
    *            "product_meta_title": "",
    *            "product_meta_keywords": "",
    *            "product_meta_description": "",
    *            "is_virtual": "0",
    *            "virtual_data_arr": null,
    *            "virtual_data": null,
    *            "tax_ids": "1",
    *            "bonuses_units": "0"
    *        }  
    *     }
    * }
    * </pre>
    * 
    * @return array Возвращает список объектов и связанные с ним сведения.
    */
    protected function process($token = null,
                               $parent_id = 0,                                 
                               $filter = [],
                               $sort = 'sortn', 
                               $sections = ['image'])
    {
        $result = parent::process($token, $parent_id, $filter, $sort);
        
        //Получим, также корневую директорию, если она существует
        if ($parent_id>0){
            $root_dir = new \Catalog\Model\Orm\Dir($parent_id);
            if ($root_dir['id']){ //Если категория существует
                \Catalog\Model\ApiUtils::prepareImagesSection($root_dir->__image);    
                $result['response']['root_category'] = \ExternalApi\Model\Utils::extractOrm($root_dir);    
            }else{ //Если категория удалена
                $result['response']['root_category'] = false;
            }  
        }
                      
        return $result;
    }
}