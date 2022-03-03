<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Site\Model;

use Article\Model\CatApi;
use Banners\Model\BannerApi;
use Catalog\Model\BrandApi;
use Catalog\Model\CostApi;
use Catalog\Model\CurrencyApi;
use Catalog\Model\Dirapi;
use Catalog\Model\OneClickItemApi;
use Catalog\Model\PropertyApi;
use Catalog\Model\PropertyDirApi;
use Catalog\Model\UnitApi;
use Catalog\Model\WareHouseApi;
use Feedback\Model\FieldApi as FeedbackFieldApi;
use Feedback\Model\FormApi as FeedbackFormApi;
use Feedback\Model\ResultApi as FeedbackResultApi;
use RS\Db\Adapter as DbAdapter;
use RS\Module\AbstractModel\EntityList;
use RS\Orm\AbstractObject;
use RS\Orm\Request as OrmRequest;
use RS\Site\Manager as SiteManager;
use Site\Model\Orm\Site;
use Users\Model\Orm\AccessMenu;
use Users\Model\Orm\AccessModuleRight;
use Users\Model\Orm\AccessSite;

class Api extends EntityList
{
    public function __construct()
    {
        parent::__construct(new Orm\Site, [
            'sortField' => 'sortn',
            'defaultOrder' => 'sortn',
            'nameField' => 'title'
        ]);
    }

    /**
     * Копирует права групп пользователей к сайту
     *
     * @param Site|null $site_to - сайт на который происходит копирование, если не указан то текущий сайт
     * @param Site|null $site_from - сайт с которого происходит копирование, если не указан то сайт по умолчанию
     * @return void
     */
    public static function copyGroupRights(Site $site_to = null, Site $site_from = null)
    {
        if ($site_to === null) {
            $site_to = SiteManager::getSite();
        }
        if ($site_from === null) {
            $site_from = OrmRequest::make()
                ->from(new Site())
                ->where(['default' => 1])
                ->object();
        }
        if ($site_to['id'] == $site_from['id']) {
            return;
        }

        self::copyAccessModuleGroupRights($site_to, $site_from);
        self::copyAdminMenuGroupRights($site_to, $site_from);
        self::copyUserMenuGroupRights($site_to, $site_from);
        self::copySiteGroupRights($site_to, $site_from);
    }

    /**
     * Копирует права на доступ к модулям
     *
     * @param Site $site_to - сайт на который происходит копирование
     * @param Site $site_from - сайт с которого происходит копирование
     * @return void
     */
    protected static function copyAccessModuleGroupRights(Site $site_to, Site $site_from)
    {
        self::copyRights($site_to, $site_from, new AccessModuleRight());
    }

    /**
     * Копирует права на доступ к меню администратора
     *
     * @param Site $site_to - сайт на который происходит копирование
     * @param Site $site_from - сайт с которого происходит копирование
     * @return void
     */
    protected static function copyAdminMenuGroupRights(Site $site_to, Site $site_from)
    {
        self::copyRights($site_to, $site_from, new AccessMenu(), [
            'menu_type' => AccessMenu::ADMIN_MENU_TYPE,
        ]);
    }

    /**
     * Копирует права на доступ к меню пользователя
     *
     * @param Site $site_to - сайт на который происходит копирование
     * @param Site $site_from - сайт с которого происходит копирование
     * @return void
     */
    protected static function copyUserMenuGroupRights(Site $site_to, Site $site_from)
    {
        self::copyRights($site_to, $site_from, new AccessMenu(), [
            'menu_type' => AccessMenu::USER_MENU_TYPE,
            'menu_id' => AccessMenu::FULL_USER_ACCESS,
        ]);
    }

    /**
     * Копирует права на доступ к административной панели сайта
     *
     * @param Site $site_to - сайт на который происходит копирование
     * @param Site $site_from - сайт с которого происходит копирование
     * @return void
     */
    protected static function copySiteGroupRights(Site $site_to, Site $site_from)
    {
        self::copyRights($site_to, $site_from, new AccessSite());
    }

    /**
     * @param Site $site_to - сайт на который происходит копирование
     * @param Site $site_from - сайт с которого происходит копирование
     * @param AbstractObject $orm - orm объект прав
     * @param array $where - дополнительные условия во where
     * @return void
     */
    protected function copyRights(Site $site_to, Site $site_from, AbstractObject $orm, $where = [])
    {
        OrmRequest::make()
            ->delete()
            ->from($orm)
            ->where(array_merge(['site_id' => $site_to['id']], $where))
            ->exec();

        $rights = OrmRequest::make()
            ->from($orm)
            ->where(array_merge(['site_id' => $site_from['id']], $where))
            ->exec()->fetchAll();

        if ($rights) {
            $fields = [];
            foreach ($orm->getProperties() as $key => $property) {
                if (!$property->isRuntime()) {
                    $fields[] = $key;
                }
            }
            $values_parts = [];
            foreach ($rights as $key => $values) {
                $values['site_id'] = $site_to['id'];
                $values_parts[] = '("' . implode('","', $values) . '")';
            }
            $sql = 'insert into ' . $orm->_getTable() . ' (`' . implode('`,`', $fields) . '`) values ' . implode(',', $values_parts);
            DbAdapter::sqlExec($sql);
        }
    }

    /**
     * Удаляет у удаляемого сайта всю привязанную к сайту информацию со всех используемых таблиц
     *
     * @param integer $site_id - id сайта
     */
    public function deleteSiteNoNeedInfo($site_id)
    {
        //Удаляем склады привязанные к сайту
        $warehouse_api = new WareHouseApi();
        $warehouses = $warehouse_api->setFilter('site_id', $site_id)->getList();
        if (!empty($warehouses)) {
            foreach ($warehouses as $warehouse) {
                $warehouse->delete();
            }
        }
        unset($warehouses);

        //Удаляем категории товаров и товары соотвественно
        $dir_api = new Dirapi();
        $dirs = $dir_api->setFilter('site_id', $site_id)->getList();
        if (!empty($dirs)) {
            foreach ($dirs as $dir) {
                $dir->delete();
            }
        }
        unset($dirs);

        //Удаляем валюты и курсы валют
        $currency_api = new CurrencyApi();
        $currencies = $currency_api->setFilter('site_id', $site_id)->getList();
        foreach ($currencies as $currency) {
            $currency->delete();
        }
        unset($currencies);

        //Удаляем бренды
        $brand_api = new BrandApi();
        $brands = $brand_api->setFilter('site_id', $site_id)->getList();
        foreach ($brands as $brand) {
            $brand->delete();
        }
        unset($brands);

        //Удаляем купивших в 1 клик
        $oneclick_api = new OneClickItemApi();
        $oneclicks = $oneclick_api->setFilter('site_id', $site_id)->getList();
        foreach ($oneclicks as $oneclick) {
            $oneclick->delete();
        }
        unset($oneclicks);

        //Удаляем типы цены
        $typecost_api = new CostApi();
        $typecosts = $typecost_api->setFilter('site_id', $site_id)->getList();
        foreach ($typecosts as $typecost) {
            $typecost->delete();
        }
        unset($typecosts);

        //Удаляем единицы измерений
        $unit_api = new UnitApi();
        $units = $unit_api->setFilter('site_id', $site_id)->getList();
        foreach ($units as $unit) {
            $unit->delete();
        }
        unset($units);

        //Удаляем директории характеристик
        $propdirs_api = new PropertyDirApi();
        $propdirs = $propdirs_api->setFilter('site_id', $site_id)->getList();
        foreach ($propdirs as $propdir) {
            $propdir->delete();
        }
        unset($propdirs);

        //Удаляем характеристики
        $props_api = new PropertyApi();
        $props = $props_api->setFilter('site_id', $site_id)->getList();
        foreach ($props as $prop) {
            $prop->delete();
        }
        unset($props);

        //Удаляем категории статей и статьи
        $arctilecat_api = new CatApi();
        $arctilecats = $arctilecat_api->setFilter('site_id', $site_id)->getList();
        foreach ($arctilecats as $arctilecat) {
            $arctilecat->delete();
        }
        unset($arctilecats);

        //Удаляет баннеры
        $banners_api = new BannerApi();
        $banners = $banners_api->setFilter('site_id', $site_id)->getList();
        foreach ($banners as $banner) {
            $banner->delete();
        }
        unset($banners);

        //Удаляем комментарии
        $comments_api = new \Comments\Model\Api();
        $comments = $comments_api->setFilter('site_id', $site_id)->getList();
        foreach ($comments as $comment) {
            $comment->delete();
        }
        unset($comments);

        //Удаляем всё что связано с формами
        $forms_api = new FeedbackFormApi();
        $forms = $forms_api->setFilter('site_id', $site_id)->getList();
        foreach ($forms as $form) {
            $form->delete();
        }
        unset($forms);

        $formfield_api = new FeedbackFieldApi();
        $formfields = $formfield_api->setFilter('site_id', $site_id)->getList();
        foreach ($formfields as $formfield) {
            $formfield->delete();
        }
        unset($formfields);

        $formresult_api = new FeedbackResultApi();
        $formresults = $formresult_api->setFilter('site_id', $site_id)->getList();
        foreach ($formresults as $formresult) {
            $formresult->delete();
        }
        unset($formresults);
    }
}
