<div class="rs-group sitelist">
    <span class="rs-active">{$current.title}</span>
    <ul class="rs-dropdown">
        {foreach $sites as $site}
        <li {if $site@first}class="first"{/if}><a href="{$router->getUrl('main.admin', ['Act' => 'changeSite', 'site' => $site.id])}">{$site.title}</a></li>
        {/foreach}
    </ul>
</div>