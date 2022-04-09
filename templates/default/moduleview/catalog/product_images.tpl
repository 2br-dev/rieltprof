{* Изображения товара *}
<span class="labels">
    {foreach from=$product->getMySpecDir() item=spec}
    {if $spec.image && $spec.is_label}
        <img src="{$spec->__image->getUrl(62,62, 'xy')}">
    {/if}
    {/foreach}
</span>

{if !$product->hasImage()}      
    {$main_image=$product->getMainImage()}
    <span class="mainPicture"><img src="{$main_image->getUrl(310,310,'xy')}" class="photo" alt="{$main_image.title|default:"{$product.title}"}"/></span>
{else}
    {* Главные фото *}
    {assign var=images value=$product->getImages()}
    {if $product->isOffersUse()}
        {* Назначенные фото у первой комлектации *}
        {$first_offer = $product->getMainOffer()}
        {$offer_images = $first_offer.photos_arr}
    {/if}
    {foreach from=$images key=key item=image name=biglist}
        <a href="{$image->getUrl(800,600,'xy')}" data-n="{$key}" data-id="{$image.id}" class="photo mainPicture viewbox {if ($offer_images && ($image.id!=$offer_images.0)) || (!$offer_images && !$image@first)} hidden{/if}" {if ($offer_images && in_array($image.id, $offer_images)) || (!$offer_images)}rel="bigphotos"{/if}><img src="{$image->getUrl(310,310,'xy')}" alt="{$image.title|default:"{$product.title} {t}фото{/t} {$key+1}"}"></a>
    {/foreach}
    
    {* Нижняя линейка фото *}
    {if count($images)>1}
    <div class="productGalleryWrap">
        <div class="gallery">
            <ul>
                {foreach from=$product->getImages() key=key item=image}
                <li data-id="{$image.id}" class="{if $offer_images && !in_array($image.id, $offer_images)}hidden{/if}"><a href="{$image->getUrl(800,600,'xy')}" data-n="{$key}" target="_blank" class="preview"><img src="{$image->getUrl(64,64)}" alt="{$image.title}"></a></li>
                {/foreach}
            </ul>
        </div>
        <a class="control prev"></a>
        <a class="control next"></a>
     </div>        
     {/if}
{/if}