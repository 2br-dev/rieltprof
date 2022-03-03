<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Catalog\Controller\Block;

use Catalog\Model\Api as ProductApi;
use Catalog\Model\DirApi;
use Catalog\Model\Orm\Dir;
use RS\Cache\Manager as CacheManager;
use RS\Controller\StandartBlock;
use RS\Debug\Action as DebugAction;
use RS\Debug\Tool as DebugTool;
use RS\Helper\Paginator;
use RS\Helper\Tools as HelperTools;
use RS\Module\AbstractModel\TreeList\AbstractTreeListIterator;
use RS\Orm\Type;

/**
 * Контроллер - топ товаров из указанных категорий одним списком
 */
class TopProducts extends StandartBlock
{
    protected static $controller_title = 'Продукты из категории';
    protected static $controller_description = 'Отображает нужное количество товаров из заданных категорий';

    protected $default_params = [
        'indexTemplate' => 'blocks/topproducts/top_products.tpl', //Должен быть задан у наследника
        'pageSize' => 15,
        'cache_html_lifetime' => 300
    ];
    protected $page;

    /** @var DirApi $dirapi */
    public $dirapi;
    /** @var ProductApi $api */
    public $api;

    function getParamObject()
    {
        return parent::getParamObject()->appendProperty([
            'pageSize' => new Type\Integer([
                'description' => t('Количество элементов на страницу'),
            ]),
            'dirs' => new Type\ArrayList([
                'description' => t('Категории, из которых выводить товары'),
                'tree' => ['Catalog\Model\DirApi::staticTreeList', 0, [0 => t('- Корень каталога -')]],
                'attr' => [[
                    AbstractTreeListIterator::ATTRIBUTE_MULTIPLE => true,
                ]],
            ]),
            'block_title' => new Type\Varchar([
                'description' => t('Заголовок блока'),
                'hint' => t('Если не указан, то в качестве заголовка будет использовано название первой категории в списке (со ссылкой на эту категорию)'),
            ]),
            'order' => new Type\Varchar([
                'description' => t('Поле сортировки'),
                'listFromArray' => [[
                    'id' => 'ID',
                    'title' => t('Название'),
                    'dateof' => t('Дата'),
                    'rating' => t('Рейтинг'),
                    'sortn DESC' => t('Сортировочный вес'),
                    'id DESC' => t('ID обратн. порядок'),
                    'title DESC' => t('Название обратн. порядок'),
                    'dateof DESC' => t('Дата обратн. порядок'),
                    'rating DESC' => t('Рейтинг обратн. порядок'),

                    'rand()' => t('Случайный порядок')
                ]]
            ]),
            'only_in_stock' => new Type\Integer([
                'default' => 0,
                'description' => t('Показывать только те, что в наличии?'),
                'CheckboxView' => [1, 0],
            ]),
            'cache_html_lifetime' => new Type\Integer([
                'description' => t('Время кэширования HTML блока, секунд?'),
                'hint' => t('0 - кэширование выключено. Значение больше нуля ускоряет работу сайта, но допускает неактуальность данных на срок кэширования. Работает только если в настройках системы включено кэширование данных.'),
            ]),
        ]);
    }

    function init()
    {
        $this->api = new ProductApi();
        $this->dirapi = DirApi::getInstance();
    }

    function actionIndex()
    {
        $page = $this->myGet('page', TYPE_INTEGER, 1);

        $cache_id = json_encode($this->getParam()).$page;
        $template = $this->getParam('indexTemplate');

        if ($this->isViewCacheExpired($cache_id, $template, $this->getParam('cache_html_lifetime'))) {
            $catalog_config = $this->getModuleConfig();

            $pageSize = $this->getParam('pageSize', null);
            $order = $this->getParam('order', null);
            $in_stock = $this->getParam('only_in_stock', 0) || $catalog_config['hide_unobtainable_goods'] == 'Y';

            $dir_ids = (array)$this->getParam('dirs');
            /** @var Dir[] $categories */
            $categories = [];
            foreach ($dir_ids as $key => $dir_id) {
                $category = $this->dirapi->getById($dir_id);
                if ($category['id']) {
                    $dir_ids[$key] = $category['id'];
                    $categories[] = $category;
                }
            }

            if ($debug_group = $this->getDebugGroup()) {
                $create_href = $this->router->getAdminUrl('add', ['dir' => reset($categories)['id']], 'catalog-ctrl');
                $debug_group->addDebugAction(new DebugAction\Create($create_href));
                $debug_group->addTool('create', new DebugTool\Create($create_href));
            }

            if (!empty($categories)) {
                $product_ids = [0];
                foreach ($categories as $category) {
                    if ($category['is_virtual']) {
                        $product_ids_by_virtual_dir = $category->getVirtualDir()->getFilteredProductIds($category['id']);
                        $product_ids = array_merge($product_ids, $product_ids_by_virtual_dir);
                    }
                }
                $product_ids = array_unique($product_ids);
                $category_ids = (CacheManager::obj()->request([$this->dirapi, 'getChildsId'], $dir_ids)) ?: [0];
                $this->api->setFilter([[
                    'dir:in' => implode(',', HelperTools::arrayQuote($category_ids)),
                    '|id:in' => implode(',', HelperTools::arrayQuote($product_ids)),
                ]]);

                $this->api->setFilter('public', 1);
                if ($in_stock) { //Если показывать только в наличии
                    $this->api->setFilter('num', 0, '>');
                    $this->api->setAffiliateRestrictions(true);
                }
                $total = $this->api->getListCount();

                if (!empty($order)) {
                    $this->api->setOrder($order);
                }
                $this->api->setGroup('id');

                $paginator = new Paginator($page, $total, $pageSize, Paginator::PATTERN_KEYREPLACE, [], 'page');
                $products = $this->api->getList($page, $pageSize);
                $products = $this->api->addProductsPhotos($products);
                $products = $this->api->addProductsDirs($products);
                $products = $this->api->addProductsCost($products);
                $products = $this->api->addProductsOffers($products);
                $this->view->assign([
                    'dir' => reset($categories),
                    'paginator' => $paginator,
                    'products' => $products,
                    'page' => $page,
                    'block_title' => $this->getParam('block_title'),
                ]);
            }
        }

        return $this->result->setTemplate($template);
    }
}
