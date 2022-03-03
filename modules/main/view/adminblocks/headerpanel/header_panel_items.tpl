{foreach $items as $item}
<li>
    <a {foreach $item.attr as $key => $value}{$key}="{$value}" {/foreach}>
        {if $item.attr.icon}<i class="rs-icon rs-icon-{$item.attr.icon}"></i>{/if}
        <span>{$item.title}</span>
    </a>
</li>
{/foreach}