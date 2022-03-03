{$groups = $prop->getGroups(false, $switch)}
{foreach $groups as $i => $data}
    {foreach $data.items as $name => $item}
        {if !$item->isHidden()}
        {literal}
        <p class="label">{$elem.__{/literal}{$name}{literal}->getTitle()}</p>
        {include file=$elem.__{/literal}{$name}{literal}->getRenderTemplate() field=$elem.__{/literal}{$name}{literal}}<br>
        {/literal}
        {/if}
    {/foreach}
{/foreach}