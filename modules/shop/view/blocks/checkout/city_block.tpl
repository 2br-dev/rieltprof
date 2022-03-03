{$city = $address->getCity()}
{$region = $address->getRegion()}
{$country = $address->getCountry()}

<input type="hidden" name="addr_city_id" value="{$city.id}">
<input type="hidden" name="addr_city" value="{$city.title}">
<input type="hidden" name="addr_region_id" value="{$region.id}">
<input type="hidden" name="addr_region" value="{$region.title}">
<input type="hidden" name="addr_country_id" value="{$country.id}">
<input type="hidden" name="addr_country" value="{$country.title}">
<div class="checkout_changeRegionButton rs-checkout_changeRegionButton">
    <img width="24" src="/modules/shop/view/img/icons/pin.svg">
    <span>{$order->getAddress()->getLastRegionTitle()}</span>
    <a class="small">{t}изменить{/t}</a>
</div>

{if $order->getErrorsByForm('addr_country') || $order->getErrorsByForm('addr_region') || $order->getErrorsByForm('addr_city')}
    <div class="formFieldError margin-top">
        {if $order->getErrorsByForm('addr_country')}
            <div>{$order->getErrorsByForm('addr_country', ', ')}</div>
        {/if}
        {if $order->getErrorsByForm('addr_region')}
            <div>{$order->getErrorsByForm('addr_region', ', ')}</div>
        {/if}
        {if $order->getErrorsByForm('addr_city')}
            <div>{$order->getErrorsByForm('addr_city', ', ')}</div>
        {/if}
    </div>
{/if}