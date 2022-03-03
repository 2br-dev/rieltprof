<div class="rs-group sitelist">
    <span class="rs-active">{$current.title}</span>
    <ul class="rs-dropdown">
        {foreach from=$sites item=site name="siteselect"}
        <li {if $smarty.foreach.siteselect.first}class="first"{/if}><a href="{$router->getUrl('main.admin', ['Act' => 'changeSite', 'site' => $site.id])}">{$site.title}</a></li>
        {/foreach}
    </ul>
</div>