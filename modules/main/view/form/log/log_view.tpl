{addjs file="rs.loglist.js"}

<h2 class="title">{t}Просмотр логов{/t}</h2>
<div class="log-settings rs-log-list">
    <div class="log-list">
        {foreach $log_manager->getLogList() as $id => $log}
            <div class="log-item rs-log-item">
                <div class="log-item-title-line">
                    <h4 class="log-item-title-text rs-log-item-toggle">{$log->getTitle()}</h4>
                </div>
                <div>{$log->getDescription()}</div>

                <div class="log-item-details">
                    <table class="otable">
                        <tr>
                            <td class="otitle">{t}Лог-файлы{/t}</td>
                            <td>
                                {foreach $log->getFileLinks() as $site_id => $file}
                                    <a href="{adminurl act='view' log_class=$log->getIdentifier() site_id=$site_id}">
                                        {if $site_id == 0}
                                            {t}Без сайта{/t}
                                        {else}
                                            {$site_list[$site_id]['title']}
                                        {/if}
                                    </a>
                                {/foreach}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        {/foreach}
    </div>
</div>
