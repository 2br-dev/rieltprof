<table class="otable">
{$elem->calculateUserCost()}
{foreach $elem->getCostList() as $onecost}
    <tr>
        <td class="otitle">{$onecost.title}</td>
        <td>
            <input type="text" name="excost[{$onecost.id}][cost_original_val]" value="{if $onecost.type != 'auto'}{$elem.excost[$onecost.id]['cost_original_val']}{/if}" {if $onecost.type == 'auto'}disabled{/if}>

            {if $onecost.type=='manual'}
                <select name="excost[{$onecost.id}][cost_original_currency]">
                    {foreach from=$elem->getCurrencies() key=id item=curr}
                        <option value="{$id}" {if $elem.excost[$onecost.id].cost_original_currency == $id}selected{/if}>{$curr}</option>
                    {/foreach}
                </select>
            {else}
                <span class="m-l-10 help-icon" title="{t}Автовычесляемое поле, будет расчитано после сохранения{/t}">?</span>
            {/if}
        </td>
    </tr>
{/foreach}
</table>