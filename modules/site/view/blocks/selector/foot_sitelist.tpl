<ul class="bottomSites">
    {foreach from=$sites item=item}
    <li {if $item.id == $current_site}class="act"{/if}><a href="{$item->getRootUrl(true)}">{$item.title}</a></li>
    {/foreach}
</ul>