<div class="vt-props-item" data-id="{$property->id}">
    <div class="caption"><span class="">{$property->getDir()->title} - {$property->title}</span>  
    <a class="vt-remove-prop">&times;</a></div>
    <div class="values">
        {if $property.type == 'int'}
            {t}от{/t} <input type="text" name="virtual_data_arr[properties][{$property->id}][from]" value="{$data.from}">
            {t}до{/t} <input type="text" name="virtual_data_arr[properties][{$property->id}][to]" value="{$data.to}">
        {elseif $property.type == 'bool'}
            <select name="virtual_data_arr[properties][{$property->id}]">
                <option value="1">{t}Да{/t}</option>
                <option value="0">{t}Нет{/t}</option>
            </select>
        {elseif $property->isListType()}
            {foreach $property->valuesArr() as $id => $value}
            <div class="vt-one-value">
                <label><input type="checkbox" name="virtual_data_arr[properties][{$property->id}][]" value="{$id}" {if is_array($data) && in_array($id, $data)}checked{/if}> {$value}</label>
            </div>    
            {/foreach}
        {else}
            <input type="text" name="virtual_data_arr[properties][{$property->id}]" placeholder="{t}Укажите значение фильтра{/t}" size="100" value="{$data}">
        {/if}
    </div>
</div>