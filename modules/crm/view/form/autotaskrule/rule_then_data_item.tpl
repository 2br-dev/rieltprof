<tr class="task-item">

    <td class="drag drag-handle text-center"><a class="sort">
        <i class="zmdi zmdi-unfold-more"></i>
    </a></td>

    <td class="task-number">{$index}</td>
    <td><a class="edit">{$task_tpl.title}</a>
        <input type="hidden" name="rule_then_data_arr[]" value="{$task_tpl->getBase64Values()}" class="task-value">
    </td>
    <td>
        {if $task_tpl.implementer_user_type == 'custom_user'}
            {if $task_tpl.implementer_user_id}
                {$task_tpl->getImplementerUser()->getFio()} ({$task_tpl.implementer_user_id})
            {else}
                {t}Не выбрано{/t}
            {/if}
        {else}
            {$task_tpl.__implementer_user_type->textView()}
        {/if}
    </td>
    <td>{$task_tpl->getDurationView('date_of_planned_end')|default:"{t}Нет{/t}"}</td>
    <td>{if $task_tpl.is_autochange_status}{t}Есть{/t}{else}{t}Нет{/t}{/if}</td>
    <td class="actions">
        <div class="inline-tools">
            <a class="tool edit" title="{t}Редактировать{/t}"><i class="zmdi zmdi-edit"></i></a>
            <a class="tool remove" title="{t}удалить{/t}"><i class="zmdi zmdi-delete c-red"></i></a>
        </div>
    </td>
</tr>