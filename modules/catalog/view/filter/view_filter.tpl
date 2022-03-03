{assign var=name value=$fitem->getName()}
{assign var=value value=$fitem->getValue()}


{if $prop->isListType()}
    <div>
        <input id="f_{$prop.id}_0" type="checkbox" value="empty" name="{$name}[{$prop.id}][empty]" {if is_array($value[$prop.id]) && isset($value[$prop.id]['empty'])}checked{/if} class="pr-list">
        <label for="f_{$prop.id}_0" class="pr-list-label">{t}- Характеристика не задана у товара -{/t}</label>
    </div>
    <ul class="pr-list">
        {foreach $prop->valuesArr() as $key => $val}
            <li {if isset($filters_allowed_sorted[$prop.id][$key]) && ($filters_allowed_sorted[$prop.id][$key] == false)}class="disabled-property"{/if}
                    {if isset($filters_allowed_sorted['brand'][$key]) && ($filters_allowed_sorted['brand'][$key] == false)}class="disabled-property"{/if}>
                <input id="f_{$prop.id}_{$val@iteration}" type="checkbox" value="{$key}" name="{$name}[{$prop.id}][]" {if is_array($value[$prop.id]) && in_array($key, $value[$prop.id])}checked{/if} class="pr-list">
                <label for="f_{$prop.id}_{$val@iteration}" class="pr-list-label">{$val}</label>
            </li>
        {/foreach}
    </ul>

{elseif $prop.type == 'int'}
    <div>
        <input id="f_{$prop.id}_0" type="checkbox" value="empty" name="{$name}[{$prop.id}][empty]" {if is_array($value[$prop.id]) && isset($value[$prop.id]['empty'])}checked{/if} class="pr-list">
        <label for="f_{$prop.id}_0" class="pr-list-label">{t}Характеристика не задана у товара{/t}</label>
    </div>
    <span class="filter-inp{$prop.id}">
        <input type="text" name="{$name}[{$prop.id}][from]" value="{$value[$prop.id].from}" class="pr-int" placeholder="{t}от{/t}"> -
        <input type="text" name="{$name}[{$prop.id}][to]" value="{$value[$prop.id].to}" class="pr-int" placeholder="{t}до{/t}">
    </span>

{elseif $prop.type == 'bool'}
    <div>
        <input id="f_{$prop.id}_0" type="checkbox" value="empty" name="{$name}[{$prop.id}][empty]" {if is_array($value[$prop.id]) && isset($value[$prop.id]['empty'])}checked{/if} class="pr-list">
        <label for="f_{$prop.id}_0" class="pr-list-label">{t}Характеристика не задана у товара{/t}</label>
    </div>
    <select name="{$name}[{$prop.id}][value]" class="pr-bool">
        <option value="" {if $value[$prop.id]['value'] == ''}selected{/if}>{t}Не важно{/t}</option>
        <option value="1" {if $value[$prop.id]['value'] == '1'}selected{/if}>{t}Есть{/t}</option>
        <option value="0" {if $value[$prop.id]['value'] === '0'}selected{/if}>{t}Нет{/t}</option>
    </select>

{else}
    <div >
        <input id="f_{$prop.id}_0" type="checkbox" value="empty" name="{$name}[{$prop.id}][empty]" {if is_array($value[$prop.id]) && isset($value[$prop.id]['empty'])}checked{/if} class="pr-list">
        <label for="f_{$prop.id}_0" class="pr-list-label">{t}Характеристика не задана у товара{/t}</label>
    </div>
    <input type="text" name="{$name}[{$prop.id}][value]" value="{$value[$prop.id]['value']}" class="pr-str">
{/if}