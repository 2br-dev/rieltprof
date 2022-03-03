{assign var=bc value=$app->breadcrumbs->getBreadCrumbs()}
{if !empty($bc)}
<nav class="breadcrumb" xmlns:v="http://rdf.data-vocabulary.org/#">
    <i>
        {foreach from=$bc item=item name="path"}
            {if empty($item.href)}
                <i typeof="v:Breadcrumb">
                    <span {if $smarty.foreach.path.first}class="first"{/if} property="v:title">{$item.title}</span>
                </i>
            {else}
                <i typeof="v:Breadcrumb">
                    <a href="{$item.href}" {if $smarty.foreach.path.first}class="first"{/if} rel="v:url" property="v:title">{$item.title}</a>
                </i>
            {/if}
        {/foreach}
    </i>
</nav>
{/if}