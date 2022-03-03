<table class="otable">
    {foreach from=$elem.groups item=item}
        <tr>
            <td class="otitle">{$item.name} ({$item.alias})</td>
            <td><input type="checkbox" name="groups[]" value="{$item.alias}" {if $item.alias == $elem->getDefaultGroup() || $item.alias == $elem->getAuthorizedGroup()}disabled{/if} {if in_array($item.alias, $elem.usergroup)}checked{/if}/></td>
        </tr>
    {/foreach}
</table>