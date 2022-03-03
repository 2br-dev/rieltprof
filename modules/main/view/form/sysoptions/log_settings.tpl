{addjs file="rs.loglist.js"}

{$log_manager = $field->log_manager}
{$settings = $elem->log_settings}

<div class="log-settings rs-log-list">
    <div class="log-list">
        {foreach $log_manager->getLogList() as $id => $log}
            <div class="log-item rs-log-item">
                <div class="log-item-title-line">
                    <div class="log-item-title-text rs-log-item-toggle">
                        <span>{$log->getTitle()}</span>
                        <i class="zmdi zmdi-chevron-down f-18"></i>
                    </div>
                    <label>
                        <input type="checkbox" name="log_settings[{$id}][enabled]" value="1" {if $log->isEnabled()}checked{/if}>
                        <span class="log-item-enabled-label">{t}Включен{/t}</span>
                    </label>
                </div>
                <div>{$log->getDescription()}</div>

                <div class="log-item-details">
                    <table class="otable">
                        <tr>
                            <td class="otitle">{t}Уровни логирования{/t}</td>
                            <td>
                                {foreach $log->getLogLevelList() as $level => $title}
                                    <div>
                                        <label>
                                            <input type="checkbox" name="log_settings[{$id}][levels][{$level}]" value="{$level}" {if $log->isEnabledLevel($level)}checked{/if}>
                                            <span>{$title}</span>
                                        </label>
                                    </div>
                                {/foreach}
                            </td>
                        </tr>
                        <tr>
                            <td class="otitle">{t}Максимальный размер файла (Мб){/t}</td>
                            <td>
                                <input type="text" name="log_settings[{$id}][max_file_size]" value="{$log->getMaxFileSize()}">
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        {/foreach}
    </div>
</div>
