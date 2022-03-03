<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Catalog\Controller\Block;

use Catalog\Model\Api as ProductApi;
use Catalog\Model\Orm\Brand;
use Catalog\Model\Orm\Dir;
use Catalog\Model\Orm\Product;
use Catalog\Model\SearchLineApi;
use RS\Config\Loader as ConfigLoader;
use RS\Controller\Result\Standard as ResultStandard;
use RS\Controller\StandartBlock;
use RS\Event\Manager as EventManager;
use RS\Exception as RSException;
use RS\Orm\AbstractObject;
use RS\Orm\Type;

/**
 * Блок-контроллер Поиск по товарам
 */
class SearchLine extends StandartBlock
{
    const SORT_RELEVANT = 'relevant';

    protected static $controller_title = 'Поиск товаров по названию';
    protected static $controller_description = 'Отображает форму для поиска товаров по ключевым словам';

    protected $action_var = 'sldo';
    protected $default_params = [
        'searchLimit' => 5,
        'searchBrandLimit' => 1,
        'searchCategoryLimit' => 1,
        'hideAutoComplete' => 0,
        'indexTemplate' => 'blocks/searchline/searchform.tpl',
        'imageWidth' => 62,
        'imageHeight' => 62,
        'imageResizeType' => 'xy',
        'order_field' => self::SORT_RELEVANT,
        'order_direction' => 'asc',
        'showUnavailableProducts' => 0
    ];

    /** @var ProductApi $api */
    public $api;
    /** @var SearchLineApi $search_line_api */
    public $search_line_api;

    /**
     * Инициализация
     */
    public function init()
    {
        $this->api = new ProductApi();
        $this->search_line_api = new SearchLineApi();
        EventManager::fire('init.searchlineapi.' . $this->getUrlName(), $this);
    }

    /**
     * Возвращает параметры блока
     *
     * @return AbstractObject
     */
    public function getParamObject()
    {
        return parent::getParamObject()->appendProperty([
            'imageWidth' => new Type\Integer([
                'description' => t('Ширина изображения в подсказках'),
                'maxLength' => 6
            ]),
            'imageHeight' => new Type\Integer([
                'description' => t('Высота изображения в подсказках'),
                'maxLength' => 6
            ]),
            'imageResizeType' => new Type\Varchar([
                'description' => t('Тип масштабирования изображения в подсказках'),
                'maxLength' => 4,
                'listFromArray' => [[
                    'xy' => 'xy',
                    'axy' => 'axy',
                    'cxy' => 'cxy',
                    'ctxy' => 'ctxy',
                ]]
            ]),
            'hideAutoComplete' => new Type\Integer([
                'description' => t('Отключить подсказку результатов поиска в выпадающем списке'),
                'checkboxView' => [1, 0]
            ]),
            'searchLimit' => new Type\Integer([
                'description' => t('Количество товаров в выпадающем списке')
            ]),
            'searchBrandLimit' => new Type\Integer([
                'description' => t('Количество брендов в выпадающем списке')
            ]),
            'searchCategoryLimit' => new Type\Integer([
                'description' => t('Количество категорий в выпадающем списке')
            ]),
            'showUnavailableProducts' => new Type\Integer([
                'description' => t('Показывать товары, даже если их нет в наличии'),
                'checkboxView' => [1, 0]
            ]),
            'order_field' => new Type\Varchar([
                'description' => t('Сортировка результатов среди товаров'),
                'listFromArray' => [[
                    self::SORT_RELEVANT => t('Не выбрано'),
                    'sortn' => t('Вес'),
                    'dateof' => t('Дата'),
                    'rating' => t('Рейтинг'),
                    'cost' => t('Цена'),
                    'title' => t('Название'),
                ]]
            ]),
            'order_direction' => new Type\Varchar([
                'description' => t('Направление сортировки среди товаров'),
                'listFromArray' => [[
                    'asc' => t('по возрастанию'),
                    'desc' => t('по убыванию')
                ]]
            ])
        ]);
    }

    /**
     * Метод обработки отображения поисковой строки
     *
     * @return ResultStandard
     */
    public function actionIndex()
    {
        $query = trim($this->url->get('query', TYPE_STRING));
        if ($this->router->getCurrentRoute() && $this->router->getCurrentRoute()->getId() == 'catalog-front-listproducts' && !empty($query)) {
            $this->view->assign('query', $query);
        }
        return $this->result->setTemplate($this->getParam('indexTemplate'));
    }

    /**
     * Метод отработки запроса на поиск. Возвращает JSON ответ
     *
     * @return string
     * @throws RSException
     */
    public function actionAjaxSearchItems()
    {
        $query = trim($this->url->request('term', TYPE_STRING));
        $result_json = [];

        if (!empty($query)) {

            //Найдем подходящие товарам
            /** @var Product[] $list */
            $this->search_line_api->prepareSearchQueryProduct($query, $this, $this->getParam('order_field'), $this->getParam('order_direction'));
            $list = $this->search_line_api->getSearchQueryProductResults($this->getParam('searchLimit'));

            $shop_config = ConfigLoader::byModule('shop');
            if (!empty($list)) {
                foreach ($list as $product) {

                    $price = ($shop_config && $shop_config->check_quantity && $product['num'] <= 0) ? t('Нет в наличии') : $product->getCost() . ' ' . $product->getCurrency();
                    $brand = $product->getBrand();
                    $available = ($shop_config && $shop_config->check_quantity && $product['num'] <= 0) ? 0 : 1;

                    $result_json[] = [
                        'value' => $product['title'],
                        'label' => preg_replace("#($query)#iu", '<b>$1</b>', $product['title']),
                        'barcode' => preg_replace("#($query)#iu", '<b>$1</b>', $product['barcode']),
                        'brand' => $brand['title'],
                        'image' => $product->getMainImage()->getUrl($this->getParam('imageWidth'), $this->getParam('imageHeight'), $this->getParam('imageResizeType')),
                        'available' => $this->getParam('showUnavailableProducts') ? 1 : $available,
                        'price' => $price,
                        'type' => 'product',
                        'url' => $product->getUrl()
                    ];
                }

                //Секция все результаты товаров
                if ($this->search_line_api->getSearchQueryProductCount() > $this->getParam('searchLimit')) {
                    $result_json[] = [
                        'value' => "",
                        'label' => t("Показать все товары"),
                        'type' => 'search',
                        'url' => $this->router->getUrl('catalog-front-listproducts', ['query' => $query])
                    ];
                }
            }

            //Найдем бренды подходящие под запрос
            /** @var Brand[] $list */
            if ($this->getParam('searchBrandLimit')) {
                $list = $this->search_line_api->getSearchQueryBrandsResults($query, $this->getParam('searchBrandLimit'));
                if (!empty($list)) {
                    foreach ($list as $brand) {
                        $result_json[] = [
                            'value' => $brand['title'],
                            'label' => preg_replace("#($query)#iu", '<b>$1</b>', $brand['title']),
                            'image' => $brand->getMainImage()->getUrl($this->getParam('imageWidth'), $this->getParam('imageHeight'), $this->getParam('imageResizeType')),
                            'type' => 'brand',
                            'url' => $brand->getUrl()
                        ];
                    }
                }
            }

            //Найдем категории подходящие под запрос
            /** @var Dir[] $list */
            if ($this->getParam('searchCategoryLimit')) {
                $list = $this->search_line_api->getSearchQueryCategoryResults($query, $this->getParam('searchCategoryLimit'));
                if (!empty($list)) {
                    foreach ($list as $dir) {
                        $result_json[] = [
                            'value' => $dir['name'],
                            'label' => preg_replace("#($query)#iu", '<b>$1</b>', $dir['name']),
                            'image' => $dir->getMainImage()->getUrl($this->getParam('imageWidth'), $this->getParam('imageHeight'), $this->getParam('imageResizeType')),
                            'type' => 'category',
                            'url' => $dir->getUrl()
                        ];
                    }
                }
            }
        }

        $this->app->headers->addHeader('content-type', 'application/json');
        return json_encode($result_json, JSON_UNESCAPED_UNICODE);
    }
}
