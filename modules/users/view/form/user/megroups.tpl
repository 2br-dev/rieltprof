<table class="otable">
    <tbody>
        <tr class="editrow">
            <td class="ochk" width="20">
                <input id="me-users-user-groups" title="" type="checkbox" class="doedit" name="doedit[]" value="groups" data-original-title="Отметьте, чтобы применить изменения по этому полю">
            </td>
            <td class="otitle">
                <label for="me-users-user-groups">Группы</label>&nbsp;&nbsp;
            </td>
            <td>
                <div class="multi_edit_rightcol coveron">
                    <div class="cover"></div>
                    <table>
                        {foreach from=$elem.groups item=item}
                            <tr>
                                <td class="otitle">{$item.name} ({$item.alias})</td>
                                <td><input type="checkbox" name="groups[]" value="{$item.alias}" {if $item.alias == $elem->getDefaultGroup() || $item.alias == $elem->getAuthorizedGroup()}disabled{/if}/></td>
                            </tr>
                        {/foreach}
                    </table>
                </div>
            </td>
        </tr>
    </tbody>
</table>
