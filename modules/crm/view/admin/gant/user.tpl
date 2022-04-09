<div class="g-zindex-zero">
    {include file="%crm%/admin/gant/filter.tpl"}

    <div class="viewport">
        {if $chart_data.rows}
            <div class="gant">
                <div class="left-part">
                    <div class="g-columns g-lines">
                        <i class="user-column"></i>
                        <i class="ready-column"></i>
                    </div>
                    <div class="g-columns g-head">
                        <div class="user-column">{t}Исполнитель{/t}</div>
                        <div class="ready-column">{t}Выполнено{/t}</div>
                    </div>
                    {foreach $chart_data.rows as $implementor_id => $row}
                        {$edit_task_url = {adminUrl do="edit" id=$task.id mod_controller="crm-taskctrl"}}
                        <div class="g-columns g-body" data-row-id="id-{$implementor_id}">
                            <div class="user-column">
                                <div class="g-text-overflow">
                                    {$row.short_fio|default:"{t}Отсутствует{/t}"}
                                </div>
                            </div>
                            <div class="ready-column">
                                {$row.completed_task_percent}% ({$row.completed_task}/{$row.total_task})<br>
                                <span class="f-10">Просрочено: {$row.expired_task}</small>
                            </div>
                        </div>
                    {/foreach}
                </div>

                <div class="right-part">
                    <div class="fix">
                        <div class="g-columns g-lines">
                            {foreach $chart_data.days as $day}
                                <i {if $day.is_weekend}class="weekend"{/if}></i>
                            {/foreach}
                        </div>
                        {if $chart_data.time_line}
                            <div class="g-columns g-day-columns g-today-line">
                                <div class="g-today" style="grid-column:{$chart_data.time_line.column_start}; margin-left:{$chart_data.time_line.margin_start}%"></div>
                            </div>
                        {/if}
                        <div class="g-columns g-head">
                            {foreach $chart_data.days as $day}
                                <div>{$day.label}</div>
                            {/foreach}
                        </div>
                        {foreach $chart_data.rows as $implemetor_id => $row}
                            <div class="g-user-line">
                                {foreach $row.tasks as $task}
                                    <div class="g-columns g-body g-day-columns">
                                        {$status = $task->getStatus()}
                                        <div data-row-id="id-{$implemetor_id}" class="g-chart-wrapper" style="grid-column:{$task.grid_column_start}/{$task.grid_column_end};margin-left:{$task.grid_margin_start}%;margin-right:{$task.grid_margin_end}%;">
                                            <a href="{adminUrl do="edit" id=$task.id mod_controller="crm-taskctrl"}"
                                               style="background: {$status->color};" class="crud-edit g-chart">
                                               <i title="{t status=$status.title num=$task.task_num mean=$task.title}№ %num (%status)<br>%mean<br>{/t}" data-animation="false" data-trigger="manual"></i>
                                               </a>
                                            {if $task.is_start_at_range}
                                                <div class="g-time-from">{$task.date_of_create|date_format:"H:i"}</div>
                                            {/if}
                                            {if $task.is_end_at_range}
                                                <div class="g-time-to">{$task.date_of_planned_end|date_format:"H:i"|default:"&infin;"}</div>
                                            {/if}
                                        </div>
                                    </div>
                                {/foreach}
                            </div>
                        {/foreach}
                    </div>
                </div>
            </div>
        {else}
            <div class="g-empty">
                <p><img src="{$Setup.IMG_PATH}/adminstyle/empty.svg"></p>
                {t}За выбранный период нет задач{/t}
            </div>
        {/if}
    </div>
</div>