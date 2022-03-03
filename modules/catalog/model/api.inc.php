<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Catalog\Model;

use Catalog\Model\Orm\Brand;
use Catalog\Model\Orm\Dir;
use Catalog\Model\Orm\Offer;
use Catalog\Model\Orm\Product;
use Catalog\Model\Orm\Property\Item;
use Catalog\Model\Orm\Property\ItemValue;
use Catalog\Model\Orm\Xdir;
use Catalog\Model\Orm\Xcost;
use Catalog\Model\Product\ProductOffersList;
use Photo\Model\Stub as PhotoStub;
use RS\AccessControl\DefaultModuleRights;
use RS\Db\Exception as DbException;
use RS\Event\Exception as EventException;
use RS\Helper\Tools;
use RS\Module\AbstractModel\EntityList;
use RS\Orm\Exception as OrmException;
use RS\Router\Manager as RouterManager;
use Catalog\Model\Orm\Xstock;
use RS\Config\Loader as ConfigLoader;
use RS\Exception as RSException;
use RS\Module\Manager as ModuleManager;
use RS\Orm\Request as OrmRequest;
use RS\Site\Manager;
use RS\Http\Request as HttpRequest;

/**
 * Класс содержит API функции для работы с товарами
 */
class Api extends EntityList
{
    const WEIGHT_UNIT_G  = 'g';
    const WEIGHT_UNIT_KG = 'kg';
    const WEIGHT_UNIT_T  = 't';
    const WEIGHT_UNIT_LB = 'lb';
    const REQUESTED_PRODUCTS = 1000;

    protected $dynamic_num_warehouses_id;

    function __construct()
    {
        parent::__construct(new Product(), [
            'multisite' => true,
            'aliasField' => 'alias',
            'nameField' => 'title',
            'defaultOrder' => 'dateof DESC',
        ]);
    }

    /**
     * Добавляет к запросу выборки условия для присоединения цен
     *
     * @param Orm\Typecost[] $cost_types - типы цен
     * @return void
     */
    function addCostQuery(array $cost_types)
    {
        foreach($cost_types as $cost_type) {
            $alias = 'XC'.$cost_type['id'];
            $this->queryObj()
                ->select("$alias.cost_val as cost_".$cost_type['id'])
                ->leftjoin(new Xcost(), "$alias.product_id=A.id AND $alias.cost_id='{$cost_type['id']}'", $alias);
        }
    }

    /**
     * Возвращает список товаров для таблицы в административной части
     *
     * @param integer $page - номер страницы
     * @param integer $page_size - количество элементов на страницу
     * @param string $order - сортировка
     * @return array
     */
    public function getTableList($page = null, $page_size = null, $order = null)
    {
        $list = parent::getList($page, $page_size, $order);
        $list = $this->addProductsPhotos($list);
        return $list;
    }

    /**
     * Добавляет ко всем товарам характеристики
     *
     * @param array $products
     * @return array
     */
    public function addProductsProperty($products)
    {
        $api = new PropertyApi();
        $proplist = $api->getProductProperty($products);

        if(isset($products) && is_array($products))   foreach ($products as $item) {
            if (isset($proplist[$item[$this->id_field]])) {
                $item['properties'] = $proplist[$item[$this->id_field]];
            } else {
                $item['properties'] = [];
            }
        }
        return $products;
    }

    /**
     * Загружает свойства cost и xcost в объекты товаров, с учетом текущей группы пользователя, для списка товаров
     *
     * @param Orm\Product[] - список товаров
     * @return Orm\Product[]
     */
    public function addProductsCost($products)
    {
        $tmp = [];
        foreach ($products as $item) {
            $tmp[] = $item[$this->id_field];
        }
        $products_id = $tmp;

        if (empty($products_id))
            return $products;

        $res = OrmRequest::make()
                ->from(new Xcost())
                ->whereIn('product_id', $products_id)
                ->exec();

        $products_xcost = [];
        $products_excost = [];
        while ($row = $res->fetchRow()) {
            $products_xcost[$row['product_id']][$row['cost_id']] = $row['cost_val'];
            $products_excost[$row['product_id']][$row['cost_id']] = $row;
        }

        foreach ($products as $product) {
            /** @var Product $product */
            if (isset($products_xcost[$product[$this->id_field]])) {
                $product['xcost'] = $products_xcost[$product[$this->id_field]];
                $product['excost'] = $products_excost[$product[$this->id_field]];
            }
            $product->calculateUserCost();
        }
        return $products;
    }

    /**
     * Загружает ссылки на фотографии к группе товаров (в свойство images)
     *
     * @param Orm\Product[] - список товаров
     * @return Orm\Product[]
     */
    public function addProductsPhotos($products)
    {
        $products_id = [];
        foreach ($products as $item) {
            $products_id[] = $item[$this->id_field];
        }

        if (empty($products_id))
            return $products;

        $product_photos = OrmRequest::make()
                ->from(new \Photo\Model\Orm\Image())
                ->where([
                    'type' => 'catalog'
                ])
                ->whereIn('linkid', $products_id)
                ->orderby('sortn')
                ->objects(null, 'linkid', true);

        foreach ($products as $product) {
            if (isset($product_photos[$product[$this->id_field]])) {
                $product['images'] = $product_photos[$product[$this->id_field]];
            } else {
                $product['images'] = [new PhotoStub()];
            }
        }
        return $products;
    }

    /**
     * Возвращает сгенерированный артикул
     *
     * @return string
     */
    function genereteBarcode(){
       $config = ConfigLoader::byModule($this);

       $barcode_mask = $config['auto_barcode'];
       //Запросим максимальный элемент в таблице с товарами
       $q = OrmRequest::make()
            ->select('max(id) as id')
            ->from(new Product())
            ->exec();
            
       $row    = $q->fetchRow();
       $max_id = $row['id']+1; //Наш максимальный id
       
       $barcode = preg_replace_callback('/\{(.*?)\}/si',function ($matches) use ($max_id){
          $mask      = explode("|",$matches[1]);
          $max_id = sprintf('%0'.$mask[1].'d', $max_id);
          
          return  $max_id;
           
       }, $barcode_mask);
       
       return $barcode; 
    }

    /**
    *  Устанавливает фильтр для последующей выборки элементов
    * 
    * @param string | array $key - имя поля (соответствует имени поля в БД) или массив для установки группового фильтра
    *
    * Пример применения группового фильтра:
    * array(
    *   'title' => 'Название',                     // AND title = 'Название'
    *   '|title:%like%' => 'Текст'                 // OR title LIKE '%Текст%'
    *   '&title:like%' => 'Текст'                  // AND title LIKE 'Текст%'
    *   'years:>' => 18,                           // AND years > 18
    *   'years:<' => 21,                           // AND years < 21
    *   ' years:>' => 30,                          // AND years > 30  #пробелы по краям вырезаются
    *   ' years:<' => 40,                          // AND years < 40  #пробелы по краям вырезаются
    *   'id:in' => '12,23,45,67,34',               // AND id IN (12,23,45,67,34)
    *   '|id:notin' => '44,33,23'                  // OR id NOT IN (44,33,23)
    * 
    *   array(                                     // AND (
    *       'name' => 'Артем',                     // name = 'Артем'
    *       '|name' => 'Олег'                      // OR name = 'Олег'
    *   ),                                         // )
    *   
    *   '|' => array(                              // OR (
    *       'surname' => 'Петров'                  // surname = 'Петров'
    *       '|surname' => 'Иванов'                 // OR surname = 'Иванов'
    *   )                                          // )
    * )
    * Общая маска ключей массива: 
    * [пробелы][&|]ИМЯ ПОЛЯ[:ТИП ФИЛЬТРА]
    * 
    * @param mixed $value - значение 
    * @param string $type - =,<,>, in, notin, fulltext, %like%, like%, %like тип соответствия поля значению.
    * @param string $prefix условие связки с предыдущими условиями (AND/OR/...)
    * @param array $options
    *
    * @return Api|\RS\Module\AbstractModel\EntityList
    */
    public function setFilter($key, $value = null, $type = '=', $prefix = 'AND', array $options = [])
    {
        if ($key == 'dir') {
            $q = $this->queryObj();
            if (!$q->issetTable(new Xdir())) {
                $q->join(new Xdir(), "{$this->def_table_alias}.id = X.product_id", 'X');
            }

            parent::setFilter('X.dir_id', $value, $type, 'AND');
            return $this;
        }

        return parent::setFilter($key, $value, $type, $prefix, $options);
    }

    /**
     * Возвращает верное количество товаров,
     * которое вернет getList если выбираются товары из нескольких директорий одновременно
     *
     * @return integer
     * @throws DbException
     */
    public function getMultiDirCount()
    {
        $q = clone $this->queryObj();
        $q->orderby(false);

        if ($q->having) {
            //Используем сложный запрос для подсчета количества элементов,
            // если в запросе используется having
            $q->select = $this->defAlias().'.id';
            $count = OrmRequest::make()
                ->from('('.$q->toSql().')', 'subquery')
                ->count();
        } else {
            $q->groupby(false)->select = 'COUNT(DISTINCT A.id) cnt';
            $count = $q->exec()->getOneField('cnt', 0);
        }

        return $count;
    }

    /**
     * Возвращает имя поля БД, в котором будет актуальный общий остаток товара с учетом опций.
     * В мегамаркете есть опция, которая включает динамические остатки
     *
     * @return string (num или dynamic_num)
     */
    public function getNumField()
    {
        if (strpos($this->queryObj()->select, 'dynamic_num') !== false) {
            $num_field = 'dynamic_num';
        } else {
            $num_field = 'A.num';
        }

        return $num_field;
    }

    /**
     * Устанавливает сортировку товаров
     *
     * @param string $field поле сортировки
     * @param string $direction направление сортировки asc или desc
     * @param bool|null $in_stock_first
     */
    public function setSortOrder($field, $direction, $in_stock_first = null)
    {
        if ($in_stock_first === null) {
            $in_stock_first = ConfigLoader::byModule($this)->list_order_instok_first;
        }

        $q = $this->queryObj();
        $order_prefix = '';

        $num_field = $this->getNumField();

        if ($field == 'A.num') {
            $field = $num_field; //Актуально только для Мегамаркета
        }

        if ($in_stock_first
            && $field != $num_field
            && $num_field != 'dynamic_num') {

            //Опция "товары в наличии в начале" не будет работать
            $order_prefix .= $num_field.' > 0 desc, ';
        }

        if ($field == 'cost' || $field == 'A.cost') {
            //Подключаем таблицу цен, если таковая не подключена
            if (!$q->issetTable(new Xcost())) {
                $current_cost_type = CostApi::getUserCost(); //Текущий тип цен
                $current_cost_type = CostApi::getInstance()->getManualType($current_cost_type);
                $q->leftjoin(new Xcost(), "A.id = XC.product_id AND XC.cost_id='{$current_cost_type}'", 'XC');
            }
            $q->orderby($order_prefix . 'XC.cost_val ' . $direction.', A.id');
        } else {
            //При прочих равных всегда должен быть детерминированный столбец A.id, иначе
            //товары на разных страницах могут повторяться. См. Mysql LIMIT Query Optimization
            $this->setOrder($order_prefix . $field . ' ' . $direction.', A.id');
        }

    }

    /**
     * Возвращает массив текущих переданных базовых фильтров из массива $_REQUEST
     *
     * @return array
     * @throws DbException
     * @throws RSException
     */
    public function getBaseFilters()
    {
        //Разберем ЧПУ адрес с фильтрами предварительно, если включена опция и они есть
        $decoded_filters = self::decodeDirFilterParamsFromUrl();
        $bfilter = \RS\Http\Request::commonInstance()->request('bfilter', TYPE_ARRAY);

        return (isset($decoded_filters['bfilter'])) ? $bfilter + $decoded_filters['bfilter'] : $bfilter;
    }

    /**
     * Применяет "базовые" фильтры, т.е. фильтры, которые не зависят от таблиц характеристик товара.
     *
     * @param null|array $bfilters - массив базовых фильтров
     * @return bool
     * @throws DbException
     * @throws RSException
     */
    public function applyBaseFilters($bfilters = null)
    {
        if ($bfilters === null) {
            $bfilters = $this->getBaseFilters();
        }
        foreach ($bfilters as $key => $filter) {
            //Чистим неактивные фильтры
            if ($filter === '' || (is_array($filter) && isset($filter['from']) && empty($filter['from']) && empty($filter['to']))) {
                unset($bfilters[$key]);
            }
        }

        $keys = array_keys($bfilters);
        if (empty($keys))
            return false;

        foreach ($keys as $n => $key) {
            //Вызываем метод на установку фильтров
            $func = $key . 'Filter';
            if (method_exists($this, $func))
                $this->$func($bfilters[$key]);
        }
        return true;
    }

    /**
     * Устанавливает фильтр по наличию
     *
     * @param string $filter - строка с типом фильтра ("", "1", "0")
     * @throws RSException
     */
    protected function isNumFilter($filter)
    {
        if ($filter != "") {
            $config = ConfigLoader::byModule($this);
            if (ModuleManager::staticModuleExists('affiliate') && $config['affiliate_stock_restriction'] && !RouterManager::obj()->isAdminZone() && $filter) {
                //До вызова данного метода должен быть вызван setAffiliateRestrictions(true)
                $this->queryObj()->having('SUM(XST.stock) > 0');
            } elseif ($filter) { //Если надо только в наличии
                $q = $this->queryObj();
                $q->where('num>0');
            } else { //Если надо не в наличии
                $q = $this->queryObj();
                $q->where('num<=0');
            }
        }
    }

    /**
     * Устанавливает фильтр по бренду
     *
     * @param string|array $filter - бренд или массив брендов
     */
    protected function brandFilter($filter)
    {
        $filter = (array)$filter;
        if ($filter) {
            $q = $this->queryObj();
            $q->whereIn('brand_id', $filter);
        }
    }

    /**
     * Устанавливает фильтр по цене с учетом цен текущего пользователя
     *
     * @param array $filter - массив со сведениями о переданном фильтре по цене (ключи to, from)
     */
    protected function costFilter($filter)
    {
        $q = $this->queryObj();

        $current_cost_type = CostApi::getUserCost(); //Текущий тип цен
        $current_cost_type = CostApi::getInstance()->getManualType($current_cost_type);

        $q->leftjoin(new Xcost(), "A.id = XC.product_id AND XC.cost_id='{$current_cost_type}'", 'XC')
                ->orderby('cost_val');

        //Корректируем цены для фильтра, если цена пользователя - автоматическая
        $costapi = CostApi::getInstance();
        $currencyApi = new CurrencyApi();
        if (!empty($filter['from'])) {
            $cost_from = $costapi->correctCost($filter['from']);
            $cost_from = floor($currencyApi->convertToBase($cost_from));
            $q->where("XC.cost_val>='#cost_from'", ['cost_from' => $cost_from]);
        }
        if (!empty($filter['to'])) {
            $cost_to = $costapi->correctCost($filter['to']);
            $cost_to = ceil($currencyApi->convertToBase($cost_to));
            $q->where("XC.cost_val<='#cost_to'", ['cost_to' => $cost_to]);
        }
    }

    /**
     * Загружает товарам свойства xdir, xspec, в которых содержится принадлежность категориям
     *
     * @param Orm\Product[] $products - массив товаров
     * @return Orm\Product[]
     */
    public function addProductsDirs($products)
    {
        $products_id = [];
        foreach ($products as $item) {
            $products_id[] = $item[$this->id_field];
        }
        if (empty($products_id))
            return $products;

        $dirapi = DirApi::getInstance();
        $dirapi->setFilter('is_spec_dir', 'Y');
        $spec_dirs = $dirapi->getAssocList('id');

        $res = OrmRequest::make()->select('*')
                        ->from(new Xdir())
                        ->whereIn('product_id', $products_id)
                        ->exec()->fetchAll();
        if (empty($res))
            return $products;

        $xdir = [];
        $xspec = [];
        foreach ($res as $cats) {
            $dir_id = $cats['dir_id'];
            $products_id = $cats['product_id'];

            $xdir[$products_id][] = $dir_id;
            if (isset($spec_dirs[$dir_id])) {
                $xspec[$products_id][] = $dir_id;
            }
        }

        foreach ($products as $item) {
            $item['xdir'] = isset($xdir[$item['id']]) ? $xdir[$item['id']] : [];
            $item['xspec'] = isset($xspec[$item['id']]) ? $xspec[$item['id']] : [];
        }

        return $products;
    }

    /**
     * Загружает списку товаров комплектации
     *
     * @param Orm\Product[] $products - массив товаров
     * @return Orm\Product[]
     */
    public function addProductsOffers($products)
    {
        $products_id = [];
        foreach ($products as $item) {
            $products_id[] = $item[$this->id_field];
        }

        if (empty($products_id))
            return $products;

        //Загружаем комплектации для списка товаров
        $q = OrmRequest::make()
                        ->select('O.*')
                        ->from(new Orm\Offer(), 'O')
                        ->whereIn('O.product_id', $products_id)
                        ->orderby('O.product_id, O.sortn');

        $this->appendOfferDynamicNum($q);

        $offers = $q->objects(null, 'product_id', true);

        //Раскладываем по товарам
        foreach ($products as $item) {
            $one_offers = [
                'use' => 1,
            ];
            $item_offers = [];
            if (isset($offers[$item['id']])) {
                foreach ($offers[$item['id']] as $offer) {
                    $item_offers[$offer['id']] = $offer;
                }
            } else {
                $one_offers['use'] = 0;
            }
            $one_offers['items'] = new ProductOffersList($item_offers);
            $item['offers'] = $one_offers;
        }
        return $products;
    }

    /**
     * Устанавливает склады для формирования динамической цены
     *
     * @param array $warehouses
     */
    public function setWarehousesForDynamicNum(array $warehouses)
    {
        $this->dynamic_num_warehouses_id = $warehouses;
    }

    /**
     * Добавляет в запрос условие для выборки динамического остатка, если соответствующая опция включена
     * Актуально только в клиентской части
     * Актуально только если присутствует модуль Филиальная сеть
     *
     * @param Request $q
     */
    protected function appendOfferDynamicNum($q)
    {
        if ($this->dynamic_num_warehouses_id) {
            $warehouse_ids_str = implode(',', $this->dynamic_num_warehouses_id);

            $q->select('COALESCE(SUM(XST.stock), 0) as dynamic_num')
                ->leftjoin(new Xstock(), 'XST.offer_id = O.id AND XST.warehouse_id IN (' . $warehouse_ids_str . ')', 'XST')
                ->groupby('O.id');
        }
    }

    /**
     * Добавляет для списка товаров поле с динамическим остатком (на некоторых складах)
     * в случае, если соответствующая опция включена. После вызова данной функции addProductOffers также
     * будет добавлять поле dynamic_num для комплектаций.
     *
     * @param Orm\Product[] $products
     * @param bool $auto_init_affiliate - Вызывает setAffiliateRestrictions при первом вызове
     * @return Product[]
     */
    public function addProductsDynamicNum($products, $auto_init_affiliate = true)
    {
        if ($auto_init_affiliate && $this->dynamic_num_warehouses_id === null) {
            $this->setAffiliateRestrictions();
        }

        if (!$this->dynamic_num_warehouses_id) {
            return $products;
        }

        $products_id = [];
        foreach ($products as $item) {
            $products_id[] = $item[$this->id_field];
        }

        if (empty($products_id))
            return $products;

        $dynamic_nums = OrmRequest::make()
            ->select('product_id, COALESCE(SUM(stock), 0) as dynamic_num')
            ->from(new Xstock())
            ->whereIn('warehouse_id', $this->dynamic_num_warehouses_id)
            ->whereIn('product_id', $products_id)
            ->groupby('product_id')
            ->exec()->fetchSelected('product_id', 'dynamic_num');

        foreach ($products as $item) {
            $item['dynamic_num'] = isset($dynamic_nums[$item['id']]) ? $dynamic_nums[$item['id']] : 0;
        }

        return $products;
    }

    /**
     * Загружает списку товаров уровни многомерных комплектаций
     *
     * @param Orm\Product[] $products массив товаров
     * @return Orm\Product[]
     */
    public function addProductsMultiOffers($products)
    {
        $products_id = [];
        foreach ($products as $item) {
            $products_id[] = $item[$this->id_field];
        }

        if (empty($products_id))
            return $products;

        $levels_res = OrmRequest::make()
                        ->select('I.title as prop_title, Level.*')
                        ->from(new Orm\MultiOfferLevel(),'Level')
                        ->join(new Orm\Property\Item(),'Level.prop_id = I.id','I')
                        ->where([
                            'I.site_id'        => \RS\Site\Manager::getSiteId()
                        ])
                        ->whereIn('I.type', Orm\Property\Item::getListTypes())
                        ->whereIn('Level.product_id', $products_id)
                        ->orderby('product_id, Level.sortn')
                        ->exec();
                        
        $levels = [];
        $props_id = [];
        while($row = $levels_res->fetchRow()) {
            $levels[ $row['product_id'] ][ $row['prop_id'] ] = $row + ['values' => []];
            $props_id[ $row['prop_id'] ] = $row['prop_id'];
        }
        
        if ($props_id) {
            $values_res = OrmRequest::make()
                    ->select('V.*, V.value as val_str, L.product_id') //Для совместимости
                    ->from(new Orm\Property\ItemValue(), 'V')
                    ->join(new Orm\Property\Link(), 'L.val_list_id = V.id', 'L')
                    ->whereIn('L.product_id', $products_id)
                    ->whereIn('L.prop_id', $props_id)
                    ->where("V.value != ''")
                    ->orderby('V.sortn')
                    ->exec();
                    
            while($row = $values_res->fetchRow()) {
                if ($row['val_str'] != '') {
                    $link = new Orm\Property\ItemValue();
                    $link->getFromArray($row);
                    if (isset($levels[ $row['product_id'] ][ $row['prop_id'] ])) {
                        $levels[ $row['product_id'] ][ $row['prop_id'] ]['values'][] = $link;
                    }
                }
            }
        }
                  
        foreach ($products as $item) {
            $product_multioffers = ['use' => false];
            if (isset($levels[ $item['id'] ])) {
                $product_multioffers['use'] = true;
                $product_multioffers['levels'] = $levels[ $item['id'] ];
            }
            $item['multioffers'] = $product_multioffers;
        }
        
        return $products;
    }

    /**
     * Загружает списку товаров флаг, находится ли товар в избранном
     *
     * @param Orm\Product[] $products массив товаров
     * @return Orm\Product[]
     */
    public function addProductsFavorite($products)
    {
        if ($products) {
            $favorite_api = FavoriteApi::getInstance();

            $data = OrmRequest::make()
                ->from(new Orm\Favorite(), 'F')
                ->select("product_id")
                ->where("(F.guest_id = '#guest' OR F.user_id = '#user')",
                    [
                        'guest' => $favorite_api->getGuestId(),
                        'user' => $favorite_api->getUserId(),
                    ])
                ->exec()->fetchSelected('product_id', 'product_id');

            foreach ($products as $item) {
                $item['isInFavorite'] = isset($data[$item['id']]);
            }
        }

        return $products;
    }

    /**
     * Загружает списку товаров информацию о том используются ли комплектации и многомерные
     * комплектации без загрузки подробных сведений. Данный метод удобно использовать на странице со списком товаров
     *
     * @param array $products список товаров
     * @return array
     */
    public function addProductsMultiOffersInfo($products)
    {
        $products_id = [];
        if (!empty($products)){
             foreach ($products as $item) {
                $products_id[] = $item[$this->id_field];
            }
        }
        if (!$products_id) {
            return $products;
        }
                    
        $use_multioffers = OrmRequest::make()
            ->select('DISTINCT product_id')
            ->from(new Orm\MultiOfferLevel())
            ->whereIn('product_id', $products_id)
            ->exec()->fetchSelected('product_id', 'product_id');
        
        $use_offers = OrmRequest::make()
            ->select('product_id, COUNT(*) as cnt')
            ->from(new Orm\Offer())
            ->whereIn('product_id', $products_id)
            ->groupby('product_id')
            ->exec()->fetchSelected('product_id', 'cnt');
        
        foreach ($products as $item) {
            $item->setFastMarkMultiOffersUse( isset($use_multioffers[ $item['id'] ]) );
            $item->setFastMarkOffersUse( isset($use_offers[ $item['id'] ]) && $use_offers[ $item['id'] ]>1 );
        }
        return $products;
    }


    /**
     * Возвращает группы, в которых состоят товары (включая родителей групп)
     *
     * @param int[]|Product[] $products_id - массив товаров
     * @param bool $add_root - добавлять корневую категорию
     * @param bool $is_products - добавлять в запрос идентификаторы из переданных товаров
     * @return array|false
     */
    public function getProductsDirs($products_id, $add_root = false, $is_products = true)
    {
        if ($is_products) {
            $tmp = [];
            foreach ($products_id as $item) {
                if (gettype($item) == 'object') {
                    if (isset($item[$this->id_field])) {
                        $tmp[] = $item[$this->id_field];
                    }
                } else {
                    $tmp[] = $item;
                }
            }
            $products_id = $tmp;
        }

        if (empty($products_id)){
            return false;
        }

        $list = OrmRequest::make()
                ->from(new Xdir())
                ->whereIn('product_id', $products_id)
                ->exec();
        
        $dir_parents = OrmRequest::make()
            ->from(new Orm\Dir())
            ->exec()->fetchSelected('id', 'parent');

        $result = [];
        while ($row = $list->fetchRow()) {
            $dir_id = $row["dir_id"];
            while ($dir_id != 0) {
                if (!isset($result[$row["product_id"]][$dir_id]) && isset($dir_parents[$dir_id])) {
                    $result[$row["product_id"]][$dir_id] = $dir_id;
                    $dir_id = $dir_parents[$dir_id];
                } else {
                    break;
                }
            }
        }
        // если нужно - добавляем корневую категорию
        if ($add_root) {
            foreach ($result as $key=>$item) {
                $result[$key][0] = '0';
            }
        }
        return $result;
    }

    /**
     * Функция быстрого группового удаления товаров по их идентификаторам
     *
     * @param array $ids - массив id товаров
     * @param integer $dir - если >0, то будет удалено только из категории $dir
     * @return bool
     * @throws DbException
     * @throws EventException
     * @throws DbException
     * @throws EventException
     */
    function multiDelete($ids, $dir = 0)
    {
        //Проверяем права на запись для модуля
        if ($this->noWriteRights(DefaultModuleRights::RIGHT_DELETE)) return false;
        
        $event_result = \RS\Event\Manager::fire('orm.beforemultidelete.catalog-product', [
            'ids' => $ids,
            'api' => $this
        ]);
        
        if ($event_result->getEvent()->isStopped()){ //Если нужно оставить из стороннего модуля
            return false;
        }

        if (empty($ids)){ //Если не переданы идентификаотры
            return false;
        }

        //Удаляем связи
        $q = OrmRequest::make()
                ->delete()
                ->from(new Xdir())
                ->whereIn('product_id', $ids);

        if ($dir > 0) {
            $dir_api = new \Catalog\Model\Dirapi();
            $child_ids = $dir_api->getChildsId($dir);
            $q->whereIn('dir_id', $child_ids);
        }
        $q->exec();

        $del_ids = array_flip($ids);

        if ($dir > 0) {
            //Помечаем на удаление товар, на который нет ссылок
            $res = OrmRequest::make()
                    ->from(new Xdir())
                    ->whereIn("product_id", $ids)
                    ->exec();

            while ($row = $res->fetchRow()) {
                if (isset($del_ids[$row['product_id']])) {
                    unset($del_ids[$row['product_id']]);
                }
            }
        }

        if (!empty($del_ids)) {
            $del_ids = array_keys($del_ids);
            $del_ids_in = implode(',', \RS\Helper\Tools::arrayQuote($del_ids));
            //Определяем связанные фотографии
            $photo_api = new \Photo\Model\PhotoApi();

            $photo_ids = OrmRequest::make()
                            ->select('id')->from($photo_api->getElement())
                            ->where([
                                'type' => 'catalog'
                            ])
                            ->where("linkid IN ($del_ids_in)")
                            ->exec()->fetchSelected(null, 'id');

            $photo_api->multiDelete($photo_ids); //Удаляем связанные фото
            //Удаляем сами товары
            $product_table = $this->obj_instance->_getTable();
            OrmRequest::make()
                    ->delete()
                    ->from($this->obj_instance)
                    ->where("id IN($del_ids_in)")
                    ->exec();

            //Удаляем цены
            OrmRequest::make()
                    ->delete()
                    ->from(new Xcost())
                    ->where("product_id IN($del_ids_in)")
                    ->exec();

            //Удаляем товарные предложения (комплектации)
            OrmRequest::make()
                    ->delete()
                    ->from(new \Catalog\Model\Orm\Offer())
                    ->where("product_id IN($del_ids_in)")
                    ->exec();
                    
            //Удаляем уровни многомерных комплектаций
            OrmRequest::make()
                    ->delete()
                    ->from(new \Catalog\Model\Orm\MultiOfferLevel())
                    ->where("product_id IN($del_ids_in)")
                    ->exec();

            //Удаляем характеристики
            OrmRequest::make()->delete()
                ->from(new \Catalog\Model\Orm\Property\Link())
                ->where("product_id IN($del_ids_in)")
                ->exec();
                
            //Удаляем из поискового индекса
            OrmRequest::make()->delete()
                ->from(new \Search\Model\Orm\Index())
                ->where("entity_id IN($del_ids_in)")
                ->where([
                    'result_class' => get_class($this->obj_instance)
                ])
                ->exec();
            
            //Удаляем остатки на складах
            OrmRequest::make()->delete()
                ->from(new \Catalog\Model\Orm\Xstock())
                ->where("product_id IN($del_ids_in)")
                ->exec();
        }

        DirApi::updateCounts(); //Обновляем счетчики у категорий
        //Добавим событие на удалении
        \RS\Event\Manager::fire('orm.multidelete.catalog-product', [
            'ids' => $del_ids,
            'api' => $this
        ]);
        return true;
    }

    /**
     * Мульти обновление цен только у товаров
     *
     * @param array $ids - массив с id товаров цены которых надо изменить
     * @param array $excost - массив сведений об изменении цены
     * @param array $all_prices - массив сведений о прошлых ценах
     */
    private function multiUpdateProductPrices( $ids, $excost, $all_prices)
    {
        //Поменяем значения цен у товаров
        foreach ($ids as $id) {
            foreach($excost as $cost_id => $cdata) {
                //Если задано мульти редактирование накрутки цены
                if (isset($cdata['edit_multi'])) { 
                    
                    $product_before_cost = isset($all_prices[$cost_id][$id]) ? $all_prices[$cost_id][$id] : 
                        [
                            'cost_original_currency' => 0,
                            'cost_original_val' => 0
                        ];
                    
                    $cdata['cost_original_currency'] = $product_before_cost['cost_original_currency'];
                    if ($cdata['plus_type']) { //Если в еденицах
                        $delta = $cdata['plus_value'];
                    } else {
                        //Если задано в процентах
                        $delta = $product_before_cost['cost_original_val'] * ($cdata['plus_value'] / 100);
                    }
                    
                    $way = $cdata['way'] ? -1 : 1;
                    $cdata['cost_original_val'] = $product_before_cost['cost_original_val'] + ($way * $delta);
                    
                    $cdata['cost_original_val'] = \Catalog\Model\CostApi::roundCost($cdata['cost_original_val']);
                }

                $cost_item = new Xcost();
                $cost_item->fillData($cost_id, $id, $cdata);
                $cost_item->insert();
             }
        }
        
    }

    /**
     * Мульти обновление цен только у комплектаций
     *
     * @param array $ids - массив с id товаров цены которых надо изменить
     * @param array $excost - массив сведений об изменении цены
     * @param array $all_prices - массив сведений о прошлых ценах
     * @param boolean $update_price_round - флаг округлять ли цены - !устарел
     * @param integer $update_price_round_value - количество дробных цифр в округлённой цене (округляется вверх) - !устарел
     * @throws DbException
     * @throws OrmException
     */
    private function multiUpdateProductOffers( $ids, $excost, $all_prices, $update_price_round = false, $update_price_round_value = 0 )
    {
        //Поменяем значения цен у комплектаций, подгрузив значения pricedata у комплектации   
        $pricedata_offers = (array)OrmRequest::make()
            ->from(new \Catalog\Model\Orm\Offer())
            ->whereIn('product_id',$ids)
            ->where('sortn > 0')
            ->exec()
            ->fetchSelected('id');
        if (empty($pricedata_offers)){
            return;
        }
            
        //Получим валюты в системе
        $currencies = OrmRequest::make()
                ->from(new \Catalog\Model\Orm\Currency())
                ->orderby('`default` DESC')
                ->objects(null,'id');
                
        $currency_keys       = array_keys($currencies);
        $currency_default_id = $currency_keys[0]; //id валюты по умолчанию
        
        static 
            $prices; //Все сущесвующие цены в системе

        //Перебираем комплектации
        foreach ($pricedata_offers as $offer_id=>$offer_price_info){
            $product_id = $offer_price_info['product_id'];
            $pricedata  = (array)unserialize($offer_price_info['pricedata']);
            
            //Если установлен флаг "для всех типов цен" и знак установлен в равно, то мы снимем галочку, заново сформировав массив с ценами для этой комплектации
            if (isset($pricedata['oneprice']) && ($pricedata['oneprice']['znak']=="=") && $pricedata['oneprice']['original_value']>0){ 
                if ($prices === null){
                   $prices = OrmRequest::make()
                                ->from(new \Catalog\Model\Orm\Typecost())
                                ->where([
                                    'site_id' => \RS\Site\Manager::getSiteId(),
                                    'type' => 'manual'
                                ])
                                ->objects();
                }
                
                $pricedata['price'] = [];
                //Пересоберём массив цен
                foreach ($prices as $price){
                   $pricedata['price'][$price['id']] = [
                      'znak' => '=',  
                      'unit' => $pricedata['oneprice']['unit'],  
                      'original_value' => $pricedata['oneprice']['original_value'],  
                      'value' => $pricedata['oneprice']['value'],
                   ];
                }
                unset($pricedata['oneprice']);
            }else if (isset($pricedata['oneprice']) && ($pricedata['oneprice']['znak']=="+")){
                //Если выбрано "Для всех типов цен" у комплектации и знак "+", то пропустим
                continue;
            }else if (isset($pricedata['oneprice']) && ($pricedata['oneprice']['znak']=="=" && $pricedata['oneprice']['original_value']=="")){
                //Если выбрано "Для всех типов цен" у комплектации и знак "=", и значения пустые, то пропустим
                continue;
            }
            
            //Если у нас не образовался массив с ценами по какой-то причине, или он вообще не создавался никогда
            if (!isset($pricedata['price'])){ 
                continue;
            }
            
            foreach($excost as $cost_id => $cdata){
                
                //Если у нас у подсчитываемой цены у комплектации стоит валюты, не как валюта, а процент
                if (isset($pricedata['price'][$cost_id]['unit']) && ($pricedata['price'][$cost_id]['unit']=="%")){
                    continue;
                }
                
                //Если задано мульти редактирование накрутки цены
                if (isset($cdata['edit_multi']) && $cdata['edit_multi']) {  
                    //Если стоит галочка
                    $from_price = $cdata['from_price'];
                    //Если цены от которой считаем не существует у комплектации, добавим запись
                    if (!isset($pricedata['price'][$from_price]['znak'])){
                        $pricedata['price'][$from_price] = [
                            'znak' => "=",
                            'unit' => $all_prices[$cost_id][$product_id]['cost_original_currency'],
                            'cost_original_currency' => "",
                            'cost_original_val' => "",
                        ];
                    }
                    
                    //Если цены для которой считаем не существует
                    if (!isset($pricedata['price'][$cost_id]['znak'])){
                        $pricedata['price'][$cost_id] = [
                            'znak' => "=",
                            'unit' => $all_prices[$cost_id][$product_id]['cost_original_currency'],
                            'cost_original_currency' => "",
                            'cost_original_val' => "",
                        ];
                    }
                    
                     //Если цена существует
                    if(isset($pricedata['price'][$from_price]['original_value'])){
                        //Если цена задана и знак "=" и не процент от суммы
                        if (($pricedata['price'][$cost_id]['znak'] == "=") && ($pricedata['price'][$from_price]['unit'] != "%") && ($pricedata['price'][$from_price]['original_value'] > 0)){
                       
                            //Если цена от которой будем считать имеет знак + и имеет значение больше 0
                            if ($pricedata['price'][$from_price]['znak']=="+"){
                                $currency_id   = $all_prices[$cost_id][$product_id]['cost_original_currency'];
                                $original_cost = $all_prices[$cost_id][$product_id]['cost_original_val']; //Цена в валюте которую установили
                                //Прибавим плюсовые значения
                                if ($pricedata['price'][$from_price]['unit']=="%"){
                                    $original_cost = $original_cost + ($original_cost*($pricedata['price'][$from_price]['original_value']/100));
                                }else{
                                    $original_cost = $original_cost + $pricedata['price'][$from_price]['original_value'];
                                }
                            }else{
                                $currency_id   = $pricedata['price'][$from_price]['unit'];
                                $original_cost = $pricedata['price'][$from_price]['original_value']; //Цена в валюте которую установили
                            } 
                        

                            //Увеличим или уменьшим цену в зависимости от того, что выбрано
                            if ($cdata['plus_type']) { //Если в еденицах
                                $delta = $cdata['plus_value'];
                            } else {
                                //Если задано в процентах
                                $delta = $original_cost * ($cdata['plus_value']/100);
                            }
                       
                            $way = $cdata['way'] ? -1 : 1;
                            $original_cost = $original_cost + ($way * $delta); //Посчитываем цену
                            $cost = \Catalog\Model\CostApi::roundCost($original_cost); //Цена в валюте по умолчанию
                            //Если у нас валюта не по умолчанию, то подсчитаем значения по курсу для значения в валюте по умолчанию
                            if ($currency_id && $currency_id!=$currency_default_id){
                                $cost = $original_cost*$currencies[$currency_id]['ratio'];
                                $cost = \Catalog\Model\CostApi::roundCost($cost);
                            }
                       
                            $pricedata['price'][$cost_id] = [
                                'znak' => '=',
                                'unit' => $currency_id,
                                'original_value' => $original_cost,
                                'value' => $cost,
                            ];
                        }
                    }
                    //Если просто задано равное значение цены
                }else if (($pricedata['price'][$cost_id]['znak'] == "=") && ($pricedata['price'][$cost_id]['unit'] != "%")){
                
                    $currency_id   = $cdata['cost_original_currency'];
                    $original_cost = $cdata['cost_original_val']; //Цена в валюте которую установили
                    $cost = $original_cost;
                    if ($currency_id != $currency_default_id){
                       $cost = $original_cost * $currencies[$currency_id]['ratio'];
                       $cost = \Catalog\Model\CostApi::roundCost($cost);
                    }
                    $pricedata['price'][$cost_id] = [
                       'znak' => '=',
                       'unit' => $currency_id,
                       'original_value' => $original_cost,
                       'value' => $cost,
                    ];
                }
                
            }
            
            //Обновляем цены комплектации, уже подготовленным массивом
            OrmRequest::make()
                    ->update()
                    ->from(new \Catalog\Model\Orm\Offer())
                    ->set([
                       'pricedata' => serialize((array)$pricedata)
                    ])
                    ->where([
                        'id' => $offer_id,
                        'site_id' => \RS\Site\Manager::getSiteId()
                    ])
                    ->exec();
        } 
    }

    /**
     * Мульти обновление цены у товаров и комплектаций
     *
     * @param array $ids - массив id товаров
     * @param array $excost - массив сведений об именении цены или с полями изменения цен
     * @throws DbException
     * @throws EventException
     * @throws OrmException
     */
    function multiUpdateProductsAndOffersPrices($ids = [], array $excost )
    {
        $all_prices = []; //Массив с ценами, если поставлен флаг увеличения значения
        //Если необходимо мультиредактирование цен товаров и оно задано, то получим массив 
        $replace_price = [];
        foreach ($excost as $cost_id => $cdata) {
           //Получим массив с id цен, который будет заменён  
           if (trim($cdata['cost_original_val']) !== "" ) $replace_price[$cost_id] = $cost_id;
           if (isset($cdata['edit_multi']) && trim($cdata['plus_value']) !== "") {
               
             //Получим в массив старые цены для всех найденных объектов
             $all_prices[$cost_id] = OrmRequest::make()
                ->from(new Xcost())
                ->where(['cost_id'=>$cdata['from_price']])
                ->whereIn('product_id', $ids)->exec()->fetchSelected('product_id');
             
             $replace_price[$cost_id] = $cost_id; //Добавим на замену   
           } 
           
           //Исключим из массива цены, которые менять не надо
           if (!isset($replace_price[$cost_id])){
              unset($excost[$cost_id]); 
           }
        }

        if (!empty($replace_price)){ //Какие точно удалять, чтобы заменить
            //Удалим старые цены
            OrmRequest::make()->delete()
                ->from(new Xcost())
                ->whereIn('cost_id', $replace_price)  
                ->whereIn('product_id', $ids)
                ->exec();  
        }
        
         
        //Обновляем цены товара и его комплектаций
        if (!empty($excost)) {
            //Cначала цены товара
            $this->multiUpdateProductPrices($ids, $excost, $all_prices);
            //Обновим для товара цены комплектаций
            $this->multiUpdateProductOffers($ids, $excost, $all_prices);
        }
    }

    /**
     * Удаляет дубликаты характеристик в БД
     *
     * @return void
     * @throws DbException
     * @throws RSException
     */
    function deleteDuplicateProperties()
    {
        $sufix = mb_substr(sha1(time()), 0, 8);
        $temp_table = \Setup::$DB_TABLE_PREFIX.'prop_temp_'.$sufix;
        $junk_table = \Setup::$DB_TABLE_PREFIX.'junk_temp_'.$sufix;
        
        $prop_link = new \Catalog\Model\Orm\Property\Link();
        //Получим запрос на создание таблиции подменим имя
        $table_info = $prop_link->_getTableArray();
        $create_table_query = str_replace($table_info[1], $temp_table, \RS\Db\Adapter::sqlExec('SHOW CREATE TABLE '.$prop_link->_getTable())->getOneField('Create Table', ''));    
        
        \RS\Db\Adapter::sqlExec($create_table_query.' SELECT DISTINCT * FROM '.$prop_link->_getTable());
        \RS\Db\Adapter::sqlExec('ALTER TABLE '.$prop_link->_getTable().' RENAME '.$junk_table);
        \RS\Db\Adapter::sqlExec('ALTER TABLE `'.$temp_table.'` RENAME '.$prop_link->_getTable());
        \RS\Db\Adapter::sqlExec('DROP TABLE `'.$junk_table.'`');
    }

    /**
     * Функция быстрого группового редактирования товаров
     *
     * @param array $data - массив данных для обновления
     * @param array $ids - идентификаторы товаров на обновление
     * @return int
     * @throws DbException
     * @throws EventException
     * @throws OrmException
     * @throws RSException
     */
    function multiUpdate(array $data, $ids = [])
    {  
        //Добавим событие перед обновлением
        $event_result = \RS\Event\Manager::fire('orm.beforemultiupdate.catalog-product', [
            'data' => $data, 
            'ids' => $ids, 
            'api' => $this,
        ]);
        if ($event_result->getEvent()->isStopped()) return false;
        list($data, $ids) = $event_result->extract();
        
        $sql = [];
        $need_calculate_product_num = false;
        
        $spec_dirs = $this->obj_instance->getSpecDirs(true);

        $xdir       = new Xdir();
        $xdir_table = $xdir->_getTable();

        $merged_xdir  = [];
        $need_reindex = array_intersect(['title', '_property_', 'barcode', 'brand_id',
                                              'short_description', 'meta_keywords'], array_keys($data)) != false;
        
        // Сбрасываем хэши импорта
        OrmRequest::make()
            ->update(new \Catalog\Model\Orm\Product())
            ->set(['import_hash' => null])
            ->whereIn('id', $ids)
            ->where(['site_id' => \RS\Site\Manager::getSiteId()])
            ->exec();
        
        //Загружаем фотографии к товарам
        if (isset($data['simage'])) {
            $photo_api = new \Photo\Model\PhotoApi();
            
            if (!empty($data['simage']['delete_all_photos'])) {
                $q = $photo_api->queryObj();
                $q->where(['type' => Orm\Product::IMAGES_TYPE])
                  ->whereIn('linkid', $ids)
                  ->select = 'id';
                    
                $photo_ids = $q->exec()->fetchSelected(null, 'id');
                $photo_api->multiDelete($photo_ids);
            }
            
            if (isset($_FILES['simagefile'])) {
                $photo_api->addFromUrl($_FILES['simagefile']['tmp_name'], 'catalog', $ids);
            }
            unset($data['simage']);
        }

        //Обновляем спец. категории
        if (isset($data['xspec']) && count($spec_dirs) > 0) {
            $merged_xdir = $data['xspec'];
            
            if (!empty($data['xspec']['delbefore'])) {
                \RS\Db\Adapter::sqlExec("DELETE FROM ".$xdir_table." WHERE product_id IN (" . implode(',', $ids) . ") 
                                            AND dir_id IN (" . implode(',', array_keys($spec_dirs)) . ")");
            }
            unset($data['xspec']['delbefore']);

            if (!empty($data['xspec'])) {
                $xspec = [];
                $dirs_with_sortn = [];
                foreach ($ids as $id)
                    foreach ($data['xspec'] as $dir) {
                        $xspec[] = "('".$id."', '".$dir."')";
                    }
                $sql[] = "INSERT IGNORE INTO ".$xdir_table."(product_id, dir_id) VALUES" . implode(',', $xspec);
            }
            unset($data['xspec']);
        }

        //Обновляем категории     
        if (isset($data['xdir'])) {
            if (!empty($data['xdir'])) {
                //Если удалить связь со старыми категориями
                if (!isset($data['xdir']['notdelbefore']) || !$data['xdir']['notdelbefore']) {
                    $add_dir_where = (count($spec_dirs) > 0) ? ' AND dir_id NOT IN (' . implode(',', array_keys($spec_dirs)) . ')' : '';
                    \RS\Db\Adapter::sqlExec("DELETE FROM ".$xdir_table." WHERE product_id IN (" . implode(',', $ids) . ") " . $add_dir_where);
                } else {
                    unset($data['xdir']['notdelbefore']); 
                }
                
                $merged_xdir = array_merge($merged_xdir, $data['xdir']);

                $dirs_with_sortn = [];
                $xdirs = [];
                foreach ($ids as $id) {
                   foreach ($data['xdir'] as $dir) {
                      $xdirs[] = "('".$id."', '".$dir."')";
                   } 
                } 
                $sql[] = "INSERT IGNORE INTO ".$xdir_table."(product_id, dir_id) VALUES" . implode(',', $xdirs);
            }
            unset($data['xdir']);
        }
        
        //Обновляем характеристики
        if (isset($data['_property_'])){
            
           $save_prop_links = false; 
           //Если пользователь выбрал удалить все существующие характеристики у выбранных товаров 
           if (isset($data['_property_'][0])){
               $q = OrmRequest::make()->delete()
                        ->from(new \Catalog\Model\Orm\Property\Link())
                        ->where([
                            'site_id' => \RS\Site\Manager::getSiteId()
                        ])
                        ->whereIn('product_id', $ids)
                        ->exec();  
               unset($data['_property_'][0]);
           }else{
               //Удалим предыдущие значения этих характеристик
               foreach($data['_property_'] as $k=>$property){
                    if (!isset($property['savelink'])){ //Если не нужно сохранять связи с ранее установлеными данными
                        $q = OrmRequest::make()->delete()
                            ->from(new \Catalog\Model\Orm\Property\Link())
                            ->where([
                                'prop_id' => $property['id'],
                                'site_id' => $property['site_id']
                            ])
                            ->whereIn('product_id', $ids)
                            ->exec();    
                    }else{
                        $save_prop_links = true;
                    }
                    //Вычеркнем те характеристики у которых флаг удалить отмечен
                    if (isset($property['delit']) && !isset($property['savelink'])){
                        unset($data['_property_'][$k]);
                    }  
               }
           } 
           
           if (!empty($data['_property_'])){
               //Заполним выбранные характеристики для товаров
               foreach ($ids as $id) {
                   foreach($data['_property_'] as $property){
                       $prop_item = new \Catalog\Model\Orm\Property\Link();
                       if (!empty($property['check'])) {
                          foreach($property['check'] as $value){ //Если список значений
                             $prop_item->fillData($id, $value, $property);
                             unset($prop_item['val_str']);
                             $prop_item->insert();
                          }
                       } else { //Если всё остальное
                          $prop_item->fillData($id, $property['value'], $property);
                          $prop_item->insert();
                       }
                   } 
               }
               //Если флаг сохранения ранее установленных значений был установлен
               //Исключим возможные дубликаты
               if ($save_prop_links){ 
                   $this->deleteDuplicateProperties();
               }
           }
           
           unset($data['_property_']);
        }
        
        
        //Обновляем комплектации и многомерные комплектации
        if ( isset($data['_offers_']) ) {
            
            // Сбрасываем хэши импорта комплектаций
            OrmRequest::make()
                ->update(new \Catalog\Model\Orm\Offer())
                ->set(['import_hash' => null])
                ->whereIn('product_id', $ids)
                ->where(['site_id' => \RS\Site\Manager::getSiteId()])
                ->exec();
            
            //Удаляем комплектации, если есть флаг
            if ( isset($data['_offers_']['delete']) ) {
               $offer_api = new \Catalog\Model\OfferApi(); 
               $offer_api->deleteOffersByProductId($ids);
            }
            
            $moffer_api        = new \Catalog\Model\MultiOfferLevelApi();
            $levels_by_product = [];//Массив с уровнями многомерных комплектаций соотвественно товару
            
            
            //Записываем уровни многомерных комплектаций      
            if ( isset($data['_offers_']['levels']) ) {
                foreach ($ids as $id) {     
                   //Оставим только те уровни которые необходимы товару                   
                   $levels_by_product[$id] = $moffer_api->prepareRightMOLevelsToProduct($id, $data['_offers_']['levels']);
                   
                   if (isset($data['_offers_']['is_photo']) && ($data['_offers_']['is_photo']>0) && !empty($levels_by_product[$id]) && isset($levels_by_product[$id][$data['_offers_']['is_photo']])){ //Флаг "С фото" У многомерных комплектаций
                       $levels_by_product[$id][$data['_offers_']['is_photo']]['is_photo'] = 1;  
                   }
                   
                   //Сохранение уровней мн. комплектаций
                   $moffer_api->saveMultiOfferLevels($id, $levels_by_product[$id]); 
                } 
            }else{
                foreach ($ids as $id) {  
                    $moffer_api->clearMultiOfferLevelsByProductId($id);
                }
            }
            
            //Записываем сами комплектации из мн. уровней комплектаций
            if (isset($data['_offers_']['create_autooffers']) && isset($data['_offers_']['levels'])){
                foreach ($ids as $id) { 
                    if ($moffer_api->createOffersFromLevels($id, $levels_by_product[$id]) === false) {
                        $this->addError($moffer_api->getErrorsStr());
                        $moffer_api->cleanErrors();
                    }
                }
            }
            
            $need_calculate_product_num = true;
            unset($data['_offers_']);
        }
        
        
        //Обновляем цены
        if (isset($data['excost'])) {
            $this->multiUpdateProductsAndOffersPrices($ids, $data['excost']);
            unset($data['excost']);
        }
        
        
        
        //Обновляем рекоммендуемые товары
        if (isset($data['recommended_arr']) && isset($data['recommended_arr']['product'])){
           $recomended =  $data['recommended_arr']['product'] == [0=>'0'] ? serialize([]) : serialize($data['recommended_arr']);
           OrmRequest::make()
                    ->from(new \Catalog\Model\Orm\Product())
                    ->set([
                       'recommended' => $recomended
                    ])
                    ->whereIn('id', $ids)
                    ->where([
                        'site_id' => \RS\Site\Manager::getSiteId()
                    ])
                    ->update()
                    ->exec();
        }
        unset($data['recommended_arr']);
        
        //Обновляем сопутсвующие товары
        if (isset($data['concomitant_arr']) && isset($data['concomitant_arr']['product'])){
            $concomitant = $data['concomitant_arr']['product'] == [0=>'0'] ? serialize([]) : serialize($data['concomitant_arr']);
            OrmRequest::make()
                    ->from(new \Catalog\Model\Orm\Product())
                    ->set([
                       'concomitant' => $concomitant
                    ])
                    ->whereIn('id', $ids)
                    ->where([
                        'site_id' => \RS\Site\Manager::getSiteId()
                    ])
                    ->update()
                    ->exec(); 
        }
        unset($data['concomitant_arr']);
        
        //Обновляем остатки для нулевой комплектации
        if (isset($data['num'])){
            
           //Получим id нулевых комплектаций 
           $offers = OrmRequest::make()
                ->from(new \Catalog\Model\Orm\Offer())
                ->where([
                    'site_id' => \RS\Site\Manager::getSiteId(),
                    'sortn' => 0
                ])
                ->whereIn('product_id',$ids)
                ->exec()
                ->fetchSelected('product_id','id'); 
            
           $offer_sql = [];
           $stockClass = new \Catalog\Model\Orm\Xstock();           
           foreach($data['num'] as $warehouse_id=>$num){
              if ($num !== '') {
                  $stock = (int)$num;
                  
                  //Удалим остатки нулевой комплектации, если задано значение
                  if ($offers) {
                      OrmRequest::make()
                            ->from($stockClass) 
                            ->whereIn('offer_id', $offers)
                            ->where([
                              'warehouse_id' => $warehouse_id
                            ])
                            ->delete()
                            ->exec();
                  }
                        
                  $num_rows = [];
                  //Добавим остатки для нулевых комплектаций для каждого склада
                  foreach($ids as $id){
                      if (!isset($offers[$id])) {
                          $offer = new \Catalog\Model\Orm\Offer();
                          $offer['product_id'] = $id;
                          $offer['sortn'] = 0;
                          $offer->insert();
                          $offers[$id] = $offer['id'];
                      }
                      
                      $num_rows[] = "('". (int)$id."','".(int)$offers[$id]."','".(int)$warehouse_id."','".(int)$stock."')"; 
                  }
                  
                  $offer_sql[] = "INSERT INTO ".$stockClass->_getTable()." (
                    product_id,
                    offer_id,
                    warehouse_id,
                    stock
                  ) VALUES ".implode(",", $num_rows); 
              } 
           } 
           
           foreach ($offer_sql as $query) {
                \RS\Db\Adapter::sqlExec($query);
           }
           
           //Пересчитываем общий остаток комплектаций
           $offer = new \Catalog\Model\Orm\Offer();
           OrmRequest::make()
                ->update()
                ->from($offer, 'O')
                ->set('O.num = (SELECT SUM(stock) FROM '.$stockClass->_getTable().' S WHERE S.offer_id = O.id)')
                ->whereIn('O.product_id', $ids)
                ->exec();
                
           $need_calculate_product_num = true;
            
           unset($data['num']); 
        }
        
        if ($need_calculate_product_num) {
           //Пересчитываем общий остаток товара
           $sub_query = OrmRequest::make()
                ->select('SUM(num)')
                ->from(new Orm\Offer(), 'O')
                ->where('O.product_id = P.id');
         
           OrmRequest::make()
                ->update()
                ->from(new \Catalog\Model\Orm\Product, 'P')
                ->set('P.num = ('.$sub_query.')')
                ->whereIn('P.id', $ids)
                ->exec();
        }
        
        foreach ($sql as $query) {
            \RS\Db\Adapter::sqlExec($query);
        }
        
        //Обновляем товары
        $ret = parent::multiUpdate($data, $ids);
         
        //Обновляем кэширующее поле public в таблице ..._x_dir
        if (!empty($merged_xdir) || isset($data['public'])) {
            Dirapi::updateCounts();
        }
        
        //Если необходимо переиндексировать товары
        if ($need_reindex) {
            $this->updateProductSearchIndex($ids);
        }
       
        //Добавим событие на обновлении
        \RS\Event\Manager::fire('orm.multiupdate.catalog-product', [
            'ids' => $ids,
            'api' => $this
        ]);
        return $ret;
    }

    /**
     * Обновляет поисковый индекс у товаров
     *
     * @param array $ids - список id товаров
     * @return bool
     */
    function updateProductSearchIndex($ids)
    {
        if (empty($ids)) return false;
        
        $i = 0;
        $page_size = 100;
        
        $q = OrmRequest::make()
            ->from($this->obj_instance)
            ->whereIn('id', $ids)
            ->limit($page_size);
        
        while($rows = $q->offset($i)->objects()) {
            foreach($rows as $product) {
                /** @var \Catalog\Model\Orm\Product $product */
                $product->updateSearchIndex();
            }
            $i = $i + $page_size;
        }
        return true;
    }

    /**
     * Возвращает именно товары по результатам поиска
     *
     * @param array $list_of_results - массив результатов поиска
     * @return array
     */
    function getBySearchResult($list_of_results)
    {
        $ids = [];
        foreach ($list_of_results as $result) {
            $ids[] = $result['entity_id'];
        }
        if (empty($ids)){
            return [];
        }

        $this->setFilter('id', $ids, 'in');
        $this->setOrder('FIELD(id,' . implode(',', $ids) . ')');
        $products = $this->getList();
        return $products;
    }

    /**
     * Устанавливает фильтры, которые следуют из параметров конфигурации модуля "Каталог"
     *
     * @return OrmRequest
     */
    function filterRequest()
    {
        $config = ConfigLoader::byModule($this);

        $q = OrmRequest::make()
                ->join($this->obj_instance, 'P.id = A.entity_id', 'P')
                ->where('public = 1');
        if ($config['hide_unobtainable_goods'] == 'Y') {
            $q->where('num > 0');
        }
        return $q;
    }

    /**
    * Устанавливает фильтры, от компонента html_filter
    */
    public function addFilterControl(\RS\Html\Filter\Control $filter_control)
    {
        parent::addFilterControl($filter_control);
    }

    /**
     * Возвращает список категорий в которых состоят товары, возвращаемые функцией getList
     *
     * @return Dir[]
     */
    function getDirList()
    {
        $q = clone $this->queryObj();
        $q->select = 'DL.*, COUNT(*) as itemcount';
        $q->join(new Dir(), 'A.maindir = DL.id', 'DL')
                ->where('DL.public = 1')
                ->orderby(null)
                ->groupby('DL.id');
        return $q->objects(new Dir());
    }

    /**
     * Возвращает список товаров с заданным отклонением цены
     * @param $product
     * @param $delta
     * @param $page_size
     * @param bool $only_in_stock
     * @return \RS\Orm\AbstractObject[]
     */
    function getSameByCostProducts($product, $delta, $page_size, $only_in_stock = false)
    {
        $cost = $product->getCost(null, null, false);
        
        $this->costFilter([
            'from' => $cost - $cost * ($delta / 100),
            'to'   => $cost + $cost * ($delta / 100)
        ]);
        
        $this->setFilter('dir', $product->maindir);
        $this->setFilter('id', $product->id, '!=');

        if(ConfigLoader::byModule('catalog')->hide_unobtainable_goods == 'Y' || $only_in_stock){
            $this->setFilter('num', 0 , '>');
        }
        return $this->getList(1, $page_size);
    }

    /**
     * Получает доступные значения брендов для категорий
     *
     * @param string $cache_key  - ключ для кэша
     * @param boolean $cache     - флаг включения кэша
     *
     * @return Brand[]|false
     */
    function getAllowableBrandsValues($cache_key, $cache = true){
        if ($cache) {
            return \RS\Cache\Manager::obj()
                ->tags(CACHE_TAG_UPDATE_CATEGORY)
                ->request([$this, 'getAllowableBrandsValues'], $cache_key, false);
        } else {
            $q = clone $this->queryObj();
            $q->select = 'Brand.*';
            $q->join(new \Catalog\Model\Orm\Brand(),'A.brand_id=Brand.id','Brand')
                ->groupby('A.brand_id')
                ->where(['A.public' => 1])
                ->orderby('Brand.title ASC');
            
            $config = ConfigLoader::byModule($this);
            if ($config['hide_unobtainable_goods'] == 'Y') {
                $q->where('A.num>0'); //Не учитываем товары с нулевым остатком
            }
                
            return $q->objects('\Catalog\Model\Orm\Brand', 'id');
       }
    }

    /**
     * Возвращает все возможные значения свойств для текущей выборки товаров,
     * - диапазоны значений и шаг для характеристик типа int
     * - массив возможных значений для характеристик типа list
     * - массив возможных значений для характеристик типа bool
     *
     * @param integer $dir_id id текущей категории товаров
     * @param mixed $cache_key - id кэша
     * @param bool $cache - Если true, то будет использоваться кэширование
     * @return array
     * @throws DbException
     * @throws EventException
     * @throws OrmException
     * @throws RSException
     */
    function getAllowablePropertyValues($dir_id, $cache_key, $cache = true)
    {
        if ($cache) {
            return \RS\Cache\Manager::obj()
                ->tags(CACHE_TAG_UPDATE_CATEGORY)
                ->request([$this, 'getAllowablePropertyValues'], $dir_id, $cache_key, false);
        } else {
            //Получаем фильтрующие характеристики для текущего каталога
            $result = [];
            
            $properties = [];
            $prop_api = new \Catalog\Model\Propertyapi();
            foreach($prop_api->getGroupProperty($dir_id, true, true) as $group) {
                $properties += $group['properties'];
            }
            $public_prop_ids = array_keys($properties);
            
            //Запрашиваем возможные значения
            if ($public_prop_ids) {
                $res = $this->getSelectAllowableValuesQuery($public_prop_ids)->exec();
                
                $min_max = [];
                $values = [];
                $list_values_id = [];
                
                while($row = $res->fetchRow()) {
                    $prop_id = $row['prop_id'];
                    if (isset($properties[$prop_id])) {
                        $value = $row[ $properties[$prop_id]->getValueLinkField() ];
                        if ($value === '' || $value === null) continue;
                        
                        switch($properties[$prop_id]['type']) {
                            case 'int': {
                                if (!isset($min_max[$prop_id])) {
                                    $min_max[$prop_id] = [
                                        'interval_from' => $value,
                                        'interval_to' => $value,
                                        'step' => 1
                                    ];
                                } else {
                                    if ($value < $min_max[$prop_id]['interval_from']) {
                                        $min_max[$prop_id]['interval_from'] = $value;
                                    }
                                    if ($value > $min_max[$prop_id]['interval_to']) {
                                        $min_max[$prop_id]['interval_to'] = $value;
                                    }
                                }
                                if ($this->getDecimal($min_max[$prop_id]['step'])< $this->getDecimal($value)) {
                                    $min_max[$prop_id]['step'] = 1 / ('1'.str_repeat('0', $this->getDecimal($value)));
                                }
                                break;
                            }
                            
                            default: {
                                if ($row['val_list_id'] > 0 && $properties[$prop_id]->isListType()) {
                                    //Собираем ID значений характеристик, чтобы заполнить их далее
                                    $list_values_id[] = $row['val_list_id']; 
                                } else {
                                    $values[$prop_id]['allowed_values'][$value] = $value;
                                }
                            }
                        }
                    }
                }
                
                $values = $this->appendListValues($values, $list_values_id);
                $result = $min_max + $values;
            }
            return $result;
        }
    }

    /**
     * Загружает значения для списковых характеристик
     *
     * @param array $values - значения характеристик установленных ранее
     * @param array $list_values_id - список идентификаторов значений характеристик для добавления к существующим
     * @return array
     * @throws OrmException
     */
    private function appendListValues($values, $list_values_id)
    {
        if ($list_values_id) {
            $result = [];
            //Загружаем отсортированные объекты значений
            $item_values = OrmRequest::make()
                ->from(new Orm\Property\ItemValue())
                ->whereIn('id', $list_values_id)
                ->orderby('prop_id, sortn')
                ->objects();
            
            foreach($item_values as $item_value) {
                $result[$item_value['prop_id']]['allowed_values'][$item_value['id']] = $item_value['value']; //Для совместимости с предыдущими версиями
                $result[$item_value['prop_id']]['allowed_values_objects'][$item_value['id']] = $item_value;
            }
            $values = $result + $values;
        }
        return $values;
    }

    /**
     * Возвращает объект запроса на выборку возможных значений
     *
     * @param $public_prop_ids
     * @return Request
     * @throws RSException
     */
    private function getSelectAllowableValuesQuery($public_prop_ids)
    {
        $q = clone $this->queryObj();
        $q->orderby(false);
        $q->select = 'ALP.prop_id, ALP.val_str, ALP.val_int, ALP.val_list_id';
        $q->join(new \Catalog\Model\Orm\Property\Link(), "ALP.product_id = A.id", "ALP")
            ->groupby(null)
            ->where('A.public = 1')
            ->whereIn('ALP.prop_id', $public_prop_ids);
            
        $config = ConfigLoader::byModule($this);
        
        if ($config->link_property_to_offer_amount) {
            $q->where('ALP.available = 1');
        }
            
        if ($config['hide_unobtainable_goods'] == 'Y') {
            $q->where('A.num>0'); //Не учитываем товары с нулевым остатком
        }
        return $q;
    }
    
    /**
    * Возвращает дробную часть числа
    * 
    * @param float $float - число с плавающей точкой
    * @return integer
    */
    private function getDecimal($float)
    {
        if (!$dot_pos = strpos($float, '.')) {
            return 0;
        }
        return strlen($float)-$dot_pos-1;
    }

    /**
     * Добавляет символьные идентификаторы товарам, у которых они не установлены
     *
     * @param integer $count_def - счетчик по-умолчанию
     * @return array|int
     * @throws DbException
     * @throws RSException
     */
    function addTranslitAliases($count_def = 0)
    {
        $start_time = time();
        $max_exec_time = ConfigLoader::byModule('main')->csv_timeout;
        $url = HttpRequest::commonInstance();

        $count = $url->request('count',TYPE_INTEGER, $count_def);
        $this->queryObj()
            ->where("(alias IS NULL OR alias='')");

        $res = $this->getListAsResource();

        if($res) {
            while ($row = $res->fetchRow()) {
                $count++;
                $product = new Orm\Product();
                $product->getFromArray($row);
                $product->setFlag(Product::FLAG_DONT_UPDATE_DIR_COUNTER);
                $product->setFlag(Product::FLAG_DONT_UPDATE_SEARCH_INDEX);
                $i = 0;
                $ok = false;
                while (!$ok && $i < 15) {
                    $product[$this->alias_field] = \RS\Helper\Transliteration::str2url(Tools::unEntityString($product['title'])) . (($i > 0) ? "-$i" : '');
                    $ok = $product->update();
                    $i++;
                }
                if(((time() - $start_time) >= ($max_exec_time - 2))) {
                    return [true, 'count' => $count];
                }
            }

        }
        return $count;
    }
    
    /**
    * Получает риски для цен диапозона цен
    * 
    * @return string
    */
    function getHeterogeneity($min, $max)
    {
           $max = floatval($max); 
           $min = floatval($min); 
           //Проверим возможно ли это
           if ($max==$min) return "";
           $delta = $max-$min;
           $d25 = ceil($min + (($delta/100)*25)); 
           $d50 = ceil($min + (($delta/100)*50)); 
           $d75 = ceil($min + (($delta/100)*75)); 
           return '"25/'.$d25.'","50/'.$d50.'","75/'.$d75.'"';
    }

    /**
     * Перемещает элемент from на место элемента to. Если flag = 'up', то до элемента to, иначе после
     *
     * @param int $dir_id - id категории в которой происходит перемещение
     * @param int $from - id элемента, который переносится
     * @param int $to - id ближайшего элемента, возле которого должен располагаться элемент
     * @param string $flag - up или down - флаг выше или ниже элемента $to должен располагаться элемент $from
     *
     * @return bool
     * @throws DbException
     * @throws OrmException
     */
    function moveProduct($dir_id, $from, $to, $flag)
    {
        if ($this->noWriteRights()) return false;
        
        //Только если указана директория
        if (!$dir_id) return false;
        
        $xdir = new \Catalog\Model\Orm\Xdir();
        
        $is_up = $flag == 'up';
        $this->sort_field = 'sortn';
        
        $from_obj = OrmRequest::make()
                        ->from($this->obj_instance, 'P')
                        ->join($xdir, 'X.product_id=P.id AND X.dir_id='.$dir_id, 'X')
                        ->where(['id' => $from])
                        ->object();
                        
        $to_obj = OrmRequest::make()
                        ->from($this->obj_instance, 'P')
                        ->join($xdir, 'X.product_id=P.id AND X.dir_id='.$dir_id, 'X')
                        ->where(['id' => $to])
                        ->object();
        
        $from_sort = $from_obj[$this->sort_field];
        $to_sort   = $to_obj[$this->sort_field];
        
        $r = '=';
        
        if ((!$is_up && $from_sort > $to_sort) || ($is_up && $from_sort < $to_sort)) {
            $r = ''; $is_up = !$is_up;
        }
        
        if ( $from_sort >= $to_sort) {
            $filter = $this->sort_field." >".$r." '".$to_sort."' AND ".$this->sort_field." <= '".$from_sort."'"; 
        } else {
            $filter = $this->sort_field." >= '".$from_sort."' AND ".$this->sort_field." <".$r." '".$to_sort."'";
        }     
        
        $res = OrmRequest::make()   
            ->from($xdir)
            ->where([
                'dir_id' => $dir_id
            ])
            ->where($filter)
            ->orderby($this->sort_field);
        $res = $res->exec();
            
        if ($res->rowCount() < 2) return true;
        
        $list = $res->fetchAll();
        
        if ($is_up) $list = $this->moveArrayUp($list); 
            else $list = $this->moveArrayDown($list); 
        
        foreach ($list as $newValues)
        {
            OrmRequest::make()
                ->update($xdir)
                ->set([$this->sort_field => $newValues[$this->sort_field]])
                ->where([
                    'dir_id' => $newValues['dir_id'],
                    'product_id' => $newValues['product_id'],
                ])
                ->exec();
        }
        return true;
    }


    /**
     * Перемещает объект товара в сортировке в самый верх для заданной директории
     *
     * @param integer $dir_id - id директории
     * @param integer $product_id - id товара
     * @param string $flag - up или down - флаг выше или ниже элемента $to должен располагаться элемент $from
     * @return bool
     * @throws DbException
     * @throws OrmException
     */
    function moveProductInDir($dir_id, $product_id, $flag)
    {
        //Найдем товар который находится в самом начале или в конце, и если это не тот же самый товар, то переместим его.
        $q = OrmRequest::make()
                    ->from(new \Catalog\Model\Orm\Xdir())
                    ->where([
                        'dir_id' => $dir_id,
                    ])
                    ->limit(1);
                    
        if ($flag == 'up'){
            $q->orderby('sortn ASC'); 
        }else{
            $q->orderby('sortn DESC');
        }
                    
        $xdir = $q->exec()->fetchRow();    
                    
        if ($xdir && ($xdir['product_id'] != $product_id)){
            return $this->moveProduct($dir_id, $product_id, $xdir['product_id'], $flag); 
        }
        return true;
    }

    /**
     * Возвращает информацию о минимальной и максимальной цене товаров,
     * для текущих условий выборки
     *
     * @return array
     * @throws DbException
     */
    function getMinMaxProductsCost()
    {
        $minmax_query = clone $this->queryObj();
        
        $cost_api = \Catalog\Model\CostApi::getInstance();
        
        $current_cost_type = $cost_api->getUserCost();
        $manual_cost_type = $cost_api->getManualType($current_cost_type);
        
        $minmax_query->select = 'max(XC.cost_val) interval_to, min(XC.cost_val) interval_from';
        //Если таблица с цена ещё не добавлена
        if (!$minmax_query->issetTable(new \Catalog\Model\Orm\Xcost())){
            $minmax_query
                ->leftjoin(new \Catalog\Model\Orm\Xcost(), "A.id = XC.product_id AND XC.cost_id='{$manual_cost_type}'", 'XC');
        }
        $minmax_query->orderby(null)
                     ->limit(null);
        
        $money_array = $minmax_query->exec()->fetchRow() ?: ['interval_from' => 0, 'interval_to' => 0];
        
        $current_cost = $cost_api->getCostById($current_cost_type);
        
        if ($current_cost['type'] == 'auto') {
            $money_array['interval_from'] = $cost_api->calculateAutoCost($money_array['interval_from'], $current_cost);
            $money_array['interval_to'] = $cost_api->calculateAutoCost($money_array['interval_to'], $current_cost);
        }

        //Убираем нулевые копейки
        $money_array['interval_from'] = str_replace('.00', '', $money_array['interval_from']);
        $money_array['interval_to'] = str_replace('.00', '', $money_array['interval_to']);

        return $money_array;
    }

    /**
     * Возвращает номер комплектации по значениям многомерной комплектации
     *
     * @param integer $product_id - id товара
     * @param array $multioffers_values - массив характеристик многомерной комплектации (title => value)
     * @return int
     */
    static public function getOfferByMultiofferValues($product_id, $multioffers_values)
    {
        $offers = OrmRequest::make()
            ->select('id, propsdata')
            ->from(new \Catalog\Model\Orm\Offer())
            ->where(['product_id' => $product_id,])
            ->exec()->fetchAll();

        foreach ($offers as $offer) {
            $offer['propsdata_arr'] = (!empty($offer['propsdata'])) ? unserialize($offer['propsdata']) : [];
            $found = 0;
            foreach ($offer['propsdata_arr'] as $key => $value) {
                foreach ($multioffers_values as $item) {
                    if ($item['title'] == $key && $item['value'] == $value) {
                        $found++;
                        break;
                    }
                }
            }
            if (($found == count($offer['propsdata_arr'])) and ($found == count($multioffers_values))) {
                return $offer['id'];
            }
        }
        $product = new Product($product_id);
        $main_offer = $product->getMainOffer();

        return $main_offer['id'];
    }

    /**
     * Возвращает общее количество элементов, согласно условию.
     *
     * @return integer
     */
    function getListCount()
    {
        $q = clone $this->queryObj();

        return $q->select('COUNT(DISTINCT '.$this->defAlias().'.'.$this->getIdField().') as cnt')
            ->limit(null)
            ->orderby(null)
            ->exec()
            ->getOneField('cnt', 0);
    }

    /**
     * Возвращает установленные фильтры в структурированном виде
     *
     * @return array
     */
    function getSelectedFiltersAsString($base_filters, $brands, $prop_filters)
    {
        $parts = array_merge($this->getBaseFiltersParts($base_filters, $brands),
            $this->getPropertyFiltersParts($prop_filters));

        return $parts;
    }

    /**
     * Возвращает данные по базовым фильтрам
     *
     * @param array $base_filters структура с выбранными фильтрами
     * @param array $brands Ассоциативный массив брендов
     * @return array
     */
    protected function getBaseFiltersParts($base_filters, $brands)
    {
        $parts = [];
        $part  = [];
        //Добавим информацию по стандартным фильтрам
        foreach($base_filters as $key => $values) {
            if ($values != "") {
                switch ($key) {
                    case 'brand':
                        $brand_titles = [];
                        foreach ($values as $value) {
                            if (isset($brands[$value])) {
                                $brand_titles[] = $brands[$value]['title'];
                            }
                        }
                        $part = [
                            'title' => t('Бренд:') . implode(', ', $brand_titles)
                        ];
                        break;

                    case 'cost':
                        $part = [
                            'title' => t('Цена:') .
                                (!empty($values['from']) ? t('от ').$values['from'] : '').
                                (!empty($values['to']) ? t(' до ').$values['to'] : '')
                        ];
                        break;

                    case 'isnum':
                        if ($values != "") {
                            $part = [
                                'title' => $values ? t('В наличии') : t('Нет в наличии')
                            ];
                        }
                        break;

                    default:
                        $part = ['title' => ''];
                }
            }

            $parts[] = $part + [
                    'type' => $key,
                    'filter' => 'base'
                ];
        }

        return $parts;
    }

    /**
     * Возвращает данные по установленным фильтрам по характеристикам
     *
     * @param array $prop_filters массив, полученный от Catalog\Model\PropertyApi->last_filtered_props
     * @return array
     */
    protected function getPropertyFiltersParts($prop_filters)
    {
        $parts = [];
        $part  = [];
        foreach($prop_filters as $prop_id => $data) {
            if ($data['property']->isListType()) {
                $part = [
                    'title' => $data['property']['title'].': '.implode(', ', $data['values'])
                ];
            } else {
                switch($data['property']['type']) {
                    case Item::TYPE_NUMERIC:
                        $part = [
                            'title' => $data['property']['title'] .': '.
                                (!empty($data['values']['from']) ? t('от ').$data['values']['from'] : '').
                                (!empty($data['values']['to']) ? t(' до ').$data['values']['to'] : '')
                        ];
                        break;
                    case Item::TYPE_STRING:
                        $part = [
                            'title' => $data['property']['title'].': '.implode(', ', $data['values'])
                        ];
                        break;
                    case Item::TYPE_BOOL:
                        $part = [
                            'title' => $data['property']['title'].': '.($data['values'] ? t('да') : t('нет'))
                        ];
                        break;
                }
            }
            $parts[] = $part + [
                    'type' => $prop_id,
                    'filter' => 'property'
                ];
        }

        return $parts;
    }


    /**
     * Добавляет к мета тегам Keywords, Description выбранные фильтры
     *
     * @param array $all_filters_data Массив с выбранными значениями фильтров
     * @return void
     */
    function applyFiltersToMeta($all_filters_data)
    {
        $items = [];
        foreach($all_filters_data as $part) {
            $items[] = $part['title'];
        }

        $app = \RS\Application\Application::getInstance();
        $append_text = implode('; ', $items);
        $app->meta->addKeywords($append_text);
        $app->meta->addDescriptions($append_text);
    }
    
    /**
    * Возвращает список доступных единиц измерения веса
    * 
    * @return array
    */
    public static function getWeightUnits()
    {
        return [
            self::WEIGHT_UNIT_G => [
                'ratio' => 1,
                'title' => t('грамм'),
                'short_title' => t('г', null, 'сокращение "грамм"'),
            ],
            self::WEIGHT_UNIT_KG => [
                'ratio' => 1000,
                'title' => t('килограмм'),
                'short_title' => t('кг', null, 'сокращение "килограмм"'),
            ],
            self::WEIGHT_UNIT_T => [
                'ratio' => 1000000,
                'title' => t('тонна'),
                'short_title' => t('т', null, 'сокращение "тонна"'),
            ],
            self::WEIGHT_UNIT_LB => [
                'ratio' => 453.59237,
                'title' => t('английский фунт'),
                'short_title' => t('lb', null, 'сокращение "фунт" (английский)'),
            ],
        ];
    }
    
    /**
    * Возвращает список наименований доступных единиц измерения веса
    * 
    * @return array
    */
    public static function getWeightUnitsTitles()
    {
        $list = [];
        foreach (self::getWeightUnits() as $key=>$unit) {
            $list[$key] = $unit['title'];
        }
        return $list;
    }

    /**
     * Возвращает объекты, выбранные с помощью Like
     *
     * @param string $term - слово или фраза для поиска
     * @param array $like_fields - поля для like поиска
     * @return array
     * @throws \RS\Orm\Exception
     */
    public function getLike($term, $like_fields)
    {
        $q = clone $this->queryObj();

        $q->select('id', 'title');
        $q->openWGroup();
        foreach($like_fields as $field) {
            $q->where("`{$field}` like '%#term%'", ['term' => $term], 'OR');
        }
        $q->closeWGroup();

        return $q->objects();
    }

    /**
     * Генерирует и присваивает артикулы всем товарам и комплектациям
     *
     * @throws DbException
     * @throws OrmException
     * @throws RSException
     */
    function generateBarcodeForAll()
    {
        $limit = 100;
        $offset = 0;

        $q = OrmRequest::make()
            ->from(new \Catalog\Model\Orm\Product())
            ->limit($limit);

        while($products = $q->offset($offset)->objects()){
            foreach ($products as $product) {
                /**
                 * @var \Catalog\Model\Orm\Product $product
                 */
                if(!$product['barcode']){
                    $barcode = $this->genereteBarcode();
                    $product['barcode'] = $barcode;
                    $product->update();
                }
                $offers = $product->fillOffers();
                $counter = 0;
                foreach ($offers['items'] as $sortn => $offer){
                    /**
                     * @var \Catalog\Model\Orm\Offer $offer
                     */
                    if($offer['barcode']){
                        continue;
                    }
                    $offer['barcode'] = $product['barcode']."-".$counter;
                    $counter++;
                    if (!$offer->update()) {
                        var_dump($offer->getErrorsStr());
                    };
                }
            }
            $offset += $limit;
        }
    }

    /**
     * Генерирует уникальный идентификаотр для товара
     *
     * @return string
     * @throws DbException
     * @throws OrmException
     * @throws RSException
     */
    function generateUniqBarcode()
    {
        $barcode = $this->genereteBarcode();
        if($this->isExistBarcode($barcode)){
            $this->generateUniqBarcode();
        }
        return $barcode;
    }

    /**
     * Проверяет существует ли переданный артикул в системе
     *
     * @param string $barcode - артикул
     * @return bool
     */
    function isExistBarcode($barcode)
    {
        $in_products = OrmRequest::make()
            ->from(new Product())
            ->where(['barcode' => $barcode])
            ->object();
        $in_offers = OrmRequest::make()
            ->from(new Offer())
            ->where(['barcode' => $barcode])
            ->object();
        return ($in_offers || $in_products);
    }

    /**
     * Парсит часть ЧПУ адреса с фильтрами и возвращает массив из найденных в адресе фильтров
     *
     * @return array
     * @throws DbException
     * @throws RSException
     */
    public static function decodeDirFilterParamsFromUrl()
    {
        static $url_filtes_cache; //Кэш разбора

        if ($url_filtes_cache === null){
            $config           = ConfigLoader::byModule('catalog');
            $url_filters_part = \RS\Http\Request::commonInstance()->get('filters', TYPE_STRING, "");

            if ($url_filters_part && !$config['use_seo_filters']) {
                throw new RSException(t('ЧПУ фильтры выключены.'));
            }

            $filters = [];
            if ($config['use_seo_filters'] && !empty($url_filters_part)){
                $url_filters = explode("/", $url_filters_part); //Разберем части

                //Разбор установленных брендов
                $brands = [];
                if (preg_match('/^(brand_)/i', $url_filters[0])){ //Смотрим есть ли фильтр по брендам
                    $brands_url_part = array_shift($url_filters);
                    $brands = explode("_", $brands_url_part);
                    array_shift($brands);
                }

                //Посмотрим есть ли значения брендов в адресе и добавим в общий массив данных
                if (!empty($brands)){
                    //Заполним массив фильтров бренда
                    $brand_api = new \Catalog\Model\BrandApi();
                    foreach ($brands as $brand_alias_or_id) {
                        $brand = $brand_api->getById($brand_alias_or_id);
                        if ($brand && $brand['id']){
                            $filters['bfilter']['brand'][] = $brand['id'];
                        }
                    }
                }

                if (!empty($url_filters)){
                    $prop_api       = new \Catalog\Model\PropertyApi();

                    $props = [];
                    //Пройдемся по группам характеристик и значений
                    foreach ($url_filters as $property_dataparts){
                        $prop_data   = explode("_", $property_dataparts);
                        //Подгрузим псевдоним
                        $prop_alias  = array_shift($prop_data);
                        $property_id = $prop_api->getIdByAlias($prop_alias, 'alias'); //Получим id по псевдониму

                        if ($property_id){ //id характеристики
                            foreach ($prop_data as $value_alias){
                                $props[$property_id][] = OrmRequest::make()
                                    ->from(new ItemValue())
                                    ->where([
                                        'site_id' => \RS\Site\Manager::getSiteId(),
                                        'prop_id' => $property_id
                                    ])
                                    ->where("(id = '#alias_or_id' OR alias = '#alias_or_id')", ['alias_or_id' => $value_alias])
                                    ->limit(1)
                                    ->orderby("alias != '#alias_or_id'", ['alias_or_id' => $value_alias])
                                    ->exec()
                                    ->getOneField('id', 0);
                            }
                        } else {
                            throw new RSException(t('Таких характеристик не существует.'));

                        }
                    }
                    //Заполним массив фильтров
                    if (!empty($props)){
                        $filters['pf'] = $props;
                    }
                }
            }
            $url_filtes_cache = $filters;
        }

        return $url_filtes_cache;
    }

    /**
     * Возвращает преобразованный массив с фильтрами приходящими извне и поготавливает в зависимости от включенной
     * или выключенной опции SEO фильтров массив параметров для формирования адреса категории в том числе с фильтрами
     *
     * @param array $bfilters - базовые фильтры
     * @param array $pf - фильтры по характеристикам
     * @param string $query - строковый запрос
     * @return array
     */
    private static function getEncodeDirFilterParamsArrayUrlParts($bfilters = [], $pf = [], $query = "")
    {
        $filters_as_url = []; //Фильтры строкой

        $config = ConfigLoader::byModule('catalog'); //Конфиг модуля

        $params = [];
        if (isset($bfilters['cost'])){ //Фильтр по цене
            $params['bfilter']['cost'] = $bfilters['cost'];
        }

        if (isset($bfilters['isnum'])){ //Фильтр по наличию
            $params['bfilter']['isnum'] = $bfilters['isnum'];
        }

        if (isset($bfilters['brand'])){ //Фильтр по бренду
            if ($config['use_seo_filters']){ //ЧПУ включен
                $brand_aliases = [];
                foreach ($bfilters['brand'] as $brand_id){
                    $brand = new \Catalog\Model\Orm\Brand($brand_id);
                    $brand_aliases[] = !empty($brand['alias']) ? $brand['alias'] : $brand['id'];
                }

                asort($brand_aliases); //Сортируем значения всегда по алфавиту
                $filters_as_url[] = "brand_".implode("_", $brand_aliases);
            }else{
                $params['bfilter']['brand'] = $bfilters['brand'];
            }
        }

        if (!empty($pf)) { //Фильтр по бренду
            if ($config['use_seo_filters']) { //ЧПУ включен
                $seo_filter_types = [ //Список типов для адресов ЧПУ
                    Item::TYPE_LIST,
                    Item::TYPE_IMAGE,
                    Item::TYPE_COLOR,
                    Item::TYPE_RADIO_LIST
                ];
                foreach ($pf as $prop_id=>$values){
                    $property = new Item($prop_id);
                    $property_alias = (!empty($property['alias'])) ? $property['alias']: $property['id'];
                    if (in_array($property['type'], $seo_filter_types)){ //Если это список
                        $value_aliases = [];
                        foreach ($values as $value_id){ //Посмотрим все значения
                            $value = new ItemValue($value_id);
                            $value_aliases[] = (!empty($value['alias'])) ? $value['alias']: $value['id'];
                        }
                        asort($value_aliases); //Сортируем значения всегда по алфавиту
                        $filters_as_url[] = $property_alias."_".implode("_", $value_aliases);
                    }else{
                        $params['pf'][$prop_id] = $values;
                    }
                }
            }else{
                $params['pf'] = $pf;
            }
        }

        if (!empty($query)){
            $params['query'] = $query;
        }

        //Если SEO фмльтры включены получим преобразованную часть
        if (!empty($filters_as_url)){ 
            $params['filters'] = implode("/", $filters_as_url);
        }
        
        return $params;
    }


    /**
     * Переводит переданные фильтры категории в ЧПУ адрес
     *
     * @param Dir|null $category - объект категории
     * @param array $bfilters - базовые фильтры
     * @param array $pf - фильтры по характеристикам
     * @param string $query - строковый запрос
     * @param array $additional_params - дополнительные параметры
     * @return string
     * @throws RSException
     */
    public static function encodeDirFilterParamsToUrl(Dir $category = null, $bfilters = [], $pf = [], $query = "", $additional_params = [])
    {
        static
            $encoded_filters_params;
        if ($encoded_filters_params === null){
            $encoded_filters_params = self::getEncodeDirFilterParamsArrayUrlParts($bfilters, $pf, $query);
        }

        $params = $encoded_filters_params;

        //Соединим всё в адрес
        if ($category === null) {
            $category = new Dir();
            $category->declareRoot();
        }
        $url = $category->getUrl();
        if (isset($params['filters'])){
            $url .= $params['filters']."/";
            unset($params['filters']);
        }

        //Посмотрим дополнительные параметры
        $get = \RS\Http\Request::commonInstance()->getSource(GET);
        //Удалим ненужное, которое уже обработано
        unset($get['bfilter']);
        unset($get['query']);
        unset($get['pf']);
        unset($get['f']);
        unset($get['filters']);
        unset($get['p']);
        unset($get['id']);
        unset($get['category']);

        $params += $get; //Соединим параметры

        //Если доп. параметры заданы, то объединими их
        if (!empty($additional_params)){
            $params = array_merge($params, $additional_params);
        }

        if (!empty($params)){
            $url .= "?".http_build_query($params);
        }
        return urldecode($url);
    }

    /**
     * Возвращает массив параметров для формирования адреса категории с или без фильтров
     * 
     * @param Dir $category - объект категории
     * @param array $bfilters - базовые фильтры
     * @param array $pf - фильтры по характеристикам
     * @param string $query - строковый запрос
     * @return array
     * @throws RSException
     */
    public static function getCategoryPaginatorRouteParamsForProductsList(Dir $category, $bfilters = [], $pf = [], $query = "", $additional_params = [])
    {
        $params = self::getEncodeDirFilterParamsArrayUrlParts($bfilters, $pf, $query);

        //Посмотрим дополнительные параметры
        $get = \RS\Http\Request::commonInstance()->getSource(GET);
        //Удалим ненужное, которое уже обработано
        unset($get['bfilter']);
        unset($get['query']);
        unset($get['pf']);
        unset($get['filters']);
        unset($get['p']);
        unset($get['category']);

        $params += $get; //Соединим параметры

        //Если доп. параметры заданы, то объединими их
        if (!empty($additional_params)){
            $params = array_merge($params, $additional_params);
        }

        if ($category['id']){ //Если мы в конкретной категории
            $params['category'] = !empty($category['alias']) ? $category['alias'] : $category['id'];
        }
        return $params;
    }

    /**
     * Создаёт URL для страницы каталоге при помощи URL в соответствии с правилами настроек
     *
     * @param array $params - массив параметров для генерации адреса
     * @return string
     * @throws DbException
     * @throws RSException
     */
    public function urlMakeCatalogParams($params)
    {
        //Разберем ЧПУ адрес с фильтрами предварительно, если включена опция и они есть
        $decoded_filters = $this->decodeDirFilterParamsFromUrl();

        $router  = \RS\Router\Manager::obj();
        $request = \RS\Http\Request::commonInstance();
        /** @var Dir $category */
        $category = clone $router->getCurrentRoute()->getExtra('category');
        $query    = $request->request('query', TYPE_STRING, "");

        if (isset($params['category'])) {
            //Подменяем alias категории, если таковой был передан
            $category['_alias'] = $params['category'];
            unset($params['category']);
        }

        static
            $url_make_filters; //Кэш характеристик
        if ($url_make_filters === null){
            $old_version_filters = $request->request('f', TYPE_ARRAY); //Для совместимости с предыдущими версиями RS
            $prop_api = new PropertyApi();
            $filters  = $request->request('pf', TYPE_ARRAY, $prop_api->convertOldFilterValues($old_version_filters));

            if (isset($decoded_filters['pf'])){ //Если есть декодированные фильтры
                $filters = $filters + $decoded_filters['pf'];
            }

            $url_make_filters = $filters;
        }

        return self::encodeDirFilterParamsToUrl($category, $this->getBaseFilters(), $url_make_filters, $query, $params);
    }

    /**
     * Если включены опции в настройках модуля Каталог:
     * "Ограничить остатки товара только остатками на складах выбранного филиала"
     * и "Скрывать товары с нулевым остатком",
     * то добавляем условие для подсчета остатка товаров именно на связанных филиалах и скрываем по данном остатку
     *
     * @param bool $set_filter_by_warehouse
     * @return void
     */
    public function setAffiliateRestrictions($set_filter_by_warehouse = false)
    {
        $config = ConfigLoader::byModule($this);
        if (ModuleManager::staticModuleExists('affiliate') && $config['affiliate_stock_restriction'] && !RouterManager::obj()->isAdminZone()) {

            $warehouse_ids = WareHouseApi::getAvailableWarehouses();

            //Устанавливаем склады для дальнейшего расчета динамических остатков
            $this->setWarehousesForDynamicNum($warehouse_ids);

            if ($set_filter_by_warehouse) {
                $only_join_dynamic_num = $config['hide_unobtainable_goods'] == 'N';
                //Выбрать только те товары, у которых динамические остатки больше нуля
                $this->setWarehouseFilter($warehouse_ids, $only_join_dynamic_num);
            }
        }
    }

    /**
     * Добавляет к условиям выборки, условие фильтрации по наличию на выбранных складах
     * Предполагается, что GROUP BY product.id - уже присутствует в запросе
     *
     * @param [] $warehouses
     * @param bool $only_join_dynamic_num - Если true, то условие для скрытия не будет отображаться
     * @return void
     */
    public function setWarehouseFilter($warehouses, $only_join_dynamic_num = false)
    {
        if ($warehouses) {
            $this->queryObj()
                ->select("COALESCE(SUM(XST.stock), 0) as dynamic_num")
                ->leftjoin(new Orm\Xstock(), "XST.product_id = `" . $this->defAlias() . "`.`id`", "XST")
                ->whereIn('XST.warehouse_id', $warehouses);

            if (!$only_join_dynamic_num) {
                $this->queryObj()->having('SUM(XST.stock) > 0');
            }
        }
    }

    /**
     * Удаляет несвязанные сопутствующие товары
     * Операция необходима для очистки базы от неиспользуемых записей
     */
    function cleanUnusedRelatedProducts()
    {
        $site_id = Manager::getSiteId();

        $products_ids = OrmRequest::make()
            ->select('id')
            ->from(new Product())
            ->where([
                'site_id' => $site_id
            ])
            ->exec()
            ->fetchSelected('id', 'id');

        $offset = 0;
        $q = OrmRequest::make()
            ->select('id', 'concomitant', 'recommended')
            ->from(new Product())
            ->where([
                'site_id' => $site_id
            ]);

        while ($products = $q->limit($offset, self::REQUESTED_PRODUCTS)->exec()->fetchAll()) {
            foreach ($products as $product) {
                $concomitant = @unserialize($product['concomitant']);
                $recommended = @unserialize($product['recommended']);
                $update = false;

                if ($concomitant) {
                    foreach ($concomitant['product'] as $key => $item) {
                        if (!isset($products_ids[$item]) ) {
                            unset($concomitant['product'][$key]);
                            $update = true;
                        }
                    }
                }
                if ($recommended) {
                    foreach ($recommended['product'] as $key => $item) {
                        if (!isset($products_ids[$item]) ) {
                            unset($recommended['product'][$key]);
                            $update = true;
                        }
                    }
                }

                if ($update) {
                    OrmRequest::make()
                        ->update(new Product())
                        ->set([
                            'concomitant' => serialize($concomitant),
                            'recommended' => serialize($concomitant)

                        ])
                        ->where([
                            'id' => $product['id']
                        ])
                        ->exec();
                }

            }
            $offset += self::REQUESTED_PRODUCTS;
        }
    }

    /**
     * Возвращает информацию о товаре или комплектации по штрих-коду
     *
     * @param $sku
     * @return array|bool(false)
     * @throws RSException
     */
    public function getDataByBarcode($sku)
    {
        $q = $this->getCleanQueryObject();
        $q->where([
            'sku' => $sku
        ]);

        if ($product = $q->object()) { // если не найден товар
            /** @var Product $product */
            $product->fillOffers();
            $product = $product->getValues();
            $offer = $product->getMainOffer()->getValues(); // Берём первую комплектацию, т.к. был найден товар
            $offer_id = $offer['id'];
            $offer_number = 0; // Устанавливаем порядковый номер комплектации
        } else {
            $offer = OrmRequest::make()
                ->select()
                ->from(new Offer())
                ->where(['sku' => $sku])
                ->object();

            if ($offer) {
                $product = new Product($offer['product_id']); // Получаем связанный с комплектацией товар
                $product->fillOffers();
                // Определяем порядковый номер комплектации
                $offers = $product['offers']['items'];
                $offer_id = $offer['id'];
                foreach($offers as $i => $item) {
                    if ($item['id'] == $offer_id) { // Если нашли комплектацию
                        $offer_number = $i; // Устанавливаем порядковый номер комплектации
                        break;
                    }
                }

                if (!isset($offer_number)) {
                    throw new RSException(t('Не найден номер комплектации, возможно имеется не привязанная к товару комплектация'));
                }

            } else {
                return false;
            }
        }

        return [
            'id' => $product['id'],
            'title' => $product['title'],
            'offer_title' => $offer['title'],
            'offer_number' => $offer_number,
            'offer_id' => $offer_id,
            'weight' => $product['weight']
        ];
    }
}
