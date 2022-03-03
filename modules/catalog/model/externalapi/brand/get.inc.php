<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\ExternalApi\Brand;

/**
* Возвращает бренд по ID
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
        return 'brand';
    }
    
    /**
    * Возвращает объект с которым работает
    * 
    */
    public function getOrmObject()
    {
        return new \Catalog\Model\Orm\Brand();
    }
    
    /**
    * Возвращает количетсво товаров принадлежащее бренду
    * 
    * @param \Catalog\Model\Orm\Brand $brand - объект бренда
    * @return integer
    */
    static function getBrandProductsCount($brand, $cache = true)
    {
        if ($cache) {
            return \RS\Cache\Manager::obj()
                    ->request(['\Catalog\Model\ExternalApi\Brand\Get', 'getBrandProductsCount'], $brand, false);
        }else{
            return \RS\Orm\Request::make()
                    ->select('COUNT(id) as cnt')
                    ->from(new \Catalog\Model\Orm\Product())
                    ->where([
                        'brand_id' => $brand['id'],
                        'public' => 1,
                    ])->exec()
                    ->getOneField('cnt');
        } 
    }

    /**
     * Возвращает бренд по ID
     *
     * @param string $token Авторизационный токен
     * @param integer $brand_id ID бренда
     * @param array $sections Секции с данными, которые следует включить в ответ. Возможные значения:
     *
     *
     * @example GET api/methods/brand.get?token=2bcbf947f5fdcd0f77dc1e73e73034f5735de486&brand_id=1
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
    function process($token = null, $brand_id, $sections = ['products_count'])
    {
        $response = parent::process($token, $brand_id);
        
        //Загружаем количество товаров принадлежашее бренду
        if (in_array('products_count', $sections)) {
            $response['response']['brand']['products_count'] = self::getBrandProductsCount($this->object);
        }
        
        return $response;
    }
}