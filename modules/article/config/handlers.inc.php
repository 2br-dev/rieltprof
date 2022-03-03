<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Article\Config;

use RS\Event\HandlerAbstract;

/**
 * Класс предназначен для объявления событий, которые будет прослушивать данный модуль и обработчиков этих событий.
 */
class Handlers extends HandlerAbstract
{
    function init()
    {
        $this->bind('getroute');
        $this->bind('getmenus');
        $this->bind('comments.gettypes');
        $this->bind('getpages');
    }

    /**
     * Получает маршруты для этого модуля
     *
     * @param array $routes
     * @return \RS\Router\Route[]
     */
    public static function getRoute($routes)
    {
        $routes[] = new \RS\Router\Route('article-front-view', [
            '/text-{category}/{id}/'
        ], null, t('Просмотр новости'));

        $routes[] = new \RS\Router\Route('article-front-previewlist', [
            '/text-{category}/'
        ], null, t('Список новостей/статей'));

        $routes[] = new \RS\Router\Route('article-front-search', [
            '/text/search/'
        ], [
            'controller' => 'article-front-previewlist'
        ], t('Поиск по новостям'));

        $routes[] = new \RS\Router\Route('article-front-rss', [
            '/rss-{category}/'
        ], null, t('RSS канал'), true);

        return $routes;
    }

    public static function getPages($urls)
    {
        $cat_api = new \Article\Model\Catapi();
        $cat_api->setFilter('use_in_sitemap', 1);
        $res = $cat_api->getListAsResource();
        $dir_ids = $res->fetchSelected(null, 'id');
        if ($dir_ids) {
            $api = new \Article\Model\Api();
            $api->setFilter('parent', $dir_ids, 'in');
            $page = 1;
            while ($list = $api->getList($page, 100)) {
                $page++;
                foreach ($list as $article) {
                    $urls[] = [
                        'loc' => $article->getUrl(),
                        'lastmod' => date('c', strtotime($article['dateof'])),
                        'priority' => '0.3'
                    ];
                }
            }
        }
        return $urls;
    }

    /**
     * Возвращает пункты меню этого модуля в виде массива
     *
     */
    public static function getMenus($items)
    {
        $items[] = [
            'title' => t('Контент'),
            'alias' => 'article',
            'typelink' => 'link',
            'link' => '%ADMINPATH%/article-ctrl/',
            'parent' => 'website',
            'sortn' => 10
        ];
        return $items;
    }

    /**
     * Зарегистрируем класс комментариев
     *
     * @param array $list
     * @return array
     */
    public static function commentsGetTypes($list)
    {
        $list[] = new \Article\Model\CommentType\Article();
        return $list;
    }
}
