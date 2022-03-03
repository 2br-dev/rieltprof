<div class="currency">
    <span class="currency-act rs-parent-switcher">{$current_currency.stitle}</span>
    <ul class="currency-list">
        {foreach from=$currency_list item=item}
        <li><a href="{$router->getUrl('catalog-block-selectcurrency', ['scdo' => "changeCurrency", "currency"=> $item.title, "_block_id" => $_block_id, "referer" => $referer])}">{$item.stitle}</a></li>
        {/foreach}
    </ul>
</div>