{$task = $data->task}

<h1>{t num=$task->task_num}Изменена задача №%num{/t}
    {if $task->creator_user_id} {t}от{/t} {$task->getCreatorUser()->getFio()}{/if}</h1>

<p>{t}Суть задачи{/t}: {$task.title}</p>

<h3>{t}Подробности изменений{/t}</h3>

<table class="table">
    <thead>
        <tr>
            <th>{t}Название поля{/t}</th>
            <th>{t}Старое значение{/t}</th>
            <th>{t}Новое значение{/t}</th>
        </tr>
    </thead>
    <tbody>
    {foreach $data->changed_values as $key => $item}
        <tr>
            <td>{$item.title}</td>
            <td>{$item.before_value}</td>
            <td>{$item.current_value}</td>
        </tr>
    {/foreach}
    </tbody>
</table>