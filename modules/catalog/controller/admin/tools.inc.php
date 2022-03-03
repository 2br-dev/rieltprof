<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Controller\Admin;

use Catalog\Model\Api as ProductApi;
use Catalog\Model\BrandApi;
use Catalog\Model\Dirapi;
use Catalog\Model\OfferApi;
use Catalog\Model\OneClickItemApi;
use Catalog\Model\Orm\Offer;
use Catalog\Model\Orm\OneClickItem;
use Catalog\Model\Orm\Product;
use Catalog\Model\PropertyApi;
use Catalog\Model\PropertyValueApi;
use RS\Controller\Admin\Front;
use RS\Orm\Request as OrmRequest;
use RS\Site\Manager as SiteManager;

/**
* Содержит действия по обслуживанию
*/
class Tools extends Front
{
    /**
     * Удаление несвязанных характеристик
     *
     * @return \RS\Controller\Result\Standard
     */
    public function actionAjaxCleanProperty()
    {
        $api = new PropertyApi();
        $count = $api->cleanUnusedProperty();
        
        return $this->result->setSuccess(true)->addMessage(t('Удалено %0 характеристик', [$count]));
    }

    /**
     * Добавление ЧПУ товарам и категориям
     *
     * @return \RS\Controller\Result\Standard
     */
    public function actionAjaxCheckAliases()
    {
        $api = new ProductApi();
        $product_count = $api->addTranslitAliases();
        if (is_array($product_count)) {

            //Обновление нужно продолжить
            return $this->result
                ->setSuccess(true)
                ->addSection('repeat',true)
                ->addSection('count',$product_count['count'])
                ->addSection('queryParams', [
                    'url' => $this->url->getSelfUrl(),
                    'data'=> [
                        'ajax' => 1,
                        'count' => $product_count['count'],
                    ]
                ])
                ->addSection('redirect',\RS\Router\Manager::obj()->getAdminUrl('ajaxCheckAliases'));


        }elseif (is_integer($product_count)){

            $dir_api = new Dirapi();
            $dir_count = $dir_api->addTranslitAliases();

            if (is_array($dir_count)) {

                //Обновление нужно продолжить
                return $this->result
                    ->setSuccess(true)
                    ->addSection('repeat',true)
                    ->addSection('count',$dir_count['count'])
                    ->addSection('queryParams', [
                        'url' => $this->url->getSelfUrl(),
                        'data'=> [
                            'ajax' => 1,
                            'count' => $dir_count['count'],
                        ]
                    ])
                    ->addSection('redirect',\RS\Router\Manager::obj()->getAdminUrl('ajaxCheckAliases'));
            }elseif (is_integer($dir_count)){
                return $this->result->setSuccess(true)->addMessage(t('Обновлено %0 товаров, %1 категорий', [$product_count, $dir_count]));
            }

            return $this->result->setSuccess(false)->addMessage(t('Произошли ошибки при обновлении категоирй.'));
        }

        return $this->result->setSuccess(false)->addMessage(t('Произошли ошибки при обновлении товаров.'));
    }

    /**
     * Добавление ЧПУ брендам
     *
     * @return \RS\Controller\Result\Standard
     */
    public function actionAjaxCheckBrandsAliases()
    {
        $api = new BrandApi();
        $brands_count = $api->addTranslitAliases();

        return $this->result->setSuccess(true)->addMessage(t('Обновлено %0 брендов', [$brands_count]));
    }

    /**
     * Добавление ЧПУ характеристикам и их значениям
     *
     * @return \RS\Controller\Result\Standard
     */
    public function actionAjaxCheckPropertyAliases()
    {
        $api = new PropertyApi();
        $property_count = $api->addTranslitAliases();

        $api = new PropertyValueApi();
        $property_value_count = $api->addTranslitAliases();

        return $this->result->setSuccess(true)->addMessage(t('Обновлено %0 характеристик и значений характеристик %1', [$property_count, $property_value_count]));
    }

    /**
     * Удаляет несвязанные комплектации
     *
     * @return \RS\Controller\Result\Standard
     */
    public function actionAjaxCleanOffers()
    {
        $api = new OfferApi();
        $delete_count = $api->cleanUnusedOffers();
        
        return $this->result->setSuccess(true)->addMessage(t('Удалено %0 комплектаций', [$delete_count]));
    }

    /**
     * Удаляет несвязанные сопутствующие товары
     *
     * @return \RS\Controller\Result\Standard
     */
    public function actionAjaxCleanRelatedProducts()
    {
        $api = new ProductApi();
        $delete_count = $api->cleanUnusedRelatedProducts();

        return $this->result->setSuccess(true)->addMessage(t('Обновлены сопутствующие и рекомендованные товары', [$delete_count]));
    }

    /**
     * Переиндексация товаров
     *
     * @return \RS\Controller\Result\Standard
     */
    public function actionajaxReIndexProducts()
    {
        $config = $this->getModuleConfig();
        $property_index = in_array('properties', $config['search_fields']);
        
        $api = new ProductApi();
        $count = 0;
        $page = 1;
        /** @var Product[] $list */
        while($list = $api->getList($page, 200)) {
            if ($property_index) {
                $list = $api->addProductsProperty($list);
            }
            
            foreach($list as $product) {
                $product->updateSearchIndex();
            }
            $count += count($list);
            $page++;
        }
        
        return $this->result->setSuccess(true)->addMessage(t('Обновлено %0 товаров', [$count]));
    }
    
    /**
    * Сброс всех хешей импорта
    *
    * @return \RS\Controller\Result\Standard
    */
    public function actionAjaxCleanImportHash()
    {
        // товары
        OrmRequest::make()
            ->update(new Product())
            ->set(['import_hash' => null])
            ->where(['site_id' => SiteManager::getSiteId()])
            ->exec();
        // комплектации
        OrmRequest::make()
            ->update(new Offer())
            ->set(['import_hash' => null])
            ->where(['site_id' => SiteManager::getSiteId()])
            ->exec();
        
        return $this->result->setSuccess(true)->addMessage(t('Хеши импорта удалены'));
    }

    /**
     * Выполняет поиск ID покупки в 1 клик по строке
     *
     * @return string
     */
    public function actionAjaxSearchOneClickItem()
    {
        $api = new OneClickItemApi();

        $term = $this->url->request('term', TYPE_STRING);
        $cross_multisite = $this->url->request('cross_multisite', TYPE_INTEGER);

        if ($cross_multisite) {
            //Устанавливаем поиск по всем мультисайтам
            $api->setMultisite(false);
        }
        /** @var OneClickItem[] $list */
        $list = $api->search($term, ['id', 'title', 'user_fio', 'user_phone', 'user'], 8);

        $json = [];
        foreach ($list as $one_click) {

            $user = $one_click->getUser();
            $json[] = [
                'label' => t('Покупка в 1 клик №%num от %date', [
                    'num' => $one_click['id'],
                    'date' => date('d.m.Y', strtotime($one_click['dateof']))
                ]),
                'id' => $one_click['id'],
                'desc' => t('Покупатель').':'.$user->getFio().
                    ($user['id'] ? "({$user['id']})" : '').
                    ($user['is_company'] ? t(" ; {$user['company']}(ИНН:{$user['company_inn']})") : '')
            ];
        }

        return json_encode($json);
    }

    /**
     * Создание основной комплектации у товаров
     *
     * @return \RS\Controller\Result\Standard
     */
    public function actionAjaxCreateMainOffers()
    {
        $exist = OrmRequest::make()
            ->select('product_id')
            ->from(new Offer())
            ->where(['site_id' => SiteManager::getSiteId()])
            ->groupby('product_id')
            ->toSql();

        $q = OrmRequest::make()
            ->select('id')
            ->from(new Product())
            ->where(['site_id' => SiteManager::getSiteId()]);
        if ($exist) {
            $q->where("id not in ($exist)");
        }
        $empty_products = $q->exec()->fetchSelected(null, 'id');

        foreach ($empty_products as $id) {
            $offer = new Offer();
            $offer['product_id'] = $id;
            $offer['sortn'] = 0;
            $offer->insert();
        }

        return $this->result->setSuccess(true)->addMessage(t('Обновлено %0 товаров', [count($empty_products)]));
    }
}
