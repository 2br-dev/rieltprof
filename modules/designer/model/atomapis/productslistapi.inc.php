<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Model\AtomApis;

use Catalog\Model\Api;
use Catalog\Model\DirApi;
use RS\Config\Loader as ConfigLoader;
use RS\Module\AbstractModel\BaseModel;

/**
 * Класс API для компонента списка товаров
 */
class ProductsListApi extends ProductApi
{
    /**
     * Возвращает список товароы для определённой категории
     *
     * @param integer $root - корнево каталог
     * @param integer $page - нужная страница
     * @param integer $pageSize - количества на странице
     * @param array $photo_params - параметры для отображения фото
     *
     * @return array
     * @throws \RS\Exception
     */
    function getProducts($root = 0, $page = 1, $pageSize = 20, $photo_params = [])
    {
        $category = new \Catalog\Model\Orm\Dir($root);
        $api      = new Api();
        $dirApi   = new DirApi();

        $dir_ids = $dirApi->getChildsId($root);

        //Устанавливаем дополнительные условия фильтрации, если открыта "Виртуальная категория"
        if ($category['is_virtual']) {
            if ($product_ids_by_virtual_dir = $category->getVirtualDir()->getFilteredProductIds($dir_ids)) {
                $api->setFilter('id', $product_ids_by_virtual_dir, 'in');
            }
        } //Устанавливаем обычный фильтр по категории
        elseif ($dir_ids) {
            $api->setFilter('dir', $dir_ids, 'in');
        }

        $api->setFilter('public', 1);

        $config = ConfigLoader::byModule($this);
        if ($config['hide_unobtainable_goods'] == 'Y') {
            $api->setFilter('num', '0', '>');
        }

        $list = $api->getList($page, $pageSize);

        $arr = [];
        foreach ($list as $product){
            /**
             * @var \Catalog\Model\Orm\Product $product
             */
            $info = \ExternalApi\Model\Utils::extractOrm($product);
            $image = $product->getMainImage();
            $info['url']            = $product->getUrl();
            $info['image']['url']   = $image->getUrl((int)$photo_params['width'], (int)$photo_params['height'], $photo_params['type']);
            $info['image']['title'] = $image['title'] ? $image['title'] : $product['title'];
            $info['cost']           = $product->getCost();
            if ($product->getOldCost(null, false) > 0){
                $info['old_cost'] = $product->getOldCost();
            }
            $info['currency'] = $product->getCurrency();
            $this->getProductButtons($info, $product, null);
            $arr[] = $info;
        }
        return $arr;
    }

    /**
     * Возвращает дерево категорий для компонента в виде массива для определённого корневого каталога
     *
     * @param integer $root - корнево каталог
     *
     * @return array
     * @throws \RS\Exception
     */
    function getTreeForCategory($root = 0)
    {
        $arr  = [];
        if ($root == 0){
            $arr[] = [
                'id'     => 0,
                'title'  => t('Корневая директория'),
                'public' => 1,
                'link'   => '/catalog/',
                'childscount' => 0,
                'childs' => []
            ];
        }
        $list = DirApi::getInstance()
                ->setFilter('public', 1)
                ->getTreeList($root);

        foreach ($list as $dir){
            $childs = $this->getTreeForCategory($dir['fields']['id']);
            $arr[] = [
                'id'     => $dir['fields']['id'],
                'title'  => $dir['fields']['name'],
                'public' => $dir['fields']['public'],
                'link'   => $dir['fields']->getUrl(),
                'childscount' => !empty($childs) ? $dir->getChildsCount() : 0,
                'childs' => $childs
            ];
        }

        return $arr;
    }
}