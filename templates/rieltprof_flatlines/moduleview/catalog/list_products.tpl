{* Просмотр списка товаров в категории, просмотр результатов поиска *}

{$shop_config=ConfigLoader::byModule('shop')}
{$config = \RS\Config\Loader::byModule('rieltprof')}
{$check_quantity=$shop_config.check_quantity}
{$list = $this_controller->api->addProductsDirs($list)}
{addjs file="rs.ajaxpagination.js"}

{if $THEME_SETTINGS.enable_favorite}
    {$list = $this_controller->api->addProductsFavorite($list)}
    {addjs file="rs.favorite.js"}
{/if}
<div class="toolbar">
    <div class="toolbar-body">
        <div class="input-field">
            {moduleinsert name="\Rieltprof\Controller\Block\SearchLine" indexTemplate='%catalog%/blocks/searchline/searchform.tpl'}
        </div>
        <div class="separator"></div>
        <div class="segmented-radio">
            {if $category->getMainParent()->name == "Продажа"}
                <p class="sale-category-link active">Продажа</p>
            {else}
                {if $category->getOtherCategory()}
                    <a href="{$category->getOtherCategory()->getUrl()}" class="sale-category-link trigger-action-link" data-action="rent">Продажа</a>
                {/if}
            {/if}
            {if $category->getMainParent()->name == "Аренда"}
                <p class="rent-category-link active">Аренда</p>
            {else}
                {if $category->getOtherCategory()}
                    <a href="{$category->getOtherCategory()->getUrl()}" class="rent-category-link trigger-action-link" data-action="sale">Аренда</a>
                {/if}
            {/if}
        </div>
        <div class="icons">
            {$countFavorite = \Catalog\Model\FavoriteApi::getInstance()->getFavoriteCount()}
            <a
                data-href="{$router->getUrl('catalog-front-favorite')}"
                data-favorite-url="{$router->getUrl('catalog-front-favorite')}"
                class="fav {if $countFavorite}active{/if} rs-favorite-block"
            >
                <span class="chip counter rs-favorite-items-count">{$countFavorite}</span>
            </a>
            {if isset($smarty.cookies.view_mode) && $smarty.cookies.view_mode == 'list'}
                <a class="toggle-view" data-mode="list" data-target="#table-list"></a>
            {else}
                <a class="toggle-view" data-mode="cards" data-target="#table-cards"></a>
            {/if}
            {if $query == ''}
                <a class="filters-trigger" title="Показать фильтры"></a>
            {/if}
        </div>
    </div>
</div>
<div id="products" class="catalog content {if $shop_config}shopVersion{/if}">
    {if $no_query_error}
        <div class="empty-list">
            {moduleinsert name="\Main\Controller\Block\BreadCrumbs"}
            <div class="title">
                <span>Результаты поиска</span>
            </div>
            {t}Не задан поисковый запрос{/t}
        </div>
    {else}
        {if count($list)}
            {include file="list_products_items.tpl" total=$total}
            <div class="rs-pagination-block">
                {if $paginator->page < $paginator->total_pages}
                    <div class="mt-5">
                        <a class="btn btn-outline-primary col-12 rs-ajax-paginator"
                           data-pagination-options='{ "appendElement":".ads-list", "loaderBlock":".rs-pagination-block", "replaceBrowserUrl": false, "clickOnScroll": true}'
                           data-url="{$paginator->getPageHref($paginator->page+1)}"
                           data-scroll-element="#products"
                        >
{*                            <span>{t}Показать еще{/t}</span>*}
                        </a>
                    </div>
                {/if}
            </div>
        {else}
            <div class="empty-list">
                {if !empty($query)}
                    {moduleinsert name="\Main\Controller\Block\BreadCrumbs"}
                    <div class="title">
                        <span>Результаты поиска: {$query}</span>
                    </div>
                    {t}Извините, ничего не найдено{/t}
                {else}
                    {moduleinsert name="\Main\Controller\Block\BreadCrumbs"}
                    <div class="title">
                        <span>{$category['name']}</span>
                        <span class="title-count-object"> ({$config->getCountObjectByDirId($category['id'])})</span>
                    </div>
                    {t}Нет ни одного объекта{/t}
                {/if}
            </div>
        {/if}
    {/if}
    <div class="fab-wrapper plus">
        {$refererUrl = {urlmake}}
        {if isset($smarty.cookies.action_folder) && $smarty.cookies.action_folder == 'rent'}
            <a href="{$router->getAdminUrl('add', ['referer' => $refererUrl, 'sale_dir' => 6, 'rent_dir' => 16, 'object' => 'Дом', 'action' => 'rent'], 'rieltprof-housectrl')}" class="fab-menu-item crud-add"><i class="icon" id="house"></i><span>Дом</span></a>
            <a href="{$router->getAdminUrl('add', ['referer' => $refererUrl, 'sale_dir' => 3, 'rent_dir' => 13, 'object' => 'Квартира', 'action' => 'rent'], 'rieltprof-flatctrl')}" class="fab-menu-item crud-add"><i class="icon" id="apartment"></i><span>Квартира</span></a>
            <a href="{$router->getAdminUrl('add', ['referer' => $refererUrl, 'sale_dir' => 5, 'rent_dir' => 15, 'object' => 'Комната', 'action' => 'rent'], 'rieltprof-roomctrl')}" class="fab-menu-item crud-add"><i class="icon" id="room"></i><span>Комната</span></a>
            <a href="{$router->getAdminUrl('add', ['referer' => $refererUrl, 'sale_dir' => 10, 'rent_dir' => 20, 'object' => 'Дача', 'action' => 'rent'], 'rieltprof-countryhousectrl')}" class="fab-menu-item crud-add"><i class="icon" id="garden-house"></i><span>Дача</span></a>
            <a href="{$router->getAdminUrl('add', ['referer' => $refererUrl, 'sale_dir' => 9, 'rent_dir' => 19, 'object' => 'Участок', 'action' => 'rent'], 'rieltprof-plotctrl')}" class="fab-menu-item crud-add"><i class="icon" id="land"></i><span>Участок</span></a>
            <a href="{$router->getAdminUrl('add', ['referer' => $refererUrl, 'sale_dir' => 7, 'rent_dir' => 17, 'object' => 'Таунхаус', 'action' => 'rent'], 'rieltprof-townhousectrl')}" class="fab-menu-item crud-add"><i class="icon" id="townhouse"></i><span>Таунхаус</span></a>
            <a href="{$router->getAdminUrl('add', ['referer' => $refererUrl, 'sale_dir' => 8, 'rent_dir' => 18, 'object' => 'Дуплекс', 'action' => 'rent'], 'rieltprof-duplexctrl')}" class="fab-menu-item crud-add"><i class="icon" id="townhouse"></i><span>Дуплекс</span></a>
            <a href="{$router->getAdminUrl('add', ['referer' => $refererUrl, 'sale_dir' => 11, 'rent_dir' => 21, 'object' => 'Гараж', 'action' => 'rent'], 'rieltprof-garagectrl')}" class="fab-menu-item crud-add"><i class="icon" id="garage"></i><span>Гараж</span></a>
            <a href="{$router->getAdminUrl('add', ['referer' => $refererUrl, 'sale_dir' => 12, 'rent_dir' => 22, 'object' => 'Коммерция', 'action' => 'sale'], 'rieltprof-commercialctrl')}" class="fab-menu-item crud-add"><i class="icon" id="commerce"></i><span>Коммерция</span></a>
        {else}
            <a href="{$router->getAdminUrl('add', ['referer' => $refererUrl, 'sale_dir' => 6, 'rent_dir' => 16, 'object' => 'Дом', 'action' => 'sale'], 'rieltprof-housectrl')}" class="fab-menu-item crud-add"><i class="icon" id="house"></i><span>Дом</span></a>
            <a href="{$router->getAdminUrl('add', ['referer' => $refererUrl, 'sale_dir' => 3, 'rent_dir' => 13, 'object' => 'Квартира', 'action' => 'sale'], 'rieltprof-flatctrl')}" class="fab-menu-item crud-add"><i class="icon" id="apartment"></i><span>Квартира</span></a>
            <a href="{$router->getAdminUrl('add', ['referer' => $refererUrl, 'sale_dir' => 5, 'rent_dir' => 15, 'object' => 'Комната', 'action' => 'sale'], 'rieltprof-roomctrl')}" class="fab-menu-item crud-add"><i class="icon" id="room"></i><span>Комната</span></a>
            <a href="{$router->getAdminUrl('add', ['referer' => $refererUrl, 'sale_dir' => 10, 'rent_dir' => 20, 'object' => 'Дача', 'action' => 'sale'], 'rieltprof-countryhousectrl')}" class="fab-menu-item crud-add"><i class="icon" id="garden-house"></i><span>Дача</span></a>
            <a href="{$router->getAdminUrl('add', ['referer' => $refererUrl, 'sale_dir' => 9, 'rent_dir' => 19, 'object' => 'Участок', 'action' => 'sale'], 'rieltprof-plotctrl')}" class="fab-menu-item crud-add"><i class="icon" id="land"></i><span>Участок</span></a>
            <a href="{$router->getAdminUrl('add', ['referer' => $refererUrl, 'sale_dir' => 7, 'rent_dir' => 17, 'object' => 'Таунхаус', 'action' => 'sale'], 'rieltprof-townhousectrl')}" class="fab-menu-item crud-add"><i class="icon" id="townhouse"></i><span>Таунхаус</span></a>
            <a href="{$router->getAdminUrl('add', ['referer' => $refererUrl, 'sale_dir' => 8, 'rent_dir' => 18, 'object' => 'Дуплекс', 'action' => 'sale'], 'rieltprof-duplexctrl')}" class="fab-menu-item crud-add"><i class="icon" id="townhouse"></i><span>Дуплекс</span></a>
            <a href="{$router->getAdminUrl('add', ['referer' => $refererUrl, 'sale_dir' => 4, 'rent_dir' => 14, 'object' => 'Новостройка', 'action' => 'sale'], 'rieltprof-newbuildingctrl')}" class="fab-menu-item crud-add"><i class="icon" id="newbuilds"></i><span>Новостройка</span></a>
            <a href="{$router->getAdminUrl('add', ['referer' => $refererUrl, 'sale_dir' => 11, 'rent_dir' => 21, 'object' => 'Гараж', 'action' => 'sale'], 'rieltprof-garagectrl')}" class="fab-menu-item crud-add"><i class="icon" id="garage"></i><span>Гараж</span></a>
            <a href="{$router->getAdminUrl('add', ['referer' => $refererUrl, 'sale_dir' => 12, 'rent_dir' => 22, 'object' => 'Коммерция', 'action' => 'sale'], 'rieltprof-commercialctrl')}" class="fab-menu-item crud-add"><i class="icon" id="commerce"></i><span>Коммерция</span></a>
        {/if}

        <div class="separator"></div>
        <a href="" class="fab-menu-trigger"><span>Закрыть</span></a>
    </div>
</div>

