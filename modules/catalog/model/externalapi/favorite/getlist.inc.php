<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Catalog\Model\ExternalApi\Favorite;

use Catalog\Model\FavoriteApi;
use \ExternalApi\Model\Exception as ApiException;

/**
* Получает товары из избранного для пользователя
*/
class GetList extends \ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod
{
    const
        RIGHT_FAVORITE = 1,
        RIGHT_COST_LOAD = 2;
        
    protected
        $token_require = false,
        $token_error = [];
    public
        $product_api,
        $favorite_api;

    protected $dirs_x_id = [];
        
        
    function __construct()
    {
        parent::__construct();
        //Добавим API
        $this->favorite_api = FavoriteApi::getInstance();
        $this->product_api  = new \Catalog\Model\Api();
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
            self::RIGHT_FAVORITE => t('Доступ к функционалу "Избранного"'),
            self::RIGHT_COST_LOAD => t('Загрузка полного списка цен товаров')
        ];
    }
    
    
    /**
    * Проверяет права на выполнение данного метода
    * 
    * @param array $params - массив приходящих параметров
    * @param string $version - текущая версия
    * @throws ApiException
    */
    public function validateRights($params, $version)
    {
        try{
            parent::validateRights($params, $version);
        }catch(ApiException $e){
            if ($e->getCodeString() == ApiException::ERROR_METHOD_ACCESS_DENIED || $e->getCodeString() == ApiException::ERROR_WRONG_PARAM_VALUE){
                $this->token_error['code']    = $e->getCodeString();
                $this->token_error['message'] = $e->getMessage();
            }else{
                throw $e;
            }
        }
    }
    
    
    /**
    * Добавляет секцию с изображениями к товару
    * 
    * @param array $list - массив товаров
    * @return array
    */
    protected function addImageData($list)
    {
        //Загружаем изображения
        if (in_array('image', $this->method_params['sections'])) {
            $product = new \Catalog\Model\Orm\Product();
            $product->getPropertyIterator()->append([
                'image' => new \RS\Orm\Type\ArrayList([
                    'description' => t('Изображения'),
                    'appVisible' => true
                ])
            ]);
            $list = $this->product_api->addProductsPhotos($list);
            foreach($list as $product) {
                $images = [];
                foreach($product->getImages() as $image) {
                    $images[] = \Catalog\Model\ApiUtils::prepareImagesSection($image);
                }
                $product['image'] = $images;
            }
        }
        return $list;
    }
    
    
    /**
    * Добавляет секцию с ценами к товару
    * 
    * @param array $list - массив товаров
    * @return array
    */
    protected function addCostData($list)
    {
        $list = $this->product_api->addProductsCost($list);
        
        //Добавим секцию со сведениями о ценах
        $list = \Catalog\Model\ApiUtils::addProductCostValuesSection($list);
        
        //Загружаем цены
        if (!in_array('cost', $this->method_params['sections']) || !$this->checkAccessError(self::RIGHT_COST_LOAD)) {
            $product = new \Catalog\Model\Orm\Product();
            $product->__excost->setVisible(false, 'app');
        }     
        return $list;
    }
    
    /**
    * Добавляет секцию с характеристиками к товару
    * 
    * @param array $list - массив товаров
    * @return array
    */
    protected function addPropertyData($list)
    {
        if (in_array('property', $this->method_params['sections'])) {
            $api     = new \Catalog\Model\Api();
            $product = new \Catalog\Model\Orm\Product();
            $product->getPropertyIterator()->append([
                'property_values' => new \RS\Orm\Type\ArrayList([
                    'description' => t('Характеристики товара'),
                    'appVisible' => true
                ])
            ]);
            
            $list = $api->addProductsProperty($list);

            foreach($list as $product){
                /**
                 * @var \Catalog\Model\Orm\Product $product
                 */
                $product_props = [];
                if ($product['properties']){
                    foreach($product['properties'] as $data) {
                        if (!$data['group']['hidden']) {
                            $group = \ExternalApi\Model\Utils::extractOrm($data['group']); //Группа характеристик
                            $group['list'] = [];
                            foreach($data['properties'] as $prop_id => $prop) {
                                if (!$prop['hidden']) {
                                    /**
                                     * @var \Catalog\Model\Orm\Property\Item $prop
                                     */
                                    $prop_data = \ExternalApi\Model\Utils::extractOrm($prop);
                                    $prop_data['value'] = $prop->textView();
                                    $prop_data['text_value'] = trim($prop_data['value'] . " " . $prop_data['unit']);
                                    $prop_data['parent_title'] = $group['title'];
                                    $group['list'][] = $prop_data;
                                }
                            }
                            $product_props[] = $group;
                        }
                    }
                }
                $product['property_values'] = $product_props;
            }
        }
        return $list;
    }

    /**
     * Возвращает категорию по идентификатору
     *
     * @param integer $id - id категории
     * @return array
     */
    protected function getDirByID($id)
    {
        if (!isset($this->dirs_x_id[$id])){
            $dir = new \Catalog\Model\Orm\Dir($id);
            if ($dir['image']){
                \Catalog\Model\ApiUtils::prepareImagesSection($dir->__image);
            }
            $this->dirs_x_id[$id] = $dir;
        }
        return $this->dirs_x_id[$id];
    }

    /**
     * Добавяляет сведения по категориям
     *
     * @param array $favorite - массив товаров
     *
     * @return array
     */
    protected function addDirData($favorite)
    {
        //Расширим товар
        $product = new \Catalog\Model\Orm\Product();
        $product->getPropertyIterator()->append([
            'specdirs' => new \RS\Orm\Type\ArrayList([
                'description' => t('Спец. категории'),
                'appVisible' => true
            ]),
            'category' => new \RS\Orm\Type\MixedType([
                'description' => t('Главная директория'),
                'appVisible' => true
            ]),
        ]);
        //Разберём спец. категории
        $specdirs = $product->getSpecDirs();
        $favorite = $this->product_api->addProductsDirs($favorite); //Добавим сведения по категориям
        if (!empty($favorite)){
            foreach ($favorite as $product){
                if (!empty($specdirs)){
                    /**
                     * @var \Catalog\Model\Orm\Product $product
                     */
                    $arr = [];
                    foreach ($specdirs as $specdir){
                        if (in_array($specdir['id'], $product['xspec'])){
                            if ($specdir['image']){
                                \Catalog\Model\ApiUtils::prepareImagesSection($specdir->__image);
                            }
                            $arr[] = \ExternalApi\Model\Utils::extractOrm($specdir);
                        }
                    }
                    $product['specdirs'] = $arr;
                    $product['category'] = \ExternalApi\Model\Utils::extractOrm($this->getDirByID($product['maindir']));
                }
            }
        }

        return $favorite;
    }
    
    
    /**
    * Возвращает список из товаров в избранном
    * 
    * @param integer $page - текущая страница
    * @param integer $pageSize - количество товаров на странице
    * @return array
    */
    protected function getList($page, $pageSize)
    {
        $list = $this->favorite_api->getFavoriteList($page, $pageSize);
        
        if (!empty($list)){
           $list = $this->addCostData($list);
           $list = $this->addDirData($list);
           $list = $this->addImageData($list);
           $list = $this->addPropertyData($list);
        }
        
        return $list;
    }

    
    /**
    * Получает товары списком из избранного. Для незарегестрированных пользователей передавать токен не нужно, а для присуствующих в системе обязательно.
    * Если токен не указан, или указан неправильно, то будет возвращен список для неавторизованного пользователя.
    * 
    * @param string $token Авторизационный токен
    * @param integer $page Номер страницы 
    * @param integer $pageSize Количество элементов на страницу
    * @param array $sections Дополнительные секции, которые должны быть представлены в результате.
    * Возможные значения:
    * <b>image</b> - изображения товара
    * <b>cost</b> - цены товара
    * <b>property</b> - характеристики товара
    * <b>unit</b> - единица измерения
    * <b>current_currency</b> - текущая валюта
    * 
    * @example GET api/methods/favorite.getlist?token=2bcbf947f5fdcd0f77dc1e73e73034f5735de486
    * Ответ
    * <pre>
    * {
    *     "response": {
    *        "summary": {
    *        "token_used": false,
    *        "page": "1",
    *        "pageSize": "20",
    *        "total": "2"
    *     },
    *     "list": [
    *        {
    *            "id": "650",
    *            "title": "Acer beTouch E400",
    *            "alias": "acer-betouch-e400",
    *            "short_description": "",
    *            "description": "<p>Подробности товара</p>",
    *            "barcode": "55773-ROGRJKP",
    *            "weight": "0",
    *            "dateof": "2013-08-07 00:40:02",
    *            "unit": "0",
    *            "min_order": null,
    *            "public": "1",
    *            "no_export": "0",
    *            "xdir": null,
    *            "num": 2,
    *            "maindir": "215",
    *            "xspec": null,
    *            "reservation": "default",
    *            "brand_id": "1",
    *            "rating": "0.0",
    *            "group_id": null,
    *            "xml_id": null,
    *            "offer_caption": "",
    *            "meta_title": "",
    *            "meta_keywords": "",
    *            "meta_description": "",
    *            "tax_ids": "category",
    *            "bonuses_units": "0",
    *            "discount_ignore": "0",
    *            "cost_values": {
    *                "cost": "12599.00",
    *                "cost_format": "12 599 р.",
    *                "old_cost": "35.00",
    *                "old_cost_format": "35 р."
    *            },
    *            "image": [
    *                {
    *                    "id": "2718",
    *                    "title": null,
    *                    "original_url": "http://full.readyscript.ru/storage/photo/original/h/unnkx43cqemrse0.jpg",
    *                    "big_url": "http://full.readyscript.ru/storage/photo/resized/xy_1000x1000/h/unnkx43cqemrse0_d13dcf92.jpg",
    *                    "middle_url": "http://full.readyscript.ru/storage/photo/resized/xy_600x600/h/unnkx43cqemrse0_96c590cf.jpg",
    *                    "small_url": "http://full.readyscript.ru/storage/photo/resized/xy_300x300/h/unnkx43cqemrse0_ff1c760d.jpg",
    *                    "micro_url": "http://full.readyscript.ru/storage/photo/resized/xy_100x100/h/unnkx43cqemrse0_1e0c1cf3.jpg"
    *                    "nano_url": "http://full.readyscript.ru/storage/photo/resized/xy_100x100/h/unnkx43cqemrse0_1e0c1cf3.jpg"
    *                },
    *                ...
    *            ]
    *         },
    *         ...
    *      ]
    *    }
    * }
    * </pre>
    * @return array
    */
    function process($token = null, 
                     $page = 1,
                     $pageSize = 20,
                     $sections = ['image', 'cost', 'property', 'current_currency'])
    {
        if (!$token){ //Если токен не использовали.
            $response['response']['summary']['token_used'] = false; 
            $this->favorite_api->setGuestId(session_id());
        }else{ //Если токен использовали.
            $response['response']['summary']['token_used'] = true; 
            $this->favorite_api->setUserId($this->token->getUser()->id); //Установим пользователя из токена
        }
        
        //Если есть секция с ошибками токена
        if (!empty($this->token_error)){
            $response['response']['summary']['token_error'] = $this->token_error;
            $response['response']['summary']['token_used'] = false;  //Если ошибки токен не действителен
        }
        
        $response['response']['summary']['page']     = $page;
        $response['response']['summary']['pageSize'] = $pageSize;
        
        //Получим список товаров в избранном
        $list = $this->getList($page, $pageSize);
        
        $costs = [];
        $units = [];
        
        foreach($list as $product) {
            $costs += (array)$product['excost'];
            $units[$product['unit']] = true;
        }

        $product = new \Catalog\Model\Orm\Product();

        //Добавляем разрешение на получение num
        $product->getPropertyIterator()->append([
            'num' => new \RS\Orm\Type\Decimal([
                'maxLength' => 11,
                'decimal' => 3,
                'allowEmpty' => false,
                'appVisible' => true
            ]),
        ]);
        
        $list = \ExternalApi\Model\Utils::extractOrmList($list); //Преобразуем список
        $response['response']['summary']['total'] = $this->favorite_api->getFavoriteCount();
        
        //Загружаем справочник цен
        if (in_array('cost', $sections)) {            
            if ($costs) {
                $cost_api = new \Catalog\Model\CostApi();
                $cost_api->setFilter('id', array_keys($costs), 'in');
                $cost_objects = $cost_api->getList();
            } else {
                $cost_objects = [];
            }
            $result['response']['cost'] = \ExternalApi\Model\Utils::extractOrmList($cost_objects, 'id');
        }
        
        //Валюта товара
        if (in_array('current_currency', $sections)) {      
            $result['response']['current_currency'] = \ExternalApi\Model\Utils::extractOrm(\Catalog\Model\CurrencyApi::getCurrentCurrency());
        }
        
        $response['response']['list'] = $list;

        return $response;
    }
}