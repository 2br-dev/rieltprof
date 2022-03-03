<?php
namespace Rieltprof\Model;

use Rieltprof\Controller\Block\SearchLine as CatalogSearchLine;
use Catalog\Model\Orm\Product;
use RS\Config\Loader as ConfigLoader;
use RS\Event\Manager as EventManager;
use RS\Exception as RSException;
use Search\Model\SearchApi;

class Api extends \Catalog\Model\SearchLineApi {
    /**
     * Подготавливает поиск по товаром в зависимости от запроса
     *
     * @param string $query - строка для поиска
     * @param CatalogSearchLine $controller - контроллер строки поиска
     * @param string $order_field - колонка для сортировки
     * @param string $order_direction - лимит результатов поиска
     * @throws RSException
     */
    public function prepareSearchQueryProduct($query, $controller, $order_field, $order_direction)
    {
        $q = $this->api->resetQueryObject()->queryObj();
        $q->select = 'A.*';
        $search = SearchApi::currentEngine();
//        $search->setFilter('B.result_class', 'Catalog\Model\Orm\Product');
        $search->setQuery($query);
        $search->joinQuery($q, 'A', 'B');
        $this->api->setFilter('public', 1);

        if ($order_field != CatalogSearchLine::SORT_RELEVANT) {
            $this->api->setSortOrder($order_field, $order_direction);
        }

        if (ConfigLoader::byModule($this)['hide_unobtainable_goods'] == 'Y') {
            $this->api->setFilter('num', '0', '>');
        }
        EventManager::fire('init.api.catalog-front-listproducts', $controller);
    }

    /**
     * Возвращает результаты поиска по товарам
     *
     * @param integer $limit - лимит результатов поиска
     * @return Product[]
     * @throws RSException
     */
    public function getSearchQueryProductResults($limit = 1)
    {
        $list = $this->api->getList(1, $limit);
        $this->api->setFilter('B.result_class', [
            'Catalog\Model\Orm\Product',
            'Rieltprof\Model\Orm\Flat',
            'Rieltprof\Model\Orm\Commercial',
            'Rieltprof\Model\Orm\CountryHouse',
            'Rieltprof\Model\Orm\Duplex',
            'Rieltprof\Model\Orm\Garage',
            'Rieltprof\Model\Orm\House',
            'Rieltprof\Model\Orm\NewBuilding',
            'Rieltprof\Model\Orm\Plot',
            'Rieltprof\Model\Orm\Room',
            'Rieltprof\Model\Orm\TownHouse'
        ], 'in');
//        var_dump($this->api->queryObj()->toSql());
        $list = $this->api->addProductsPhotos($list);
        $list = $this->api->addProductsCost($list);
        return $list;
    }
}
