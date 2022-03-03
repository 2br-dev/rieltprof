<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\ExternalApi\Product;
use ExternalApi\Model\Utils;

/**
 * Возвращает товар по ID
 */
class GetList extends \ExternalApi\Model\AbstractMethods\AbstractGetList
{
    const
        RIGHT_LOAD = 1,
        RIGHT_COST_LOAD = 2,
        FILTER_TYPE_BFILTER = 'bfilter', //Базовые фильтры
        FILTER_TYPE_PF = 'pf', //Фильтр характеристик
        FILTER_TYPE_DIR = 'dir'; //Фильтр директорий

    protected
        $token_require = false,
        $costs_loaded = false, //Цены были уже загружены?
        $current_currency, //Текущая валюта
        $dirs_x_id = [], //Массив категорий с ключами id
        $list_products,
        $filter_cache; //Кэш для фильтров

    /**
     * @var \Catalog\Model\Api $dao
     */
    protected $dao;

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
            self::RIGHT_LOAD => t('Загрузка списка объектов'),
            self::RIGHT_COST_LOAD => t('Загрузка полного списка цен товаров')
        ];
    }

    /**
     * Возвращает объект, который позволит производить выборку товаров
     *
     * @return \Catalog\Model\Api
     */
    function getDaoObject()
    {
        if ($this->dao == null){
            $this->dao = new \Catalog\Model\Api();
            $this->dao->setFilter('public', 1);

            $this->dao->getElement()->getPropertyIterator()->append([
                'num' => new \RS\Orm\Type\Decimal([
                    'maxLength' => 11,
                    'decimal' => 3,
                    'allowEmpty' => false,
                    'appVisible' => true
                ]),
            ]);
        }

        return $this->dao;
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
                'title' => t('ID товара. Одно значение или массив значений'),
                'type' => 'integer[]',
                'func' => self::FILTER_TYPE_IN
            ],
            'title' => [
                'title' => t('Название товара, частичное совпадение'),
                'type' => 'string',
                'func' => self::FILTER_TYPE_LIKE
            ],
            'barcode' => [
                'title' => t('Артикул'),
                'type' => 'string',
                'func' => self::FILTER_TYPE_EQ
            ],
            'bfilter' => [
                'title' => t('Цена от и до и фильтр по брендам'),
                'type' => 'array',
                'func' => self::FILTER_TYPE_BFILTER
            ],
            'pf' => [
                'title' => t('Фильтр по характеристикам'),
                'type' => 'array',
                'func' => self::FILTER_TYPE_PF
            ],
            'dir' => [
                'title' => t('Категория'),
                'type' => 'integer[]',
                'func' => self::FILTER_TYPE_DIR
            ],
            'group_id' => [
                'title' => t('Идентификатор группы товаров (не путать с категорией)'),
                'type' => 'string',
                'func' => self::FILTER_TYPE_EQ
            ],
            'xml_id' => [
                'title' => t('Внешний уникальный идентификатор'),
                'type' => 'string',
                'func' => self::FILTER_TYPE_EQ
            ]
        ];
    }

    /**
     * Возвращает возможные значения для сортировки
     *
     * @return array
     */
    public function getAllowableOrderValues()
    {
        return ['id', 'id desc', 'dateof', 'dateof desc', 'dateof asc',
            'title', 'title asc', 'title desc', 'barcode', 'barcode desc',
            'num', 'num asc', 'num desc', 'rating', 'rating asc', 'rating desc',
            'cost', 'sortn', 'sortn asc', 'sortn desc', 'cost asc', 'cost desc'];
    }

    /**
     * Устанавливает фильтр для выборки
     *
     * @param \Catalog\Model\Api $dao - api
     * @param array $filter - значение фильтров
     *
     * @throws \RS\Exception
     * @return void
     */
    public function setFilter($dao, $filter)
    {
        parent::setFilter($dao, $filter);

        //Добавляем полнотекстовый фильтр
        if ($this->method_params['fulltext_filter']) {
            $q = $dao->queryObj();
            $q->select = $dao->defAlias().'.*';

            $search = \Search\Model\SearchApi::currentEngine();
            $search->setFilter('B.result_class', 'Catalog\Model\Orm\Product');
            $search->setQuery($this->method_params['fulltext_filter']);
            $search->joinQuery($q);
        }

        $config = \RS\Config\Loader::byModule('catalog');

        if ($config['hide_unobtainable_goods'] == 'Y'){ //Если нужно скрывать товары которых нет в наличии
            $dao->queryObj()->where("num > 0");
        }

        $dao->queryObj()->groupby($dao->defAlias().'.id');
    }

    /**
     * Устаналивает фильтр по секции dir - фильтр по категориям
     *
     * @param string $key - секция фильтров
     * @param array $value - значение фильтров секции
     * @param array $filters - все фильтры
     * @param array $filter_settings - настройки фильтров
     * @return array
     */
    protected function makeFilterDir($key, $value, $filters, $filter_settings)
    {
        $request_dirs = (array)$value;
        //Добавим дочерние категории к запрашиваемым
        $value = [];
        $first_dir_id = reset($request_dirs);
        if (!empty($request_dirs) &&  $first_dir_id != 0){ //Если есть директории и первыя не корневая директория
            $dir_api = new \Catalog\Model\Dirapi();
            foreach ($request_dirs as $request_dir_id){
                $value = array_merge($value, $dir_api->getChildsId($request_dir_id));
            }
        }

        if (count($request_dirs) == 1 && $first_dir_id != 0){
            /**
             * @var \Catalog\Model\Orm\Dir $category
             */
            $category = $this->getDirByID($first_dir_id);
            //Устанавливаем дополнительные условия фильтрации, если открыта "Виртуальная категория"
            if ($category['is_virtual']) {
                if ($product_ids_by_virtual_dir = $category->getVirtualDir()->getFilteredProductIds($value)) {
                    $this->dao->setFilter('id', $product_ids_by_virtual_dir, 'in');
                    $value = [];
                }
            }
        }

        if (!empty($value)) {
            return [
                "$key:in" => implode(',', \RS\Helper\Tools::arrayQuote($value))
            ];
        } else {
            return [];
        }
    }


    /**
     * Устаналивает фильтр по секции bfilter - базовые фильтры
     *
     * @param string $key - секция фильтров
     * @param array $value - значение фильтров секции
     * @param array $filters - все фильтры
     * @param array $filter_settings - настройки фильтров
     * @return array
     */
    protected function makeFilterBFilter($key, $value, $filters, $filter_settings)
    {
        $filters_array = (array)$filters['dir'];
        if (!empty($value)){
            $this->dao->applyBaseFilters($value);
        }
        return [];
    }

    /**
     * Устаналивает фильтр по секции pf - фильтр по характеристикам
     *
     * @param string $key - секция фильтров
     * @param array $value - значение фильтров секции
     * @param array $filters - все фильтры
     * @param array $filter_settings - настройки фильтров
     * @return array
     */
    protected function makeFilterPF($key, $value, $filters, $filter_settings)
    {
        if (!empty($value)){
            //Загружаем свойства для фильтров                
            $prop_api = new \Catalog\Model\Propertyapi();
            $pids = $prop_api->getFilteredProductIds($value);
            if ($pids !== false) $this->dao->setFilter('id', $pids, 'in');
        }
        return [];
    }

    /**
     * Возвращает список объектов
     *
     * @param \Catalog\Model\Api $dao
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function getResultList($dao, $page, $pageSize)
    {
        $this->list_products = $dao->getList($page, $pageSize);
        $this->addImageData($dao);
        $this->addDirData($dao);
        $this->addCostData($dao);
        $this->addPropertyData($dao);
        $this->addConcomitantData($dao);

        \Catalog\Model\ApiUtils::addProductCostValuesSection($this->list_products);

        $list = \ExternalApi\Model\Utils::extractOrmList( $this->list_products );

        foreach ($list as &$product){
            $product['short_description'] = nl2br($product['short_description']);
            $product['description'] = Utils::prepareHTML($product['description']);
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
     * @param \Catalog\Model\Api $dao
     */
    protected function addDirData($dao)
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
        $this->list_products = $dao->addProductsDirs($this->list_products); //Добавим сведения по категориям
        if (!empty($this->list_products)){
            foreach ($this->list_products as $product){
                if (!empty($specdirs)){
                    /** @var \Catalog\Model\Orm\Product $product */
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
                }

                $category = \ExternalApi\Model\Utils::extractOrm($this->getDirByID($product['maindir']));
                $product['category'] = $category ? $category : ['name' => t('Bce')];
            }
        }
    }

    /**
     * Добавляет секцию с изображениями к товару
     *
     * @param \Catalog\Model\Api $dao
     * @return void
     */
    protected function addImageData($dao)
    {
        //Загружаем изображения
        if (in_array('image', $this->method_params['sections'])) {
            $dao->getElement()->getPropertyIterator()->append([
                'image' => new \RS\Orm\Type\ArrayList([
                    'description' => t('Изображения'),
                    'appVisible' => true
                ])
            ]);
            $this->list_products = $dao->addProductsPhotos($this->list_products);
            foreach($this->list_products as $product) {
                $images = [];
                foreach($product->getImages() as $image) {
                    $images[] = \Catalog\Model\ApiUtils::prepareImagesSection($image);
                }
                $product['image'] = $images;
            }
        }
    }

    /**
     * Добавляет секцию с ценами к товару
     *
     * @param mixed $dao
     * @return void
     */
    protected function addCostData($dao)
    {
        //Загружаем цены
        if (in_array('cost', $this->method_params['sections']) && !$this->checkAccessError(self::RIGHT_COST_LOAD)) {
            if (!$this->costs_loaded){
                $this->list_products = $this->dao->addProductsCost($this->list_products);
                $this->costs_loaded  = true;
            }
        } else {
            $dao->getElement()->__excost->setVisible(false, 'app');
        }
    }

    /**
     * Добавляет секцию с сопутствующими товарами к товару
     *
     * @param \Catalog\Model\Api $dao - Апи товаров
     * @return void
     */
    protected function addConcomitantData($dao)
    {
        if (in_array('concomitant', $this->method_params['sections'])) {
            $dao->getElement()->getPropertyIterator()->append([
                'concomitant' => new \RS\Orm\Type\ArrayList([
                    'description' => t('Сопутствующие товары'),
                    'appVisible' => true
                ])
            ]);
            foreach($this->list_products as &$product) {
                /**
                 * @var \Catalog\Model\Orm\Product $product
                 */
                $concomitants = $product->getConcomitant();

                $product['concomitant'] = [];
                if (!empty($concomitants)){
                    $concomitants = $dao->addProductsCost($concomitants);
                    $concomitants = $dao->addProductsPhotos($concomitants);
                    \Catalog\Model\ApiUtils::addProductCostValuesSection($concomitants);

                    $arr = [];
                    foreach($concomitants as $concomitant){
                        /**
                         * @var \Catalog\Model\Orm\Product $concomitant
                         */
                        $concomitant['image'] = \Catalog\Model\ApiUtils::prepareImagesSection($concomitant->getMainImage());
                        $arr[] = \ExternalApi\Model\Utils::extractOrm($concomitant);
                    }
                    $product['concomitant'] = $arr;
                }
            }
        }
    }

    /**
     * Добавляет секцию с характеристиками к товару
     *
     * @param \Catalog\Model\PropertyApi $dao - объект API
     * @return void
     */
    protected function addPropertyData($dao)
    {
        if (in_array('property', $this->method_params['sections'])) {
            $dao->getElement()->getPropertyIterator()->append([
                'property_values' => new \RS\Orm\Type\ArrayList([
                    'description' => t('Характеристики товара'),
                    'appVisible' => true
                ])
            ]);

            $this->list_products = $dao->addProductsProperty($this->list_products);

            foreach($this->list_products as $product){
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
    }

    /**
     * Возвращает список характеристик фильтров для товаров
     *
     * @param integer $category_id - id категории
     *
     * @return array
     */
    function getFilterPropertyList($category_id)
    {
        $prop_api  = new \Catalog\Model\Propertyapi();
        $prop_list = $prop_api->getGroupProperty($category_id, true, true);

        //Получим по кэшу характеристики значений.
        $allowable_values = $this->dao->getAllowablePropertyValues($category_id, $this->filter_cache);

        //Фильтруем значения характеристик в зависимости от состава отображаемых товаров
        $prop_list = $prop_api->filterByAllowedValues($prop_list, $allowable_values);
        //Значения характеристик для фильтра
        $prop_values = \Catalog\Model\ApiUtils::prepareFiltersPropertyListSections($prop_list);

        return $prop_values;
    }


    /**
     * Возвращает список брендов для фильтров по товарам
     *
     * @return array
     */
    function getBrandsList()
    {
        $cache_id = $this->query; //Ключ кэша
        $brands_filters_list = $this->api->getAllowableBrandsValues($cache_id);
        return \ExternalApi\Model\Utils::extractOrmList($brands_filters_list);
    }

    /**
     * Устанавливает сортировку при выборке
     *
     * @param \Catalog\Model\Api $dao - объект текущего API
     * @param string $sort - сортировка
     */
    function setOrder($dao, $sort)
    {
        $sort = $this->makeOrder($sort);
        $sort_info = explode(" ", $sort);
        $direction = isset($sort_info[1]) ? $sort_info[1] : '';
        $dao->setSortOrder($sort_info[0], $direction);
    }

    /**
     * Возвращает общее количество элементов, согласно условию.
     *
     * @param \Catalog\Model\Api $dao - объект API
     * @return integer
     */
    function getResultCount($dao)
    {
        $q = clone $dao->queryObj();

        return $q->select('COUNT(DISTINCT '.$dao->defAlias().'.'.$dao->getIdField().') as cnt')
            ->limit(null)
            ->orderby(null)
            ->groupby(null)
            ->exec()
            ->getOneField('cnt', 0);
    }


    /**
     * Возвращает список товаров
     *
     * @param string $token Авторизационный токен
     * @param string $fulltext_filter Полнотекстовый поиск по товарам. Использует стандартные настройки поиска в системе
     * @param array $filter фильтр товаров по параметрам. Возможные ключи: #filters-info
     * @param string $sort Сортировка товаров по параметрам. Возможные значения #sort-info
     * @param integer $page Номер страницы
     * @param integer $pageSize Количество элементов на страницу
     * @param array $sections Дополнительные секции, которые должны быть представлены в результате.
     *
     * Возможные значения:
     * <b>image</b> - изображения товара
     * <b>cost</b> - цены товара
     * <b>property</b> - характеристики товара
     * <b>concomitant</b> - сопутствующие товары
     * <b>filters</b> - фильтры в категории. Действует только если передана одна категория в параметре dir.
     * Также отсутствует, если фильтры переданы в запросе (bfilter, pf).
     * <b>root_category</b> - корневая категория товара, появляется только тогда, когда выбран фильтр только по одной категории
     * <b>unit</b> - единица измерения
     * <b>current_currency</b> - текущая валюта
     *
     * @example GET /api/methods/product.getlist?token=2bcbf947f5fdcd0f77dc1e73e73034f5735de486
     * //Фильтр по категории с id = 1
     * GET /api/methods/product.getlist?token=2bcbf947f5fdcd0f77dc1e73e73034f5735de486&filter[dir][]=1
     * //Фильтр по категории и фильтрам по цене в этой категории.
     * GET /api/methods/product.getlist?token=2bcbf947f5fdcd0f77dc1e73e73034f5735de486&filter[dir][]=1&filter[bfilter][<b>cost</b>][from]=0&filter[bfilter][<b>cost</b>][to]=15000
     * //Фильтр по категории и фильтрам по бренду в этой категории.
     * GET /api/methods/product.getlist?token=2bcbf947f5fdcd0f77dc1e73e73034f5735de486&filter[dir][]=1&filter[bfilter][<b>brand</b>][]=1
     * //Фильтр по категории и фильтрам по характеристикам в этой категории.
     * GET /api/methods/product.getlist?token=2bcbf947f5fdcd0f77dc1e73e73034f5735de486&filter[dir][]=1&filter[<b>pf</b>][]=2
     *
     * Ответ:
     * <pre>
     * {
     *     "response": {
     *         "summary": {
     *             "page": "1",
     *             "pageSize": 1,
     *             "total": "192"
     *         },
     *         "list": [
     *             {
     *                 "id": "494",
     *                 "title": "Стул изысканный",
     *                 "alias": "stul-izyskannyy",
     *                 "short_description": "",
     *                 "description": "",
     *                 "barcode": "a000494",
     *                 "weight": "0",
     *                 "dateof": "2016-08-02 16:41:07",
     *                 "excost": {
     *                     "1": {
     *                         "product_id": "494",
     *                         "cost_id": "1",
     *                         "cost_val": "7200.00",
     *                         "cost_original_val": "7200.00",
     *                         "cost_original_currency": "1"
     *                     },
     *                     "2": {
     *                         "product_id": "494",
     *                         "cost_id": "2",
     *                         "cost_val": "0.00",
     *                         "cost_original_val": "0.00",
     *                         "cost_original_currency": "1"
     *                     },
     *                     "11": {
     *                         "product_id": "494",
     *                         "cost_id": "11",
     *                         "cost_val": "0.00",
     *                         "cost_original_val": "0.00",
     *                         "cost_original_currency": "1"
     *                     }
     *                 },
     *                 "cost_values": {
     *                   "cost": "16500.0 руб.",
     *                   "cost_format": 16 500,
     *                   "old_cost"  18500.0,
     *                   "old_cost_format": "18 500 руб."
     *                 }
     *                 "unit": "0",
     *                 "min_order": "0",
     *                 "public": "1",
     *                 "xdir": [
     *                     "226",
     *                     "231"
     *                 ],
     *                 "maindir": "226",
     *                 "xspec": [
     *                     "231"
     *                 ],
     *                 "reservation": "default",
     *                 "brand_id": "0",
     *                 "rating": "0.0",
     *                 "group_id": "",
     *                 "xml_id": null,
     *                 "num": 10,
     *                 "offer_caption": "",
     *                 "meta_title": "",
     *                 "meta_keywords": "",
     *                 "meta_description": "",
     *                 "tax_ids": "category",
     *                 "image": [
     *                     {
     *                         "id": 1,
     *                         "title": "",
     *                         "original_url": "http://full.readyscript.local/storage/photo/original/c/9sc29n10h6kqjvy.jpg",
     *                         "big_url": "http://full.readyscript.local/storage/photo/resized/xy_1000x1000/c/9sc29n10h6kqjvy_adea43a6.jpg",
     *                         "small_url": "http://full.readyscript.local/storage/photo/resized/xy_300x300/c/9sc29n10h6kqjvy_5610e96e.jpg"
     *                         "micro_url": "http://full.readyscript.local/storage/photo/resized/xy_300x300/c/9sc29n10h6kqjvy_5610e96e.jpg"
     *                         "nano_url": "http://full.readyscript.local/storage/photo/resized/xy_300x300/c/9sc29n10h6kqjvy_5610e96e.jpg"
     *                     }
     *                 ],
     *                 "specdirs": [ //Спец. категории
     *                 {
     *                    "id": "231",
     *                    "name": "Популярные вещи",
     *                    "alias": "populyarnye-veshchi",
     *                    "parent": "0",
     *                    "public": "0",
     *                    "image": {
     *                        "original_url": "http://full.readyscript.local/storage/system/original/0991615122a9eb972b72581d6f9625dc.jpg",
     *                        "big_url": "http://full.readyscript.local/storage/system/resized/xy_1000x1000/0991615122a9eb972b72581d6f9625dc_e262d049.jpg",
     *                        "middle_url": "http://full.readyscript.local/storage/system/resized/xy_600x600/0991615122a9eb972b72581d6f9625dc_2791f974.jpg",
     *                        "small_url": "http://full.readyscript.local/storage/system/resized/xy_300x300/0991615122a9eb972b72581d6f9625dc_4e481fb6.jpg",
     *                        "micro_url": "http://full.readyscript.local/storage/system/resized/xy_100x100/0991615122a9eb972b72581d6f9625dc_af587548.jpg"
     *                        "nano_url": "http://full.readyscript.local/storage/system/resized/xy_100x100/0991615122a9eb972b72581d6f9625dc_af587548.jpg"
     *                    },
     *                    "weight": "0",
     *                    "description": "",
     *                    "meta_title": "",
     *                    "meta_keywords": "",
     *                    "meta_description": "",
     *                    "product_meta_title": "",
     *                    "product_meta_keywords": "",
     *                    "product_meta_description": "",
     *                    "is_virtual": "0",
     *                    "virtual_data_arr": null,
     *                    "virtual_data": null,
     *                    "tax_ids": "1",
     *                    "bonuses_units": "0",
     *                    "bonuses_units_type": "0"
     *                 },
     *                 "category": {
     *                    "id": "204",
     *                    "name": "Демо-продукты",
     *                    "alias": "demo-produkty",
     *                    "parent": "0",
     *                    "public": "1",
     *                    "image": {
     *                        "original_url": "http://full.readyscript.local/storage/system/original/0991615122a9eb972b72581d6f9625dc.jpg",
     *                        "big_url": "http://full.readyscript.local/storage/system/resized/xy_1000x1000/0991615122a9eb972b72581d6f9625dc_e262d049.jpg",
     *                        "middle_url": "http://full.readyscript.local/storage/system/resized/xy_600x600/0991615122a9eb972b72581d6f9625dc_2791f974.jpg",
     *                        "small_url": "http://full.readyscript.local/storage/system/resized/xy_300x300/0991615122a9eb972b72581d6f9625dc_4e481fb6.jpg",
     *                        "micro_url": "http://full.readyscript.local/storage/system/resized/xy_100x100/0991615122a9eb972b72581d6f9625dc_af587548.jpg"
     *                        "nano_url": "http://full.readyscript.local/storage/system/resized/xy_100x100/0991615122a9eb972b72581d6f9625dc_af587548.jpg"
     *                    },
     *                    "weight": "0",
     *                    "description": "<p>В данной категории находятся товары, ярко демонстрирующие различные возможности системы. Переключитесь к табличному виду представления товаров, чтобы прочитать краткие описания к товарам.</p>",
     *                    "meta_title": "",
     *                    "meta_keywords": "",
     *                    "meta_description": "",
     *                    "product_meta_title": "",
     *                    "product_meta_keywords": "",
     *                    "product_meta_description": "",
     *                    "is_virtual": "0",
     *                    "virtual_data_arr": null,
     *                    "virtual_data": null,
     *                    "tax_ids": "1",
     *                    "bonuses_units": "0",
     *                    "bonuses_units_type": "0"
     *                 },
     *                 "cost_values": {
     *                     "cost" => "16500",
     *                     "old_cost" => "0",
     *                 }
     *                 "property_values": [
     *                     {
     *                        "id": "76",
     *                        "title": "Аксессуары",
     *                        "hidden": "0",
     *                        "list": [
     *                           {
     *                              "id": "951",
     *                              "title": "Назначение",
     *                              "type": "list",
     *                              "unit": "",
     *                              "unit_export": null,
     *                              "name_for_export": null,
     *                              "parent_id": "76",
     *                              "int_hide_inputs": "0",
     *                              "hidden": "0",
     *                              "no_export": "0",
     *                              "value": "",
     *                              "text_value": "",
     *                              "parent_title": "Аксессуары"
     *                           },
     *                           ...
     *                       ]
     *                     },
     *                     ...
     *                 ]
     *             }
     *         ],
     *         //Секции с фильтрами для категорий
     *         "category_filters" : {//Если отсутстуют фильтры для категории, то будет null
     *           "price": { //Фильтр по цене
     *               "interval_to": "23560.00",
     *               "interval_from": "5860.00",
     *               "unit": "р."
     *           },
     *           "property": [ //Фильтры по характеристикам
     *               {
     *                   "id": null,
     *                   "title": "Без группы",
     *                   "hidden": null,
     *                   "properties": [
     *                       {
     *                           "id": "1",
     *                           "title": "Форм-фактор",
     *                           "type": "list",
     *                           "sortn": "1",
     *                           "interval_from": "", //Только если числовая характеристика
     *                           "interval_to": "", //Если числовая характеристика
     *                           "unit": "",
     *                           "group_id": "187",
     *                           "parent_id": "0",
     *                           "hidden": "0",
     *                           "no_export": "0",
     *                           "public": "1",
     *                           "allowed_values": [ //Возможные значения (для списковых характеристик)
     *                               {
     *                                   "id": "1",
     *                                   "value": "Моноблок",
     *                                   "color": "#912626"
     *                               },
     *                               ...
     *                           ]
     *                       },
     *                       ...
     *                   ]
     *               }
     *           ],
     *           "concomitant": [
     *                 {
     *                     "id": "41",
     *                     "title": "Планшет ViewSonic ViewPad 10",
     *                     "alias": "planshet-viewsonic-viewpad-10",
     *                     "short_description": "Ноутбуки серии специально разрабатывались для игр....",
     *                     "description": "Ноутбук (англ. notebook — блокнот, блокнотный ПК) — портативный персональный компьютер...",
     *                     "barcode": "22257-DS4UTN2",
     *                     "weight": "0",
     *                     "dateof": "2013-08-07 11:02:54",
     *                     "excost": null,
     *                     "unit": "0",
     *                     "min_order": null,
     *                     "public": "1",
     *                     "xdir": null,
     *                     "maindir": "16",
     *                     "xspec": null,
     *                     "reservation": "default",
     *                     "brand_id": "0",
     *                     "rating": "0.0",
     *                     "group_id": null,
     *                     "xml_id": null,
     *                     "offer_caption": "",
     *                     "meta_title": "",
     *                     "meta_keywords": "",
     *                     "meta_description": "",
     *                     "tax_ids": "category"
     *                 }
     *           ],
     *           "brands": [ //Фильтры по брендам
     *               {
     *                   "id": "6",
     *                   "title": "HTC",
     *                   "alias": "htc",
     *                   "public": "1",
     *                   "image": {
     *                       "original_url": "http://test22.local/storage/system/original/8ddbf4a7fc53d49653be2ae2c5b8fff1.png",
     *                       "big_url": "http://test22.local/storage/system/resized/xy_1000x1000/8ddbf4a7fc53d49653be2ae2c5b8fff1_30b3f677.png",
     *                       "middle_url": "http://test22.local/storage/system/resized/xy_600x600/8ddbf4a7fc53d49653be2ae2c5b8fff1_525f105a.png",
     *                       "small_url": "http://test22.local/storage/system/resized/xy_300x300/8ddbf4a7fc53d49653be2ae2c5b8fff1_3b86f698.png",
     *                       "micro_url": "http://test22.local/storage/system/resized/xy_100x100/8ddbf4a7fc53d49653be2ae2c5b8fff1_da969c66.png"
     *                       "nano_url": "http://test22.local/storage/system/resized/xy_100x100/8ddbf4a7fc53d49653be2ae2c5b8fff1_da969c66.png"
     *                   },
     *                   "description": "<p>Описание бренда</p>",
     *                   "xml_id": null,
     *                   "meta_title": "",
     *                   "meta_keywords": "",
     *                   "meta_description": ""
     *               }
     *           ]
     *         },
     *         "cost": { //Если есть права на доступ
     *             "1": {
     *                 "id": "1",
     *                 "title": "Розничная",
     *                 "type": "manual"
     *             },
     *             "2": {
     *                 "id": "2",
     *                 "title": "Зачеркнутая цена",
     *                 "type": "manual"
     *             },
     *             "11": {
     *                 "id": "11",
     *                 "title": "Типовое соглашение с клиентом",
     *                 "type": "manual"
     *             }
     *         },
     *         "root_category": { //Появляется, только тогда когда выбран фильтр только по одной категории
     *               "id": "204",
     *               "name": "Демо-продукты",
     *               "alias": "demo-produkty",
     *               "parent": "0",
     *               "public": "1",
     *               "image": null,
     *               "weight": "0",
     *               "description": "<p>В данной категории находятся товары, ярко демонстрирующие различные возможности системы. Переключитесь к табличному виду представления товаров, чтобы прочитать краткие описания к товарам.</p>",
     *               "meta_title": "",
     *               "meta_keywords": "",
     *               "meta_description": "",
     *               "product_meta_title": "",
     *               "product_meta_keywords": "",
     *               "product_meta_description": "",
     *               "is_virtual": "0",
     *               "virtual_data_arr": null,
     *               "virtual_data": null,
     *               "tax_ids": "1",
     *               "bonuses_units": "0",
     *               "bonuses_units_type": "0"
     *         },
     *         "unit": {
     *             "1": {
     *                 "id": "1",
     *                 "code": "796",
     *                 "icode": "PCE",
     *                 "title": "штука",
     *                 "stitle": "шт."
     *             },
     *             "0": {
     *                 "id": "1",
     *                 "code": "796",
     *                 "icode": "PCE",
     *                 "title": "штука",
     *                 "stitle": "шт."
     *             }
     *         },
     *         "current_currency": {
     *                "id": "1",
     *                "title": "RUB",
     *                "stitle": "р.",
     *                "is_base": "1",
     *                "ratio": "1",
     *                "public": "1",
     *                "default": "1"
     *         }
     *     }
     * }
     * </pre>
     * @throws \RS\Exception
     * @return array Возвращает список объектов и связанные с ним сведения.
     */
    function process($token = null,
                     $fulltext_filter = '',
                     $filter = [],
                     $sort = 'dateof desc',
                     $page = 1,
                     $pageSize = 20,
                     $sections = ['image', 'cost', 'property', 'concomitant', 'unit', 'current_currency', 'filters'])
    {
        //Если фильтруем только по одной категории, то и получим по ней сведения.
        $root_category = false;
        if (isset($filter['dir']) && ((isset($filter['dir']) && !is_array($filter['dir'])) || (is_array($filter['dir']) && count($filter['dir'])==1))){
            $dir_id = $filter['dir'];
            if (is_array($filter['dir'])){
                $dir_id = reset($filter['dir']);
            }
            if ($dir_id){
                $root_dir = new \Catalog\Model\Orm\Dir($dir_id);
                if ($root_dir['id']){ //Если категория существует
                    \Catalog\Model\ApiUtils::prepareImagesSection($root_dir->__image);
                    $root_category = \ExternalApi\Model\Utils::extractOrm($root_dir);
                }else{ //Если категория удалена
                    $result['response']                     = [];
                    $result['response']['summary']['total'] = 0;
                    $result['response']['list']             = [];
                    $result['response']['root_category']    = $root_category;
                    return $result;
                }
            }
        }

        $result = parent::process($token, $filter, $sort, $page, $pageSize);

        if ($root_category){
            $result['response']['root_category'] = $root_category;
        }

        $costs = [];
        $units = [];

        if (!empty($this->list_products)){ //Если товары найдены
            foreach($this->list_products as $product) {
                $costs += (array)$product['excost'];
                $units[$product['unit']] = true;
            }

            //Добавляет секцию с фильтрами для списка товаров, работает тогда когда получаем только для одной директории товары
            if ((isset($filter['dir']) && ((is_array($filter['dir']) && count($filter['dir'])==1) || !is_array($filter['dir'])) && in_array('filters', $sections))
                && (!isset($filter['bfilter']['cost']))//Если секция с фильтром по цене не задана
            ) {
                //Получим id категории
                $filter_dir  = (array)$filter['dir'];
                $category_id = current($filter_dir);
                $category    = $this->getDirByID($category_id);
                if (is_array($filter['dir'])){
                    reset($filter['dir']);
                }
                //Очистим для запроса
                $this->dao->queryObj()->offset = null;
                $this->dao->queryObj()->limit  = null;

                $this->filter_cache   = $category_id.$fulltext_filter;
                $result['response']['category_filters'] = null;

                $this->dao->queryObj()->groupby = null;
                $price_minmax = $this->dao->getMinMaxProductsCost();
                
                $this->dao->queryObj()->groupby( $this->dao->defAlias().'.id');
                if ($price_minmax['interval_to']){
                    $result['response']['category_filters']['price']         = $price_minmax;
                    $result['response']['category_filters']['price']['unit'] = \Catalog\Model\CurrencyApi::getDefaultCurrency()->stitle;
                    if (!$category['is_virtual']){
                        $result['response']['category_filters']['property']  = $this->getFilterPropertyList($category_id);
                    }
                }

                if (!$category['is_virtual']){
                    //Значения брендов характеристик
                    $brands_filters_list = $this->dao->getAllowableBrandsValues($this->filter_cache);
                    $brands = [];
                    foreach ($brands_filters_list as $brand){
                        $value = \ExternalApi\Model\Utils::extractOrm($brand);
                        if ($brand['image']){
                            $value['image'] = \Catalog\Model\ApiUtils::prepareImagesSection($brand->__image);
                        }
                        $brands[] = $value;
                    }
                    if (!empty($brands)){
                        $result['response']['category_filters']['brands'] = $brands;
                    }
                }
            }

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

            //Загружаем единицы измерения
            if (in_array('unit', $sections)) {
                $default_unit = (int)\RS\Config\Loader::byModule($this)->default_unit;

                $unit_api = new \Catalog\Model\UnitApi();
                $unit_api->setFilter('id', array_merge(array_keys($units), [$default_unit]), 'in');
                $unit_objects = \ExternalApi\Model\Utils::extractOrmList($unit_api->getList(), 'id');

                //Загружаем единицу измерения по умолчанию.
                if ($default_unit && isset($units[0])) {
                    $unit_objects[0] = $unit_objects[$default_unit];
                }
                $result['response']['unit'] = $unit_objects;
            }

            //Валюта товара
            if (in_array('current_currency', $sections)) {
                $result['response']['current_currency'] = \ExternalApi\Model\Utils::extractOrm(\Catalog\Model\CurrencyApi::getCurrentCurrency());
            }
        }else{
            $result['response']                     = [];
            $result['response']['summary']['total'] = 0;
            $result['response']['list']             = [];
        }



        return $result;
    }
}