
{foreach $products as $document_product}
    {$offers = $api->getProductOffers($document_product.product_id)}
    {$product = $api->getProduct($document_product.product_id)}
    {$_offers = $product->fillOffers()}
    {$m_offers = $product->fillMultiOffers()}
    <tr data-uniq="{$document_product.uniq}" class="product-row admin-style" style="border-bottom: solid 1px #dddddd">
        <td><input class="m_delete" data-uniq="{$document_product.uniq}" type="checkbox"></td>
        <td>{$document_product.title}</td>
        <td>
            {if $product.multioffers.use}
                <div style="border: solid 1px #dddddd; padding: 10px; display:inline-block; margin:20px 0;">
                {foreach $product.multioffers.levels as $level}
                    {if !empty($level.values)}
                        <div class="title">{if $level.title}{$level.title}{else}{$level.prop_title}{/if}</div>
                        <select name="props[{$n}][multioffers][{$level.prop_id}]" class="product-multioffer" data-url="{adminUrl do="getOfferPrice" product_id=$product.id}" data-prop-title="{if $level.title}{$level.title}{else}{$level.prop_title}{/if}">
                            <option value="-1">{t}Не выбрано{/t}</option>
                            {foreach $level.values as $value}
                                <option value="{$value.val_str}" {if $value.val_str == $multioffers_values[$level.prop_id].value}selected="selected"{/if} >{$value.val_str}</option>
                            {/foreach}
                        </select>
                    {/if}
                {/foreach}
                </div>
                <br>
                <div class="title">{t}Название комплектации{/t}</div>
                <select class="offers" data-uniq="{$document_product.uniq}" >
                    {foreach $offers.items as $sortn => $offer}
                        <option value="{$offer.id}" {if $offer.id == $document_product.offer_id}selected{/if} data-info='{str_replace('&quot;', '*`*', {$offer->getPropertiesJson()})}'>{$offer.title}</option>
                    {/foreach}
                    <option value="-1">{t}Не выбрано{/t}</option>
                </select>
            {else}
                <select class="offers" data-uniq="{$document_product.uniq}" >
                    {foreach $offers.items as $sortn => $offer}
                        <option value="{$offer.id}" {if $offer.id == $document_product.offer_id}selected{/if} data-info='{str_replace('&quot;', '*`*', {$offer->getPropertiesJson()})}'>{if $offer.title}{$offer.title}{else}{t}Основная{/t}{/if}</option>
                    {/foreach}
                    {if empty($offers.items)}
                        <option value="0" selected >{t}Основная{/t}</option>
                    {/if}
                </select>
            {/if}
        </td>
        {if $is_inventory}
            <td><input disabled class="calc-amount" type="number" value="{$document_product.calc_amount}"></td>
            <td><input class="fact-amount" type="number" value="{$document_product.fact_amount}"></td>
            <td><input disabled class="final-amount" type="number" value="{$document_product.final_amount}"></td>
        {else}
            <td><input type="number" class="amount" data-uniq="{$document_product.uniq}" value="{if $document_product.amount < 0}{-$document_product.amount}{else}{$document_product.amount}{/if}"></td>
        {/if}
        <td class="keyvalTable">{if !$disable_edit}<a class="remove zmdi zmdi-delete" data-uniq="{$document_product.uniq}"></a>{/if}</td>
    </tr>
{/foreach}