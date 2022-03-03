{assign var=product value=$elem->getProduct()}
{assign var=offers value=$elem->getArrayMultiOffer()} 

{if $product->isMultiOffersUse()}
   {foreach $product.multioffers.levels as $level}
       <div>
            <div class="key">{if $level.title}{$level.title}{else}{$level.prop_title}{/if}</div>
            <div class="value">
                <select name="multioffers[{$level.prop_id}]" data-prop-title="{if $level.title}{$level.title}{else}{$level.prop_title}{/if}">
                   {foreach $level.values as $value}
                       {if $level.title}
                          {$level_title=$level.title}
                       {else} 
                          {$level_title=$level.prop_title}
                       {/if}
                       <option value="{$level_title}: {$value.val_str}" {if $offers[$level_title]==$value.val_str}selected="selected"{/if}>{$value.val_str}</option> 
                   {/foreach}
                </select>
            </div>
       </div>
   {/foreach}
{else}
    -
{/if}