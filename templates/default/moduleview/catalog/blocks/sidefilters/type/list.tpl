<div class="filter typeMultiselect">
    <h4>{$prop.title}: <a class="removeBlockProps hidden" title="{t}Сбросить выбранные параметры{/t}"></a></h4>
    <ul class="propsContentSelected hidden"></ul>
    <div class="propsContainer">
        <ul class="propsContent">
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