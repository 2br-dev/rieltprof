{if count($products)}
<div class="sideBlock">
    <h2><span>{t}Просмотренные товары{/t}</span></h2>
    <div class="wrapWidth">
        <ul class="lastViewedList">
            {foreach from=$products item=product}
            {$main_image=$product->getMainImage()}
            <li><a href="{$product->getUrl()}" title="{$product.title}"><img src="{$main_image->getUrl(64,64, 'xy')}" alt="{$main_image.title|default:$product.title}"/></a></li>
            {/foreach}
        </ul>
    </div>
</div>
{/if}