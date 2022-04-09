{assign var=bc value=$app->breadcrumbs->getBreadCrumbs()}
{if !empty($bc)}
<nav class="breadcrumb">
    <i>
        {foreach from=$bc item=item name="path"}
            {if empty($item.href)}
                <i><span {if $smarty.foreach.path.first}class="first"{/if}>{$item.title}</span></i>
            {else}
                <i><a href="{$item.href}" {if $smarty.foreach.path.first}class="first"{/if}>{$item.title}</a></i>
            {/if}
        {/foreach}
    </i>
</nav>
{/if}