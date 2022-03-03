{$task = $data->task}

<h1>{t num=$task->task_num}Назначена новая задача №%num{/t}
    {if $task->creator_user_id} {t}от{/t} {$task->getCreatorUser()->getFio()}{/if}</h1>

{include file="%crm%/notice/task_table.tpl" task=$task}