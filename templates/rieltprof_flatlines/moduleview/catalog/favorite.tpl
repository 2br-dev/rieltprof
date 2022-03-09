{* Страница со списком избранных товаров *}

{$shop_config=ConfigLoader::byModule('shop')}
{$check_quantity=$shop_config->check_quantity}
{$list = $this_controller->api->addProductsMultiOffersInfo($list)}
<div class="toolbar">
    <div class="toolbar-body">
        <div class="input-field">
            {moduleinsert name="\Rieltprof\Controller\Block\SearchLine" indexTemplate='%catalog%/blocks/searchline/searchform.tpl'}
        </div>
        <div class="separator"></div>
{*        <div class="segmented-radio">*}
{*            {if $category->getMainParent()->name == "Продажа"}*}
{*                <p class="sale-category-link active">Продажа</p>*}
{*            {else}*}
{*                {if $category->getOtherCategory()}*}
{*                    <a href="{$category->getOtherCategory()->getUrl()}" class="sale-category-link trigger-action-link" data-action="rent">Продажа</a>*}
{*                {/if}*}
{*            {/if}*}
{*            {if $category->getMainParent()->name == "Аренда"}*}
{*                <p class="rent-category-link active">Аренда</p>*}
{*            {else}*}
{*                {if $category->getOtherCategory()}*}
{*                    <a href="{$category->getOtherCategory()->getUrl()}" class="rent-category-link trigger-action-link" data-action="sale">Аренда</a>*}
{*                {/if}*}
{*            {/if}*}
{*        </div>*}
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
<div class="rs-favorite-page content">

    {if $list}
        {include file="list_products_items.tpl" }

        <div class="pull-right">
            {include file="%THEME%/paginator.tpl"}
        </div>
    {else}
        <div class="empty-list">
            {t}Нет товаров в избранном{/t}
        </div>
    {/if}
</div>

