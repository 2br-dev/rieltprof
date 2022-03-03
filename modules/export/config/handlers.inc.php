<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Export\Config;

use Catalog\Model\Orm\Dir;
use Catalog\Model\Orm\Offer;
use Catalog\Model\Orm\Product;
use Export\Model\Api;
use Export\Model\ExportType;
use Export\Model\ExportType\Vkontakte\Utils\VkTools;
use Export\Model\Orm\ExportProfile;
use Export\Model\Orm\ExternalProductLink;
use Export\Model\Orm\Vk\VkCategoryLink;
use RS\Cache\Manager;
use RS\Router\Manager as RouterManager;
use RS\Config\Loader;
use RS\Event\HandlerAbstract;
use RS\Router\Route;
use RS\Orm\Type as OrmType;
use RS\Router\RouteAbstract;

/**
 * Класс содержит обработчики различных событий
 */
class Handlers extends HandlerAbstract
{
    function init()
    {
        $this->bind('getmenus');
        $this->bind('getroute');
        $this->bind('export.gettypes');
        $this->bind('cron');
        $this->bind('orm.init.catalog-dir');
        $this->bind('orm.init.catalog-product');
        $this->bind('orm.init.catalog-offer');
        $this->bind('orm.afterwrite.catalog-dir');
        $this->bind('orm.afterwrite.catalog-product');
        $this->bind('orm.beforemultiupdate.catalog-dir', null, null, 0);
    }

    /**
     * Возвращает маршруты данного модуля
     *
     * @param RouteAbstract[] $routes список маршрутов
     * @return array
     */
    public static function getRoute(array $routes)
    {
        $routes[] = new Route('export-front-gate', [
            '/site{site_id}/export-{export_type}-{export_id}.xml',
            '/site{site_id}/export-{export_type}-{export_id}/',
        ], null, t('Шлюз экспорта данных'), true);

        return $routes;
    }

    /**
     * Возвращает список доступных типов экспорта
     *
     * @param ExportType\AbstractType[] $list список доступных типов экспорта
     * @return array
     */
    public static function exportGetTypes($list)
    {
        $list[] = new ExportType\Yandex\Yandex();
        $list[] = new ExportType\MailRu\MailRu();
        $list[] = new ExportType\Google\Google();
        $list[] = new ExportType\Avito\Avito();
        $list[] = new ExportType\Facebook\Facebook();
        $list[] = new ExportType\Vkontakte\Vkontakte();
        return $list;
    }

    /**
     * Возвращает пункты меню этого модуля в виде массива
     *
     */
    public static function getMenus($items)
    {
        $items[] = [
            'title' => t('Экспорт данных'),
            'alias' => 'export',
            'link' => '%ADMINPATH%/export-ctrl/',
            'typelink' => 'link',
            'parent' => 'products',
            'sortn' => 7
        ];
        return $items;
    }


    /**
     * Запускает экспорт данных по API
     *
     * @throws \RS\Orm\Exception
     * @throws \Exception
     */
    public static function cron($params)
    {
        $export_api = new Api();
        $export_api->setFilter('is_enabled', 1);
        $export_api->setFilterExchangableByApi();

        $list = Manager::obj()
            ->expire(0)
            ->watchTables($export_api->getElement())
            ->request([$export_api, 'getList']);

        foreach($list as $profile) {

            $profile_type = $profile->getTypeObject();
            $time_to_autorun = false;
            foreach ($params['minutes'] as $minute) {
                if ( ($profile_type['life_time']>0 && ($minute % $profile_type['life_time']) == 0)
                        || ($profile_type['life_time'] == 1440 && $minute == 0))
                {
                    $time_to_autorun = true;
                    break;
                }
            }

            //Если вручную запущен экспорт, Если включен авто-экспорт, Если экспорт незавершен
            $is_planned = $export_api->isPlannedExchange($profile);

            if ($profile['is_exporting'] || $is_planned || $time_to_autorun) {

                \RS\Router\Manager::obj()->initRoutes();
                echo t('-- Запуск экспорта профиля %title --'.PHP_EOL, ['title' => $profile['title']]);
                $result = $profile_type->doExchange($profile);

                if ($result !== true) {
                    $profile['is_exporting'] = 1;
                    echo t('-- Завершение одного шага экспорта профиля %title. Обработано %n товаров --'.PHP_EOL, ['title' => $profile['title'], 'n' => $result ]);
                } else {
                    $profile['is_exporting'] = 0;
                }

                $profile->update();
                if ($is_planned) {
                    $export_api->endPlane($profile);
                }

                echo t('-- Завершение экспорта профиля %title --'.PHP_EOL, ['title' => $profile['title']]);
            }
        }
    }

    /**
     * Добавляет поле в категориях для категорий ВК
     *
     * @param Dir $dir
     */
    public static function ormInitCatalogDir(Dir $dir)
    {
        $dir->getPropertyIterator()->append([
            t('Экспорт в ВКонтакте'),
            'vk_dir' => new OrmType\ArrayList( [
                'description' => t('Сопоставление категорий с ВК'),
                'template' => '%export%/vk/vk_cat_export.tpl',
                'vk_tools' => new VkTools(),
                'export_vk_link' => new VkCategoryLink(),
                'rootVisible' => false
            ]),
        ]);
    }

    /**
     * Добавляем поле в объект комплектации
     *
     * @param Offer $offer
     */
    public static function ormInitCatalogOffer(Offer $offer)
    {
        $offer->getPropertyIterator()->append([
            t('Экспорт'),
            'market_sku' => new OrmType\Varchar( [
                'description' => t('SKU на Яндекс.Маркете (market-sku)'),
                'hint' => t('Используется в выгрузке YML на Яндекс.Маркет')
            ]),
        ]);
    }

    /**
     * Добавляет поле в объект товара
     *
     * @param Product $product
     */
    public static function ormInitCatalogProduct(Product $product)
    {
        $product->getPropertyIterator()->append([
            t('Экспорт'),
            'market_sku' => new OrmType\Varchar( [
                'description' => t('SKU на Яндекс.Маркете (market-sku)'),
                'hint' => t('Используется в выгрузке YML на Яндекс.Маркет')
            ]),
        ]);
    }


    /**
     * Сохраняет указанную категорию ВК для категории на сайте
     *
     * @param $params
     * @throws \RS\Db\Exception
     * @throws \RS\Exception
     */
    public static function ormAfterWriteCatalogDir($params)
    {
        /**
         * @var $dir Dir
         */
        $dir = $params['orm'];
        if ($dir->isModified('vk_dir')) {
            VkCategoryLink::saveLinks($dir['id'], $dir['vk_dir']);
        }
    }

    /**
     * Помещает в очередь на экспорт товар (все его комплектации) после обновления
     *
     * @param $params
     * @throws \RS\Db\Exception
     */
    public static function ormAfterWriteCatalogProduct($params)
    {
        $config = Loader::byModule(__CLASS__);
        if ($config['check_product_change']) {
            /**
             * @var Product $product
             */
            $product = $params['orm'];
            ExternalProductLink::activateExport($product['id'], ExternalProductLink::EXPORT_ITEM_PRODUCT);
        }
    }

    /**
     * Помещает в очередь на экспорт комплектацию после обновления
     *
     * @param $params
     * @throws \RS\Db\Exception
     * @throws \RS\Exception
     */
    public static function ormAfterWriteCatalogOffer($params)
    {
        $config = Loader::byModule(__CLASS__);
        if ($config['check_product_change']) {
            $offer = $params['orm'];
            if (!isset($offer['MASS_SAVE_OFFER_OPERATION'])) {
                //Выполняем только, если это единичная коррекция комплектации, иначе ormAfterWriteCatalogProduct - все сделает
                ExternalProductLink::activateExport($offer['id'], ExternalProductLink::EXPORT_ITEM_OFFER);
            }
        }
    }

    /**
     * Обработчик мультиредактирования категорий
     *
     * @param $params
     * @throws \RS\Db\Exception
     */
    public static function ormBeforeMultiupdateCatalogDir($params)
    {
        $ids = $params['ids'];
        if (isset($params['data']['vk_dir'])) {
            VkCategoryLink::saveLinks($ids, $params['data']['vk_dir']);
            unset($params['data']['vk_dir']);
        }

        return $params;
    }
}