{$shop_config = ConfigLoader::byModule('shop')}
{$region_autocomplete_url = $router->getUrl('shop-front-selectedaddresschange', ['Act' => 'regionAutocomplete'])}
{$address = $selected_address->getAddress()}
{$selected_country = $address->getCountry()}
{$selected_region = $address->getRegion()}

{$country_list = $this_controller->getRegionsByParent(0)}

{$selected_country_id = $selected_country.id}
{if !$selected_country_id}
    {$selected_country_id = $shop_config->getDefaultCountryId()}
{/if}
{if !$selected_country_id}
    {$first_country = reset($country_list)}
    {$selected_country_id = $first_country.id}
{/if}

{$region_list = $this_controller->getRegionsByParent($selected_country_id)}

{$selected_region_id = $selected_region.id}
{if !$selected_region_id && $selected_country_id == $shop_config->getDefaultCountryId()}
    {$selected_region_id = $shop_config->getDefaultRegionId()}
{/if}
{if !$selected_region_id}
    {$first_region = reset($region_list)}
    {$selected_region_id = $first_region.id}
{/if}

<div class="form-style modal-body mobile-width-small selectedAddressChange rs-selectedAddressChange" data-url="{$router->getUrl('shop-front-selectedaddresschange')}" data-region-autocomplete-url="{$region_autocomplete_url}">
    {$app->autoloadScripsAjaxBefore()}
    {addjs file="libs/jquery.autocomplete.js"}
    {addjs file="%shop%/rs.selectedaddresschange.js"}
    {addcss file='%shop%/selectedaddress.css'}

    <div class="h2">{t}Выбор города{/t}</div>
        <div class="selectedAddressChange_existAddress rs-selectedAddressChange_existAddress rs-open">
            <div class="selectedAddressChange_regionInputWrapper rs-selectedAddressChange_regionInputWrapper">
                <input type="text" class="rs-selectedAddressChange_regionInput" placeholder="Поиск">
            </div>
            <ul class="selectedAddressChange_markedList">
                {foreach $this_controller->getMarkedCities() as $city}
                    <li class="selectedAddressChange_markedRegion rs-selectedAddressChange_markedRegion" data-region-id="{$city.id}">
                        <a>{$city.title}</a>
                    </li>
                {/foreach}
            </ul>
            <div class="selectedAddressChange_otherAddressOpenButton rs-selectedAddressChange_otherAddressOpenButton">{t}Указать другой населённый пункт{/t}</div>
        </div>

        <div class="selectedAddressChange_otherAddress rs-selectedAddressChange_otherAddress">
            <form class="uk-width-1-1 selectedAddressChange_otherAddressForm rs-selectedAddressChange_otherAddressForm">
                <div class="selectedAddressChange_otherAddressCloseButton rs-selectedAddressChange_otherAddressCloseButton">{t}Назад к списку городов{/t}</div>

                <div class="form-group">
                    <label class="label-sup">{t}Страна{/t}</label>
                    {if $country_list}
                        <select name="country_id">
                            {foreach $country_list as $country}
                                <option value="{$country.id}" {if $country.id == $selected_country_id}selected{/if}>
                                    {$country.title}
                                </option>
                            {/foreach}
                        </select>
                    {else}
                        <input type="text" name="country" value="{$country.title}">
                    {/if}
                </div>

                <div class="form-group">
                    <label class="label-sup">{t}Регион{/t}</label>
                    <div class="rs-selectedAddressChange_regionBlock">
                        {if $region_list}
                            <select name="region_id">
                                {foreach $region_list as $region}
                                    <option value="{$region.id}" {if $region.id == $selected_region_id}selected{/if}>
                                        {$region.title}
                                    </option>
                                {/foreach}
                            </select>
                        {else}
                            <input type="text" name="region" value="{$selected_region.title}">
                        {/if}
                    </div>
                </div>

                <div class="form-group">
                    <label class="label-sup">{t}Город{/t}</label>
                    <input type="text" name="city" value="{$address->getCity()->title}">
                </div>

                <div class="link link-more selectedAddressChange_otherAddressSelectButton rs-selectedAddressChange_otherAddressSelectButton">{t}Выбрать{/t}</div>
            </form>
        </div>

    {$app->autoloadScripsAjaxAfter()}
</div>