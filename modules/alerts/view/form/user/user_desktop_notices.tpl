{$notice_types = $elem.__desktop_notice_locks->alerts_api->getDesktopNoticeTypes()}
{$locks = $elem.__desktop_notice_locks->alerts_api->getAllLockedUserDesktopNotices($elem.id)}
{$sites = $elem.__desktop_notice_locks->sites}

<div>
    {if count($sites)>1}
        <ul class="tab-nav" role="tablist">
            {foreach $sites as $site}
                <li {if $site@first}class="active"{/if}>
                    <a class="" data-target="#tab_noticelock_site_{$site.id}" role="tab" data-toggle="tab">{$site.title}</a>
                </li>
            {/foreach}
        </ul>
    {/if}
    <div class="tab-content">
        {foreach $sites as $site}
            <div class="tab-pane {if $site@first}active{/if}" id="tab_noticelock_site_{$site.id}" role="tabpanel">
                {foreach $notice_types as $key => $data}
                    <label><input type="checkbox" name="desktop_notice_locks[{$site.id}][]" value="{$key}" {if isset($locks[$site.id]) && in_array($key, $locks[$site.id])}checked{/if}> {$data.title}</label><br>
                {/foreach}
            </div>
        {/foreach}
    </div>
</div>