{if !empty($recommended)}
<section class="recommended">
    <h2><span>{$recommended_title|default:t("С этим товаром покупают")}</span></h2>
    <div class="previewList">
        <div class="gallery">
            <ul>
                {foreach from=$recommended item=product}
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