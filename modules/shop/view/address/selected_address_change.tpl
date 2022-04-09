{extends "%THEME%/helper/wrapper/dialog/standard.tpl"}
{block "class"}modal-lg{/block}
{block "title"}{t}Выбор города{/t}{/block}
{block "body"}

    {$shop_config = ConfigLoader::byModule('shop')}
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

    <div data-url="{$router->getUrl('shop-front-selectedaddresschange')}" class="rs-region-change">
        <div class="collapse show change-city-type">
            <div class="mb-lg-4 mb-3 position-relative">
                <label class="form-label">{t}Поиск города{/t}</label>
                <input type="text" class="form-control rs-region-input" data-region-autocomplete-url="{$router->getUrl('shop-front-selectedaddresschange', ['Act' => 'regionAutocomplete'])}">
                <div class="head-search__dropdown rs-autocomplete-result"></div>
            </div>
            {$cities = $this_controller->getMarkedCities()}
            {if $cities}
                <div class="mb-lg-6 mb-4">
                    <div class="row row-cols-lg-3 row-cols-sm-2 g-3 fs-5">
                        {foreach $this_controller->getMarkedCities() as $city}
                            <div data-region-id="{$city.id}" class="rs-region-marked">
                                <a class="text-decoration-underline">{$city.title}</a></div>
                        {/foreach}
                    </div>
                </div>
            {/if}
            <div>
                <div class="fs-3 mb-lg-4 mb-3">{t}Не нашли свой город?{/t}</div>
                <a class="btn btn-outline-primary col-12 col-lg-auto"
                   data-bs-toggle="collapse" data-bs-target=".change-city-type" role="button">{t}Указать населенный пункт{/t}</a>
            </div>
        </div>
        <div class="collapse change-city-type">
            <form method="post">
                <div class="row g-4">
                    <div>
                        <a class="return-link" data-bs-toggle="collapse" data-bs-target=".change-city-type" role="button">
                            <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                      d="M14.7803 5.72846C15.0732 6.03307 15.0732 6.52693 14.7803 6.83154L9.81066 12L14.7803 17.1685C15.0732 17.4731 15.0732 17.9669 14.7803 18.2715C14.4874 18.5762 14.0126 18.5762 13.7197 18.2715L8.21967 12.5515C7.92678 12.2469 7.92678 11.7531 8.21967 11.4485L13.7197 5.72846C14.0126 5.42385 14.4874 5.42385 14.7803 5.72846Z"/>
                            </svg>
                            <span class="ms-2">{t}К поиску города{/t}</span>
                        </a>
                    </div>
                    <div>
                        <div class="form-label">{t}Страна{/t}</div>
                        {if $country_list}
                            <select class="form-select" name="country_id">
                                {foreach $country_list as $country}
                                    <option value="{$country.id}" {if $country.id == $selected_country_id}selected{/if}>
                                        {$country.title}
                                    </option>
                                {/foreach}
                            </select>
                        {else}
                            <input type="text" name="country" value="{$country.title}" class="form-control">
                        {/if}
                    </div>
                    <div>
                        <div class="form-label">{t}Регион{/t}</div>
                        <div class="rs-region-block">
                            {if $region_list}
                                <select name="region_id" class="form-select">
                                    {foreach $region_list as $region}
                                        <option value="{$region.id}" {if $region.id == $selected_region_id}selected{/if}>
                                            {$region.title}
                                        </option>
                                    {/foreach}
                                </select>
                            {else}
                                <input type="text" name="region" value="{$selected_region.title}" class="form-control">
                            {/if}
                        </div>
                    </div>
                    <div>
                        <label for="input-city21" class="form-label">{t}Населенный пункт{/t}</label>
                        <input type="text" name="city" value="{$address->getCity()->title}" class="form-control">
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary col-12 col-lg-auto">{t}Выбрать{/t}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
{/block}