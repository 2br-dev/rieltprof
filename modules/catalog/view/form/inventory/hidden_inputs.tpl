{foreach $items as $item}
    <input type="hidden" name="items[{$item.uniq}][product_id]" value="{$item.product_id}">
    <input type="hidden" name="items[{$item.uniq}][amount]" value="{if $item.amount < 0}{-$item.amount}{else}{$item.amount}{/if}">
    <input type="hidden" name="items[{$item.uniq}][offer_id]" value="{$item.offer_id}">
    {if $is_inventory}
        <input type="hidden" name="items[{$item.uniq}][fact_amount]" value="{$item.fact_amount}">
        <input type="hidden" name="items[{$item.uniq}][calc_amount]" value="{$item.calc_amount}">
        <input type="hidden" name="items[{$item.uniq}][item_id]" value="{$item.id}">
    {/if}
{/foreach}

