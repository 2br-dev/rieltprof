{assign var=check_quantity value=$shop_config.check_quantity}
{assign var=catalog_config value=$this_controller->getModuleConfig()}
{if $list}
    <div class="concomitantBlock">
        <div class="concomitantTitle">
            {t}Сопутствующие товары{/t}
        </div>
        {foreach $list as $product}
            <div class="concomitantItem">
                <input id="concomitantItem{$product.id}" type="checkbox" name="concomitant[]" value="{$product.id}"/> 
                <label for="concomitantItem{$product.id}">{$product.title} (+{$product->getCost()} {$product->getCurrency()})</label>
            </div>
        {/foreach}
    </div>
{/if}