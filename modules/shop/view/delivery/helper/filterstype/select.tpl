<select id="{$fitem->getId()}" data-key="{$fitem->getKey()}">
    {foreach $fitem->getList() as $key=>$val}
        <option value="{$key}" {if $key==$fitem->getDefault()}selected="selected"{/if}>{$val}</option>
    {/foreach}
</select>