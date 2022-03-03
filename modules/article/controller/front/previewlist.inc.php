<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Article\Controller\Front;

use Article\Model\Api as ArticleApi;
use Article\Model\CatApi;
use Article\Model\Orm\Category;
use RS\Config\Loader as ConfigLoader;
use RS\Controller\Front;
use RS\Helper\Paginator;
use Search\Model\SearchApi;

/**
 * Контроллер отвечает за список статей/новостей
 */
class PreviewList extends Front
{
    const SESSION_PAGE_KEY = 'article_previewlist_page';

    /** @var ArticleApi */
    public $api;
    /** @var CatApi */
    public $cat_api;

    function init()
    {
        $this->api = new ArticleApi();
        $this->cat_api = new CatApi();
    }

    function actionIndex()
    {
        $query = $this->url->get('query', TYPE_STRING);
        $category = $this->url->get('category', TYPE_STRING);
        $is_search_page = $this->router->getCurrentRoute()->getId() == 'article-front-search';

        $dir = new Category();
        if ($category) {//Если страница не поиска
            $dir = $this->cat_api->getById($category);
        }

        if (!$is_search_page && (!$dir || !$dir['public'])) { //Если категория не найдена
            $this->e404(t('Такой страницы не существует'));
        }

        //Если есть alias и открыта страница с id вместо alias, то редирект
        $this->checkRedirectToAliasUrl($category, $dir, $dir->getUrl());

        $page = $_SESSION[self::SESSION_PAGE_KEY] = $this->url->get('p', TYPE_INTEGER, 1);
        $pageSize = ConfigLoader::byModule('article')->preview_list_pagesize;

        //Заполняем хлебные крошки
        $path = $this->cat_api->getPathToFirst($dir['id']);
        $last_dir = array_pop($path);
        if (!empty($path)) {
            foreach ($path as $one_dir) {
                if ($one_dir['public']) {
                    $this->app->breadcrumbs->addBreadCrumb($one_dir['title'], $one_dir->getUrl());
                }
            }
        }

        if ($query != '') {
            //Если идет поиск
            $q = $this->api->queryObj();
            $q->select = 'A.*';

            $search = SearchApi::currentEngine();
            $search->setFilter('B.result_class', 'Article\Model\Orm\Article');
            $search->setQuery($query);
            $search->joinQuery($q);

            $this->app->breadcrumbs->addBreadCrumb(t('Результаты поиска'));
        } elseif ($is_search_page) {
            $this->app->breadcrumbs->addBreadCrumb(t('Поиск'));
        } else {
            $this->app->breadcrumbs->addBreadCrumb($last_dir['title'], $query ? $this->router->getUrl('article-front-previewlist', ['category' => $category]) : null);
        }

        if ($debug_group = $this->getDebugGroup()) {
            $create_href = $this->router->getAdminUrl('add', ['dir' => $dir['id']], 'article-ctrl');
            $debug_group->addDebugAction(new \RS\Debug\Action\Create($create_href));
            $debug_group->addTool('create', new \RS\Debug\Tool\Create($create_href));
        }

        if ($dir['id']) {
            $this->api->setFilter('parent', $this->cat_api->getChildsId($dir['id']), 'in');
        }

        $this->api->setFilter([
            'public' => 1,
            [
                'dont_show_before_date' => 0,
                '|dateof:<' => date('Y-m-d H:i:s'),
            ],
        ]);
        $total = $this->api->getListCount();
        $list = $this->api->getList($page, $pageSize);

        //Подгрузим подкатегории, если есть такие
        $this->cat_api->setFilter("public", 1);
        $this->cat_api->setOrder('title ASC');
        $sub_dirs = $this->cat_api->getTreeList($dir['id']);

        if (empty($list) && $page > 1) {
            $this->e404(t('Такой страницы не существует'));
        }

        $this->app->title
            ->addSection($dir['meta_title'] ?: $dir['title']);

        $this->app->meta
            ->addKeywords($dir['meta_keywords'])
            ->addDescriptions($dir['meta_description']);

        $paginator = new Paginator($page, $total, $pageSize);
        $this->view->assign([
            'query' => $query,
            'category' => $category,
            'dir' => $dir,             //Категория
            'dirlist' => $sub_dirs,   //Подкатегрии если есть
            'paginator' => $paginator, //html пагинации       
            'list' => $list            //Список статей
        ]);

        return $this->result->setTemplate('preview_list.tpl');
    }
}
