{* Шаблон для фильтра с типом - список изображений *}

<div class="filter filter-clickable filter-image rs-type-multiselect {if $filters[$prop.id] || $prop.is_expanded}open{/if}">
    <a class="expand">
        <span class="right-arrow"><i class="pe-2x pe-7s-angle-down-circle"></i></span>
        <span>{$prop.title} {if $prop.unit}({$prop.unit}){/if}
            <span class="filter-remove rs-remove hidden" title="{t}Сбросить выбранные параметры{/t}"><i class="pe-va pe-7s-close-circle"></i></span></span>
    </a>
    <div class="detail">
        <ul>
            {foreach $prop->getAllowedValuesObjects() as $key => $item_value}
                <li class="filter-clickable_item {if isset($filters_allowed_sorted[$prop.id][$key]) && ($filters_allowed_sorted[$prop.id][$key] == false)}disabled-property{/if}">
                    <input type="checkbox" {if is_array($filters[$prop.id]) && in_array($key, $filters[$prop.id])}checked{/if} name="pf[{$prop.id}][]" value="{$key}" class="filter-clickable_value" id="cb_{$prop.id}_{$item_value@iteration}">
                    <label for="cb_{$prop.id}_{$item_value@iteration}" title="{$item_value.value}">
                        {if $item_value.image}
                            <img src="{$item_value.__image->getUrl(60, 60, 'cxy')}">
                        {/if}
                    </label>
                </li>
            {/foreach}
        </ul>
    </div>
</div>