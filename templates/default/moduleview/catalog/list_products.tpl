{addjs file="jquery.changeoffer.js?v=2"}
{addjs file="jcarousel/jquery.jcarousel.min.js"}
{addjs file="list_product.js"}
{$shop_config=ConfigLoader::byModule('shop')}
{$check_quantity=$shop_config->check_quantity}
{$list = $this_controller->api->addProductsMultiOffersInfo($list)}
{$list = $this_controller->api->addProductsDirs($list)}
{if $THEME_SETTINGS.enable_favorite}
{$list = $this_controller->api->addProductsFavorite($list)}
{/if}

{if $no_query_error}
<div class="noQuery">
    {t}Не задан поисковый запрос{/t}
</div>      
{else}
<div id="products" {if $shop_config}class="shopVersion"{/if}>
    {if $category.description && !$THEME_SETTINGS.cat_description_bottom}<div class="categoryDescription">{$category.description}</div>{/if}
    {if count($sub_dirs)}{$one_dir=reset($sub_dirs)}{/if}
    {if empty($query) || (count($sub_dirs) && $dir_id != $one_dir.id)}
    <nav class="subCategory">
        {foreach $sub_dirs as $item}
        <a href="{urlmake category=$item._alias p=null pf=null filters=null bfilter=null}">{$item.name}</a>
        {/foreach}
    </nav>
    {/if}

    {if count($list)}
    {hook name="catalog-list_products:options" title="{t}Просмотр категории продукции:параметры отображения{/t}"}
        <div class="viewOptions">
            <a data-href="{$this_controller->api->urlMakeCatalogParams(['viewAs' => 'table'])}" class="viewAs table{if $view_as == 'table'} act{/if}" rel="nofollow"></a>
            <a data-href="{$this_controller->api->urlMakeCatalogParams(['viewAs' => 'blocks'])}" class="viewAs blocks{if $view_as == 'blocks'} act{/if}" rel="nofollow"></a>
            {t}Сортировать по{/t}:&nbsp;&nbsp;                  
            <div class="lineListBlock sortBlock">
                <a class="lineTrigger rs-parent-switcher">{if $cur_sort=='sortn'}{t}умолчанию{/t}
                                                          {elseif $cur_sort=='dateof'}{t}по дате{/t}
                                                          {elseif $cur_sort=='rating'}{t}популярности{/t}
                                                          {elseif $cur_sort=='title'}{t}названию{/t}
                                                          {elseif $cur_sort=='num'}{t}наличию{/t}
                                                          {elseif $cur_sort=='rank'}{t}релевантности{/t}
                                                          {else}{t}цене{/t}{/if}</a>
                <ul class="lineList">
                    <li><a href="{$this_controller->api->urlMakeCatalogParams(['sort' => 'sortn', 'nsort' => $sort.sortn])}" class="item{if $cur_sort=='sortn'} {$cur_n}{/if}" rel="nofollow"><i>{t}умолчанию{/t}</i></a></li>
                    <li><a href="{$this_controller->api->urlMakeCatalogParams(['sort' => 'cost', 'nsort' => $sort.cost])}" class="item{if $cur_sort=='cost'} {$cur_n}{/if}" rel="nofollow"><i>{t}цене{/t}</i></a></li>
                    <li><a href="{$this_controller->api->urlMakeCatalogParams(['sort' => 'rating', 'nsort' => $sort.rating])}" class="item{if $cur_sort=='rating'} {$cur_n}{/if}" rel="nofollow"><i>{t}популярности{/t}</i></a></li>
                    <li><a href="{$this_controller->api->urlMakeCatalogParams(['sort' => 'dateof', 'nsort' => $sort.dateof])}" class="item{if $cur_sort=='dateof'} {$cur_n}{/if}" rel="nofollow"><i>{t}дате{/t}</i></a></li>
                    <li><a href="{$this_controller->api->urlMakeCatalogParams(['sort' => 'num', 'nsort' => $sort.num])}" class="item{if $cur_sort=='num'} {$cur_n}{/if}" rel="nofollow"><i>{t}наличию{/t}</i></a></li>
                    <li><a href="{$this_controller->api->urlMakeCatalogParams(['sort' => 'title', 'nsort' => $sort.title])}" class="item{if $cur_sort=='title'} {$cur_n}{/if}" rel="nofollow"><i>{t}названию{/t}</i></a></li>
                    {if $can_rank_sort}
                    <li><a href="{$this_controller->api->urlMakeCatalogParams(['sort' => 'rank', 'nsort' => $sort.rank])}" class="item{if $cur_sort=='rank'} {$cur_n}{/if}" rel="nofollow"><i>{t}релевантности{/t}</i></a></li>
                    {/if}
                </ul>
            </div>
        </div>
    {/hook}

    <div class="pages-line before">
        <div class="pageSizeBlock">
            {t}Показывать по:{/t}&nbsp;&nbsp; 
            <div class="lineListBlock collapse720">
                <a class="lineTrigger rs-parent-switcher">{$page_size}</a>
                <ul class="lineList">
                    {foreach $items_on_page as $item}
                    <li><a href="{$this_controller->api->urlMakeCatalogParams(['pageSize' => $item])}" class="item{if $page_size==$item} act{/if}"><i>{$item}</i></a></li>
                    {/foreach}
                </ul>
            </div>
        </div>
        {include file="%THEME%/paginator.tpl"}
        <div class="clearboth"></div>
    </div>

        <section class="topProducts">
            <div class="productWrap">
                {include file="list_products_items.tpl"}
            </div>
            <div class="clear"></div>
        </section>        

        <div class="pages-line">
            {include file="%THEME%/paginator.tpl"}
            <div class="clearboth"></div>
        </div>        
    {else}    
        <div class="noProducts">
            {if !empty($query)}
            {t}Извините, ничего не найдено{/t}
            {else}
            {t}В данной категории нет ни одного товара{/t}
            {/if}
        </div>
    {/if}
    {if $category.description && $THEME_SETTINGS.cat_description_bottom}<div class="categoryDescription">{$category.description}</div>{/if}
</div>
{/if}