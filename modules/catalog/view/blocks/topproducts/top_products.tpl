{if $products}
<section class="topProducts pl{$_block_id}">
        <h2 class="mbot10"><span>{if $block_title}{$block_title}{else}{$dir.name}{/if}</span></h2>
        <div class="productWrap">
                <ul class="productList">
                    {foreach from=$products item=product}
                    <li {$product->getDebugAttributes()}>
                        <a href="{$product->getUrl()}" class="pic">
                        <span class="labels">
                            {foreach from=$product->getMySpecDir() item=spec}
                            {if $spec.image && $spec.is_label}
                                <img src="{$spec->__image->getUrl(62,62, 'xy')}" alt="{$spec.name}"/>
                            {/if}
                            {/foreach}
                        </span>
                        {$main_image=$product->getMainImage()}
                        <img src="{$main_image->getUrl(141,185,'xy')}" alt="{$main_image.title|default:"{$product.title}"}"/></a>
                        <a href="{$product->getUrl()}" class="info">
                            <h3>{$product.title}</h3>
                            <div class="group">
                                <div class="scost">
                                    {$last_price = $product->getOldCost()}
                                    {if $last_price>0}<div class="lastPrice">{$last_price}</div>{/if}
                                    <span>{$product->getCost()} {$product->getCurrency()}</span>
                                </div>
                                <span class="name">{$product->getMainDir()->name}</span>
                            </div>
                        </a>
                    </li>
                    {/foreach}
                </ul>
                <div class="clearLeft"></div>
                {if $paginator->total_pages > $paginator->page}
                    <a data-pagination-options='{ "appendElement":".productList", "context":".pl{$_block_id}" }' data-href="{$router->getUrl('catalog-block-topproducts', ['_block_id' => $_block_id, 'page' => $paginator->page+1])}" class="onemoreEmpty ajaxPaginator">{t}посмотреть еще{/t}</a>
                {/if}
        </div>
</section>
{else}
    {include file="theme:default/block_stub.tpl"  class="blockTopProducts" do=[
        [
            'title' => t("Добавьте категорию с товарами"),
            'href' => {adminUrl do=false mod_controller="catalog-ctrl"}
        ],
        [
            'title' => t("Настройте блок"),
            'href' => {$this_controller->getSettingUrl()},
            'class' => 'crud-add'
        ]
    ]}
{/if}