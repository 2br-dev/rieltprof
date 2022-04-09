{foreach $items as $item}
<li>
    <a {foreach $item.attr as $key => $value} {if $key != 'icon'}{$key}="{$value}"{/if} {/foreach}>
        {if $item.attr.icon}<i class="rs-icon rs-public-icon {$item.attr.icon}"><!----></i>{/if}
        <span>{$item.title}</span>
    </a>
</li>
{/foreach}