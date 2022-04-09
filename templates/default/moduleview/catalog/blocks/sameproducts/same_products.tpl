{if $sameproducts}
<section class="recommended">
    <h2><span>{t}Похожие товары{/t}</span></h2>
    <div class="previewList">
        <div class="gallery">
            <ul>
                {foreach from=$sameproducts item=product}
                {$main_image=$product->getMainImage()}
                <li><a href="{$product->getUrl()}" title="{$product.title}"><img src="{$main_image->getUrl(64, 64)}" alt="{$main_image.title|default:"{$product.title}"}"/></a></li>
                {/foreach}
            </ul>
        </div>
        <a class="control prev"></a>
        <a class="control next"></a>
    </div>
</section>
{/if}