{* Шаблон для фильтра с типом - список *}

<div class="filter filter-checkbox rs-type-multiselect {if $filters[$prop.id] || $prop.is_expanded}open{/if}">
    <a class="expand">
        <span class="right-arrow"><i class="pe-2x pe-7s-angle-down-circle"></i></span>
        <span>{$prop.title} {if $prop.unit}({$prop.unit}){/if}
            <span class="filter-remove rs-remove hidden" title="{t}Сбросить выбранные параметры{/t}"><i class="pe-va pe-7s-close-circle"></i></span></span>
    </a>
    <div class="detail">

        <ul class="filter-checkbox_selected rs-selected hidden"></ul>
        <div class="filter-checkbox_container">
            <ul class="filter-checkbox_content rs-content">
                {$i = 1}
                {foreach $prop->getAllowedValues() as $key => $value}
                    <li style="order: {$i++};" {if isset($filters_allowed_sorted[$prop.id][$key]) && ($filters_allowed_sorted[$prop.id][$key] == false)}class="disabled-property"{/if}>
                        <input type="checkbox" {if is_array($filters[$prop.id]) && in_array($key, $filters[$prop.id])}checked{/if} name="pf[{$prop.id}][]" value="{$key}" class="cb" id="cb_{$prop.id}_{$value@iteration}">
                        <label for="cb_{$prop.id}_{$value@iteration}">{$value}</label>
                    </li>
                {/foreach}
            </ul>
        </div>
    </div>
</div>