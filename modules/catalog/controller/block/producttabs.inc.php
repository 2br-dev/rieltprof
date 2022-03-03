<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Catalog\Controller\Block;

use Catalog\Model\Api as ProductApi;
use Catalog\Model\Dirapi;
use Catalog\Model\Orm\Dir;
use RS\Controller\StandartBlock;
use RS\Helper\Tools as HelperTools;
use RS\Module\AbstractModel\TreeList\AbstractTreeListIterator;
use RS\Orm\Type;

/**
 * Контроллер - топ товаров из указанных категорий
 */
class ProductTabs extends StandartBlock
{
    protected static $controller_title = 'Товары из нескольких категорий';
    protected static $controller_description = 'Отображает товары, распределенные по закладкам, соответствующим названию категории';

    protected $default_params = [
        'indexTemplate' => 'blocks/producttabs/producttabs.tpl', //Должен быть задан у наследника
        'pageSize' => 4,
        'cache_html_lifetime' => 300
    ];
    protected $page;

    /** @var Dirapi */
    public $dirapi;
    /** @var ProductApi */
    public $api;

    function getParamObject()
    {
        return parent::getParamObject()->appendProperty([
            'pageSize' => new Type\Integer([
                'description' => t('Количество элементов в закладке'),
            ]),
            'categories' => new Type\ArrayList([
                'description' => t('Товары каких спецкатегорий показывать?'),
                'tree' => ['Catalog\Model\DirApi::staticTreeList'],
                'attr' => [[
                    AbstractTreeListIterator::ATTRIBUTE_MULTIPLE => true,
                ]]
            ]),
            'order' => new Type\Varchar([
                'description' => t('Поле сортировки'),
                'listFromArray' => [[
                    'id' => 'ID',
                    'title' => t('Название'),
                    'dateof' => t('Дата'),
                    'rating' => t('Рейтинг'),

                    'id DESC' => t('ID обратн. порядок'),
                    'title DESC' => t('Название обратн. порядок'),
                    'dateof DESC' => t('Дата обратн. порядок'),
                    'rating DESC' => t('Рейтинг обратн. порядок'),
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
        $this->dirapi = new Dirapi();
    }

    function actionIndex()
    {
        $cache_id = json_encode($this->getParam());
        $template = $this->getParam('indexTemplate');

        if ($this->isViewCacheExpired($cache_id, $template, $this->getParam('cache_html_lifetime'))) {

            $dir_ids = $this->getParam('categories');
            /** @var Dir[] $categories */
            $categories = [];
            foreach ($dir_ids as $key => $dir_id) {
                $category = $this->dirapi->getById($dir_id);
                if ($category['id']) {
                    $dir_ids[$key] = $category['id'];
                    $categories[] = $category;
                }
            }

            if (!empty($categories)) {
                $this->dirapi->setFilter('id', $dir_ids, 'in');
                $this->dirapi->setOrder('FIELD(id, ' . implode(',', HelperTools::arrayQuote($dir_ids)) . ')');
                $dirs = $this->dirapi->getAssocList('id');

                $products_by_dirs = [];
                $catalog_config = $this->getModuleConfig();
                $in_stock = $this->getParam('only_in_stock', 0) || $catalog_config['hide_unobtainable_goods'] == 'Y';
                foreach ($categories as $category) {
                    $q = $this->api->clearFilter()->setFilter('public', 1);
                    if ($in_stock) { //Если показывать только в наличии
                        $q->setFilter('num', 0, '>');
                    }

                    if ($category['is_virtual']) {
                        if ($product_ids_by_virtual_dir = $category->getVirtualDir()->getFilteredProductIds($category['id'])) {
                            $q->setFilter('id', $product_ids_by_virtual_dir, 'in');
                        }
                    } else {
                        $q->setFilter('dir', $category['id']);
                    }

                    $products_by_dirs[$category['id']] = $q->getList(1, $this->getParam('pageSize'), $this->getParam('order', 'id DESC'));
                    $products_by_dirs[$category['id']] = $this->api->addProductsDirs($products_by_dirs[$category['id']]);
                }

                $this->view->assign([
                    'dirs' => $dirs,
                    'products_by_dirs' => $products_by_dirs,
                ]);
            }
        }

        return $this->result->setTemplate($template);
    }
}