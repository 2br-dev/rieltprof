<div class="g-zindex-zero">
    {include file="%crm%/admin/gant/filter.tpl"}

    <div class="viewport">
        {if $chart_data.tasks}
            <div class="gant">
            <div class="left-part">
                <div class="g-columns g-lines">
                    <i class="num-column"></i>
                    <i class="task-column hidden-xs"></i>
                    <i class="user-column visible-lg-flex"></i>
                </div>
                <div class="g-columns g-head">
                    <div class="num-column">{t}№{/t}</div>
                    <div class="task-column hidden-xs">{t}Задача{/t}</div>
                    <div class="user-column visible-lg-flex">{t}Исполнитель{/t}</div>
                </div>
                {foreach $chart_data.tasks as $task}
                    {$status = $task->getStatus()}
                    {$edit_task_url = {adminUrl do="edit" id=$task.id mod_controller="crm-taskctrl"}}
                    <div class="g-columns g-body" data-row-id="{$task.id}">
                        <div class="num-column">
                            <span class="text-nowrap">
                                <span class="vertMiddle orderStatusColor" style="background-color:{$status.color}"></span>
                                <a href="{$edit_task_url}" class="crud-edit">{$task.task_num}</a>
                            </span>
                        </div>
                        <div class="task-column hidden-xs">
                            <div class="g-text-overflow">
                                {$task.title}
                            </div>
                        </div>
                        <div class="user-column visible-lg-flex">
                            <div class="g-text-overflow">
                                {$task->getImplementerUser()->getShortFio()}
                            </div>
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
                    {foreach $chart_data.tasks as $task}
                        {$status = $task->getStatus()}
                        <div class="g-columns g-body g-day-columns">
                            <div class="g-chart-wrapper" style="grid-column:{$task.grid_column_start}/{$task.grid_column_end};margin-left:{$task.grid_margin_start}%;margin-right:{$task.grid_margin_end}%;"
                                 data-row-id="{$task.id}">
                                <a href="{adminUrl do="edit" id=$task.id mod_controller="crm-taskctrl"}"
                                   style="background: {$status->color};" class="crud-edit g-chart">
                                    <i title="{t status=$status.title}Статус: %status{/t}" data-animation="false" data-trigger="manual"></i></a>
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