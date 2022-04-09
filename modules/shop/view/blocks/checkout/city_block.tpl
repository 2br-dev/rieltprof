{$city = $address->getCity()}
{$region = $address->getRegion()}
{$country = $address->getCountry()}

<input type="hidden" name="addr_city_id" value="{$city.id}">
<input type="hidden" name="addr_city" value="{$city.title}">
<input type="hidden" name="addr_region_id" value="{$region.id}">
<input type="hidden" name="addr_region" value="{$region.title}">
<input type="hidden" name="addr_country_id" value="{$country.id}">
<input type="hidden" name="addr_country" value="{$country.title}">

<div class="mb-5">
    <div class="fs-3 mb-4">{t}Ваш город{/t}</div>
    <div class="city-select">
        <span class="city-select__name">{$order->getAddress()->getLastRegionTitle()}</span>
        <a href="#" class="rs-checkout_changeRegionButton">{t}Изменить{/t}</a>
    </div>
    {if $order->getErrorsByForm('addr_country') || $order->getErrorsByForm('addr_region') || $order->getErrorsByForm('addr_city')}
        <div class="invalid-feedback d-block mt-2">
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
</div>