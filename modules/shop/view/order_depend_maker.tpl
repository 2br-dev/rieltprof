{literal}{* Файл генерируется автоматически исходя из полей объекта Order *}{/literal}
{$groups = $prop->getGroups(false, $switch, false)}

<table class="otable">
    {foreach $groups as $i => $data}
        {foreach $data.items as $name => $item}
            {literal}
                <tr>
                <td class="otitle">{$elem.__{/literal}{$name}{literal}->getTitle()}&nbsp;&nbsp;{if $elem.__{/literal}{$name}{literal}->getHint() != ''}<a class="help-icon" title="{$elem.__{/literal}{$name}{literal}->getHint()|escape}">?</a>{/if}
                </td>
                <td>{include file=$elem.__{/literal}{$name}{literal}->getRenderTemplate() field=$elem.__{/literal}{$name}{literal}}</td>
                </tr>{/literal}
        {/foreach}
    {/foreach}
</table>