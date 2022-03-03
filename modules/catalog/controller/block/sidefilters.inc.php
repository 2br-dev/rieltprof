<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Controller\Block;
use \RS\Orm\Type;

/**
* Контроллер - фильтры в боковой колонке
*/
class SideFilters extends \RS\Controller\StandartBlock
{
    protected static
        $controller_title = 'Фильтр по характеристикам',
        $controller_description = 'Отображает включенные в административной панели фильтры по характеристикам для текущей категории';
        
    protected
        $default_params = [
            'indexTemplate' => 'blocks/sidefilters/filters.tpl', //Должен быть задан у наследника
            'category_id' => '',
            'use_allowed_values' => 1,
            'show_cost_filter' => 1,
            'show_brand_filter' => 1,
            'show_only_public' => 1,
            'show_is_num' => 0
    ];

    /**
     * @var \Catalog\Model\Api $api
     */
    public $api;
    /**
     * @var \Catalog\Model\Dirapi $dirapi
     */
    public $dirapi;


    function init()
    {
        $this->api = new \Catalog\Model\Api();
        $this->dirapi = new \Catalog\Model\Dirapi();
    }
    
    function getParamObject()
    {
        return parent::getParamObject()->appendProperty([
            'category_id' => new Type\Varchar([
                'description' => t('Категория из которой выводить фильтры'),
                'listFromArray' => [['' => t('Назначать автоматически из адреса страницы')] + \Catalog\Model\DirApi::selectList()]
            ]),
            'show_cost_filter' => new Type\Integer([
                'description' => t('Отображать фильтр по цене?'),
                'checkboxView' => [1, 0]
            ]),
            'show_brand_filter' => new Type\Integer([
                'description' => t('Отображать фильтр по бренду?'),
                'default' => 1,
                'checkboxView' => [1, 0]
            ]),
            'show_is_num' => new Type\Integer([
                'description' => t('Отображать фильтр по наличию товара'),
                'checkboxView' => [1, 0]
            ]),
            'expanded' => new Type\ArrayList([
                'description' => t('Отображать всегда развернутыми'),
                'hint' => t('Актуально для тем оформления, где используется сворачивание фильтров'),
                'listFromArray' => [[
                    'cost' => t('Цена'),
                    'brand' => t('Бренд'),
                    'num' => t('Наличие')
                ]],
                'checkboxListView' => true
            ]),
            'show_only_public' => new Type\Integer([
                'description' => t('Отображать только видимые характеристики'),
                'checkboxView' => [1,0]
            ]),
        ]);
    }
    
    function actionIndex()
    {
        $dir_id = $this->getParam('category_id');
        if ($dir_id == ''){
            $dir = urldecode($this->url->get('category', TYPE_STRING));
            $dir_id = (int)$this->dirapi->getIdByAlias($dir);
        }

        $prop_api  = new \Catalog\Model\Propertyapi();
        $prop_list = $prop_api->getGroupProperty($dir_id, true, $this->getParam('show_only_public') ?: null );
        
        $old_version_filters = $this->url->request('f', TYPE_ARRAY); //Для совместимости с предыдущими версиями RS
        $filters = $this->url->request('pf', TYPE_ARRAY, $prop_api->convertOldFilterValues($old_version_filters));

        $basefilters = $this->api->getBaseFilters();

        //Разберем ЧПУ адрес с фильтрами предварительно, если включена опция и они есть
        $decoded_filters = $this->api->decodeDirFilterParamsFromUrl();
        if (isset($decoded_filters['pf'])){
            $filters = $filters + $decoded_filters['pf'];
        }

        $allowable_brand_values = [];
        $filters_allowed_sorted = [];

        if ($this->router->getCurrentRoute()->getId() == 'catalog-front-listproducts') { //Если мы на странице какой-либо категории
            $allowable_values = $this->router->getCurrentRoute()->getExtra('allowable_values');
            $filters_allowed_sorted = $this->router->getCurrentRoute()->getExtra('filters_allowed_sorted');
            //Получим бренды, если они необходимы
            if ($this->getParam('show_brand_filter')){
               $allowable_brand_values = $this->router->getCurrentRoute()->getExtra('allowable_brand_values');
            }
            $money_array = $this->router->getCurrentRoute()->getExtra('money_array');
        }elseif ($dir_id != ''){ //Если мы не в категории, но нам нужны значения из определенной категории категории
            $category = new \Catalog\Model\Orm\Dir($dir_id);
            $dir_ids = $this->dirapi->getChildsId($dir_id);
            //Устанавливаем дополнительные условия фильтрации, если открыта "Виртуальная категория"
            if ($category['is_virtual']) {
                if ($product_ids_by_virtual_dir = $category->getVirtualDir()->getFilteredProductIds($dir_ids)) {
                    $this->api->setFilter('id', $product_ids_by_virtual_dir, 'in');
                }
            }
            //Устанавливаем обычный фильтр по категории
            elseif ($dir_ids)  {
                $this->api->setFilter('dir', $dir_ids, 'in');
            }
            
            $money_array = $this->api->getMinMaxProductsCost();
            $money_array += [ //Данные, необходимые для отображения JS-слайдера
                'step'  => 1,
                'round' => 0,
                'unit'  => \Catalog\Model\CurrencyApi::getDefaultCurrency()->stitle,
                'heterogeneity' => $this->api->getHeterogeneity($money_array['interval_from'], $money_array['interval_to'])
            ];

            $cache_id = $dir_id; //Ключ кэша
            $allowable_values = $this->api->getAllowablePropertyValues($dir_id, $cache_id);
            if ($this->getParam('show_brand_filter')) {
                $allowable_brand_values = $this->api->getAllowableBrandsValues($cache_id);
            }
        }
        
        //Фильтруем значения характеристик в зависимости от состава отображаемых товаров
        $prop_list = $prop_api->filterByAllowedValues($prop_list, $allowable_values);

        $this->view->assign([
            'prop_list' => $prop_list,
            'filters' => $prop_api->cleanNoActiveFilters($old_version_filters ?: $filters),
            'basefilters' => $basefilters,
            'filters_allowed_sorted' => $filters_allowed_sorted,
            'moneyArray' => $money_array,
            'allowable_values' => $allowable_values,
            'brands' => $allowable_brand_values
        ]);
        
        return $this->result->setTemplate( $this->getParam('indexTemplate') );
    }
}