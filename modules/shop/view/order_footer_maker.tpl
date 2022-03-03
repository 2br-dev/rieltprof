{literal}{* Файл генерируется автоматически исходя из полей объекта Order *}{/literal}
{$groups = $prop->getGroups(false, $switch)}
{foreach $groups as $i => $data}
    {foreach $data.items as $name => $item}
        {if $item->isVisible($switch, false)}
        {literal}
        {include file=$elem.__{/literal}{$name}{literal}->getRenderTemplate() field=$elem.__{/literal}{$name}{literal}}
        {/literal}
        {/if}
    {/foreach}
{/foreach}