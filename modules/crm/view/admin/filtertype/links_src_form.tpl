{$groups=$prop->getGroups(false, $switch)}
{foreach $groups as $i => $data}
    {foreach $data.items as $name => $item}
        {if !$item->isHidden()}
        {literal}
            <div class="form-group">
            {include file=$elem.__{/literal}{$name}{literal}->getRenderTemplate() field=$elem.__{/literal}{$name}{literal}}
            </div>
        {/literal}
        {/if}
    {/foreach}
{/foreach}