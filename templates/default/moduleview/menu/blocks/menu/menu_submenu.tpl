{if $items}
    <nav class="menuSubMenu">
        {foreach from=$items item=item}
            <a href="{$item.fields->getHref()}" {if $item.fields.target_blank}target="_blank"{/if}>{$item.fields.title}</a>
        {/foreach}
    </nav>
{/if}