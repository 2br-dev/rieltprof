{if $client.id > 0 && ModuleManager::staticModuleExists('support')}
    <div class="tel-line">
        <div class="tel-row">
            {$count = $client->getLastSupport(false)}
            {if $count}
                {$filter = [
                "user_id" => $client.id
                ]}
            {else} {$filter = null} {/if}

            <a href="{adminUrl do=false mod_controller="support-topicsctrl" f=$filter}">{t}Обращений в поддержку{/t}: {if $count > 0}{$count}{else}{t}нет{/t}{/if}</a>
            {if $count}
                <div class="tel-dot"></div>
                <div>
                    <a class="btn btn-default btn-rect btn-inline zmdi zmdi-chevron-down" data-toggle-class="active-more" data-target-closest=".tel-line"></a>
                </div>
            {/if}
        </div>
        {if $count}
            <div class="tel-more-block">
                {foreach $client->getLastSupport() as $topic}
                    <a href="{adminUrl do=false id=$topic.id mod_controller="support-supportctrl"}">№{$topic.id}, {$topic.updated|dateformat:"@date"}</a>
                {/foreach}
            </div>
        {/if}
    </div>
{/if}