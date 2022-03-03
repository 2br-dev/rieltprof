<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Banners\Model\ExternalApi\Banner;

/**
* Возвращает баннер по Зоне
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
    * Возвращает объект с которым работает
    * 
    */
    public function getOrmObject()
    {
        return new \Banners\Model\Orm\Banner();
    }
    
    /**
    * Возвращает один баннер по идентификатору зоны
    * 
    * @param string $token Авторизационный токен
    * @param string $zone Симв. идентификатор баннерной зоны
    * 
    * @example GET api/methods/banner.get?token=2bcbf947f5fdcd0f77dc1e73e73034f5735de486&zone=main
    * или
    * @example GET api/methods/banner.get?zone=main
    * Ответ
    * <pre>
    * {
    *     "response": {
    *         "banner": {
    *             "id": "9",
    *             "title": "IPhone",
    *             "use_original_file": "0",
    *             "info": "Ноутбуки с оптимальным соотношением цены и возможностей. ...",
    *             "link": "",
    *             "targetblank": "0",
    *             "use_original_file" : "0",  
    *             "public": "1",
    *             "xzone": NULL,
    *             "weight": "100",
    *             "mobile_banner_type": "Menu",
    *             "mobile_link": "",
    *             "mobile_menu_id": "1",
    *             "mobile_product_id": "0",
    *             "mobile_category_id": "0",
    *             "file": [ //Файл с баннером, может быть null
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
    *
    * @return array
    */
    function process($token = null, $zone = '')
    {
        $api = new \Banners\Model\ZoneApi();
        
        /**
        * @var \Banners\Model\Orm\Zone $banner_zone
        */
        $banner_zone = $api->getById($zone);
        if (!$banner_zone){ //Если зона не найдена
            $banner_zone = new \Banners\Model\Orm\Zone();
        }
        
        $banner   = $banner_zone->getOneBanner();
        $banner->getPropertyIterator()->append([
                    'mobile_banner_type' => new \RS\Orm\Type\Varchar([
                        'description' => t('Тип баннера'),
                        'appVisible' => true
                    ]),
                    'mobile_link' => new \RS\Orm\Type\Varchar([
                        'description' => t('Страницы для показа пользователю'),
                        'appVisible' => true
                    ]),
                    'mobile_menu_id' => new \RS\Orm\Type\Integer([
                        'description' => t('Страницы для показа пользователю'),
                        'appVisible' => true
                    ]),
                    'mobile_product_id' => new \RS\Orm\Type\Integer([
                        'description' => t('Товар для показа пользователю'),
                        'appVisible' => true
                    ]),
                    'mobile_category_id' => new \RS\Orm\Type\Integer([
                        'description' => t('Категория для показа пользователю'),
                        'appVisible' => true
                    ])
        ]);
        $response = parent::process($token, $banner['id']);
        
        //Загружаем изображения категории
        if (!empty($response['response']['banner']['file'])){
            $response['response']['banner']['file'] = \Banners\Model\ApiUtils::prepareImagesSection($this->object);
        }
        
        return $response;
    }
}