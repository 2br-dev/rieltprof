{$filter_value = $fitem->getValue()}
<select name="f[{$fitem->getKey()}][type]" class="form-inline">
    {foreach $fitem->handbookEntityType() as $key => $value}
        <option value="{$key}" {if isset($filter_value.type) && $filter_value.type == $key}selected{/if}>{$value}</option>
    {/foreach}
</select>
<input type="text" name="f[{$fitem->getKey()}][id]" value="{if !empty($filter_value['id'])}{$filter_value['id']}{/if}" placeholder="{t}id объекта{/t}">
