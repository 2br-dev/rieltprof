{addjs file="jquery.changeoffer.js?v=2"}
{addjs file="jcarousel/jquery.jcarousel.min.js"}
{addjs file="list_product.js"}
{$shop_config=ConfigLoader::byModule('shop')}
{$check_quantity=$shop_config->check_quantity}
{$list = $this_controller->api->addProductsMultiOffersInfo($list)}

<div id='favorite'>

    {if $list}
        <div class="viewOptions">
            <a href="{urlmake viewAs=table}" class="viewAs table{if $view_as == 'table'} act{/if}" rel="nofollow"></a>
            <a href="{urlmake viewAs=blocks}" class="viewAs blocks{if $view_as == 'blocks'} act{/if}" rel="nofollow"></a>                
            <div class="pageSizeBlock">
            {t}Показывать по:{/t}&nbsp;&nbsp; 
                <div class="lineListBlock collapse720">
                    <a class="lineTrigger rs-parent-switcher">{$page_size}</a>
                    <ul class="lineList">
                        {foreach $items_on_page as $item}
                        <li><a href="{urlmake pageSize=$item}" class="item{if $page_size==$item} act{/if}"><i>{$item}</i></a></li>
                        {/foreach}
                    </ul>
                </div>
            </div>
        </div>

        <div class="pages-line before">
            {include file="%THEME%/paginator.tpl"}
            <div class="clearboth"></div>
        </div>
        
       {include file="list_products_items.tpl"}
    {else}
        <div class="noProducts">
            {t}Нет товаров в избранном{/t}
        </div>
    {/if}
</div>