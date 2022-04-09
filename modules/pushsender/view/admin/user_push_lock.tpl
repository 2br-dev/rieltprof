{$locks = $elem.__push_lock->lockApi->getUserLocks($elem.id)}
{$sites=$elem.__push_lock->sites}

<div>
    {if count($sites)>1}
        <ul class="tab-nav" role="tablist">
        {foreach $sites as $site}
            <li {if $site@first}class="active"{/if}>
                <a class="" data-target="#tab_site_{$site.id}" role="tab" data-toggle="tab">{$site.title}</a>
            </li>
        {/foreach}
        </ul>
    {/if}
    <div class="tab-content">
    {foreach $sites as $site}
        <div class="tab-pane {if $site@first}active{/if}" id="tab_site_{$site.id}" role="tabpanel">
        {foreach $elem.__push_lock->lockApi->getPushNotices(false) as $data}
            <div class="app-push-group m-b-20">
                <b>{t appname=$data.title}Приложение: %appname{/t}</b><br><br>
                <label><input type="checkbox" name="push_lock[{$site.id}][{$data.app}][]" value="all" {if isset($locks[$site.id][$data.app]) && in_array('all', $locks[$site.id][$data.app])}checked{/if}> Все</label><br>
                {foreach $data.notices as $key => $notice}
                <label><input type="checkbox" name="push_lock[{$site.id}][{$data.app}][]" value="{$key}" {if isset($locks[$site.id][$data.app]) && in_array($key, $locks[$site.id][$data.app])}checked{/if}> {$notice}</label><br>
                {/foreach}
            </div>
        {/foreach}
        </div>
    {/foreach}
    </div>
</div>