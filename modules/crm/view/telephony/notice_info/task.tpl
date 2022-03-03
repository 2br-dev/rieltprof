<div class="tel-line">
    <div class="tel-row">
        {$task_count = $client->getLastTasks(false)}
        {if $task_count}
            {if $client.id > 0}
                {$filter = [
                "links" => [
                "crm-linktypeuser" => ["user_id" => $client.id],
                "crm-linktypecall" => ["call_history_id" => $call_history.id]
                ]]}
            {else}
                {$filter = [
                "links" => [
                "crm-linktypecall" => ["call_history_id" => $call_history.id]
                ]
                ]}
            {/if}
        {else} {$filter = null} {/if}
        <a href="{adminUrl do=false mod_controller="crm-taskctrl" f=$filter}">{t}Задач{/t}: {if $task_count > 0}{$task_count}{else}{t}нет{/t}{/if}</a>
        <div class="tel-dot"></div>
        <div>
            {if $task_count}
                <a class="btn btn-default btn-rect btn-inline zmdi zmdi-chevron-down" data-toggle-class="active-more" data-target-closest=".tel-line"></a>
            {/if}
            <a href="{adminUrl do="add" from_call=$call_history.id mod_controller="crm-taskctrl"}" class="btn btn-warning btn-rect btn-inline zmdi zmdi-plus crud-edit" title="{t}Создать задачу{/t}"></a>
        </div>
    </div>
    {if $task_count}
        <div class="tel-more-block">
            {foreach $client->getLastTasks() as $task}
                <a href="{adminUrl do=edit id=$task.id mod_controller="crm-taskctrl"}" class="crud-edit">{$task.task_num}</a>
            {/foreach}
        </div>
    {/if}
</div>