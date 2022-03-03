<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Menu\Config;

use Menu\Model\Api as MenuApi;
use Menu\Model\MenuType;
use Menu\Model\Orm\Menu;
use RS\Cache\Manager as CacheManager;
use RS\Event\HandlerAbstract;
use RS\Site\Manager as SiteManager;

class Handlers extends HandlerAbstract
{
    function init()
    {
        $this->bind('getmenus');
        $this->bind('getroute');
        $this->bind('getpages');
        $this->bind('menu.gettypes');
    }

    public static function getRoute($routes)
    {
        if (SiteManager::getSite() !== false) {
            $api = new MenuApi();
            $routes = array_merge($routes, $api->getMenuRoutes());
        }
        return $routes;
    }

    public static function getPages($urls)
    {
        $api = new MenuApi();
        $api->setFilter('public', 1);
        $api->setFilter('hide_from_url', 0);
        $api->setFilter('menutype', 'user');
        /** @var Menu[] $list */
        $list = $api->getList();
        $local_urls = [];
        foreach ($list as $item) {
            $url = $item->getHref();
            $local_urls[$url] = [
                'loc' => $url
            ];
        }
        $urls = array_merge($urls, array_values($local_urls));
        return $urls;
    }

    /**
     * Возвращает пункты меню этого модуля в виде массива
     *
     * @param array $items - список пунктов меню
     * @return array
     */
    public static function getMenus($items)
    {
        $items[] = [
            'title' => t('Веб-сайт'),
            'alias' => 'website',
            'link' => '%ADMINPATH%/menu-ctrl/',
            'sortn' => 30,
            'typelink' => 'link',
            'parent' => 0,
        ];
        $items[] = [
            'title' => t('Управление'),
            'alias' => 'control',
            'link' => '%ADMINPATH%/main-options/',
            'sortn' => 40,
            'typelink' => 'link',
            'parent' => 0,
        ];
        $items[] = [
            'title' => t('Разное'),
            'alias' => 'modules',
            'link' => 'JavaScript:;',
            'sortn' => 50,
            'typelink' => 'link',
            'parent' => 0,
        ];
        $items[] = [
            'title' => t('Меню'),
            'alias' => 'menu',
            'link' => '%ADMINPATH%/menu-ctrl/',
            'parent' => 'website',
            'sortn' => 0,
            'typelink' => 'link',
        ];
        $items[] = [
            'title' => t('Пользователи'),
            'alias' => 'userscontrol',
            'link' => '%ADMINPATH%/users-ctrl/',
            'sortn' => 6,
            'parent' => 'control',
            'typelink' => 'link',
        ];

        return $items;
    }

    /**
     * Возвращает список пунктов меню
     *
     * @param \Menu\Model\MenuType\AbstractType[] $menu_types
     * @return \Menu\Model\MenuType\AbstractType[]
     */
    public static function menuGetTypes($menu_types)
    {
        $menu_types[] = new MenuType\Article();
        $menu_types[] = new MenuType\Link();
        $menu_types[] = new MenuType\Page();

        return $menu_types;
    }
}
