<table class="table">
    <tr>
        <td>{t}Номер задачи{/t}</td>
        <td>{$task->task_num}</td>
    </tr>
    <tr>
        <td>{t}Суть задачи{/t}</td>
        <td>{$task->title}</td>
    </tr>
    <tr>
        <td>{t}Описание задачи{/t}</td>
        <td>{$task->description}</td>
    </tr>
    <tr>
        <td>{t}Статус{/t}</td>
        <td>{$task->getStatus()->title}</td>
    </tr>
    <tr>
        <td>{t}Дата создания{/t}</td>
        <td>{$task.date_of_create|dateformat:"@date @time:@sec"}</td>
    </tr>
    <tr>
        <td>{t}Планируемая дата завершения задачи{/t}</td>
        <td>{$task.date_of_planned_end|dateformat:"@date @time:@sec"}</td>
    </tr>
    <tr>
        <td>{t}Создатель{/t}</td>
        <td>{$task->getCreatorUser()->getFio()}</td>
    </tr>
    <tr>
        <td>{t}Исполнитель{/t}</td>
        <td>{$task->getImplementerUser()->getFio()}</td>
    </tr>

    {foreach $data->user_fields_manager->getStructure() as $item}
        <tr>
            <td>{$item.title}</td>
            <td>{$item.current_val}</td>
        </tr>
    {/foreach}
</table>