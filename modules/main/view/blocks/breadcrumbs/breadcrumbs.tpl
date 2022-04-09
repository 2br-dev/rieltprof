{$bc = $app->breadcrumbs->getBreadCrumbs()}
{if !empty($bc)}
<nav class="breadcrumb" aria-label="breadcrumb">
    <ul class="breadcrumb__list">
        {foreach from=$bc item=item name="path"}
            {if empty($item.href)}
                <li class="breadcrumb__item"><span>{$item.title}</span></li>
            {else}
                <li class="breadcrumb__item"><a href="{$item.href}" {if $smarty.foreach.path.first}class="first"{/if}>{$item.title}</a></li>
            {/if}
        {/foreach}
    </ul>
</nav>
{/if}