<div class="filter typeMultiselect typeClickable typeColor">
    <h4>{$prop.title}: <a class="removeBlockProps hidden" title="{t}Сбросить выбранные параметры{/t}"></a></h4>
    <ul class="propsContent">
        {foreach $prop->getAllowedValuesObjects() as $key => $item_value}
        <li class="clickableItem {if isset($filters_allowed_sorted[$prop.id][$key]) && ($filters_allowed_sorted[$prop.id][$key] == false)}disabled-property{/if}">
            <input type="checkbox" {if is_array($filters[$prop.id]) && in_array($key, $filters[$prop.id])}checked{/if} name="pf[{$prop.id}][]" value="{$key}" class="noStyle clickableItemValue" id="cb_{$prop.id}_{$item_value@iteration}">
            <label for="cb_{$prop.id}_{$item_value@iteration}" title="{$item_value.value}" style="background-color:{$item_value.color}">
                {if $item_value.image}
                    <img src="{$item_value.__image->getUrl(22, 22, 'axy')}">
                {/if}
            </label>
        </li>
        {/foreach}
    </ul>
</div>