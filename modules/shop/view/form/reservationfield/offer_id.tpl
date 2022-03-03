{assign var=product value=$elem->getProduct()}

{if $product->isOffersUse() && !$product->isMultiOffersUse()} 
   <select name="offer_id"> 
       {foreach $product.offers.items as $offer} 
          <option value="{$offer.id}" {if $elem.offer_id==$offer.sortn || $elem.offer == $offer.title}selected="selected"{/if}>{$offer.title}</option>
       {/foreach}
   </select> 
{else}
   -
{/if}