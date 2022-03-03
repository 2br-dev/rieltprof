{$product=$elem->getProduct()}
{$product.id=$elem.product_id} {* Даже если товар еще не создан, присваеваем его ID *}

{$images=$product->getImages()}
{if !empty($images)}
  <div class="offer-images-line">  
  {foreach $images as $image}
    {$is_act=is_array($elem.photos_arr) && in_array($image.id, $elem.photos_arr)}
     <a data-id="{$image.id}" data-name="photos_arr[]" class="{if $is_act}act{/if}"><img src="{$image->getUrl(30,30,'xy')}"/></a>
     {if $is_act}<input type="hidden" name="photos_arr[]" value="{$image.id}">{/if}
  {/foreach}  
  </div>
{/if} 