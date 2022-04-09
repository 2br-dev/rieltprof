{addjs file="%catalog%/rscomponent/sidefilters.js"}
{$catalog_config = ConfigLoader::byModule('catalog')}

<div class="rs-filter-wrapper">
    <div class="rs-filter-section {if $basefilters || $filters}rs-filter-active{/if}" data-query-value="{$url->get('query', $smarty.const.TYPE_STRING)}">
        <div class="catalog-filter">
            <div class="catalog-filter__head">
                <div class="h3">
                    <img width="24" height="24" src="{$THEME_IMG}/icons/filter.svg" alt="">
                    <span class="ms-2">{t}Фильтры{/t}</span>
                </div>
                <div>
                    <div class="offcanvas-close">
                        <img src="{$THEME_IMG}/icons/close.svg" width="24" height="24" alt="">
                    </div>
                </div>
            </div>
            <form method="GET" class="rs-filters" action="{urlmake filters=null pf=null bfilter=null p=null}" autocomplete="off">
                <div class="accordion filter-accordion mt-4">
                    {if $param.show_cost_filter}
                        {$is_open = $basefilters.cost || (is_array($param.expanded) && in_array('cost', $param.expanded))}
                        <div class="accordion-item rs-type-interval">
                            <div class="accordion-header">
                                <button class="accordion-button {if !$is_open}collapsed{/if}" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#accordionCostFilter">
                                    <span class="me-2">{t}Цена{/t}</span>
                                </button>
                            </div>
                            <div id="accordionCostFilter" class="accordion-collapse collapse {if $is_open}show{/if}">
                                <div class="accordion-body">
                                    <div class="row row-cols-2 g-3">
                                        <div>
                                            <label class="form-label">{t}От{/t}</label>
                                            <input type="number" class="form-control rs-filter-from"
                                                   min="{$moneyArray.interval_from}" max="{$moneyArray.interval_to}" name="bfilter[cost][from]"
                                                   value="{if !$catalog_config.price_like_slider}{$basefilters.cost.from}{else}{$basefilters.cost.from|default:$moneyArray.interval_from}{/if}"
                                                   data-start-value="{if $catalog_config.price_like_slider}{$moneyArray.interval_from|floatval}{/if}">
                                        </div>
                                        <div>
                                            <label class="form-label">{t}До{/t}</label>
                                            <input type="number" min="{$moneyArray.interval_from}" max="{$moneyArray.interval_to}"
                                                   class="form-control rs-filter-to" name="bfilter[cost][to]"
                                                   value="{if !$catalog_config.price_like_slider}{$basefilters.cost.to}{else}{$basefilters.cost.to|default:$moneyArray.interval_to}{/if}"
                                                   data-start-value="{if $catalog_config.price_like_slider}{$moneyArray.interval_to|floatval}{/if}">
                                        </div>
                                        {if $catalog_config.price_like_slider && ($moneyArray.interval_to > $moneyArray.interval_from)}
                                        <div class="col-12">
                                            <div class="px-3">
                                                <input type="hidden" data-slider='{ "from":{$moneyArray.interval_from}, "to":{$moneyArray.interval_to},
                                                        "step": "{$moneyArray.step}", "round": {$moneyArray.round}, "dimension": " {$moneyArray.unit}",
                                                        "heterogeneity": [{$moneyArray.heterogeneity}]  }'
                                                       value="{$basefilters.cost.from|default:$moneyArray.interval_from};{$basefilters.cost.to|default:$moneyArray.interval_to}"
                                                       class="rs-plugin-input"/>
                                            </div>
                                        </div>
                                        {/if}
                                    </div>
                                </div>
                            </div>
                        </div>
                    {/if}
                    {if $param.show_is_num}
                        {$is_open = $basefilters.isnum != '' || (is_array($param.expanded) && in_array('num', $param.expanded))}
                        <div class="accordion-item rs-type-radio">
                            <div class="accordion-header">
                                <button class="accordion-button {if !$is_open}collapsed{/if}" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#accordionNum">
                                    <span class="me-2">{t}Наличие{/t}</span>
                                </button>
                            </div>
                            <div id="accordionNum" class="accordion-collapse collapse {if $is_open}show{/if}">
                                <div class="accordion-body">
                                    <ul class="filter-list">
                                        <li>
                                            <div class="radio check">
                                                <input id="cb-isnum-empty" type="radio" {if !isset($basefilters.isnum)}checked{/if} name="bfilter[isnum]" value="" data-start-value class="radio">
                                                <label for="cb-isnum-empty">
                                                    <span class="radio-attr">
                                                        {include file="%THEME%/helper/svg/radio.tpl"}
                                                    </span>
                                                    <span>{t}Неважно{/t}</span>
                                                </label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="radio check">
                                                <input id="cb-isnum-yes" type="radio" {if $basefilters.isnum == '1'}checked{/if} name="bfilter[isnum]" value="1" class="radio">
                                                <label for="cb-isnum-yes">
                                                    <span class="radio-attr">
                                                        {include file="%THEME%/helper/svg/radio.tpl"}
                                                    </span>
                                                    <span>{t}Есть{/t}</span>
                                                </label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="radio check">
                                                <input id="cb-isnum-no" type="radio" {if $basefilters.isnum == '0'}checked{/if} name="bfilter[isnum]" value="0" class="radio">
                                                <label for="cb-isnum-no">
                                                    <span class="radio-attr">
                                                        {include file="%THEME%/helper/svg/radio.tpl"}
                                                    </span>
                                                    <span>{t}Нет{/t}</span>
                                                </label>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    {/if}

                    {if $param.show_brand_filter && $brands && count($brands) > 1}
                        {$is_open = $basefilters.brand || (is_array($param.expanded) && in_array('brand', $param.expanded))}
                        <div class="accordion-item rs-type-multiselect">
                            <div class="accordion-header">
                                <button class="accordion-button {if !$is_open}collapsed{/if}" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#accordionBrand">
                                    <span class="me-4">{t}Бренд{/t}</span>
                                </button>
                                <a class="filter-clear rs-clear-one-filter"><img src="{$THEME_IMG}/icons/close.svg" alt="" width="16"></a>
                            </div>
                            <div id="accordionBrand" class="accordion-collapse collapse {if $is_open}show{/if}">
                                <div class="accordion-body">
                                    <ul class="filter-list mb-4 rs-selected d-none"></ul>
                                    <ul class="filter-list rs-unselected">
                                        {foreach $brands as $brand}
                                            <li style="order: {$brand@iteration};" {if isset($filters_allowed_sorted['brand'][$brand.id]) && ($filters_allowed_sorted['brand'][$brand.id] == false)}class="disabled-property"{/if}>

                                                <div class="checkbox check">
                                                    <input id="cb-brand-{$brand.id}" type="checkbox" {if is_array($basefilters.brand) && in_array($brand.id, $basefilters.brand)}checked{/if} name="bfilter[brand][]" value="{$brand.id}">
                                                    <label for="cb-brand-{$brand.id}">
                                                        <span class="checkbox-attr">
                                                            {include file="%THEME%/helper/svg/checkbox.tpl"}
                                                        </span>
                                                        <span>{$brand.title}</span>
                                                    </label>
                                                </div>
                                            </li>
                                        {/foreach}
                                    </ul>
                                </div>
                            </div>
                        </div>
                    {/if}

                    {foreach $prop_list as $item}
                        {foreach $item.properties as $prop}
                            {include file="%catalog%/blocks/sidefilters/type/{$prop.type}.tpl"}
                        {/foreach}
                    {/foreach}
                </div>
            </form>
        </div>
        <div class="catalog-offcanvas-buttons">
            <button class="btn btn-primary offcanvas-close w-100 mt-3 catalog-filter__apply">{t}Применить фильтр{/t}</button>
            <button type="button" class="btn btn-outline-primary offcanvas-close col-12 mt-3 catalog-filter__clean rs-clean-filter">{t}Сбросить фильтры{/t}</button>
        </div>
    </div>
</div>