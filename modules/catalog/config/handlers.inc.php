<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Catalog\Config;

use Catalog\Model\Api as ProductApi;
use Catalog\Model\Behavior as CatalogBehavior;
use Catalog\Model\BrandApi;
use Catalog\Model\CostApi;
use Catalog\Model\CsvSchema\Warehouse as CsvSchemaWarehouse;
use Catalog\Model\CurrencyApi;
use Catalog\Model\CustomRoute;
use Catalog\Model\Dirapi;
use Catalog\Model\FavoriteApi;
use Catalog\Model\Log\LogImportYml;
use Catalog\Model\OneClickItemApi;
use Catalog\Model\Orm\Brand;
use Catalog\Model\Orm\Currency;
use Catalog\Model\Orm\Dir;
use Catalog\Model\Orm\Product;
use Catalog\Model\Orm\Typecost;
use Catalog\Model\Orm\WareHouse;
use Catalog\Model\WareHouseApi;
use Crm\Model\Autotask\Ruleif\CreateOneClick;
use Crm\Model\Links\Type\LinkTypeOneClickItem;
use Partnership\Model\Api as PartnershipApi;
use Partnership\Model\Orm\Partner;
use RS\Config\Loader as ConfigLoader;
use RS\Controller\Admin\Helper\CrudCollection;
use RS\Event\Exception as EventException;
use RS\Event\HandlerAbstract;
use RS\Exception as RSException;
use RS\Log\AbstractLog;
use RS\Module\Item as ModuleItem;
use RS\Module\Manager as ModuleManager;
use RS\Orm\AbstractObject;
use RS\Orm\Exception as OrmException;
use RS\Orm\Type as OrmType;
use RS\Html\Table\Type as TableType;
use RS\Router\Route;
use Site\Model\Api as SiteApi;
use Sitemap\Model\Api as SitemapApi;
use Users\Model\Orm\User;
use Users\Model\Orm\UserGroup;

/**
 * Класс предназначен для объявления событий, которые будет прослушивать данный модуль и обработчиков этих событий.
 */
class Handlers extends HandlerAbstract
{
    function init()
    {
        $this
            ->bind('controller.exec.users-admin-ctrl.index')
            ->bind('controller.exec.users-admin-ctrlgroup.index')
            ->bind('initialize')
            ->bind('getroute')
            ->bind('orm.init.users-user')
            ->bind('orm.init.users-usergroup')
            ->bind('orm.afterload.users-user')
            ->bind('orm.afterload.users-usergroup')
            ->bind('orm.beforewrite.users-user')
            ->bind('orm.beforewrite.users-usergroup')
            ->bind('user.auth')
            ->bind('comments.gettypes')
            ->bind('getlogs')
            ->bind('getmenus')
            ->bind('getpages')
            ->bind('meter.recalculate')
            ->bind('crm.deal.getlinktypes')
            ->bind('crm.getifrules')
            ->bind('cron');

        if (\Setup::$INSTALLED) {
            $this->bind('orm.afterwrite.site-site', $this, 'onSiteCreate');
        }
    }

    /**
     * Возвращает классы логирования этого модуля
     *
     * @param AbstractLog[] $list - список классов логирования
     * @return AbstractLog[]
     */
    public static function getLogs($list)
    {
        $list[] = LogImportYml::getInstance();
        return $list;
    }

    /**
     * Добавляет в CRM сделки возможность связи с покупкой в 1 клик
     *
     * @param array $link_types
     * @return array
     * @throws RSException
     */
    public static function crmDealGetLinkTypes($link_types)
    {
        $link_types[] = LinkTypeOneClickItem::getId();
        return $link_types;
    }

    /**
     * Расширяем поведение объекта Пользователь
     */
    public static function initialize()
    {
        User::attachClassBehavior(new CatalogBehavior\UsersUser);
    }

    /**
     * Возвращает счетчик непросмотренных объектов
     *
     * @param int[] $meters - счетчик непросмотренных объектов
     * @return int[]
     */
    public static function meterRecalculate($meters)
    {
        $oneclick_api = new OneClickItemApi();
        $oneclick_meter_api = $oneclick_api->getMeterApi();
        $meters[$oneclick_meter_api->getMeterId()] = $oneclick_meter_api->getUnviewedCounter();

        return $meters;
    }

    /**
     * Возвращает массив маршрутов для системы
     *
     * @param Route[] $routes - массив установленных ранее маршрутов
     * @return Route[]
     */
    public static function getRoute($routes)
    {
        //Просмотр категории продукции
        $routes[] = new CustomRoute('catalog-front-listproducts', [
            '/catalog/{category}/{filters:(.+)}/',
            '/catalog/{category}/',
            '/catalog/'
        ], null, t('Просмотр категории продукции'));

        //Карточка товара
        $routes[] = new Route('catalog-front-product', '/product/{id}/', null, t('Карточка товара'));

        //Сравнение товаров    
        $routes[] = new Route('catalog-front-compare', '/compare/', null, t('Сравнение товаров'));

        //Избранные товары  
        $routes[] = new Route('catalog-front-favorite', '/favorite/', null, t('Избранные товары'));

        //Обработка страницы купить в 1 клик, добавление маршрута    
        $routes[] = new Route('catalog-front-oneclick', '/oneclick/{product_id}/', null, t('Купить в один клик'));

        //Отображение всех брендов в алфавитном порядке
        $routes[] = new Route('catalog-front-allbrands', '/brand/all/', null, t('Список брендов'));

        //Отображение отдельно брендов
        $routes[] = new Route('catalog-front-brand', '/brand/{id}/', null, t('Просмотр отдельного бренда'));

        //Отображение отдельно склада
        $routes[] = new Route('catalog-front-warehouse', '/warehouse/{id}/', null, t('Просмотр отдельного склада'));

        return $routes;
    }

    /**
     * @param $params
     * @throws RSException
     * @throws EventException
     */
    public static function onSiteCreate($params)
    {
        if ($params['flag'] == AbstractObject::INSERT_FLAG) {
            //Добавляем цену по-умолчанию
            $site = $params['orm'];
            $defaultCost = new Typecost();
            $defaultCost->getFromArray([
                'site_id' => $site['id'],
                'title' => t('Розничная'),
                'type' => 'manual',
                'round' => 0
            ])->insert();

            //Добавляем валюту по-умолчанию
            $defaultCurrency = new Currency();
            $defaultCurrency->getFromArray([
                'site_id' => $site['id'],
                'title' => 'RUB',
                'stitle' => t('р.'),
                'is_base' => 1,
                'ratio' => 1,
                'public' => 1,
                'default' => 1
            ])->insert();


            $catalog_config = ConfigLoader::byModule('catalog', $site['id']);
            if ($catalog_config) {
                $catalog_config['default_cost'] = $defaultCost['id'];
                $catalog_config->update();
            }

            $module = new ModuleItem('catalog');
            $installer = $module->getInstallInstance();
            $installer->importCsv(new CsvSchemaWarehouse(), 'warehouse', $site['id']);
        }
    }

    /**
     * Расширяем объект User, добавляя в него доп свойство - тип цены
     *
     * @param User $user
     */
    public static function ormInitUsersUser(User $user)
    {
        $user->getPropertyIterator()->append([
            t('Настройка цен'),
                'user_cost' => new OrmType\ArrayList([
                    'description' => t('Персональная цена'),
                    'template' => '%catalog%/form/user/personal_price.tpl',
                    'cost_api' => CostApi::getInstance(),
                ]),
                'cost_id' => new OrmType\Varchar([
                    'description' => t('Персональная цена (сериализованная)'),
                    'visible' => false,
                    'maxLength' => 1000,
                ]),
        ]);
    }

    /**
     * Расширяем объект UserGroup, добавляя в него доп свойство - тип цены
     *
     * @param UserGroup $orm
     */
    public static function ormInitUsersUserGroup(UserGroup $orm)
    {
        $orm->getPropertyIterator()->append([
            t('Настройка цен'),
            'user_cost' => new OrmType\ArrayList([
                'description' => t('Персональная цена'),
                'hint' => t('Если пользователь состоит более чем в одной группе с персональной ценой - более приоритетной считается группа которая выше в списке'),
                'template' => '%catalog%/form/user/personal_price.tpl',
                'cost_api' => CostApi::getInstance(),
            ]),
            'cost_id' => new OrmType\Varchar([
                'description' => t('Персональная цена (сериализованная)'),
                'visible' => false,
                'maxLength' => 1000,
            ]),
        ]);
    }

    /**
     * Добавляем к списку пользователей колонку персональной цены
     *
     * @param CrudCollection $helper
     */
    public static function controllerExecUsersAdminCtrlIndex(CrudCollection $helper)
    {
        $tpl = '%catalog%/form/user/column_personal_price.tpl';
        $properties = [
            'cost_list' => CostApi::staticSelectList(),
            'site_list' => SiteApi::staticSelectList(),
        ];
        $helper['table']->getTable()->addColumn(new TableType\Usertpl('user_cost', t('Персональная цена'), $tpl, $properties), -1);
    }

    /**
     * Добавляем к списку пользователей колонку персональной цены
     *
     * @param CrudCollection $helper
     */
    public static function controllerExecUsersAdminCtrlGroupIndex(CrudCollection $helper)
    {
        $tpl = '%catalog%/form/user/column_personal_price.tpl';
        $properties = [
            'cost_list' => CostApi::staticSelectList(),
            'site_list' => SiteApi::staticSelectList(),
        ];
        $helper['table']->getTable()->addColumn(new TableType\Usertpl('user_cost', t('Персональная цена'), $tpl, $properties), -1);
    }

    public static function ormAfterLoadUsersUser($params)
    {
        /** @var User $user */
        $user = $params['orm'];

        $user['user_cost'] = @unserialize($user['cost_id']) ?: [];
    }

    /**
     * Функция срабытывает перед сохранением пользователя
     * Сериализует массив c ценами сайтов для поля cost_id
     *
     * @param array $params - массив с параметрами
     */
    public static function ormBeforeWriteUsersUser($params)
    {
        /** @var User $user */
        $user = $params['orm'];

        if ($user->isModified('user_cost')) {
            $user['cost_id'] = serialize($user['user_cost']);
        }
    }

    public static function ormAfterLoadUsersUserGroup($params)
    {
        /** @var UserGroup $group */
        $group = $params['orm'];

        $group['user_cost'] = @unserialize($group['cost_id']) ?: [];
    }

    /**
     * Функция срабытывает перед сохранением группы пользователей
     * Сериализует массив c ценами сайтов для поля cost_id
     *
     * @param array $params - массив с параметрами
     */
    public static function ormBeforeWriteUsersUserGroup($params)
    {
        /** @var UserGroup $group */
        $group = $params['orm'];

        if ($group->isModified('user_cost')) {
            $group['cost_id'] = serialize($group['user_cost']);
        }
    }

    /**
     * Действия после авторизации пользователя
     */
    public static function userAuth()
    {
        FavoriteApi::getInstance()->mergeFavorites();
    }

    /**
     * Добавляет новые страницы в Sitemap
     *
     * @param array $urls - массив адресов из sitemap
     * @return array
     * @throws RSException
     * @throws OrmException
     */
    public static function getPages($urls)
    {
        //Добавим страницы из категорий товаров в sitemap
        $api = new Dirapi();
        $api->setFilter('public', 1);
        $page = 1;

        //Если партёрский сайт, то изменим с учетом партнёрского сайта
        if (ModuleManager::staticModuleExists('partnership') && ModuleManager::staticModuleEnabled('partnership')) {
            $partner = PartnershipApi::getCurrentPartner();

            /** @var Partner $partner */
            if ($partner) {
                $partner_dirs = $partner->getAllowFolderList();
                if (!empty($partner_dirs)) {
                    $api->setFilter('id', $partner_dirs, 'in');
                }
            }
        }

        while ($list = $api->getList($page, 100)) {
            $page++;
            foreach ($list as $dir) {
                /** @var Dir $dir */
                $urls[] = [
                    'loc' => $dir->getUrl(),
                    'priority' => '0.7'
                ];
            }
        }

        //Добавим страницы из каталога товаров в sitemap
        $api = new ProductApi();
        $config = ConfigLoader::byModule(__CLASS__);
        $api->setFilter('public', 1);
        if ($config['hide_unobtainable_goods'] == 'Y') {
            $api->setFilter('num', '0', '>');
        }
        //Если это партнёрский сайт, то добавим дополнительные условия
        if (ModuleManager::staticModuleExists('partnership') && ModuleManager::staticModuleEnabled('partnership')) {
            $partner = PartnershipApi::getCurrentPartner();
            /** @var Partner $partner */
            if ($partner) {
                $partner_dirs = $partner->getAllowFolderList();
                if (!empty($partner_dirs)) {
                    $api->setFilter('dir', $partner_dirs, 'in');
                }
            }
        }
        $page = 1;
        while ($list = $api->getList($page, 100)) {
            $page++;
            foreach ($list as $product) {
                /** @var Product $product */
                $one_url = [
                    'loc' => $product->getUrl(),
                    'priority' => '0.5'
                ];
                foreach ($product->getImages() as $image) {
                    $one_url[] = [
                        SitemapApi::ELEMENT_NAME_KEY => 'image:image',
                        SitemapApi::ELEMENT_MAP_TYPE_KEY => 'google',
                        'image:loc' => $image->getUrl(800, 800, 'xy', true)
                    ];
                }
                $urls[] = $one_url;
            }
        }

        //Добавим страницы брендов в sitemap
        $api = new BrandApi();
        $api->setFilter('public', 1);
        $page = 1;
        while ($list = $api->getList($page, 100)) {
            /** @var Brand[] $list */
            $page++;
            foreach ($list as $brand) {
                $urls[] = [
                    'loc' => $brand->getUrl()
                ];
            }
        }

        //Добавим страницы складов в sitemap
        $api = new WareHouseApi();
        $api->setFilter('public', 1);
        $api->setFilter('use_in_sitemap', 1);
        $page = 1;
        /** @var WareHouse[] $list */
        while ($list = $api->getList($page, 100)) {
            $page++;
            foreach ($list as $warehouse) {
                $urls[] = [
                    'loc' => $warehouse->getUrl()
                ];
            }
        }

        return $urls;
    }

    /**
     * Возвращает пункты меню этого модуля в виде массива
     *
     * @param array $items - ранее установленные пункты меню
     * @return array
     */
    public static function getMenus($items)
    {
        $items[] = [
            'title' => t('Товары'),
            'alias' => 'products',
            'link' => '%ADMINPATH%/catalog-ctrl/',
            'sortn' => 20,
            'typelink' => 'link',
            'parent' => 0
        ];
        $items[] = [
            'title' => t('Каталог товаров'),
            'alias' => 'catalog',
            'link' => '%ADMINPATH%/catalog-ctrl/',
            'sortn' => 0,
            'typelink' => 'link',
            'parent' => 'products'
        ];
        $items[] = [
            'title' => t('Характеристики'),
            'alias' => 'property',
            'link' => '%ADMINPATH%/catalog-propctrl/',
            'sortn' => 1,
            'typelink' => 'link',
            'parent' => 'products'
        ];
        $items[] = [
            'title' => t('Склады'),
            'alias' => 'warehouse',
            'link' => '%ADMINPATH%/catalog-warehousectrl/',
            'typelink' => 'link',
            'sortn' => 2,
            'parent' => 'products'
        ];
        $items[] = [
            'title' => t('Складской учет'),
            'alias' => 'inventory_control',
            'link' => '%ADMINPATH%/catalog-warehousectrl/',
            'typelink' => 'link',
            'sortn' => 2,
            'parent' => 'products'
        ];
        $items[] = [
            'title' => t('Списания'),
            'alias' => 'write_off',
            'link' => '%ADMINPATH%/catalog-inventorywriteoffctrl/',
            'typelink' => 'link',
            'sortn' => 1,
            'parent' => 'inventory_control'
        ];
        $items[] = [
            'title' => t('Оприходования'),
            'alias' => 'arrival',
            'link' => '%ADMINPATH%/catalog-inventoryarrivalctrl/',
            'typelink' => 'link',
            'sortn' => 2,
            'parent' => 'inventory_control'
        ];
        $items[] = [
            'title' => t('Инвентаризации'),
            'alias' => 'inventorization',
            'link' => '%ADMINPATH%/catalog-inventorizationctrl/',
            'typelink' => 'link',
            'sortn' => 3,
            'parent' => 'inventory_control'
        ];
        $items[] = [
            'title' => t('Перемещения'),
            'alias' => 'movement',
            'link' => '%ADMINPATH%/catalog-inventorymovementctrl/',
            'typelink' => 'link',
            'sortn' => 5,
            'parent' => 'inventory_control'
        ];
        $items[] = [
            'title' => t('Резервирования'),
            'alias' => 'reservation',
            'link' => '%ADMINPATH%/catalog-inventoryreservationctrl/',
            'typelink' => 'link',
            'sortn' => 6,
            'parent' => 'inventory_control'
        ];
        $items[] = [
            'title' => t('Ожидания'),
            'alias' => 'waitings',
            'link' => '%ADMINPATH%/catalog-inventorywaitingsctrl/',
            'typelink' => 'link',
            'sortn' => 7,
            'parent' => 'inventory_control'
        ];
        $items[] = [
            'title' => t('Бренды'),
            'alias' => 'brand',
            'link' => '%ADMINPATH%/catalog-brandctrl/', //здесь %ADMINPATH% - URL админ. панели; shoplist - модуль; control - класс фронт контроллера
            'typelink' => 'link', //Тип пункта меню - ссылка
            'sortn' => 2,
            'parent' => 'products'
        ];
        $items[] = [
            'title' => t('Справочник цен'),
            'alias' => 'costs',
            'link' => '%ADMINPATH%/catalog-costctrl/',
            'sortn' => 3,
            'typelink' => 'link',
            'parent' => 'products'
        ];
        $items[] = [
            'title' => t('Единицы измерения'),
            'alias' => 'unit',
            'link' => '%ADMINPATH%/catalog-unitctrl/',
            'sortn' => 4,
            'typelink' => 'link',
            'parent' => 'products'
        ];
        $items[] = [
            'title' => t('Валюты'),
            'alias' => 'currency',
            'link' => '%ADMINPATH%/catalog-currencyctrl/',
            'sortn' => 5,
            'typelink' => 'link',
            'parent' => 'products'
        ];

        $shop_module_exists = ModuleManager::staticModuleExists('shop');

        $items[] = [
            'title' => t('Покупки в 1 клик'),
            'alias' => 'oneclick',
            'link' => '%ADMINPATH%/catalog-oneclickctrl/',
            'sortn' => $shop_module_exists ? 2 : 15,
            'typelink' => 'link',
            'parent' => $shop_module_exists ? 'orders' : 'products'
        ];
        return $items;
    }

    /**
     * Периодическое обновление кусов валют
     *
     * @param array $params - массив параметров
     * @throws RSException
     */
    public static function cron($params)
    {
        $interval = ConfigLoader::byModule('catalog')['cbr_auto_update_interval'];
        if ($interval) {
            foreach ($params['minutes'] as $minute) {
                if ((($minute - 60) % $interval) == 0) {
                    $api = new CurrencyApi();
                    echo t("\n--- Обновление курсов валют: ");
                    echo ($api->getCBRFCourseWithUpdate()) ? t("успех") : t("неудача");
                    echo " ---\n";
                }
            }
        }
    }

    /**
     * Регистрируем тип комментариев "комментарии к товару"
     *
     * @param array $list - массив установленных ранее типов комментариев
     * @return array
     */
    public static function commentsGetTypes($list)
    {
        $list[] = new \Catalog\Model\CommentType\Product();
        return $list;
    }

    /**
     * Добавляет возможность создания автозадач при создании покупки в 1 клик
     *
     * @param array $list
     * @return array
     */
    public static function crmGetIfRules($list)
    {
        $list[] = new CreateOneClick();

        return $list;
    }

    /**
     * Для совместимости с предыдущими версиями
     *
     * @deprecated
     * @param $params
     */
    public static function ormInitCatalogProduct($params)
    {}
}
