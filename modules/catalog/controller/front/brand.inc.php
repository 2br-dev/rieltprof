<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Controller\Front;

use Catalog\Model\Api as ProductApi;
use Catalog\Model\BrandApi;
use RS\Controller\Front;

/**
 * Контроллер отвечает за просмотр брэнда
 */
class Brand extends Front
{
    const ROUTE_EXTRA_BRAND_ID = 'brand_id';

    /** @var ProductApi */
    public $api;
    /** @var BrandApi */
    public $brand_api;
    public $config;

    function init()
    {
        $this->api = new ProductApi();
        $this->brand_api = new BrandApi();
    }

    function actionIndex()
    {
        $config = $this->getModuleConfig();
        $id = urldecode($this->url->get('id', TYPE_STRING));

        /** @var \Catalog\Model\Orm\Brand $brand */
        $brand = $this->brand_api->getById($id);

        if (!$brand) $this->e404(t('Бренд с таким именем не найден'));
        //Если есть alias и открыта страница с id вместо alias, то редирект
        $this->checkRedirectToAliasUrl($id, $brand, $brand->getUrl());

        $this->router->getCurrentRoute()->addExtra(self::ROUTE_EXTRA_BRAND_ID, $brand['id']); //Сообщаем системе, id просматриваемого бренда

        //Хлебные крошки
        $this->app->breadcrumbs
            ->addBreadCrumb(t("Бренды"), $this->router->getUrl('catalog-front-allbrands'))
            ->addBreadCrumb($brand['title']);

        $this->app->title->addSection($brand['meta_title'] ? $brand['meta_title'] : $brand['title']);
        $this->app->meta->addKeywords($brand['meta_keywords']);
        $this->app->meta->addDescriptions($brand['meta_description']);

        //Получим директории в которых есть товар с заданным производителем
        $dirs = $this->brand_api->getBrandDirs($brand);

        //Получим товары из спец. категорий принадлежащих этому бренду
        $limit = $config['brand_products_cnt'];
        $products = $this->brand_api->getProductsInSpecDirs($brand, $limit);

        if (!empty($products)) {
            //Загружаем только фото и цены, остальные сведения, если нужны нужно подгружать в шаблоне
            $products = $this->api->addProductsPhotos($products);
            $products = $this->api->addProductsCost($products);
            $products = $this->api->addProductsDynamicNum($products);
        }

        $this->view->assign([
            'products' => $products,         //Товары бренда в спец. категориях
            'brand' => $brand,               //Бренд
            'dirs' => $dirs                 //Категории бренда
        ]);

        return $this->result->setTemplate('brand.tpl');
    }
}
