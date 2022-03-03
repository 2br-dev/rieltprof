{if $no_rights}
    <div class="empty-widget">
        {t}Недостаточно прав на просмотр задач{/t}
    </div>
{else}
<div class="widget-filters">
    <div class="dropdown">
        <a id="task-filter-switcher" data-toggle="dropdown" class="widget-dropdown-handle">{$task_filters[$task_active_filter]} <i class="zmdi zmdi-chevron-down"></i></a>
        <ul class="dropdown-menu" aria-labelledby="task-filter-switcher">
            {foreach $task_filters as $filter_key => $filter_title}
                <li{if $task_active_filter == $filter_key} class="act"{/if}>
                    <a data-update-url="{adminUrl mod_controller="crm-widget-task" task_active_filter=$filter_key}" class="call-update">{$filter_title}</a>
                </li>
            {/foreach}
        </ul>
    </div>
</div>

{if count($tasks)}
    <div class="m-l-20 m-r-20 no-space m-b-20">
        <table border="0" class="wtable overable">
            <thead>
            <tr>
                <th class="l-w-space"></th>
                <th></th>
                <th>{t}Номер{/t}</th>
                <th>{t}Суть{/t}</th>
                <th>{t}Создано{/t}</th>
                <th>{t}Срок{/t}</th>
                <th class="r-w-space"></th>
            </tr>
            </thead>
            <tbody>
            {foreach $tasks as $task}
                {$status = $task->getStatus()}

                <tr data-url="{adminUrl mod_controller="crm-taskctrl" do="edit" id=$task.id context="widget"}" data-crud-options='{ "updateThis": true }' class="clickable crud-edit">
                    <td class="l-w-space"></td>
                    <td><span style="background:{$status->color}" title="{$status->title}" class="w-point"></span></td>
                    <td>{$task.task_num}</td>
                    <td>{$task.title}</td>
                    <td class="w-date">{$task.date_of_create|dateformat:"@date @time"}</td>
                    <td class="w-date">{$task.date_of_planned_end|dateformat:"@date @time"|default:"{t}нет{/t}"}</td>
                    <td class="r-w-space"></td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
{else}
    <div class="empty-widget">
        {t}Нет ни одной задачи{/t}
    </div>
{/if}

{include file="%SYSTEM%/admin/widget/paginator.tpl" paginatorClass="with-top-line"}
{/if}