{* Страница со списком избранных товаров *}

{$shop_config=ConfigLoader::byModule('shop')}
{$check_quantity=$shop_config->check_quantity}
{$list = $this_controller->api->addProductsMultiOffersInfo($list)}

<div class="rs-favorite-page">

    {if $list}
        {hook name="catalog-list_products:options" title="{t}Просмотр категории продукции:параметры отображения{/t}"}
            <div class="catalog-sort">
                <div class="pull-left">
                    <div class="catalog-sort_pagesize">
                            {t}Показывать по{/t}
                        <div class="dropdown">
                                <span class="dropdown-toggle" data-toggle="dropdown"><span class="dashed">{$page_size}</span></span>
                                <ul class="dropdown-menu">
                                    {foreach $items_on_page as $item}
                                        <li><a href="{urlmake pageSize=$item}">{$item}</a></li>
                                    {/foreach}
                                </ul>
                        </div>
                    </div>
                </div>
                <div class="pull-right">
                    <a data-href="{urlmake viewAs=table}" class="catalog-sort_list{if $view_as == 'table'} active{/if}" rel="nofollow"><i class="i-svg i-svg-view-table"></i></a>
                    <a data-href="{urlmake viewAs=blocks}" class="catalog-sort_table{if $view_as == 'blocks'} active{/if}" rel="nofollow"><i class="i-svg i-svg-view-blocks"></i></a>
                </div>
            </div>
        {/hook}

        {include file="list_products_items.tpl" item_column="col-xs-12 col-sm-6 col-md-3"}

        <div class="pull-right">
            {include file="%THEME%/paginator.tpl"}
        </div>
    {else}
        <div class="empty-list">
            {t}Нет товаров в избранном{/t}
        </div>
    {/if}
</div>